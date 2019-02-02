(function () {
    'use strict';

    angular.module('app')
    .factory('bookingsFactory', ['$http', function($http){
        
        var baseURL = 'api/v1/private';
        var response = {
            data: []
        };
    
        /*response.getAll = function() {
            return $http.get(baseURL + '/places').success(function(data){
                angular.copy(data, response.data);
            });
        };

        response.get = function(id) {
            return $http.get(baseURL + '/places/' + id).then(function(res){
                return res.data;
            });
        };*/
        
        response.getByFlight = function(id) {
            return $http.get(baseURL + '/bookings-by-flight/' + id).then(function(res){
                return res.data;
            });
        };

        response.capture = function(paymentId) {
            return $http.post(baseURL + '/payments/' + paymentId + '/capture');
        };
        
        /*response.edit = function(object, id) {
            return $http.put(baseURL + '/places/' + id + '/edit', object);
        };
        
        response.delete = function(id) {
            return $http.delete(baseURL + '/places/' + id + '/destroy');
        };*/

        return response;
    }])
    .controller('ViewBookingsController' ,function ($scope, $rootScope, $controller, $mdDialog, bookingsFactory, flight, bookings) {
        
        $controller('AuthController', {$scope: $scope});
        $controller('DynamicTableController', {$scope: $scope});
        
        $scope.flightName = flight.data.origin_name + " to " + flight.data.destination_name;
        
        $scope.flight = flight.data;
        
        $scope.storedData = bookings.data;
        $scope.initDynamicTable();
        
        $scope.paymentInfo = function(booking){
            if (booking.capture_state === null){
                return booking.intent + ": " + booking.external_state;
            }
            
            return booking.capture_state;
        };
        
        $scope.showCapture = function(booking){
            if (booking.capture_state === null && booking.external_state === "approved"){
                return true;
            }
            return false;
        };
        
        $scope.getTotalB = function(){
            var total = 0;
            for(var i = 0; i < $scope.storedData.length; i++){
                //var product = $scope.cart.products[i];
                //total += (product.price * product.quantity);
                total += $scope.storedData[i].body_weight;
            }
            return total;
        }
        
        $scope.getTotalL = function(){
            var total = 0;
            for(var i = 0; i < $scope.storedData.length; i++){
                //var product = $scope.cart.products[i];
                //total += (product.price * product.quantity);
                total += $scope.storedData[i].luggage_weight;
            }
            return total;
        }
        
        $scope.move = function(ev, booking){
            
            $mdDialog.show({
                clickOutsideToClose: false,
                scope: $scope,
                preserveScope: true,
                templateUrl: 'views/flights/setFlight.html',
                
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
            
            
        }
        
        $scope.capture = function(ev, booking){
            
            var confirm = $mdDialog
            .confirm()
            .title('Would you like to approve and capture the payment of this booking?')
            .content('You will approve the booking and capture the payment from paypal')
            .ariaLabel('Lucky day')
            .targetEvent(ev)
            .ok('Approve and capture')
            .cancel('Cancel');
            
            $mdDialog.show(confirm).then(function(){
                
                $rootScope.$broadcast('preloader:active');
                bookingsFactory.capture(booking.id).success(function(data, status){

                    if(status !== 200){
                        $scope.showError(status);
                        return;
                    }

                    $scope.showSuccess('Booking approved successfully');

                    bookingsFactory.getByFlight($scope.flight.id).success(function(data){
                        $scope.storedData = data.data;
                        $scope.initDynamicTable();
                    });

                }).error(function(data, status){
                    $scope.showError(status);
                });
            });
        };
    });
})(); 