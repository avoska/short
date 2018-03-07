<?php

namespace App;

use Backpack\CRUD\CrudTrait;

/**
 * @property $id
 * @method remember
 */
abstract class Model extends \Eloquent {

	use CrudTrait;

	public $timestamps = false;

	protected function newBaseQueryBuilder() {
		$conn = $this->getConnection();
		$grammar = $conn->getQueryGrammar();
		$builder = new CustomQueryBuilder($conn, $grammar, $conn->getPostProcessor());
		$builder->setEnabled(!env('DB_NO_CACHE'));

		if(isset($this->rememberFor)) {
			$builder->remember($this->rememberFor);
		}

		if(isset($this->rememberCacheTag)) {
			$builder->cacheTags($this->rememberCacheTag);
		}

		return $builder;
	}

}
