;(function(){
	'use strict';

	/** Aplication: %appname%
	  * Description: This file set some routers
	  */

	angular.module('%appname%.routes', ['ui.router'])

	.config(['APP', "$stateProvider", "$urlRouterProvider", function(APP, $stateProvider, $urlRouterProvider) {
		$urlRouterProvider.otherwise(APP.init_url);

		$stateProvider
			.state('home', {
				url: APP.init_url,
				template: ""
			})
			.state('state1', {
				url: "/state1",
				template: "<h2>Stateeeeeeee1</h2>"
			})
			.state('state2', {
				url: "/state2",
				template: "<h2>Stateeeeeeee22222222</h2>"
			})
		;
	}]);

}());