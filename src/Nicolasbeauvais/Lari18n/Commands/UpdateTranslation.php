<?php

namespace Nicolasbeauvais\Lari18n\Commands;

use Config;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Nicolasbeauvais\Lari18n\Lari18n;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Class UpdateTranslation
 * @package Nicolasbeauvais\Lari18n\Commands
 */
class UpdateTranslation extends Command
{

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'lari18n:update';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Update translations';

	/**
	 * Create a new command instance.
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$Lari18n = Lari18n::getInstance();

		// Get language basic data
		$data = $Lari18n->retrieveI18nData();

		$master = Config::get('app.fallback_locale');
		$masterData = array_dot($data['languagesData'][$master]);

		$masterLanguagesKeys = array_keys($data['languages'], $master);

		// Remove master language from arrays
		unset($data['languages'][$masterLanguagesKeys[0]]);
		unset($data['languagesData'][$master]);

		// If only one locale
		if (empty($data['languages'])) {
			$this->error('There is only one locale, nothing to update');
		}

		$slaves = $data['languages'];
		$slavesData = $data['languagesData'];

		$updated = array_flip($slaves);

		// Initialise
		foreach ($updated as $key => $value) {
			$updated[$key] = 0;
		}

		// Walk on all master translation
		foreach ($masterData as $key => $value) {

			// Verify for each slave translation
			foreach ($slavesData as $locale => $files) {

				// If translation doesn't exist, create it
				if (array_get($files, $key) === null) {
					var_dump($master, $locale, $key, $Lari18n->todo_translation_key . $value);
					$Lari18n->translate($master, $locale, $key, $Lari18n->todo_translation_key . $value);
					$updated[$locale]++;
				}
			}
		}

		foreach ($updated as $locale => $number) {
			$this->info($number . ' ' . $locale . ' locale lines updated');
		}
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array();
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array();
	}

	public function error($string)
	{
		parent::error($string);
		die;
	}
}
