;(function(){
	'use strict';

	/** Aplication: %appname%
	  * Description: This file set some routers
	  */

	angular.module('%appname%.routes', ['%appname%.config', 'ngRoute'])

	.config(['util','$routeProvider', function($routeProvider) {
		$routeProvider
			.when('/', {
				templateUrl: util.makeUrl('app/core/views/core-main.html'),
				controller: 'CoreCtrl'
			})
		;
	}]);

}());