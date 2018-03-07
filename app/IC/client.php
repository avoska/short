<?php

	/*** WARNING: DO NOT MODIFY THIS FILE !!! ***/

	if (!class_exists('IC_Encoder'))
		require_once 'encoder.php';

	/**
	 * installCore client for ISP
	 *
	 * @version   2.1
	 * @copyright ironSource 2016
	 */
	class IC_Client {

		// Error codes
		const E_ISP_CONNECTION   = 200;
		const E_INVALID_USERID   = 201;
		const E_INVALID_URL      = 203;
		const E_INVALID_RESPONSE = 205;

		// ISP request timeout
		const ISP_CONNECT_TIMEOUT = 10;
		const ISP_GLOBAL_TIMEOUT  = 20;

		// Version params
		const LANG 		= 'PHP';
		const VERSION 	= '2.1';

		private $encoder;
		private $user_id = 0;
		private $service_domain = '';
		private $service_port = '';
		private $default_domain = '';
		private $default_port = '';
		private $use_ssl = false;

		/**
		 * Class constructor
		 *
		 * @param int $user_id
		 * @param string $key_location
		 * @param string $service_domain
		 * @param string $default_domain
		 *
		 * @return IC_Client
		 * @throws IC_ClientException
		 */
		public function __construct($user_id, $key_location, $service_domain, $default_domain = '') {
			if (!extension_loaded('curl')) {
				die('curl PHP extension is required to use this library! See http://php.net/manual/en/book.curl.php');
			}
			if (!is_numeric($user_id)) {
				throw new IC_ClientException('Invalid user ID provided', self::E_INVALID_USERID);
			}
			if (empty($service_domain)) {
				throw new IC_ClientException('No service domain provided', self::E_ISP_CONNECTION);
			}

			$this->user_id = (int) $user_id;
			$this->encoder = new IC_Encoder($key_location);
			list($this->service_domain, $this->service_port) = $this->get_domain($service_domain);
			list($this->default_domain, $this->default_port) = $this->get_domain($default_domain);
			$this->default_domain = !empty($default_domain) ? $this->default_domain : '';
		}

		/**
		 * Method return download link for stub with specified user ID and parameters
		 *
		 * @param array $parameters
		 * @param string $download_as
		 * @param string $fallback_url
		 *
		 * @return string
		 * @throws IC_ClientException
		 */
		public function get_link($parameters = array(), $download_as = '', $fallback_url = '', $ext = array(), $conf = array()) {
		    $combined_array = array(
                'inject'    => $parameters,
                'ext'       => $ext,
                'config'    => $conf
            );
			// Get parameters encoded using provided key
			$encoded = $this->encoder->encode($combined_array);
			
			// Prepare service URL query string
			$request_qs = array(
				'ic_user_id' => $this->user_id,
				'c'          => 1,
				'l'	     	 => self::LANG,
				'v'	     	 => self::VERSION
			);
			if (!empty($download_as)) {
				$request_qs['downloadAs'] = $download_as;
			}
			if (!empty($fallback_url)) {
				$request_qs['fallback_url'] = $fallback_url;
			}

			// Build service URL
			$service_url = ($this->use_ssl ? 'https://' : 'http://')
						 . $this->service_domain
						 . ($this->service_port ? ':'.$this->service_port : '')
				         . '/?' . http_build_query($request_qs);
			// Prepare request headers
			$request_headers = array(
				'Content-Type: application/x-www-form-urlencoded',
				// Use X-Forwarded-For if exists, otherwise use client IP address
				// Might help in order to calculate pages views -> download clicks
				'X-Forwarded-For: ' . (!empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'])
			);

			// Prepare request data
			// Shouldn't include fallback URL (!)
			$request_data = array(
							   'ic_user_id' => $this->user_id,
							   'data'       => $encoded['data'],
							   'key'        => $encoded['key']
							);

			try {
				// Call to ISP service
				if (($ch = curl_init()) !== false) {
					// cURL options
					// function curl_setopt_array is not used because of minimal PHP version requirement (5.1.3)
					curl_setopt($ch, CURLOPT_URL, $service_url);
					if ($this->service_port)
						curl_setopt($ch, CURLOPT_PORT, $this->service_port);
					curl_setopt($ch, CURLOPT_POST, true);
					curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($request_data));
					curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
					curl_setopt($ch, CURLOPT_USERAGENT, isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');
					curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::ISP_CONNECT_TIMEOUT);
					curl_setopt($ch, CURLOPT_TIMEOUT, self::ISP_GLOBAL_TIMEOUT);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

					$response = curl_exec($ch);
					$ch_info  = curl_getinfo($ch);
					$err_msg  = curl_error($ch);
					$err_code = curl_errno($ch);
					curl_close($ch);

					$http_code = !empty($ch_info['http_code']) ? $ch_info['http_code'] : 500;
					if ($response === false || $http_code != 200) {
						throw new IC_ClientException("{$err_code}: {$err_msg}", self::E_ISP_CONNECTION);
					}

					// Prepare download URL
					$parsed_res = @parse_url($response);

					if (empty($parsed_res['host'])) {
						if (empty($this->default_domain)) {
							throw new IC_ClientException('No default domain provided, could not use ISP response', self::E_INVALID_RESPONSE);
						} else {
							$parsed_res['host'] = $this->default_domain;
							$parsed_res['port'] = $this->default_port;
						}
					}
					$du_query = (!empty($parsed_res['query']) ? '?' . $parsed_res['query'] : '');
					$du_scheme = (!empty($parsed_res['scheme']) ? $parsed_res['scheme'] : 'http') . ':/';
					$du_path   = (!empty($parsed_res['path']) ? trim($parsed_res['path'], '/') : '');

					if (!empty($parsed_res['port']))
						$download_url = implode('/', array($du_scheme, $parsed_res['host'] . ':' . $parsed_res['port'], $du_path . $du_query));
					else
						$download_url = implode('/', array($du_scheme, $parsed_res['host'], $du_path . $du_query));
				} else {
					throw new IC_ClientException('Failed to initialize cURL client', self::E_ISP_CONNECTION);
				}
			} catch (Exception $e) {
				if (!empty($fallback_url)) {
					$download_url = $fallback_url;
				} else {
					throw new IC_ClientException('Failed to get download URL', self::E_ISP_CONNECTION, $e);
				}
			}
			return $download_url;
		}

		/**
		 * Method validate and return domain and port from provided URL
		 *
		 * @param string $url
		 *
		 * @return array
		 * @throws IC_ClientException
		 */
		private function get_domain($url) {
			if (empty($url)) {
				throw new IC_ClientException('Invalid URL provided', self::E_INVALID_URL);
			}
			if (($parsed_url = @parse_url($url)) === false) {
				throw new IC_ClientException('Failed to parse provided URL', self::E_INVALID_URL);
			}
			!empty($parsed_url['port']) ? $port = $parsed_url['port'] : $port = '';
				
			if (!empty($parsed_url['host'])) {
				$domain = $parsed_url['host'];
			} elseif (!empty($parsed_url['path'])) {
				$domain = $parsed_url['path'];
			} else {
				throw new IC_ClientException('Failed to parse provided URL', self::E_INVALID_URL);
			}
			return array(trim($domain, '/'), $port);
		}

	}

	class IC_ClientException extends Exception {};
