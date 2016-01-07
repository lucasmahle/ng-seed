var path = require('path'),
	fs = require('fs'),

	log = console.log;

var haveAppFoler = function(){
	var path = __dirname + '/app/';

	try {
		fs.accessSync(path, fs.F_OK);
		return true;
	} catch (e) {
		return false;
	}
}

module.exports = {
	newModule: function($name, $options){
		// Cmd: "seed new $name $options"

		if( !haveAppFoler() ){
			log([
				'The \'app\' folder does not exist.',
				'Make sure you have started a ng-seed project.',
				'If not initiated a project, run the command \'seed init\'.',
			].join('\n'));

			return;
		}
	}
}