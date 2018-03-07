<?php

namespace App\Http\Controllers;

use App;
use App\Hasher;
use App\Link;
use App\LinkStats;
use DB;
use Illuminate\Routing\Controller;
use Jenssegers\Agent\Agent;
use Request;

class MainController extends Controller {

	/** @var Hasher */
	protected $hasher;

	/**
	 * MainController constructor.
	 */
	public function __construct(Hasher $hasher) {
		$this->hasher = $hasher;
	}

	public function index() {
		return view('main');
	}

	public function create(Hasher $hasher) {
		$link = new Link();
		$link->link = request('link'); // there should be validation that link is valid URL
		$link->save();

		return view('new', [
			'hash' => $hasher->numberToHash($link->id),
		]);
	}

	public function short($hash) {
		$link = $this->getLinkByHash($hash);

		$agent = new Agent();
		$stat = new LinkStats();
		$stat->link_id = $link->id;
		$stat->user_ip = request()->getClientIp();
		$stat->user_browser = $agent->browser();
		$stat->user_os = $agent->platform(); // $agent->device()
		$stat->save();
		$url = 'http://' . $link->link;

		return redirect($url, 301);
	}

	protected function getLinkByHash($hash) {
		return Link::findOrFail($this->hasher->hashToNumber($hash));
	}

	public function stats($hash) {
		$link = $this->getLinkByHash($hash);
		$query = LinkStats::where('link_id', $link->id);

		return view('stats', [
			'statsByBrowser' => (clone $query)->select('user_browser', DB::raw("COUNT(*) as views"))->groupBy('user_browser')->get(),
			'statsByOs' => (clone $query)->select('user_os', DB::raw("COUNT(*) as views"))->groupBy('user_os')->get(),
			'views' => (clone $query)->count(),
			'unique' => (clone $query)->count(DB::raw('DISTINCT user_ip')),
		]);
	}
}
