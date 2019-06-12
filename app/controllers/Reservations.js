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

        response.setPlane = function(object, id) {
          return $http.put(
            baseURL + "/reservation/" + id + "/set-plane",
            object
          );
        };

        response.approve = function(id) {
          return $http.post(baseURL + "/reservation/" + id + "/approve");
        };

        response.cancel = function(id) {
          return $http.post(baseURL + "/reservation/" + id + "/cancel");
        };

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
      flights,
      places,
      musers,
      $mdDialog
    ) {
      $controller("AuthController", { $scope: $scope });
      $controller("FormStandardController", { $scope: $scope });

      console.log("usuarios", musers.data.data);
      $scope.titleName = "Create a new reservation";
      $scope.extras = 0;
      $scope.musers = musers.data.data;
      $scope.flights = flights.data.data;
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
        origin_id: -1,
        destination_id: -1,
        seats: 1,
        flight_id: 0,
        body_weight: 0,
        complete_name: "",
        luggage_weight: 0,
        extras: []
      };
      $scope.getFlights = function() {
        if (
          $scope.object.origin_id !== -1 &&
          $scope.object.destination_id !== -1
        ) {
          $scope.flights = [];

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
        $scope.object.flight_id = flight.id;
        for (var i = 1; i <= flight.seats_limit; i++) {
          $scope.seats.push(i);
        }
        console.log($scope.object);
      };
      $scope.setExtras = function(extra) {
        debugger;
        if (extra > 1) {
          $scope.extras = [];
          for (var i = 0; i < extra - 1; i++) {
            $scope.extras.push(i);
          }
        }
      };
      $scope.setUserProperties = function(user) {
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
            //   $scope.hideSetPlane = function(){
            //     $scope.planeIdSelected = -1;

            // }
            if ($scope.object.extras.length > 0) {
              $scope.extra_model = $scope.object.extras[index];
            }
            $scope.canSubmitModal = function() {
              if ($scope.object.extras[index]) {
                $scope.object.extras[index] = $scope.extra_model;
              } else {
                console.log("entra al modal", $scope.object);
                $scope.object.extras.push($scope.extra_model);
              }
              $scope.extra_model = {
                complete_name: "",
                body_weight: "",
                luggage_weight: "",
                email: "",
                address: ""
              };
              $scope.cancel()
            };

            $scope.cancel = function() {
              $mdDialog.hide();
            };
          }
        });
      };
    })
    .controller("ViewAllreservationController", function(
      $scope,
      $rootScope,
      $controller,
      $mdDialog,
      $location,
      reservationFactory,
      reservation,
      planes
    ) {});
})();
