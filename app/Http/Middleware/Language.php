<?php

namespace App\Http\Middleware;

use App;
use Closure;
use Illuminate\Http\Request;

class Language {

	public function handle(Request $request, Closure $next, $guard = null) {
		$lang = $request->segment(1);
		if(!in_array($lang, config('app.locales'))) {
			$lang = 'en';
		}

		if($request->getPathInfo() == '/en') {
			return redirect('/');
		}

		App::setLocale($lang);

		return $next($request);
	}
}
