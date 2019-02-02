(function () {
    'use strict';

    angular.module('app')
    .controller('NotificationsController', function ($scope, $rootScope, $mdToast) {
        
        
        $scope.showError = function(errorType){
            
            $rootScope.$broadcast('preloader:hide');

            var message;
            
            switch (errorType) {
                
                case 409:
                    message = 'Invalid data! Please try again';
                    break;
                    
                case 500:
                    message = 'An error has ocurred! Please try again';
                    break;
            }
            
            $mdToast.show(
                $mdToast.simple()
                .content(message)
                .hideDelay(5000)
            );
        };
        
        $scope.showUserExistsError = function(errorType){
            
            
            var message = 'The email address is already in use';
            
            $mdToast.show(
                $mdToast.simple()
                .content(message)
                .hideDelay(5000)
            );
        };
        
        $scope.showMessage = function(message){

            $rootScope.$broadcast('preloader:hide');

            $mdToast.show(
                $mdToast.simple()
                .content(message)
                .hideDelay(3000)
            );
        };
        
        $scope.showSuccess = function(message){

            $rootScope.$broadcast('preloader:hide');

            $mdToast.show(
                $mdToast.simple()
                .content(message)
                .hideDelay(3000)
            );
        };
    });
})(); 