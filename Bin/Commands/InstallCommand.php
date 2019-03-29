<?php namespace WPSettingsFramework\Bin\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;

class InstallCommand extends Command {

	protected static $defaultName = 'install';

	protected function configure() {
		$this->addArgument(
			'namespace',
			InputArgument::REQUIRED,
			'Namespace of your plugin'
		);

		$this->addArgument(
			'folder',
			InputArgument::OPTIONAL,
			'Namespace of your plugin',
			'src'
		);

	}

	protected function execute( InputInterface $input, OutputInterface $output ) {
		$output->writeln('=========================================================');
		$output->writeln('Set namespace: ' . $input->getArgument('namespace'));
		$output->writeln('=========================================================');
		$output->writeln('Move to path: ' . $input->getArgument('folder'));
		$output->writeln('done.');
	}
}