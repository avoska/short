<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property $id
 * @property $link_id
 * @property $user_ip
 * @property $user_browser
 * @property $user_os
 * @property $created_at
 * @property $updated_at
 */
class LinkStats extends Model {

	public $timestamps = true;

	protected $table = 'links_stats';

}
