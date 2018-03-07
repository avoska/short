<?php

namespace App\Http\Controllers\Admin;

use App\Platform;

class PlatformsController extends CrudController {

	public function __construct() {
		parent::__construct();

		$this->crud->enableAjaxTable();

		$this->crud->setModel(Platform::class);
		$this->crud->setEntityNameStrings('console', 'consoles');
		$this->crud->setRoute('admin/consoles');
		$this->crud->denyAccess(['create']);
		$this->crud->setColumns(['id', 'name', 'shortName', 'alias', 'descTitle', 'description']);

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
				'name' => 'shortName',
				'label' => 'Short Name',
				'type' => 'text',
			],
			[
				'name' => 'alias',
				'type' => 'text',
			],
			[
				'name' => 'descTitle',
				'label' => 'Description Title',
				'type' => 'text',
			],
			[
				'name' => 'description',
				'label' => 'Description',
				'type' => 'textarea',
			],
		]);
	}

}
