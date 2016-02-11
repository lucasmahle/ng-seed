<?php
require_once("class.ngseed.php");

/**
 * Get args. If by terminal, args get from $_SERVER['argv'] else, is by post type
 */
$argv = isset($_POST['cmd']) ? explode(' ', $_POST['cmd']) : $_SERVER['argv'];

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
$ngseed->setTemplate_dir( __DIR__ . '/tempaltes' );
$ngseed->setApp_dir( __DIR__ );

// Execute with arguments
$ngseed->exec( $argv );