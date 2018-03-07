<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectTables extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {

		Schema::create('links', function(Blueprint $table) {
			$table->increments('id');
			$table->string('link');
			$table->timestamps();
		});

		Schema::create('links_stats', function(Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('link_id');
			$table->string('user_ip');
			$table->string('user_browser')->nullable();
			$table->string('user_os')->nullable();
			$table->timestamps();

			$table->foreign('link_id')->references('id')->on('links');
		});
	}

	public function down() {
		Schema::drop('links_stats');
		Schema::drop('links');
	}
}
