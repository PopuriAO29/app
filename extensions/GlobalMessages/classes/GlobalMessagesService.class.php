<?php

/**
 * Class GlobalMessagesService
 * Read-only singleton for retrieving extension lists
 */
class GlobalMessagesService {
	const I18N_FILE_EXTENSION = ".i18n.php";
	const PHP_FILE_EXTENSION = ".php";
	const CORE_LOCALISATION_FILE_REGEX = "/^.+GlobalMessages[A-Z]+\.i18n\.php$/i";
	const LOCALISATION_FILE_REGEX = "/^.+\.(i18n|aliases|alias).php$/i";

	private $rootDir;
	private $messageFiles = null;

	private static $instance;

	/**
	 * GlobalMessagesService private constructor
	 */
	private function __construct() {
		global $IP;
		$this->rootDir = $IP;
	}

	/**
	 * @return GlobalMessagesService
	 */
	public static function getInstance() {
		if ( !self::$instance ) {
			self::$instance = new static();
		}
		return self::$instance;
	}

	public function getCoreMessageFiles() {
		$messageFiles = $this->getMessageFiles();
		return array_filter( $messageFiles, function ( $messageFile ) {
			return preg_match( self::CORE_LOCALISATION_FILE_REGEX, $messageFile );
		} );
	}

	public function getExtensionMessageFiles() {
		$messageFiles = $this->getMessageFiles();
		return array_filter( $messageFiles, function ( $messageFile ) {
			return !preg_match( self::CORE_LOCALISATION_FILE_REGEX, $messageFile );
		} );
	}

	/**
	 * Helper function to include all localisation files to localisation cache rebuild process.
	 * It looks for i18n and alias files from whole code directory and returns them
	 * Implements local cache
	 */
	private function getMessageFiles() {
		if ( !isset( $this->messageFiles ) ) {
			$messageFiles = [ ];
			$directory = new RecursiveDirectoryIterator( $this->rootDir );
			$iterator = new RecursiveIteratorIterator( $directory );
			$files = new RegexIterator( $iterator, self::LOCALISATION_FILE_REGEX, RecursiveRegexIterator::GET_MATCH );

			foreach ( $files as $file ) {
				$filePath = $file[0];
				$directoryPath = mb_ereg_replace( $this->rootDir, '', $filePath );
				$directoryPath = mb_ereg_replace( '(' . self::I18N_FILE_EXTENSION . '|' . self::PHP_FILE_EXTENSION . ')$', '', $directoryPath );

				$messageFiles[$directoryPath] = $filePath;
			}
			ksort($messageFiles);
			$this->messageFiles = $messageFiles;
		}
		return $this->messageFiles;
	}
}
