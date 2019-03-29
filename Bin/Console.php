<?php namespace WPSettingsFramework\Bin;

require_once realpath( __DIR__ . '/../vendor/autoload.php' );

use Composer\Script\Event;
use Composer\Installer\PackageEvent;

class Console {

	/**
	 *
	 */
	const RAW_FILES_FOLDER = 'src';
	/**
	 *
	 */
	const INSERT_FILES_FOLDER = 'test';
	/**
	 *
	 */
	const FOLDER_NAME = 'Settings';

	/**
	 * @param Event $event
	 */
	public static function install( Event $event ) {

		$autoload = $event->getComposer()->getPackage()->getAutoload();

		if ( isset( $autoload['psr-4'] ) ) {
			foreach ( $autoload['psr-4'] as $namespace => $path ) {
				echo PHP_EOL;
				echo PHP_EOL;
				echo 'Project Namespace    --------------> ' . $namespace . PHP_EOL;
				echo PHP_EOL;
				echo PHP_EOL;
				echo 'Project Install Path --------------> ' . $path . PHP_EOL;
				echo PHP_EOL;
				echo PHP_EOL;

				$rawFilesPath = realpath( $event->getComposer()->getConfig()->get( 'vendor-dir' ) . '/../' . self::RAW_FILES_FOLDER );

				if ( is_dir( $rawFilesPath ) ) {
					$files = [];
					self::getAllRawFiles( $rawFilesPath, $files, self::RAW_FILES_FOLDER );

					if ( ! empty( $files ) ) {
						$insertFilesPath = realpath( $event->getComposer()->getConfig()->get( 'vendor-dir' ) . '/../' . self::INSERT_FILES_FOLDER );
						self::copyWithNameSpace( array_values( $files )[0], $namespace, $insertFilesPath );
					}
				}
			}
			echo "Done." . PHP_EOL . PHP_EOL;
		}
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