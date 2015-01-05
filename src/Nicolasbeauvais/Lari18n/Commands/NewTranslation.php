<?php

namespace Nicolasbeauvais\Lari18n\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Nicolasbeauvais\Lari18n\Lari18n;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Class NewTranslation
 * @package Nicolasbeauvais\Lari18n\Commands
 */
class NewTranslation extends Command
{

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'lari18n:new';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create a new translation for lari18n.';

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
		// @DEBUG
		File::deleteDirectory('C:\wamp\www\Dropbox\lari18n\app/lang/fr');

		$from = $this->argument('from_locale');
		$to = $this->argument('to_locale');

		$Lari18n = Lari18n::getInstance();
		$data = $Lari18n->retrieveI18nData();

		$languages = $data['languages'];
		$languagesPath = $data['paths']['lang'];

		// check
		if (!in_array($from, $languages)) {
			$this->error('There is no locale [' . $from . ']');
		}

		if (in_array($to, $languages)) {
			$this->error('The [' . $to . '] locale already exist');
		}

		// Create the new language directory
		File::copyDirectory($languagesPath . '/' . $from, $languagesPath . '/' . $to);

		$files = File::allFiles($languagesPath . '/' . $to);

		$Lari18n->reinitialiseFiles($files, $to);

		$this->info('The [' . $to . '] translation directory as been created from [' . $from . '] translations files');
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('from_locale', InputArgument::REQUIRED, 'en'),
			array('to_locale', InputArgument::REQUIRED, 'fr')
		);
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
