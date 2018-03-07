<?php

namespace App\Http\Controllers\Admin;

use App\Emulator;

class EmulatorsController extends CrudController {

	public function __construct() {
		parent::__construct();

		$this->crud->enableAjaxTable();

		$this->crud->setModel(Emulator::class);
		$this->crud->setEntityNameStrings('emulator', 'emulators');
		$this->crud->setRoute('admin/emulators');
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
