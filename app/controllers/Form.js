(function () {
    'use strict';

    angular.module('app')
    .controller('FormStandardController' , function ($scope, $rootScope, $controller, $mdToast, $location) {
        
        $controller('NotificationsController', {$scope: $scope});

        $scope.canSubmit = function() {
            return $scope.formStandard.$valid;
        };
        
        $scope.getDateString = function(date){
            return date.getFullYear() + '-' + (date.getMonth() + 1) + '-' + date.getDate();
        }
    
        $scope.change_origin = (coords,ident)=> {
     
            $scope.object.origin_coords = coords
            $scope.object.origin_id = ident
        }
        $scope.change_destination = (coords,ident) => {
      
            $scope.object.destination_coords = coords
            $scope.object.destination_id = ident
        }
        $scope.getTimeString = function(time){
            var hours = time.getHours() < 10 ? '0' + time.getHours() : time.getHours();
            var minutes = time.getMinutes() < 10 ? '0' + time.getMinutes() : time.getMinutes();
            
            return hours + ':' + minutes;
        }
        
        $scope.stringToDate = function(string){
            var data = string.split("-");
            return new Date(data[0], (data[1] - 1), data[2]);
        }
        
        $scope.stringToDateTime = function(str_date, str_time){
            var data_date = str_date.split("-");
            var data_time = str_time.split(":");
            return new Date(data_date[0], (data_date[1] - 1), data_date[2], data_time[0], data_time[1], 0, 0);
        }
        
        $scope.doHttp = function(httpFunction, successMessage){
            $rootScope.$broadcast('preloader:active');
            
            return httpFunction($scope.object, $scope.objectId).success(function(data, status){
                
                if(status !== 200){
                    $scope.showError(status);
                    return;
                }
                
                $scope.showSuccess(successMessage);
                
                $scope.object = null;
                
                if(typeof $scope.objectId != 'undefined'){
                   window.history.back();
                }
                
            }).error(function(data, status){
                $scope.showError(status);
            });
        }
    });
})(); 