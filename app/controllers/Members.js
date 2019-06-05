(function () {
    'use strict';

    angular.module('app')
    .factory('membersFactory', ['$http', function($http){
        
        var baseURL = 'api/v1/private';
        var response = {
            data: []
        };
    
        response.getAll = function() {
            return $http.get(baseURL + '/members/').success(function(data){
                
                // angular.copy(data, data);
                return data
            });
        };

        response.get = function(id) {
            return $http.get(baseURL + '/members/' + id).then(function(res){
                return res.data;
            });
        };

        response.create = function(object) {
          
            return $http.post(baseURL + '/members/create', object);
        };
        
        response.edit = function(object, id) {
            return $http.put(baseURL + '/members/' + id + '/edit', object);
        };
        
        response.delete = function(id) {
            return $http.delete(baseURL + '/members/' + id + '/destroy');
        };
        response.notification = function(object) {
            return  $http.post(baseURL + '/members/notification', object);
        };
        return response;
    }])
    .controller('AddMemberController', function ($scope, $rootScope, $controller, membersFactory) {
        
        $controller('AuthController', {$scope: $scope});
        $controller('FormStandardController', {$scope: $scope});
        
        $scope.titleName = "Add new member";
        console.log($scope.titleName)
        $scope.object = {
            description: '',
            discount: '0.00'
        };
        
        $scope.submitForm = function(){
            var patt = /^[0-9]{1,4}(\.[0-9][0-9])?$/i
            if($scope.object.discount.match(patt) === null){
            
                return false
            }

            $scope.doHttp(membersFactory.create, 'New Member profile added successfully');
        }
    })
    .controller('EditMemberController' , function ($scope, $rootScope, $controller, membersFactory, member) {
        
        $controller('AuthController', {$scope: $scope});
        $controller('FormStandardController', {$scope: $scope});
        
        $scope.titleName = "Edit Member";
        
        $scope.object = {
            description: member.data.description,
            discount: member.data.discount
        };
        
        $scope.objectId = member.data.id;
        
        $scope.submitForm = function(){
           
            $scope.doHttp(membersFactory.edit, 'Member edited successfully');
        }
        
    })
    .controller('ViewAllmembersController' ,function ($scope, $controller, membersFactory, members) {
        
        $controller('AuthController', {$scope: $scope});
        $controller('DynamicTableController', {$scope: $scope});
        
        $scope.editPath = '/members/edit/';
        $scope.storedData = members.data.data;
        $scope.initDynamicTable();
        
        $scope.preShowConfirmDelete = function(ev, name, id) {
            $scope.showConfirmDelete(ev, 'Would you like to delete this member?', 'You will delete: ' + name, id, membersFactory, 'Member deleted successfully');
        };
    })
      .controller('NotificationMembersController' ,function ($scope, $controller, membersFactory, members) {
        
        $controller('AuthController', {$scope: $scope});
        $controller('FormStandardController', {$scope: $scope});
       
        $scope.members = members.data.data;
        $scope.object = {
            member_id: '',
            message: ''
        };
        $scope.submitForm = function(){
          

            $scope.doHttp(membersFactory.notification, 'New Message has been sent to users');
        }
    });
})();  