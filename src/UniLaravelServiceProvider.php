<?php

namespace fahmifitu\UniLaravel;

use Illuminate\Support\ServiceProvider;

class UniLaravelServiceProvider extends ServiceProvider
{

	public function boot()
	{
		$this->publishes([
			__DIR__ . '/../config/uni.php' => config_path('uni.php')
		]);
	}

	public function register()
	{
		$this->app->singleton('uni-laravel', function () {
			return new Uni;
		});
		$this->app->alias('uni-laravel', Uni::class);
	}
}
