(function () {
    'use strict';

    angular.module('app')
    .factory('musersFactory', ['$http', function($http){
        
        var baseURL = 'api/v1/private';
        var response = {
            data: []
        };
    
        response.getAll = function() {
            return $http.get(baseURL + '/users').success(function(data){
                angular.copy(data, response.data);
            });
        };

        response.get = function(id) {
            return $http.get(baseURL + '/users/' + id).then(function(res){
                return res.data;
            });
        };

        response.create = function(object) {
            return $http.post(baseURL + '/musers/create', object);
        };
        
        response.edit = function(object, id) {
            return $http.put(baseURL + '/musers/' + id + '/edit', object);
        };
        
        response.delete = function(id) {
            return $http.delete(baseURL + '/users/' + id + '/destroy');
        };
        
        response.sendpush = function(id) {
            //return $http.post(baseURL + '/users/' + id + '/sendpush');
            return $http.get(baseURL + '/users/sendpush/' + id);
        };

        return response;
    }])
    .controller('AddMuserController' , function ($scope, $rootScope, $controller, musersFactory,members) {
        
        $controller('AuthController', {$scope: $scope});
        $controller('FormStandardController', {$scope: $scope});
        
        $scope.titleName = "Add new mobile user";
        console.log(members.data.data)
        $scope.members  = members.data.data
        $scope.object = {
            user_type: 'app_user',
            member_id:''
        };
        
        $scope.showPassword = true; //TODO FIXME
        
        $scope.submitForm = function(){
            console.log("aver",$scope.object.member_id)
            $scope.doHttp(musersFactory.create, 'New user added successfully').error(function(data,status){
                if (status === 409){
                    $scope.showUserExistsError();
                }
            });
        }
    })
    .controller('EditMuserController' , function ($scope, $rootScope, $controller, musersFactory, user) {
        
        $controller('AuthController', {$scope: $scope});
        $controller('FormStandardController', {$scope: $scope});
        
        $scope.titleName = "Edit mobile user";
        
        $scope.object = {
            name: user.data.name,
            last_name: user.data.last_name,
            address: user.data.address,
            city: user.data.city,
            state: user.data.state,
            country: user.data.country,
            zipcode: user.data.zipcode,
            cell_phone: user.data.cell_phone,
            body_weight: user.data.body_weight,
            //complete_name: user.data.complete_name,
            email: user.data.email,
            password: '----'
        };
        
        $scope.objectId = user.data.id;
        
        $scope.showPassword = false; //TODO FIXME
        
        $scope.submitForm = function(){
            $scope.doHttp(musersFactory.edit, 'User edited successfully');
        }
        
    })
    .controller('ViewAllMusersController' ,function ($scope, $controller, $mdDialog, usersFactory, users) {
        
        $controller('AuthController', {$scope: $scope});
        $controller('DynamicTableController', {$scope: $scope});
        
        $scope.editPath = '/users/edit-admin/';
        $scope.storedData = users.data.data;
        $scope.initDynamicTable();
        
        $scope.preShowConfirmDelete = function(ev, name, id) {
            $scope.showConfirmDelete(ev, 'Would you like to delete this user?', 'You will delete: ' + name, id, usersFactory, 'User deleted successfully');
        };
        
        $scope.sendPush = function(id, name) {
            var confirm = $mdDialog
            .confirm()
            .title('Send Push Message')
            .content('A push message will be sent to the selected user: '+name)
            .ok('Send')
            .cancel('Cancel');
        
        $mdDialog.show(confirm).then(function(){
        	
        	usersFactory.sendpush(id).success(function(data, status){

                    if(status !== 200){
                        $scope.showError(status);
                        return;
                    }

                    $scope.showSuccess('Message sent');

          }).error(function(data, status){
                    $scope.showError(status);
            });
        	
        });
        	    
        };
    });
})(); 