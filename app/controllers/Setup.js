(function () {
    'use strict';

    angular.module('app')
    .factory('setupFactory', ['$http', function($http){
        
        var baseURL = 'api/v1/private';
        var response = {
            data: []
        };
    
        response.get = function(id) {
            return $http.get(baseURL + '/setup/' + id).then(function(res){
                return res.data;
            });
        };

        response.edit = function(object, id) {
            return $http.put(baseURL + '/setup/' + id + '/edit', object);
        };
        
        /*response.dohome = function() {
            return $http.get(baseURL + '/setup');
        };*/
        
        return response;
    }])
    .controller('EditSetupController' , function ($scope, $rootScope, $controller, setupFactory, setup) {
        
        $controller('AuthController', {$scope: $scope});
        $controller('FormStandardController', {$scope: $scope});
        
        if(setup.data.id==1){
         $scope.titleName = "Ticket Cost";
         $scope.object = {
            paramname: setup.data.paramname,
            paramvalueamount: setup.data.paramvalueamount
         };
        }else if(setup.data.id==2){
         $scope.titleName = "END USER LICENSE AGREEMENT";
         $scope.object = {
            paramname: setup.data.paramname,
            paramtext1: setup.data.paramtext1
         };
        }else if(setup.data.id==3){
         $scope.titleName = "Privacy Policy";
         $scope.object = {
            paramname: setup.data.paramname,
            paramtext1: setup.data.paramtext1
         };
        }	
         
        
        
        $scope.objectId = setup.data.id;
        
        $scope.submitForm = function(){
            $scope.doHttp(setupFactory.edit, 'Setup edited successfully');
            //$scope.doHttp(setupFactory.dohome, 'Setup edited successfully');
        }
        
    });
})(); 