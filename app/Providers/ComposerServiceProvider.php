<?php

namespace App\Providers;

use App\Http\ViewComposers\MenuComposer;
use Illuminate\Support\ServiceProvider;

class ComposerServiceProvider extends ServiceProvider {

	/**
	 * Register bindings in the container.
	 *
	 * @return void
	 */
	public function boot() {
		view()->composer('*', MenuComposer::class);
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register() {
		//
	}
}
