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
			//.state('state-name', {
			//	url: "/statte-url",
			//	templateUrl: "url",
			//	template: "<html>"
			//})
		;
	}]);

}());