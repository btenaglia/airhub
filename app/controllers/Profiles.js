(function () {
    'use strict';

    angular.module('app')
    .factory('profilesFactory', ['$http', function($http){
        
        var baseURL = 'api/v1/private';
        var response = {
            data: []
        };
    
        response.getAll = function() {
            return $http.get(baseURL + '/profiles').success(function(data){
                angular.copy(data, response.data);
            });
        };

        response.get = function(id) {
            return $http.get(baseURL + '/profiles/' + id).then(function(res){
                return res.data;
            });
        };

        response.create = function(object) {
            return $http.post(baseURL + '/profiles/create', object);
        };
        
        response.edit = function(object, id) {
            return $http.put(baseURL + '/profiles/' + id + '/edit', object);
        };
        
        response.delete = function(id) {
            return $http.delete(baseURL + '/profiles/' + id + '/destroy');
        };

        return response;
    }])
    .controller('AddProfileController' , function ($scope, $rootScope, $controller, profilesFactory, profile) {
        
        $controller('AuthController', {$scope: $scope});
        $controller('FormStandardController', {$scope: $scope});
        
        $scope.titleName = "Add new profile";

        $scope.object = {
            name: profile.data.name,
            price: profile.data.price,
            type: profile.data.type,
            hours:  profile.data.hours,
            seats:  profile.data.seats
        };
        
        $scope.submitForm = function(){
            $scope.doHttp(profilesFactory.create, 'New profile added successfully');
        }
    })
    .controller('EditProfileController' , function ($scope, $rootScope, $controller, profilesFactory, profile) {
        
        $controller('AuthController', {$scope: $scope});
        $controller('FormStandardController', {$scope: $scope});
        
        $scope.titleName = "Edit profile";
        
        $scope.object = {
            name: profile.data.name,
            price: profile.data.price,
            type: profile.data.type,
            hours:  profile.data.hours,
            seats:  profile.data.seats
        };
        
        $scope.objectId = profile.data.id;
        
        $scope.submitForm = function(){
            $scope.doHttp(profilesFactory.edit, 'Profile edited successfully');
        }
        
    })
    .controller('ViewAllProfilesController' ,function ($scope, $controller, profilesFactory, profiles) {
        
        $controller('AuthController', {$scope: $scope});
        $controller('DynamicTableController', {$scope: $scope});
        
        $scope.editPath = '/profiles/edit/';
        $scope.storedData = profiles.data.data;
        $scope.initDynamicTable();
        
        $scope.preShowConfirmDelete = function(ev, name, id) {
            $scope.showConfirmDelete(ev, 'Would you like to delete this profile?', 'You will delete: ' + name, id, profilesFactory, 'Profile deleted successfully');
        };
    });
})(); 