;(function(){
	'use strict';

	/** Aplication: %appname%
	  * Filter: %type%
	  * Description: This filter make ...
	  */

	angular.module('%appname%')

	.filter('%type%', function() {
		return function (input) {
			return input;
		};
	});

}());