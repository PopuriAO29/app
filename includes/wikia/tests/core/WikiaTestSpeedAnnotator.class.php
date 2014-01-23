<?php

class WikiaTestSpeedAnnotator {
	const SLOW_TEST_THRESHOLD = 0.002; // 0.002s = 2ms

	// resolution is equal to number of significant characters of SLOW_TEST_THRESHOLD plus two.
	const TEST_EXECUTION_TIME_RESOLUTION = 5;

	private static $methods = [ ];
	private static $timerResolution = null;

	const REGEX_SLOW_GROUP = '/^\s*\*\s*@group\s+Slow\s*\n/m';
	const REGEX_SLOW_EXEC_TIME = '/^\s*\*\s*@slowExecutionTime\s+([0-9\.]+\s*(ms?)\s*\n)/m';
	const REGEX_EMPTY_DOCCOMMENT = '/^\s*\/\*\*[\s|\*]*\//';
	const REGEX_DOCCOMMENT_WITH_METHOD = '/(\s*/\*(.*?)\*/)?(%s\s*.*function\s+%s\s*\()/sm';
	const REGEX_INDENTATION_FOR_METHOD = '/^(\s*).*function\s+%s\s*\(/m';

	public static function initialize() {
		self::$methods = [ ];
	}

	public static function add( $className, $methodName, $executionTime, $annotations ) {
		if ( empty( self::$methods[$methodName] ) ) {
			// normalize time
			$executionTime = round( $executionTime, self::TEST_EXECUTION_TIME_RESOLUTION );

			$classReflector = new ReflectionClass( $className );
			$methodReflector = new ReflectionMethod( $className, $methodName );

			$lineNumber = $methodReflector->getStartLine();
			$docComment = $methodReflector->getDocComment();
			$filePath = $classReflector->getFileName();
			$alreadyMarkedAsSlow = self::isMarkedAsSlow( $annotations );

			self::$methods[$methodName] = [ 'filePath' => $filePath, 'lineNumber' => $lineNumber, 'docComment' => $docComment,
				'alreadyMarkedAsSlow' => $alreadyMarkedAsSlow, 'executionTime' => $executionTime ];
		}
	}

	public static function execute() {
		if ( empty( self::$methods ) ) {
			return;
		}

		$affectedFiles = [ ];

		foreach ( self::$methods as $methodName => $array ) {
			if ( ( $isSlow = ( $array['executionTime'] > self::SLOW_TEST_THRESHOLD ) ) xor $array['alreadyMarkedAsSlow'] ) {
				$affectedFiles[] = $array['filePath'];

				if ( $isSlow ) {
					self::addSlowAnnotation( $array['filePath'], $array['methodName'], $array['docComment'],
						$array['executionTime'] );
				} else {
					self::removeSlowAnnotation( $array['filePath'], $array['methodName'], $array['docComment'] );
				}
			}
		}

		// cleanup DocComments from all affected files
		foreach ( $affectedFiles as $filePath ) {
			self::cleanupEmptyDocComments( $filePath );
		}

		self::initialize();
	}

	private static function cleanupEmptyDocComments( $filePath ) {
		$fileContents = file_get_contents( $filePath );
		$fileContents = preg_replace( self::REGEX_EMPTY_DOCCOMMENT, '', $fileContents );
		file_put_contents( $filePath, $fileContents );
	}

	private static function setTimerResolution() {

		self::$timerResolution = strlen( substr( strrchr( self::SLOW_TEST_THRESHOLD, "." ), 1 ) ) + 2;
	}

	private static function isMarkedAsSlow( $annotations ) {
		return !empty( $annotations['method'] ) && !empty( $annotations['method']['group'] )
		&& in_array( 'Slow', $annotations['method']['group'] );
	}

	private static function removeSlowAnnotationFromDocComment( $docComment ) {
		$docComment = preg_replace( self::REGEX_SLOW_EXEC_TIME, '', $docComment );
		$docComment = preg_replace( self::REGEX_SLOW_GROUP, '', $docComment );

		return $docComment;
	}

	private static function removeSlowAnnotation( $filePath, $methodName, $docComment ) {
		$newDocComment = self::removeSlowAnnotationFromDocComment( $docComment );

		$fileContents = file_get_contents( $filePath );

		$fileContents = self::replaceDocCommentForMethod($fileContents, $methodName, $newDocComment);

		file_put_contents( $filePath, $fileContents );
	}

	private static function addSlowAnnotation( $filePath, $methodName, $docComment, $executionTime ) {
		$fileContents = file_get_contents( $filePath );

		$indentation = self::getIndentation($fileContents, $methodName);

		$newDocComment = empty( $docComment ) ? self::createDocComment( $indentation, $executionTime )
			: self::updateDocComment( $indentation, $docComment, $executionTime );

		$fileContents = self::replaceDocCommentForMethod($fileContents, $methodName, $newDocComment);

		file_put_contents( $filePath, $fileContents );
	}

	private static function replaceDocCommentForMethod($sourceCode, $methodName, $newDocComment) {

		$docCommentWithMethodRegex = sprintf(self::REGEX_DOCCOMMENT_WITH_METHOD, PHP_EOL, $methodName);

		// replace old DocComment and method declaration with new DocComment and method declaration
		return preg_replace( $docCommentWithMethodRegex, PHP_EOL . PHP_EOL . $newDocComment . '\2', $sourceCode );
	}

	private static function createDocComment( $indentation, $executionTime ) {
		return self::createDocCommentAnnotation( $indentation, $executionTime ) . $indentation . ' */'.PHP_EOL;
	}

	private static function createDocCommentAnnotation( $indentation, $executionTime ) {
		return $indentation . '/**'.PHP_EOL
				 . $indentation . ' * @group Slow'.PHP_EOL
				 . $indentation . ' * @slowExecutionTime ' . $executionTime . ' ms'.PHP_EOL;
	}

	private static function updateDocComment( $indentation, $docComment, $executionTime ) {
		$docComment = self::removeSlowAnnotationFromDocComment( $docComment );
		$docCommentAnnotation = self::createDocCommentAnnotation( $indentation, $executionTime );

		return str_replace( '/**', $docCommentAnnotation, $docComment );
	}

	private static function getIndentation($sourceCode, $methodName) {
		$matches = null;

		$methodWithDocCommentRegex = sprintf( self::REGEX_INDENTATION_FOR_METHOD, $methodName );

		if (preg_match_all($methodWithDocCommentRegex, $sourceCode, $matches) > 0) {
			return $matches[0][1];
		} else {
			return '';
		}
	}
}
