<?php

namespace App\Http\ViewComposers;


class MenuComposer {

	protected $cacheTime = 60;

	public function compose() {
		static $menu;

		if(!$menu) {
			$menu = new MenuViewData();
		}

		view()->share('menu', $menu);
		view()->share('isWindows', $menu);
	}
}

class MenuViewData {


	public $romsPlatforms = [];
	public $romsPlatformsCount;

	public $emulatorsPlatforms = [];
	public $emulatorsPlatformsCount;

}

