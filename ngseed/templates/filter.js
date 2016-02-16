;(function(){
	'use strict';

	/** Aplication: %appname%
	  * Filter: %type%
	  * Description: This filter do ...
	  */

	angular.module('%appname%')

	.filter('%type%', function() {
		return function (input) {
			return input;
		};
	});

}());