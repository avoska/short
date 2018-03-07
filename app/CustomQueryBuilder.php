<?php namespace App;

use DB;
use Watson\Rememberable\Query\Builder;

class CustomQueryBuilder extends Builder {

	protected $isEnabled = true;

	public function setEnabled($isEnabled) {
		$this->isEnabled = $isEnabled;
	}

	public function remember($minutes, $key = null) {
		if($this->isEnabled) {
			return parent::remember($minutes, $key);
		}
		return $this;
	}

	public function orderByEquals($field, array $values) {
		return static::orderByRaw('FIELD(`' . $field . '`, ' . implode(',', array_map(array(DB::getPdo(), 'quote'), array_reverse($values))) . ') desc');
	}
}
