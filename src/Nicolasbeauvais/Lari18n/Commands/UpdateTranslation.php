<?php

namespace Nicolasbeauvais\Lari18n\Commands;

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
		// @TODO
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
