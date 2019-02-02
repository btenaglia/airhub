(function () {
    'use strict';

    angular.module('app')
    .controller('HeaderController', function ($scope, $http, $window, $location) {
        
        $scope.logout = function(){
            delete $window.sessionStorage.token;
            $location.path('/login');
        }
    });
})(); 