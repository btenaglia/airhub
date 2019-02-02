(function () {
    'use strict';

    angular.module('app')
    // register the interceptor as a service

        .factory('HttpConfig', ['$q', '$location', '$window', function($q, $location, $window) {
            return {
    
                'request': function(config) {
                    config.headers = config.headers || {};
                    if ( typeof $window.sessionStorage.token != 'undefined') {
                        config.headers.Authorization = 'Bearer ' + $window.sessionStorage.token;
                    }
                    return config;
                },
   
                'responseError': function(response) {
                    if (response.status === 401 || response.status === 403) {
                        //$location.path('/signin');
                        console.log(response)
                    }
                    return $q.reject(response);
                }
            };

        }])
    
        .config(['$httpProvider', function($httpProvider) {
            $httpProvider.interceptors.push('HttpConfig');
        }]);
})(); 