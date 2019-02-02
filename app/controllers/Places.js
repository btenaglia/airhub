(function () {
    'use strict';

    angular.module('app')
    .factory('placesFactory', ['$http', function($http){
        
        var baseURL = 'api/v1/private';
        var response = {
            data: []
        };
    
        response.getAll = function() {
            return $http.get(baseURL + '/places').success(function(data){
                angular.copy(data, response.data);
            });
        };

        response.get = function(id) {
            return $http.get(baseURL + '/places/' + id).then(function(res){
                return res.data;
            });
        };

        response.create = function(object) {
            return $http.post(baseURL + '/places/create', object);
        };
        
        response.edit = function(object, id) {
            return $http.put(baseURL + '/places/' + id + '/edit', object);
        };
        
        response.delete = function(id) {
            return $http.delete(baseURL + '/places/' + id + '/destroy');
        };

        return response;
    }])
    .controller('AddPlaceController' , function ($scope, $rootScope, $controller, placesFactory) {
        
        $controller('AuthController', {$scope: $scope});
        $controller('FormStandardController', {$scope: $scope});
        
        $scope.titleName = "Add new place";

        $scope.object = {
            name: '',
            short_name: ''
        };
        
        $scope.submitForm = function(){
            $scope.doHttp(placesFactory.create, 'New place added successfully');
        }
    })
    .controller('EditPlaceController' , function ($scope, $rootScope, $controller, placesFactory, place) {
        
        $controller('AuthController', {$scope: $scope});
        $controller('FormStandardController', {$scope: $scope});
        
        $scope.titleName = "Edit place";
        
        $scope.object = {
            name: place.data.name,
            short_name: place.data.short_name
        };
        
        $scope.objectId = place.data.id;
        
        $scope.submitForm = function(){
            $scope.doHttp(placesFactory.edit, 'Place edited successfully');
        }
        
    })
    .controller('ViewAllPlacesController' ,function ($scope, $controller, placesFactory, places) {
        
        $controller('AuthController', {$scope: $scope});
        $controller('DynamicTableController', {$scope: $scope});
        
        $scope.editPath = '/places/edit/';
        $scope.storedData = places.data.data;
        $scope.initDynamicTable();
        
        $scope.preShowConfirmDelete = function(ev, name, id) {
            $scope.showConfirmDelete(ev, 'Would you like to delete this place?', 'You will delete: ' + name, id, placesFactory, 'Place deleted successfully');
        };
    });
})(); 