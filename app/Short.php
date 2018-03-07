<?php

namespace App;

/**
 * @property $id
 * @property $name
 * @property $alias
 * @property $lang
 * @property $md5
 * @property $sha1
 * @property $size
 * @property $bits
 * @property $desc
 * @property $product_desc
 * @property $company
 * @property $company_alias
 * @property $company_url
 * @property $ver
 * @property $downloads
 * @property $rating
 * @property $votes
 * @property $created_at
 */
class Short extends Model {

	public $timestamps = true;

	protected $table = 'links';

}
