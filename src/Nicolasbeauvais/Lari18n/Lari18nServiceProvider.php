<?php namespace Nicolasbeauvais\Lari18n;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class Lari18nServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('nicolasbeauvais/lari18n');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app['lari18n'] = $this->app->share(function($app)
		{
			return new Lari18n;
		});

		$this->app->booting(function()
		{
			$loader = AliasLoader::getInstance();
			$loader->alias('lari18n', 'Nicolasbeauvais\Lari18n\Lari18nFacade');
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('lari18n');
	}

}
