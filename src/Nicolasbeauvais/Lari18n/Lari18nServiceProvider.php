<?php namespace Nicolasbeauvais\Lari18n;

use Illuminate\Translation\TranslationServiceProvider;
use Nicolasbeauvais\Lari18n\Commands\NewTranslation;
use Nicolasbeauvais\Lari18n\Commands\UpdateTranslation;

/**
 * Class Lari18nServiceProvider
 * @package Nicolasbeauvais\Lari18n
 */
class Lari18nServiceProvider extends TranslationServiceProvider
{

	protected $defer = false;

	public function boot()
	{
		$this->package('nicolasbeauvais/lari18n');

		include __DIR__ . '/../../routes.php';

		$this->app->bindShared('translator', function($app)
		{
			// Instantiate Translator
			$loader = $app['translation.loader'];
			$locale = $app['config']['app.locale'];

			$trans = new Translator($loader, $locale, $this->app['lari18n']);

			$trans->setFallback($app['config']['app.fallback_locale']);

			return $trans;
		});

		Lari18n::getInstance()->setTranslator($this->app['translator']);

		// Provide commands
		$this->app->bind('lari18n::command.new.translation', function($app) {
			return new NewTranslation();
		});
		$this->app->bind('lari18n::command.update.translation', function($app) {
			return new UpdateTranslation();
		});

		$this->commands(array(
			'lari18n::command.new.translation',
			'lari18n::command.update.translation'
		));

		parent::boot();
	}

	public function register()
	{
		// Register filter
		$this->app['lari18n'] = $this->app->share(function ($app) {
			return Lari18n::getInstance();
		});

		$this->app['router']->after(function ($request, $response) {

			if (Lari18n::isActivated()) {
				$this->app['lari18n']->modifyResponse($request, $response);
			}
		});

		parent::register();
	}
}
