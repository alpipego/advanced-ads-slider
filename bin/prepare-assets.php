#!/usr/bin/env php
<?php
/*
 * Prepares final assets and sends them to the `public` folder.
 */
if ( 'cli' !== php_sapi_name() ) {
	fwrite( STDERR, "Please use CLI.\n" );
	exit( 1 );
}

$filenames = array( 'jquery.event.move.js', 'jquery.event.swipe.js' );

foreach ( $filenames as $filename ) {
	$src_path = dirname( dirname( __FILE__ ) ) . '/src/assets/js/' . $filename;
	$pub_path = dirname( dirname( __FILE__ ) ) . '/public/assets/js/' . $filename;

	$src_data = file_get_contents( $src_path );
	if ( false === $src_data ) {
		fwrite( STDERR, "Could not read: $src_path\n" );
		exit( 1 );
	}

	$wrap = '
(function() {
	// prevent execution if the Symbol data type is not available.
	if ( ! window.Symbol ) {
		return;
	}

	%s

})()';

	$pub_data = sprintf( $wrap, $src_data );

	if ( false === file_put_contents( $pub_path, $pub_data ) ) {
		fwrite( STDERR, "Could not write: $src_path\n" );
		exit( 1 );
	}

	fwrite( STDOUT, "Created: $pub_path\n" );
}



