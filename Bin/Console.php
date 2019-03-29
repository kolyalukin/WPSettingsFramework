<?php namespace WPSettingsFramework\Bin;

require_once realpath( __DIR__ . '/../vendor/autoload.php' );

use Composer\Script\Event;

class Console {

	/**
	 *
	 */
	const RAW_FILES_FOLDER = 'src';
	/**
	 *
	 */
	const INSERT_FILES_FOLDER = 'Settings';
	/**
	 *
	 */
	const FOLDER_NAME = 'Settings';

	/**
	 * @param Event $event
	 */
	public static function install( Event $event ) {
		$parentFolder = realpath( $event->getComposer()->getConfig()->get( 'vendor-dir' ) . '/../../../../' );
		if ( is_dir( $parentFolder ) ) {
			$composerJson = $parentFolder . '/composer.json';
			if ( is_file( $composerJson ) ) {
				$composerJson = file_get_contents( $composerJson );
				$composerJson = json_decode( $composerJson, true );

				if ( $composerJson ) {

					foreach ( $composerJson['autoload']['psr-4'] as $namespace => $path ) {

						$path = $parentFolder . '/' . $path;

						$install = $event->getIO()->ask( "Install in \e[31m{$path}\e[0m folder? (yes/no) ", 'yes' );

						if ( $install != 'yes' ) {
							return;

						}

						$namespace = $event->getIO()->ask( 'Which namespace install with (' . $namespace . ')? ',
							$namespace );

						$rawFilesPath = realpath( $event->getComposer()->getConfig()->get( 'vendor-dir' ) . '/../' . self::RAW_FILES_FOLDER );

						if ( is_dir( $rawFilesPath ) ) {
							$files = [];
							self::getAllRawFiles( $rawFilesPath, $files, self::RAW_FILES_FOLDER );

							if ( ! empty( $files ) ) {
								self::copyWithNameSpace( array_values( $files )[0], $namespace,
									$path . '/' . self::INSERT_FILES_FOLDER );
							}
						}
						echo "Done." . PHP_EOL . PHP_EOL;
					}
				}
			}
		}
		exit;
	}

	/**
	 * @param $files
	 * @param $namespace
	 * @param $path
	 */
	public static function copyWithNameSpace( $files, $namespace, $path ) {
		foreach ( $files as $subDir => $filePath ) {
			if ( is_array( $filePath ) ) {
				self::copyWithNameSpace( $filePath, $namespace, $path . '/' . $subDir );
			} else {
				echo $filePath . "  ---->  " . $path . '/' . basename( $filePath ) . PHP_EOL . PHP_EOL;

				$pathToFile = $path . '/' . basename( $filePath );

				self::createNeededFolders( $pathToFile );

				file_put_contents( $pathToFile, self::addNameSpace( $filePath, $namespace ) );
			}
		}
	}

	/**
	 * @param $filePath
	 * @param $namespace
	 *
	 * @return string|string[]|null
	 */
	public static function addNameSpace( $filePath, $namespace ) {
		$file = file_get_contents( $filePath );

		return preg_replace( '/(?<=\<\?php namespace )(?=[\w])/i', $namespace, $file );
	}

	/**
	 * @param $pathToFile
	 */
	public static function createNeededFolders( $pathToFile ) {

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
	public static function getAllRawFiles( $dir, &$files, $subDir = '/' ) {

		if ( $dh = opendir( $dir ) ) {

			while ( ( $file = readdir( $dh ) ) !== false ) {

				if ( is_dir( $dir . '/' . $file ) && realpath( $dir . '/' . $file ) !== realpath( $dir . '/../' ) && realpath( $dir . '/' . $file ) !== realpath( $dir ) ) {
					self::getAllRawFiles( $dir . '/' . $file, $files[ $subDir ], $file );
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