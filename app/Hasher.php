<?php namespace App;

class Hasher {

	protected $alphabet;

	public function __construct($alphabet) {
		$this->alphabet = $alphabet;
	}

	public function numberToHash($number) {
		$hash = '';
		$alphabetLength = strlen($this->alphabet);

		do {
			$hash = $this->alphabet[$number % $alphabetLength] . $hash;
			$number = floor($number / $alphabetLength);
		}
		while($number);

		return $hash;
	}

	public function hashToNumber($hash) {
		$number = 0;
		$alphabetLength = strlen($this->alphabet);

		for($i = 0; $i < strlen($hash); $i++) {
			$number = $number * $alphabetLength + strpos($this->alphabet, $hash[$i]);
		}

		return $number;
	}
}
