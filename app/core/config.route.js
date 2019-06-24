(function() {
  "use strict";

  angular.module("app").config([
    "$stateProvider",
    "$urlRouterProvider",
    function($stateProvider, $urlRouterProvider) {
      $stateProvider

        .state("home", {
          url: "/home",
          templateUrl: "views/home/home.html",
          controller: "HomeController"
        })

        .state("places-add", {
          url: "/places/add",
          templateUrl: "views/places/add.html",
          controller: "AddPlaceController"
        })

        .state("places-edit", {
          url: "/places/edit/{id}",
          templateUrl: "views/places/add.html",
          controller: "EditPlaceController",
          resolve: {
            place: [
              "$stateParams",
              "placesFactory",
              function($stateParams, factory) {
                return factory.get($stateParams.id);
              }
            ]
          }
        })

        .state("places-viewAll", {
          url: "/places/viewAll",
          templateUrl: "views/places/viewAll.html",
          controller: "ViewAllPlacesController",
          resolve: {
            places: [
              "placesFactory",
              function(factory) {
                return factory.getAll();
              }
            ]
          }
        })
        .state("members-add", {
          url: "/members/add",
          templateUrl: "views/members/add.html",
          controller: "AddMemberController",
          resolver: {}
        })

        .state("members-edit", {
          url: "/members/edit/{id}",
          templateUrl: "views/members/add.html",
          controller: "EditMemberController",
          resolve: {
            member: [
              "$stateParams",
              "membersFactory",
              function($stateParams, factory) {
                return factory.get($stateParams.id);
              }
            ]
          }
        })

        .state("members-viewAll", {
          url: "/members/viewAll",
          templateUrl: "views/members/viewAll.html",
          controller: "ViewAllmembersController",
          resolve: {
            members: [
              "membersFactory",
              function(factory) {
                return factory.getAll();
              }
            ]
          }
        })
        .state("members-notification", {
          url: "/members/send-notification",
          templateUrl: "views/members/send-notification.html",
          controller: "NotificationMembersController",
          resolve: {
            members: [
              "membersFactory",
              function(factory) {
                return factory.getAll();
              }
            ]
          }
        })
        .state("profiles-add", {
          url: "/profiles/add",
          templateUrl: "views/profiles/add.html",
          controller: "AddProfileController",
          resolve: {
            profile: [
              "profilesFactory",
              function(factory) {
                return factory.getAll();
              }
            ]
          }
        })

        .state("profiles-edit", {
          url: "/profiles/edit/{id}",
          templateUrl: "views/profiles/add.html",
          controller: "EditProfileController",
          resolve: {
            profile: [
              "$stateParams",
              "profilesFactory",
              function($stateParams, factory) {
                return factory.get($stateParams.id);
              }
            ]
          }
        })

        .state("profiles-viewAll", {
          url: "/profiles/viewAll",
          templateUrl: "views/profiles/viewAll.html",
          controller: "ViewAllProfilesController",
          resolve: {
            profiles: [
              "profilesFactory",
              function(factory) {
                return factory.getAll();
              }
            ]
          }
        })

        .state("planes-add", {
          url: "/planes/add",
          templateUrl: "views/planes/add.html",
          controller: "AddPlaneController"
        })

        .state("planes-edit", {
          url: "/planes/edit/{id}",
          templateUrl: "views/planes/add.html",
          controller: "EditPlaneController",
          resolve: {
            plane: [
              "$stateParams",
              "planesFactory",
              function($stateParams, factory) {
                return factory.get($stateParams.id);
              }
            ]
          }
        })

        .state("planes-viewAll", {
          url: "/planes/viewAll",
          templateUrl: "views/planes/viewAll.html",
          controller: "ViewAllPlanesController",
          resolve: {
            planes: [
              "planesFactory",
              function(factory) {
                return factory.getAll();
              }
            ]
          }
        })

        .state("flights-add", {
          url: "/flights/add",
          templateUrl: "views/flights/add.html",
          controller: "AddFlightController",
          resolve: {
            places: [
              "placesFactory",
              function(factory) {
                return factory.getAll();
              }
            ],
            planes: [
              "planesFactory",
              function(factory) {
                return factory.getAll();
              }
            ],
            status: [
              "statusFactory",
              function(factory) {
                return factory.getAll(); //factory.getAll();  //getAllcreated
              }
            ]
          }
        })

        .state("flights-edit", {
          url: "/flights/edit/{id}",
          templateUrl: "views/flights/edit.html",
          controller: "EditFlightController",
          resolve: {
            flight: [
              "$stateParams",
              "flightsFactory",
              function($stateParams, factory) {
                return factory.get($stateParams.id);
              }
            ],
            places: [
              "placesFactory",
              function(factory) {
                return factory.getAll();
              }
            ],
            planes: [
              "planesFactory",
              function(factory) {
                return factory.getAll();
              }
            ],
            status: [
              "statusFactory",
              function(factory) {
                return factory.getAll();
              }
            ]
          }
        })

        .state("flights-viewAll", {
          url: "/flights/viewAll",
          templateUrl: "views/flights/viewAll.html",
          controller: "ViewAllFlightsController",
          resolve: {
            flights: [
              "flightsFactory",
              function(factory) {
                return factory.getAll();
              }
            ],
            planes: [
              "planesFactory",
              function(factory) {
                return factory.getAll();
              }
            ]
          }
        })
        .state("flights-viewAllpassed", {
          url: "/flights/viewAllpassed",
          templateUrl: "views/flights/viewAllpassed.html",
          controller: "ViewAllpassedFlightsController",
          resolve: {
            flights: [
              "flightsFactory",
              function(factory) {
                return factory.getAllpassed();
              }
            ],
            planes: [
              "planesFactory",
              function(factory) {
                return factory.getAll();
              }
            ]
          }
        })
        .state("flights-viewAllfuture", {
          url: "/flights/viewAllfuture",
          templateUrl: "views/flights/viewAllfuture.html",
          controller: "ViewAllfutureFlightsController",
          resolve: {
            flights: [
              "flightsFactory",
              function(factory) {
                return factory.getAllfuture();
              }
            ],
            planes: [
              "planesFactory",
              function(factory) {
                return factory.getAll();
              }
            ]
          }
        })

        .state("bookings-by-flight", {
          url: "/flights/{id}/bookings",
          templateUrl: "views/bookings/viewByFlightTotal.html",
          controller: "ViewBookingsController",
          resolve: {
            flight: [
              "$stateParams",
              "flightsFactory",
              function($stateParams, factory) {
                return factory.get($stateParams.id);
              }
            ],
            bookings: [
              "$stateParams",
              "bookingsFactory",
              function($stateParams, factory) {
                return factory.getByFlight($stateParams.id);
              }
            ]
          }
        })

        .state("users-add-admin", {
          url: "/users/add-admin",
          templateUrl: "views/users/add-admin.html",
          controller: "AddUserController"
        })

        .state("users-edit-admin", {
          url: "/users/edit-admin/{id}",
          templateUrl: "views/users/add-admin.html",
          controller: "EditUserController",
          resolve: {
            user: [
              "$stateParams",
              "usersFactory",
              function($stateParams, factory) {
                return factory.get($stateParams.id);
              }
            ]
          }
        })

        .state("musers-add", {
          url: "/musers/add",
          templateUrl: "views/musers/add.html",
          controller: "AddMuserController",
          resolve: {
            members: [
              "membersFactory",
              function(factory) {
                return factory.getAll();
              }
            ]
          }
        })

        .state("musers-edit", {
          url: "/musers/edit/{id}",
          templateUrl: "views/musers/add.html",
          controller: "EditMuserController",
          resolve: {
            user: [
              "$stateParams",
              "usersFactory",
              function($stateParams, factory) {
                return factory.get($stateParams.id);
              }
            ]
          }
        })

        .state("users-viewAll", {
          url: "/users/viewAll",
          templateUrl: "views/users/viewAll.html",
          controller: "ViewAllUsersController",
          resolve: {
            users: [
              "usersFactory",
              function(factory) {
                return factory.getAll();
              }
            ]
          }
        })

        .state("payments-viewAll", {
          url: "/payments/viewAll",
          templateUrl: "views/payments/viewAll.html",
          controller: "ViewAllPaymentsController",
          resolve: {
            payments: [
              "paymentsFactory",
              function(factory) {
                return factory.getAll();
              }
            ]
          }
        })

        .state("setup-edit", {
          url: "/setup/edit/{id}",
          templateUrl: "views/setup/add.html",
          controller: "EditSetupController",
          resolve: {
            setup: [
              "$stateParams",
              "setupFactory",
              function($stateParams, factory) {
                return factory.get($stateParams.id);
              }
            ]
          }
        })

        .state("setup-edit2", {
          url: "/setup/edit2/{id}",
          templateUrl: "views/setup/add2.html",
          controller: "EditSetupController",
          resolve: {
            setup: [
              "$stateParams",
              "setupFactory",
              function($stateParams, factory) {
                return factory.get($stateParams.id);
              }
            ]
          }
        })

        .state("login", {
          url: "/login",
          templateUrl: "views/login/login.html",
          controller: "AccountsController"
        })
        // reservation
        .state("reservations-viewAll", {
          url: "/reservations/viewAll",
          templateUrl: "views/reservations/viewAll.html",
          controller: "AllReservationController",
          resolve: {
            payments: [
              "reservationFactory",
              function(factory) {
                return factory.getAll();
              }
            ]
          }
        })
        .state("reservation-add", {
          url: "/reservations/add",
          templateUrl: "views/reservations/add-reservation.html",
          controller: "AddReservationController",
          resolve: {
        
            places: [
              "placesFactory",
              function(factory) {
                return factory.getAll();
              }],
        
              musers: [
              "musersFactory",
              function(factory) {
                return factory.getAll(); //factory.getAll();  //getAllcreated
              }
            ]
                       
          }
        })


       

      $urlRouterProvider.otherwise("/home");
    }
  ]);
})();
