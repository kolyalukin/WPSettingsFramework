<?php namespace WPSettingsFramework\Console;

use Symfony\Component\Console\Application;
use WPSettingsFramework\Console\Commands\Install;

class Console {

	public static function handle() {

		$application = new Application();

		$application->add( new Install() );

		try {
			$application->run();
		} catch ( \Exception $e ) {
			echo $e->getMessage();
		}
	}
}
