;(function(){
	'use strict';

	/** Aplication: %appname%
	  * Directive: %type%
	  * Description: This directive make ...
	  */

	angular.module('%appname%.%module%')

	.directive('%type%', function() {
		return {
			restrict: 'E',
			scope: true,
			replace: true,
			template: '<div></div>'
		};
	});

}());