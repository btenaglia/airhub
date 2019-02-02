(function () {
    'use strict';

    angular.module('app')
    .factory('paymentsFactory', ['$http', function($http){
        
        var baseURL = 'api/v1/private';
        var response = {
            data: []
        };
    
        response.getAll = function() {
            return $http.get(baseURL + '/payments').success(function(data){
                angular.copy(data, response.data);
            });
        };

        response.get = function(id) {
            return $http.get(baseURL + '/payments/' + id).then(function(res){
                return res.data;
            });
        };
        return response;
    }])
    .controller('ViewAllPaymentsController' ,function ($scope, $controller, paymentsFactory, payments) {

        $controller('AuthController', {$scope: $scope});
        $controller('DynamicTableController', {$scope: $scope});
        
        $scope.storedData = payments.data.data;
        $scope.initDynamicTable();
        
        $scope.reloadData = function(){
            paymentsFactory.getAll().success(function(data){
                $scope.storedData = data.data;
                $scope.initDynamicTable();
            });
        }

    });
    //}]);
})(); 