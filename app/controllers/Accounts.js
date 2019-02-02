(function () {
    'use strict';

    angular.module('app')
    .factory('loginFactory', ['$http', function($http){
        
        var baseURL = 'api/v1/public';
        var response = {
            data: []
        };
        
        response.login = function(object) {
            return $http.post(baseURL + '/accounts/login', object);
        };

        return response;
    }])
    .controller('AccountsController', function ($scope, $controller, $http, $window, $location, loginFactory) {
      
        $controller('NotificationsController', {$scope: $scope});

        $scope.user = {
            email: '', 
            password: ''
        };
        
        $scope.message = '';
        
        $scope.canSubmit = function() {
            return $scope.formStandard.$valid;
        };   
        
        $scope.submit = function () {
        
            loginFactory.login($scope.user)
                .success(function (data, status, headers, config) {
            
                if(data.data.token){
                    $window.sessionStorage.token = data.data.token;
                }
                
                $location.path('/');
            })
          
                .error(function (data, status, headers, config) {

                // Erase the token if the user fails to log in            
                delete $window.sessionStorage.token;

                if(status === 409){
                    $scope.user = {
                        email: '', 
                        password: ''
                    }; 
                    
                    $scope.showLoginError = true;
                    $scope.showError(status);
                }
            });
        };
    });
})(); 