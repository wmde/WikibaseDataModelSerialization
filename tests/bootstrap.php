<?php

if ( PHP_SAPI !== 'cli' ) {
	die( 'Not an entry point' );
}

error_reporting( E_ALL );
ini_set( 'display_errors', 1 );

if ( !is_readable( __DIR__ . '/../vendor/autoload.php' ) ) {
	die( 'You need to install this package with Composer before you can run the tests' );
}

$classLoader = require __DIR__ . '/../vendor/autoload.php';

$classLoader->addPsr4( 'Tests\\Wikibase\\DataModel\\', __DIR__ . '/unit' );

unset( $classLoader );
