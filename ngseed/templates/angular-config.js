;(function(){
	'use strict';

	/** Aplication: %appname%
	  * Description: This file set base configurations. Like some informations or values.
	  */

	angular.module('%appname%.config', [])

	.config( function() {
		// Put yout configurations here
	})
	.constant('APP', {
		"name": "",
		"version": 0.1,
		"init_url": '/',
		'environment': 'development',
		'debug': true
	});

}());