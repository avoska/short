<?php

namespace App\Http\Controllers\Admin;

abstract class CrudController extends \Backpack\CRUD\app\Http\Controllers\CrudController {

	public function update() {
		return parent::updateCrud();
	}

	public function store() {
		return parent::storeCrud();
	}
}
