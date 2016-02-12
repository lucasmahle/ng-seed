<?php
require_once("class.ngseed.php");
function splitArguments($args) {
	preg_match_all('/[\'"].*?[\'"]|[^ ]+/m', $args, $matches);
	
	return array_map(function( $arg ){
		if( substr($arg, 0, 1) == '"' ) $arg = substr($arg, 1);
		if( substr($arg, -1, 1) == '"' ) $arg = substr($arg, 0, -1);
		return $arg;
	}, $matches[0]);
}

/**
 * Get args. If by terminal, args get from $_SERVER['argv'] else, is by post type
 */
$argv = isset($_POST['cmd']) ? splitArguments($_POST['cmd']) : $_SERVER['argv'];


/**
 * Remove first args if him is your self name
 */
if( @$argv[0] == basename(__FILE__)){
	array_shift($argv);
}

/**
 * Instace Ngseed class
 */
$ngseed = new Ngseed();

// Settings
$ngseed->setTemplate_dir( __DIR__ . '/templates' );
$ngseed->setApp_dir( __DIR__ );

// Execute with arguments
$ngseed->exec( $argv );