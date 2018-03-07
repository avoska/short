<?php

namespace App\Http\Controllers\Admin;

use App\Rom;

class RomsController extends CrudController {

	public function __construct() {
		parent::__construct();

		$this->crud->enableAjaxTable();

		$this->crud->setModel(Rom::class);
		$this->crud->setEntityNameStrings('rom', 'roms');
		$this->crud->setRoute('admin/roms');
		$this->crud->denyAccess(['create']);

		$this->crud->setColumns(['id', 'name', 'descTitle', 'description']);

		$this->crud->addFields([
			[
				'name' => 'id',
				'type' => 'integer',
			],
			[
				'name' => 'name',
				'type' => 'text',
			],
			[
				'name' => 'descTitle',
				'Label' => 'Title',
				'type' => 'text',
			],
			[
				'name' => 'description',
				'Label' => 'Description',
				'type' => 'textarea',
			],
		]);
	}
}