<?php namespace Nicolasbeauvais\Lari18n;

use Illuminate\Translation\TranslationServiceProvider;

/**
 * Class Lari18nServiceProvider
 * @package Nicolasbeauvais\Lari18n
 */
class Lari18nServiceProvider extends TranslationServiceProvider
{
	public function boot()
	{
		$this->app->bindShared('translator', function($app)
		{
			$loader = $app['translation.loader'];
			$locale = $app['config']['app.locale'];

			$trans = new Translator($loader, $locale);

			$trans->setFallback($app['config']['app.fallback_locale']);

			return $trans;
		});

		parent::boot();
	}
}
