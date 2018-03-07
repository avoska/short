<?php

namespace App\Console\Commands;

use App\Dll;
use Carbon\Carbon;
use DB;
use Illuminate\Console\Command;
use League\Csv\Reader;

class ImportDb extends Command {

	protected $signature = 'app:import-db {--companies-file= : Path to companies CSV file} {--dlls-file= : Path to dlls CSV file}';

	protected $description = 'Import dlls from CSV file';

	protected $companies = [];

	protected $csvFields = [
		'alias',
		'lang',
		'md5',
		'sha1',
		'name',
		'size',
		'bits',
		'lang_code',
		'desc',
		'product_desc',
		'company',
		'ver',
	];

	protected function clearOldData() {
		DB::table('dll')->delete();
	}

	protected function getCompanies() {
		$companies = [];
		$lines = file($this->option('companies-file'));
		foreach($lines as $line) {
			$line = explode(';', $line);
			if(isset($line[1])) {
				$companies[trim($line[0])] = trim($line[1]);
			}
		}
		return $companies;
	}

	public function handle() {

		$this->info('Clear old data');
		$this->clearOldData();

		$this->companies = $this->getCompanies();

		$filePath = $this->option('dlls-file');
		foreach($this->getCsvIterator($filePath, $this->csvFields) as $csvRow) {
			if($csvRow) {
				Dll::insert(array_filter($csvRow));
			}
		}

		$this->info("Added " . Dll::count() . " dll's in DB");
	}

	protected function fixUtf($string) {
		return str_replace('?', '', mb_convert_encoding($string, 'UTF-8', 'UTF-8'));
	}

	protected function getAliasByName($name) {
		return trim(preg_replace('/[^a-z0-9]+/', '-', strtolower($name)), '-');
	}

	protected function handleCsvRowData($row) {
		if(!$row['bits'] || !$row['sha1']) {
			return;
		}

		foreach($row as &$value) {
			$value = $this->fixUtf(trim($value));
		}

		unset($row['lang_code']);

		if(!$row['company']) {
			$row['company'] = 'Other';
		}

		$row['company_alias'] = $this->companies[$row['company']] ?? $row['company'];
		$row['company_url'] = $this->getAliasByName($row['company_alias']);
		$row['lang'] = $row['lang'] ?: 'U.S. English';
		$row['downloads'] = mt_rand(500, 5000);
		$row['rating'] = mt_rand(40, 50) / 10;
		$row['votes'] = mt_rand(5, 300);
		$row['created_at'] = Carbon::createFromTimestamp(mt_rand(time() - 60 * 60 * 24 * 365, time()));
		return $row;
	}

	protected function getCsvIterator($filePath, $fields = []) {
		$rowsHandler = function($row) {
			return $this->handleCsvRowData($row);
		};

		$reader = Reader::createFromPath($filePath);
		$reader->setDelimiter(';');
		return $fields ? $reader->fetchAssoc($fields, $rowsHandler) : $reader->fetch($rowsHandler);
	}

	protected function convertHumanSizeToNumber($humanSize) {
		if(!$humanSize) {
			return null;
		}
		$multipliers = [
			'Gb' => 1000000000,
			'Mb' => 1000000,
			'Kb' => 1000,
			'B' => 1,
		];
		foreach($multipliers as $name => $multiplier) {
			if(strpos($humanSize, $name)) {
				return str_replace($name, '', $humanSize) * $multiplier;
			}
		}
		throw new ImportInvalidDataException("Wrong human size format: $humanSize");
	}
}

class ImportInvalidDataException extends \Exception {

}

