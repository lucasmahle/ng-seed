;(function(){
	'use strict';

	/** Aplication: %appname%
	  * Description: File with configurations and routes to module %module%.
	  */

	angular.module('%appname%.%module%')

	.config(["$stateProvider", "$urlRouterProvider", function($stateProvider, $urlRouterProvider) {
	
		$stateProvider
			.state('%module%', {
				url: '/%module%',
				templateUrl: "app/%module%/views/"
			})
			//.state('state-name', {
			//	url: "/statte-url",
			//	templateUrl: "url",
			//	template: "<html>"
			//})
		;
	}]);

}());