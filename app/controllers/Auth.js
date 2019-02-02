(function () {
    'use strict';

    angular.module('app')
    .controller('AuthController', function ($scope, $window, $location) {
        
        if(typeof $window.sessionStorage.token == 'undefined'){
            $location.path('/login');
        }
    });
})(); 