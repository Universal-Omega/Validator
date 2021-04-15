<?php

/**
 * Initialization file for the Validator MediaWiki extension.
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */

// remove?
if ( defined( 'ParamProcessor_VERSION' ) ) {
	// Do not initialize more than once.
	return 1;
}

// extension.json callback key and callback function elsewhere
define( 'Validator_VERSION', '2.2.4' );
define( 'ParamProcessor_VERSION', Validator_VERSION ); // @deprecated since 1.0

// Internationalization
// extension.json MessagesDirs key
$GLOBALS['wgMessagesDirs']['Validator'] = __DIR__ . '/i18n';


$GLOBALS['wgExtensionFunctions'][] = function () {
	// extension.json requires, mediawiki key:
	if ( version_compare( $GLOBALS['wgVersion'], '1.23c', '<' ) ) {
		die( '<b>Error:</b> This version of Validator requires MediaWiki 1.23 or above.' );
	}

	// extension.json load_composer_autoloader key:
	if ( is_readable( __DIR__ . '/vendor/autoload.php' ) ) {
		include_once( __DIR__ . '/vendor/autoload.php' );
	}

	// extension.json callback key and callback function elsewhere? Or ExtensionFunctions key?
	if ( !class_exists( ParamProcessor\Processor::class ) ) {
		throw new Exception( 'Validator depends on the ParamProcessor library.' );
	}

	// extension.json relevant keys:
	$GLOBALS['wgExtensionCredits']['other'][] = [
		'path' => __FILE__,
		'name' => 'Validator',
		'version' => Validator_VERSION,
		'author' => [
			'[https://www.entropywins.wtf/mediawiki Jeroen De Dauw]',
			'[https://professional.wiki/ Professional.Wiki]',
		],
		'url' => 'https://github.com/JeroenDeDauw/Validator',
		'descriptionmsg' => 'validator-desc',
		'license-name' => 'GPL-2.0-or-later'
	];

	/**
	 * Hook to add PHPUnit test cases.
	 * @see https://www.mediawiki.org/wiki/Manual:Hooks/UnitTestsList
	 *
	 * @since 1.0
	 *
	 * @param array $files
	 *
	 * @return boolean
	 */
	// extension.json hooks key and move to function elsewhere:
	$GLOBALS['wgHooks']['UnitTestsList'][]	= function( array &$files ) {
		// @codeCoverageIgnoreStart
		$directoryIterator = new RecursiveDirectoryIterator( __DIR__ . '/tests/phpunit/' );

		/**
		 * @var SplFileInfo $fileInfo
		 */
		foreach ( new RecursiveIteratorIterator( $directoryIterator ) as $fileInfo ) {
			if ( substr( $fileInfo->getFilename(), -8 ) === 'Test.php' ) {
				$files[] = $fileInfo->getPathname();
			}
		}

		return true;
		// @codeCoverageIgnoreEnd
	};

	// extension.json callback key and callback function elsewhere
	$GLOBALS['wgDataValues']['mediawikititle'] = ParamProcessor\MediaWikiTitleValue::class;

	$GLOBALS['wgParamDefinitions']['title'] = [
		'string-parser' => ParamProcessor\TitleParser::class,
		'validator' => ValueValidators\TitleValidator::class,
	];
};

