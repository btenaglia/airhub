(function () {
    'use strict';

    angular.module('app')
    .factory('statusFactory', ['$http', function($http){
        
        var baseURL = 'api/v1/private';
        var response = {
            data: []
        };
    
        response.getAll = function() {
            return $http.get(baseURL + '/allowed-flight-status').success(function(data){
                angular.copy(data, response.data);
            });
        };
        
        response.getAllcreated = function() {
            return $http.get(baseURL + '/created-flight-status').success(function(data){
                angular.copy(data, response.data);
            });
        };
        
        return response;
    }])
    .factory('flightsFactory', ['$http', function($http){
        
        var baseURL = 'api/v1/private';
        var response = {
            data: []
        };
    
        response.getAll = function() {
            return $http.get(baseURL + '/flights').success(function(data){
                angular.copy(data, response.data);
            });
        };
        
        response.getAllfuture = function() {
            return $http.get(baseURL + '/flights/future').success(function(data){
                angular.copy(data, response.data);
            });
        };
        
        response.getAllpassed = function() {
            return $http.get(baseURL + '/flights/passed').success(function(data){
                angular.copy(data, response.data);
            });
        };

        response.get = function(id) {
            return $http.get(baseURL + '/flights/' + id).then(function(res){
                return res.data;
            });
        };

        response.create = function(object) {
            return $http.post(baseURL + '/flights/create', object);
        };
        
        response.edit = function(object, id) {
            return $http.put(baseURL + '/flights/' + id + '/edit', object);
        };
        
        response.setPlane = function(object, id) {
            return $http.put(baseURL + '/flights/' + id + '/set-plane', object);
        };
        
        response.approve = function(id) {
            return $http.post(baseURL + '/flights/' + id + '/approve');
        };
        
        response.cancel = function(id) {
            return $http.post(baseURL + '/flights/' + id + '/cancel');
        };
        
        response.delete = function(id) {
            return $http.delete(baseURL + '/flights/' + id + '/destroy');
        };

        return response;
    }])
    .controller('AddFlightController' , function ($scope, $rootScope, $controller, flightsFactory, places, planes, status) {
        
        $controller('AuthController', {$scope: $scope});
        $controller('FormStandardController', {$scope: $scope});
        
        $scope.titleName = "Add new flight";
        
        $scope.places = places.data.data;
        $scope.planes = planes.data.data;
        $scope.status = status.data.data;
        /*$scope.status = [
          { id: 1, name: 'proposed'' }
        ];*/

        $scope.object = {
            origin: -1,
            destination: -1,
            status: 'proposed', //-1,
            plane_id: -1,
            dte_departure_date: new Date(),
            dte_departure_time: new Date(),
            dte_departure_min_time: new Date(),
            dte_departure_max_time: new Date()
        };
        
        $scope.minDate = new Date($scope.object.dte_departure_date.getTime() - 86400000);
        
        $scope.submitForm = function(){
            
            //check all fields
            if($scope.object.origin === -1 || $scope.object.destination === -1 || $scope.object.status === -1 || $scope.object.plane === -1){
                $scope.showMessage('Please fill all fields before submit!');
                return;
            }
            
            //check origin and destination
            if($scope.object.origin === $scope.object.destination){
                $scope.showMessage('The origin and the destination can\'t be the same place!');
                return;
            }
            
            //check which time is selected
            if ($scope.departure_fixed_time){
                $scope.object.departure_time = $scope.getTimeString($scope.object.dte_departure_time);
            
            }else{
                
                //check time slot
                if($scope.object.dte_departure_min_time.getTime() >= $scope.object.dte_departure_max_time.getTime()){
                    $scope.showMessage('Please check the time slot!');
                    return;
                }
                
                $scope.object.departure_min_time = $scope.getTimeString($scope.object.dte_departure_min_time);
                $scope.object.departure_max_time = $scope.getTimeString($scope.object.dte_departure_max_time);
            }
            
            $scope.object.departure_date = $scope.getDateString($scope.object.dte_departure_date);
            
            delete $scope.object.dte_departure_date;
            delete $scope.object.dte_departure_time;
            delete $scope.object.dte_departure_min_time;
            delete $scope.object.dte_departure_max_time;
            
            $scope.doHttp(flightsFactory.create, 'New flight added successfully');
        }
    })
    .controller('EditFlightController' , function ($scope, $rootScope, $controller, flightsFactory, flight, places, planes, status) {
        
        $controller('AuthController', {$scope: $scope});
        $controller('FormStandardController', {$scope: $scope});
        
        $scope.titleName = "Edit flight";
        
        console.log(flight.data);
        $scope.places = places.data.data;
        $scope.planes = planes.data.data;
        $scope.status = status.data.data;
        
        $scope.object = {
            origin: flight.data.origin,
            destination: flight.data.destination,
            status: flight.data.status,
            paramvalueamount: flight.data.paramvalueamount,
            plane_id: flight.data.plane_id,
            dte_departure_date: $scope.stringToDate(flight.data.departure_date),
            dte_departure_time: flight.data.departure_time ? $scope.stringToDateTime(flight.data.departure_date, flight.data.departure_time) : new Date(),
            dte_departure_min_time: flight.data.departure_min_time ? $scope.stringToDateTime(flight.data.departure_date, flight.data.departure_min_time) : new Date(),
            dte_departure_max_time: flight.data.departure_max_time ? $scope.stringToDateTime(flight.data.departure_date, flight.data.departure_max_time) : new Date()
        };
        
        $scope.minDate = new Date($scope.object.dte_departure_date.getTime() - 86400000);
        $scope.departure_fixed_time = flight.data.departure_time !== null;
        
        $scope.objectId = flight.data.id;
        
        $scope.submitForm = function(){
            
            //check which time is selected
            if ($scope.departure_fixed_time){
                $scope.object.departure_time = $scope.getTimeString($scope.object.dte_departure_time);
            
            }else{
                
                //check time slot
                if($scope.object.dte_departure_min_time.getTime() >= $scope.object.dte_departure_max_time.getTime()){
                    $scope.showMessage('Please check the time slot!');
                    return;
                }
                
                $scope.object.departure_min_time = $scope.getTimeString($scope.object.dte_departure_min_time);
                $scope.object.departure_max_time = $scope.getTimeString($scope.object.dte_departure_max_time);
            }
            
            $scope.object.departure_date = $scope.getDateString($scope.object.dte_departure_date);
            
            delete $scope.object.dte_departure_date;
            delete $scope.object.dte_departure_time;
            delete $scope.object.dte_departure_min_time;
            delete $scope.object.dte_departure_max_time;
            
            $scope.doHttp(flightsFactory.edit, 'Flight edited successfully');
        }
        
    })
    .controller('ViewAllFlightsController' ,function ($scope, $rootScope, $controller, $mdDialog, $location, flightsFactory, flights, planes) {
        
        $controller('AuthController', {$scope: $scope});
        $controller('DynamicTableController', {$scope: $scope});
        
        $scope.editPath = '/flights/edit/';
        $scope.storedData = flights.data.data;
        $scope.initDynamicTable();
        
        $scope.planes = planes.data.data;
        $scope.planeIdSelected = -1;
        
        $scope.timeToShow = function(flight){
        
            if (flight.departure_time !== null){
                return flight.departure_time;
            } else {
                return flight.departure_min_time + " - " + flight.departure_max_time;
            }
        };
        
        $scope.checkEdit = function(flight){
            if (flight.booked_seats > 0){
                $scope.showMessage('Can not edit because the flight has been booked');
            } else {
                $scope.showEdit(flight.id);
            }
        };
        
        $scope.checkDelete = function(ev, flight){
          if (flight.booked_seats > 0){
                $scope.showMessage('Can not delete because the flight has been booked');
            } else {
                $scope.preShowConfirmDelete(ev,  flight.origin_name + ' - ' + flight.destination_name, flight.id);
            }
        };
        
        $scope.showBookings = function(flight){
            $location.path('/flights/' + flight.id + '/bookings');
        };
        
        $scope.showSetPlane = function(ev, flight){
            $mdDialog.show({
                clickOutsideToClose: false,
                scope: $scope,
                preserveScope: true,
                templateUrl: 'views/flights/setPlane.html',
                
                controller: function DialogController($scope, $mdDialog) {
                    
                    $scope.hideSetPlane = function(){
                        $scope.planeIdSelected = -1;
                        $mdDialog.hide();
                    }
                    $scope.cancel = function() {
                        $scope.hideSetPlane();
                    };
                    
                    $scope.planeSelected = function(plane){
                        for (var i = 0; i < $scope.planes.length; i++){
                            if ($scope.planes[i].id !== plane.id){
                                $scope.planes[i].checked = false;
                            }
                        }
                        
                        $scope.planeIdSelected = plane.checked === true ? plane.id : -1;
                    };
                    
                    $scope.setPlane = function(){
                        
                        $rootScope.$broadcast('preloader:active');
                        
                        var object = {
                            plane_id: $scope.planeIdSelected
                        };
                        
                        flightsFactory.setPlane(object, flight.id).success(function(data, status){
                        
                            $scope.hideSetPlane();
                                
                            if(status !== 200){
                                $scope.showError(status);
                                return;
                            }

                            $scope.showSuccess('Plane added successfully');
                            $scope.reloadData();
                            
                        }).error(function(data, status){
                            $scope.showError(status); 
                        });
                    }
                }
            });
        };
        
        $scope.approve = function(ev, flight){
            if (flight.status != "proposed"){
                $scope.showMessage('Can not approve the flight');
                return;
            }
            
            var confirm = $mdDialog
            .confirm()
            .title('Would you like to approve this flight?')
            .content('You will approve this flight')
            .ariaLabel('Lucky day')
            .targetEvent(ev)
            .ok('Approve')
            .cancel('Cancel');
            
            $mdDialog.show(confirm).then(function(){
                
                $rootScope.$broadcast('preloader:active');
                flightsFactory.approve(flight.id).success(function(data, status){

                    if(status !== 200){
                        $scope.showError(status);
                        return;
                    }

                    $scope.showSuccess('Flight approved successfully');

                    $scope.reloadData();

                }).error(function(data, status){
                    console.log(data);
                    console.log(status);
                    $scope.showError(status);
                });
            });
        };
        
        $scope.cancel = function(ev, flight){
            if (flight.status != "scheduled"){
                $scope.showMessage('Can not cancel the flight');
                return;
            }
            
            var confirm = $mdDialog
            .confirm()
            .title('Would like to cancel this flight?')
            .content('You will cancel this flight')
            .ariaLabel('Lucky day')
            .targetEvent(ev)
            .ok('Yes')
            .cancel('No');
            
            $mdDialog.show(confirm).then(function(){
                
                $rootScope.$broadcast('preloader:active');
                flightsFactory.cancel(flight.id).success(function(data, status){

                    if(status !== 200){
                        $scope.showError(status);
                        return;
                    }

                    $scope.showSuccess('Flight canceled successfully');

                    $scope.reloadData();

                }).error(function(data, status){
                    console.log(data);
                    console.log(status);
                    $scope.showError(status);
                });
            });
        };
        
        $scope.reloadData = function(){
            flightsFactory.getAll().success(function(data){
                $scope.storedData = data.data;
                $scope.initDynamicTable();
            });
        }
        
        $scope.preShowConfirmDelete = function(ev, name, id) {
            $scope.showConfirmDelete(ev, 'Would you like to delete this flight?', 'You will delete: ' + name, id, flightsFactory, 'Flight deleted successfully');
        };
    })
    .controller('ViewAllpassedFlightsController' ,function ($scope, $rootScope, $controller, $mdDialog, $location, flightsFactory, flights, planes) {
        
        $controller('AuthController', {$scope: $scope});
        $controller('DynamicTableController', {$scope: $scope});
        
        $scope.editPath = '/flights/edit/';
        $scope.storedData = flights.data.data;
        $scope.initDynamicTable();
        
        $scope.planes = planes.data.data;
        $scope.planeIdSelected = -1;
        
        $scope.timeToShow = function(flight){
        
            if (flight.departure_time !== null){
                return flight.departure_time;
            } else {
                return flight.departure_min_time + " - " + flight.departure_max_time;
            }
        };
        
        $scope.checkEdit = function(flight){
            if (flight.booked_seats > 0){
                $scope.showMessage('Can not edit because the flight has been booked');
            } else {
                $scope.showEdit(flight.id);
            }
        };
        
        $scope.checkDelete = function(ev, flight){
          if (flight.booked_seats > 0){
                $scope.showMessage('Can not delete because the flight has been booked');
            } else {
                $scope.preShowConfirmDelete(ev,  flight.origin_name + ' - ' + flight.destination_name, flight.id);
            }
        };
        
        $scope.showBookings = function(flight){
            $location.path('/flights/' + flight.id + '/bookings');
        };
        
        $scope.showSetPlane = function(ev, flight){
            $mdDialog.show({
                clickOutsideToClose: false,
                scope: $scope,
                preserveScope: true,
                templateUrl: 'views/flights/setPlane.html',
                
                controller: function DialogController($scope, $mdDialog) {
                    
                    $scope.hideSetPlane = function(){
                        $scope.planeIdSelected = -1;
                        $mdDialog.hide();
                    }
                    $scope.cancel = function() {
                        $scope.hideSetPlane();
                    };
                    
                    $scope.planeSelected = function(plane){
                        for (var i = 0; i < $scope.planes.length; i++){
                            if ($scope.planes[i].id !== plane.id){
                                $scope.planes[i].checked = false;
                            }
                        }
                        
                        $scope.planeIdSelected = plane.checked === true ? plane.id : -1;
                    };
                    
                    $scope.setPlane = function(){
                        
                        $rootScope.$broadcast('preloader:active');
                        
                        var object = {
                            plane_id: $scope.planeIdSelected
                        };
                        
                        flightsFactory.setPlane(object, flight.id).success(function(data, status){
                        
                            $scope.hideSetPlane();
                                
                            if(status !== 200){
                                $scope.showError(status);
                                return;
                            }

                            $scope.showSuccess('Plane added successfully');
                            $scope.reloadData();
                            
                        }).error(function(data, status){
                            $scope.showError(status); 
                        });
                    }
                }
            });
        };
        
        $scope.approve = function(ev, flight){
            if (flight.status != "proposed"){
                $scope.showMessage('Can not approve the flight');
                return;
            }
            
            var confirm = $mdDialog
            .confirm()
            .title('Would you like to approve this flight?')
            .content('You will approve this flight')
            .ariaLabel('Lucky day')
            .targetEvent(ev)
            .ok('Approve')
            .cancel('Cancel');
            
            $mdDialog.show(confirm).then(function(){
                
                $rootScope.$broadcast('preloader:active');
                flightsFactory.approve(flight.id).success(function(data, status){

                    if(status !== 200){
                        $scope.showError(status);
                        return;
                    }

                    $scope.showSuccess('Flight approved successfully');

                    $scope.reloadData();

                }).error(function(data, status){
                    console.log(data);
                    console.log(status);
                    $scope.showError(status);
                });
            });
        };
        
        $scope.cancel = function(ev, flight){
            if (flight.status != "scheduled"){
                $scope.showMessage('Can not cancel the flight');
                return;
            }
            
            var confirm = $mdDialog
            .confirm()
            .title('Would like to cancel this flight?')
            .content('You will cancel this flight')
            .ariaLabel('Lucky day')
            .targetEvent(ev)
            .ok('Yes')
            .cancel('No');
            
            $mdDialog.show(confirm).then(function(){
                
                $rootScope.$broadcast('preloader:active');
                flightsFactory.cancel(flight.id).success(function(data, status){

                    if(status !== 200){
                        $scope.showError(status);
                        return;
                    }

                    $scope.showSuccess('Flight canceled successfully');

                    $scope.reloadData();

                }).error(function(data, status){
                    console.log(data);
                    console.log(status);
                    $scope.showError(status);
                });
            });
        };
        
        $scope.reloadData = function(){
            flightsFactory.getAllpassed().success(function(data){
                $scope.storedData = data.data;
                $scope.initDynamicTable();
            });
        }
        
        $scope.preShowConfirmDelete = function(ev, name, id) {
            $scope.showConfirmDelete(ev, 'Would you like to delete this flight?', 'You will delete: ' + name, id, flightsFactory, 'Flight deleted successfully');
        };
    })
    .controller('ViewAllfutureFlightsController' ,function ($scope, $rootScope, $controller, $mdDialog, $location, flightsFactory, flights, planes) {
        
        $controller('AuthController', {$scope: $scope});
        $controller('DynamicTableController', {$scope: $scope});
        
        $scope.editPath = '/flights/edit/';
        $scope.storedData = flights.data.data;
        $scope.initDynamicTable();
        
        $scope.planes = planes.data.data;
        $scope.planeIdSelected = -1;
        
        $scope.timeToShow = function(flight){
        
            if (flight.departure_time !== null){
                return flight.departure_time;
            } else {
                return flight.departure_min_time + " - " + flight.departure_max_time;
            }
        };
        
        $scope.checkEdit = function(flight){
            if (flight.booked_seats > 0){
                $scope.showMessage('Can not edit because the flight has been booked');
            } else {
                $scope.showEdit(flight.id);
            }
        };
        
        $scope.checkDelete = function(ev, flight){
          if (flight.booked_seats > 0){
                $scope.showMessage('Can not delete because the flight has been booked');
            } else {
                $scope.preShowConfirmDelete(ev,  flight.origin_name + ' - ' + flight.destination_name, flight.id);
            }
        };
        
        $scope.showBookings = function(flight){
            $location.path('/flights/' + flight.id + '/bookings');
        };
        
        $scope.showSetPlane = function(ev, flight){
            $mdDialog.show({
                clickOutsideToClose: false,
                scope: $scope,
                preserveScope: true,
                templateUrl: 'views/flights/setPlane.html',
                
                controller: function DialogController($scope, $mdDialog) {
                    
                    $scope.hideSetPlane = function(){
                        $scope.planeIdSelected = -1;
                        $mdDialog.hide();
                    }
                    $scope.cancel = function() {
                        $scope.hideSetPlane();
                    };
                    
                    $scope.planeSelected = function(plane){
                        for (var i = 0; i < $scope.planes.length; i++){
                            if ($scope.planes[i].id !== plane.id){
                                $scope.planes[i].checked = false;
                            }
                        }
                        
                        $scope.planeIdSelected = plane.checked === true ? plane.id : -1;
                    };
                    
                    $scope.setPlane = function(){
                        
                        $rootScope.$broadcast('preloader:active');
                        
                        var object = {
                            plane_id: $scope.planeIdSelected
                        };
                        
                        flightsFactory.setPlane(object, flight.id).success(function(data, status){
                        
                            $scope.hideSetPlane();
                                
                            if(status !== 200){
                                $scope.showError(status);
                                return;
                            }

                            $scope.showSuccess('Plane added successfully');
                            $scope.reloadData();
                            
                        }).error(function(data, status){
                            $scope.showError(status); 
                        });
                    }
                }
            });
        };
        
        $scope.approve = function(ev, flight){
            if (flight.status != "proposed"){
                $scope.showMessage('Can not approve the flight');
                return;
            }
            
            var confirm = $mdDialog
            .confirm()
            .title('Would you like to approve this flight?')
            .content('You will approve this flight')
            .ariaLabel('Lucky day')
            .targetEvent(ev)
            .ok('Approve')
            .cancel('Cancel');
            
            $mdDialog.show(confirm).then(function(){
                
                $rootScope.$broadcast('preloader:active');
                flightsFactory.approve(flight.id).success(function(data, status){

                    if(status !== 200){
                        $scope.showError(status);
                        return;
                    }

                    $scope.showSuccess('Flight approved successfully');

                    $scope.reloadData();

                }).error(function(data, status){
                    console.log(data);
                    console.log(status);
                    $scope.showError(status);
                });
            });
        };
        
        $scope.cancel = function(ev, flight){
            if (flight.status != "scheduled"){
                $scope.showMessage('Can not cancel the flight');
                return;
            }
            
            var confirm = $mdDialog
            .confirm()
            .title('Would like to cancel this flight?')
            .content('You will cancel this flight')
            .ariaLabel('Lucky day')
            .targetEvent(ev)
            .ok('Yes')
            .cancel('No');
            
            $mdDialog.show(confirm).then(function(){
                
                $rootScope.$broadcast('preloader:active');
                flightsFactory.cancel(flight.id).success(function(data, status){

                    if(status !== 200){
                        $scope.showError(status);
                        return;
                    }

                    $scope.showSuccess('Flight canceled successfully');

                    $scope.reloadData();

                }).error(function(data, status){
                    console.log(data);
                    console.log(status);
                    $scope.showError(status);
                });
            });
        };
        
        $scope.reloadData = function(){
            flightsFactory.getAllfuture().success(function(data){
                $scope.storedData = data.data;
                $scope.initDynamicTable();
            });
        }
        
        $scope.preShowConfirmDelete = function(ev, name, id) {
            $scope.showConfirmDelete(ev, 'Would you like to delete this flight?', 'You will delete: ' + name, id, flightsFactory, 'Flight deleted successfully');
        };
    });
})(); 