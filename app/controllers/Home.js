(function () {
    'use strict';

    angular.module('app')
    .controller('HomeController', function ($scope, $controller, $http) {
  
        $controller('AuthController', {$scope: $scope});
        
        $scope.titleName = "Home";
    });
})(); 