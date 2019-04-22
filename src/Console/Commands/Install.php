<?php namespace WPSettingsFramework\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class Install extends Command {

	const FRAMEWORK_NAMESPACE = 'WPSettingsFramework\Framework';
	const RAW_FILES_FOLDER = 'src/Framework';

	const INSERT_FILES_FOLDER = 'Settings';

	/**
	 * @var string
	 */
	protected static $defaultName = 'install';

	/**
	 * @var mixed
	 */
	protected $question;

	public function execute( InputInterface $input, OutputInterface $output ) {
		$projectPath     = getcwd();
		$questionManager = $this->getHelper( 'question' );

		if ( is_dir( $projectPath ) ) {

			$composerJson = $projectPath . '/composer.json';

			if ( is_file( $composerJson ) ) {

				$composerJson = file_get_contents( $composerJson );
				$composerJson = json_decode( $composerJson, true );

				if ( $composerJson && ! empty( $composerJson['autoload']['psr-4'] ) ) {

					foreach ( $composerJson['autoload']['psr-4'] as $namespace => $path ) {

						$path = $projectPath . '/' . $path;

						$path = new Question( "\e[32mInstallation path\e[0m ({$path}):", $path );
						$path = $questionManager->ask( $input, $output, $path );

						$namespace = new Question( "\e[32mInstallation namespace\e[0m ({$namespace}):", $namespace );
						$namespace = $questionManager->ask( $input, $output, $namespace );

						$rawFilesPath = realpath( __DIR__ . '/../../../' . self::RAW_FILES_FOLDER );

						if ( is_dir( $rawFilesPath ) ) {

							$files = [];
							$this->getAllRawFiles( $rawFilesPath, $files, self::RAW_FILES_FOLDER );

							if ( ! empty( $files ) ) {
								$this->copyWithNamespace( array_values( $files )[0], $namespace,
									$path . '/' . self::INSERT_FILES_FOLDER, $output );
							}
						} else {
							throw new LogicException( 'Fail' );
						}

						echo " \e[32m Done. \e[0m" . PHP_EOL . PHP_EOL;
					}
				} else {
					throw new LogicException( 'invalid composer.json' );
				}
			} else {
				throw new LogicException( 'composer.json not found' );
			}
		} else {
			throw new LogicException( 'Invalid working dir' );
		}
		exit;
	}

	/**
	 * @param $files
	 * @param $namespace
	 * @param $path
	 * @param OutputInterface $output
	 */
	public function copyWithNamespace( $files, $namespace, $path, $output ) {
		foreach ( $files as $subDir => $filePath ) {
			if ( is_array( $filePath ) ) {
				$this->copyWithNamespace( $filePath, $namespace, $path . '/' . $subDir, $output );
			} else {
				$pathToClientFile = $path . '/' . basename( $filePath );

				$output->writeln( $filePath . ' ----> ' . $pathToClientFile );

				$this->createNeededFolders( $pathToClientFile );

				file_put_contents( $pathToClientFile, $this->addNameSpace( $filePath, $namespace ) );
			}
		}
	}

	/**
	 * @param $filePath
	 * @param $namespace
	 *
	 * @return string|string[]|null
	 */
	public function addNameSpace( $filePath, $namespace ) {
		$file = file_get_contents( $filePath );

		return str_replace( self::FRAMEWORK_NAMESPACE, $namespace, $file );
	}

	/**
	 * @param $pathToFile
	 */
	public function createNeededFolders( $pathToFile ) {

		$fileName = basename( $pathToFile );
		$folders  = explode( '/', str_replace( '/' . $fileName, '', $pathToFile ) );

		$currentFolder = '';
		foreach ( $folders as $folder ) {
			$currentFolder .= $folder . DIRECTORY_SEPARATOR;
			if ( ! file_exists( $currentFolder ) ) {
				mkdir( $currentFolder, 0755 );
			}
		}
	}

	/**
	 * @param $dir
	 * @param $files
	 * @param string $subDir
	 */
	public function getAllRawFiles( $dir, &$files, $subDir = '/' ) {

		if ( $dh = opendir( $dir ) ) {

			while ( ( $file = readdir( $dh ) ) !== false ) {

				if ( is_dir( $dir . '/' . $file ) && realpath( $dir . '/' . $file ) !== realpath( $dir . '/../' ) && realpath( $dir . '/' . $file ) !== realpath( $dir ) ) {
					$this->getAllRawFiles( $dir . '/' . $file, $files[ $subDir ], $file );
				} else {
					if ( is_file( $dir . '/' . $file ) ) {
						$files[ $subDir ][] = $dir . '/' . $file;
					}
				}
			}
			closedir( $dh );
		}
	}
}