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
		$masterData = $data['languagesData'][$master];
		$masterDataDot = array_dot($masterData);

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
			$updated[$key] = [
				'add'    => 0,
				'delete' => 0
			];
		}

		// Walk on all master translations
		foreach ($masterDataDot as $key => $value) {

			// Verify for each slave translation
			foreach ($slavesData as $locale => $files) {

				// If translation doesn't exist, create it
				if (array_get($files, $key) === null) {

					$Lari18n->translate($master, $locale, $key, $Lari18n->todo_translation_key . $value);
					$updated[$locale]['add']++;
				}
			}
		}

		if (!$this->option('remove')) {

			foreach ($updated as $locale => $numbers) {
				$this->info('+' . $numbers['add'] . ' ' . $locale . ' locale lines updated');
			}

			return;
		}

		// On each localisation
		foreach ($slavesData as $key => $slaveData) {

			$slaveDataDot = array_dot($slaveData);

			// Walk on slave translations
			foreach ($slaveDataDot as $keySlave => $valueSlave) {

				// If translation doesn't exist in master, erase it
				if (array_get($masterData, $keySlave) === null) {
					$Lari18n->remove($key, $keySlave);
					$updated[$key]['delete']++;
				}
			}
		}

		foreach ($updated as $locale => $numbers) {
			$this->info('+' . $numbers['add'] . ' -' . $numbers['delete'] . ' ' . $locale . ' locale lines updated');
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
		return array(
			array('remove', null, InputOption::VALUE_NONE, 'Remove the missing translation that doesn\'t exist in the fallback local files', null)
		);

	}

	public function error($string)
	{
		parent::error($string);
		die;
	}
}
