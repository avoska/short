<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property $id
 * @property $link
 * @property $created_at
 * @property $updated_at
 *
 * @property LinkStats[] $stats
 */
class Link extends Model {

	public $timestamps = true;

	protected $table = 'links';

	public function stats() {
		return $this->hasMany(LinkStats::class);
	}
}
