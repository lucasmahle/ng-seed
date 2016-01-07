'use strict';

var util = require('util'),
	argv = require('optimist').argv,
	seed = require('./ng-seed.js');

var actions = ['new', 'delete', 'init', 'inject', 'build'],
	types	= ['controller', 'config', 'directive', 'factory', 'service', 'test']
	action  = argv._[0],
	exit	= function(){
		//
	};if(action == 'init'){
	seed.init();
	exit();
}

if(action == 'new'){
	seed.newModule('modulo', {});
	exit();
}
if(action == 'delete'){
	seed.deleteModule('modulo', {});
	exit();
}

