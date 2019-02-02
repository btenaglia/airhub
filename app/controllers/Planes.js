(function () {
    'use strict';

    angular.module('app')
    .factory('planesFactory', ['$http', function($http){
        
        var baseURL = 'api/v1/private';
        var response = {
            data: []
        };
    
        response.getAll = function() {
            return $http.get(baseURL + '/planes').success(function(data){
                angular.copy(data, response.data);
            });
        };

        response.get = function(id) {
            return $http.get(baseURL + '/planes/' + id).then(function(res){
                return res.data;
            });
        };

        response.create = function(object) {
            return $http.post(baseURL + '/planes/create', object);
        };
        
        response.edit = function(object, id) {
            return $http.put(baseURL + '/planes/' + id + '/edit', object);
        };
        
        response.delete = function(id) {
            return $http.delete(baseURL + '/planes/' + id + '/destroy');
        };

        return response;
    }])
    .controller('AddPlaneController' , function ($scope, $rootScope, $controller, planesFactory) {
        
        $controller('AuthController', {$scope: $scope});
        $controller('FormStandardController', {$scope: $scope});
        
        $scope.titleName = "Add new plane";

        $scope.object = {
            name: '',
        };
        
        $scope.submitForm = function(){
            $scope.doHttp(planesFactory.create, 'New plane added successfully');
        }
    })
    .controller('EditPlaneController' , function ($scope, $rootScope, $controller, planesFactory, plane) {
        
        $controller('AuthController', {$scope: $scope});
        $controller('FormStandardController', {$scope: $scope});
        
        $scope.titleName = "Edit plane";
        
        $scope.object = {
            identifier: plane.data.identifier,
            name: plane.data.name,
            type: plane.data.type,
            seats_limit: plane.data.seats_limit,
            weight_limit: plane.data.weight_limit
        };
        
        $scope.objectId = plane.data.id;
        
        $scope.submitForm = function(){
            $scope.doHttp(planesFactory.edit, 'Plane edited successfully');
        }
        
    })
    .controller('ViewAllPlanesController' ,function ($scope, $controller, planesFactory, planes) {
        
        $controller('AuthController', {$scope: $scope});
        $controller('DynamicTableController', {$scope: $scope});
        
        $scope.editPath = '/planes/edit/';
        $scope.storedData = planes.data.data;
        $scope.initDynamicTable();
        
        $scope.preShowConfirmDelete = function(ev, name, id) {
            $scope.showConfirmDelete(ev, 'Would you like to delete this plane?', 'You will delete: ' + name, id, planesFactory, 'Plane deleted successfully');
        };
    });
})(); 