(function() {
  "use strict";

  angular
    .module("app")
    .factory("reservationFactory", [
      "$http",
      function($http) {
        var baseURL = "api/v1/private";
        var response = {
          data: []
        };

        response.getAll = function() {
          return $http.get(baseURL + "/reservation").success(function(data) {
            angular.copy(data, response.data);
          });
        };

        response.getAllfuture = function() {
          return $http
            .get(baseURL + "/reservation/future")
            .success(function(data) {
              angular.copy(data, response.data);
            });
        };

        response.getAllpassed = function() {
          return $http
            .get(baseURL + "/reservation/passed")
            .success(function(data) {
              angular.copy(data, response.data);
            });
        };

        response.get = function(id) {
          return $http.get(baseURL + "/reservation/" + id).then(function(res) {
            return res.data;
          });
        };

        response.create = function(object) {
          return $http.post(baseURL + "/reservation/create", object);
        };

        response.edit = function(object, id) {
          return $http.put(baseURL + "/reservation/" + id + "/edit", object);
        };

       

        // response.approve = function(id) {
        //   return $http.post(baseURL + "/reservation/" + id + "/approve");
        // };

        // response.cancel = function(id) {
        //   return $http.post(baseURL + "/reservation/" + id + "/cancel");
        // };

        response.delete = function(id) {
          return $http.delete(baseURL + "/reservation/" + id + "/destroy");
        };

        return response;
      }
    ])
    .controller("AddReservationController", function(
      $scope,
      $rootScope,
      $controller,
      reservationFactory,
      flightsFactory,
      $location,
      places,
      musers,
      $mdDialog
    ) {
      $controller("AuthController", { $scope: $scope });
      $controller("FormStandardController", { $scope: $scope });
     
      $scope.titleName = "Create a new manual reservation";
      $scope.extras = 0;
      $scope.musers = musers.data.data;

      $scope.places = places.data.data;
      $scope.extra_model = {
        complete_name: "",
        body_weight: "",
        luggage_weight: "",
        email: "",
        address: ""
      };
      $scope.seats = [];
      $scope.object = {
        user_id: -1,
        origin_id: -1,
        destination_id: -1,
        seats: 1,
        flight_id: -1,
        body_weight: 0,
        complete_name: "",
        luggage_weight: 0,
        extras: [],
        seats_limit:0,
        weight_limit:0,
        price:0,
        user:null
      };
      function resetFields() {
        $scope.object.extras = [];
        $scope.extras = [];
        $scope.seats = [];
        $scope.flights = [];
        $scope.flight_id = 0;
      }
      $scope.getFlights = function() {
        resetFields();
        if (
          $scope.object.origin_id !== -1 &&
          $scope.object.destination_id !== -1 &&
          $scope.object.origin_id !== null &&
          $scope.object.destination_id !== null
        ) {
          flightsFactory
            .getAllByPlaces(
              $scope.object.origin_id,
              $scope.object.destination_id
            )
            .then(flights => {
              $scope.flights = flights.data.data;
            });
        }
      };
      $scope.setCapacity = function(flight) {
        $scope.seats = [];
        $scope.extras = [];
        $scope.object.flight_id = flight.id;
        for (var i = 1; i <= flight.seats_limit; i++) {
          $scope.seats.push(i);
        }
        $scope.object.seats_limit = flight.seats_limit
        $scope.object.weight_limit = flight.weight_limit
        $scope.object.price = flight.price
      };
      $scope.setExtras = function(extra) {
        $scope.object.extras = [];
        $scope.extras = [];
        if (extra > 1) {
          for (var i = 0; i < extra - 1; i++) {
            $scope.extras.push(i);
          }
        }
      };
      $scope.setUserProperties = function(user) {
        $scope.object.user = user;
        $scope.object.body_weight = user.body_weight;
        $scope.object.complete_name = user.complete_name;
        
      };

      $scope.showExtraPassenger = function(index) {
        $mdDialog.show({
          clickOutsideToClose: false,
          scope: $scope,
          preserveScope: true,
          templateUrl: "views/reservations/extra-passenger-modal.html",

          controller: function DialogController($scope, $mdDialog) {
            if ($scope.object.extras.length > 0) {
              $scope.extra_model = Object.assign(
                $scope.extra_model,
                $scope.object.extras[index]
              );
            }
            $scope.canSubmitModal = function(e) {
              e.preventDefault();
              if ($scope.object.extras[index]) {
                $scope.object.extras[index] = $scope.extra_model;
              } else {
                console.log("entra al modal", $scope.object);
                $scope.object.extras.push($scope.extra_model);
              }

              clear();
              $mdDialog.hide();
            };

            $scope.cancel = function() {
              clear();
              $mdDialog.hide();
            };
          }
        });
      };
      $scope.submitForm = function() {
        if (!validation()) return false;

        $scope.doHttp(
          reservationFactory.create,
          "Reservation has been created successfully"
        );
      $scope.extras = []
      $scope.seats = [];
      clear()
      $location.path("/reservations/viewAll");
      };
      function clear() {
        $scope.extra_model = {
          complete_name: "",
          body_weight: "",
          luggage_weight: "",
          email: "",
          address: "",
          cell_phone:""
        };
        
      }
      function validation() {
        let approve = true;
        if ($scope.object.user_id == -1) {
          $scope.object.user_id = null;
          approve = false;
        }
        if ($scope.object.destination_id == -1) {
          $scope.object.destination_id = null;
          approve = false;
        }
        if ($scope.object.origin_id == -1) {
          $scope.object.origin_id = null;
          approve = false;
        }
        if ($scope.object.flight_id == -1) {
          $scope.object.flight_id = null;
          approve = false;
        }
        return approve;
      }
    })
    .controller('AllReservationController' ,function ($scope, $controller, payments,$mdDialog,reservationFactory) {

      $controller('AuthController', {$scope: $scope});
      $controller('DynamicTableController', {$scope: $scope});
      console.log("paymets",payments.data.data)
      $scope.storedData = payments.data.data;
      $scope.initDynamicTable();
      
      $scope.showEdit = function(payment) {
        $mdDialog.show({
          clickOutsideToClose: false,
          scope: $scope,
          preserveScope: true,
          templateUrl: "views/reservations/edit-state-payment-modal.html",

          controller: function DialogController($scope, $mdDialog,reservationFactory) {
            console.log(payment)
            $scope.changePayment = Object.assign({},payment)
          
            $scope.canSubmitModal = function(e){
              // e.preventDefault();
              $scope.changePayment.external_state = "approved"
                reservationFactory.edit($scope.changePayment,$scope.changePayment.payment_id).then(data => {
                  console.log(data)
                  $scope.reloadData();
                  $mdDialog.hide();
                })
               
              
            }
            $scope.cancel = function() {
             
              $mdDialog.hide();
            };
          }
        });
      }
      $scope.reloadData = function(){
        reservationFactory.getAll().success(function(data){
              $scope.storedData = data.data;
              $scope.initDynamicTable();
          });
      }
      $scope.preShowConfirmDelete = function(ev,id){
        
              // $scope.showMessage('Can not delete because the flight has been booked');
          
              $scope.showConfirmDelete(ev, 'Would you like to delete this payment?', 'You will delete: #' + id, id, reservationFactory, 'payment deleted successfully');
          };

  });
    
  
})();
