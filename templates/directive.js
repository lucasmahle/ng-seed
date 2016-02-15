;(function(){
	'use strict';

	/** Aplication: %appname%
	  * Directive: %type%
	  * Description: This directive make ...
	  */

	angular.module('%appname%')

	.directive('%type%', function() {
		return {
			restrict: 'E',
			scope: true,
			replace: true,
			template: '<div></div>'
		};
	});

}());