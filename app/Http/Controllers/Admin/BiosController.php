<?php

namespace App\Http\Controllers\Admin;

use App\Bios;

class BiosController extends CrudController {

	public function __construct() {
		parent::__construct();

		$this->crud->enableAjaxTable();

		$this->crud->setModel(Bios::class);
		$this->crud->setEntityNameStrings('bios', 'bios');
		$this->crud->setRoute('admin/bios');
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
