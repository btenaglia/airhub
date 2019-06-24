(function() {
  "use strict";

  angular
    .module("app")
    .factory("usersFactory", [
      "$http",
      function($http) {
        var baseURL = "api/v1/private";
        var response = {
          data: []
        };

        response.getAll = function() {
          return $http.get(baseURL + "/users").success(function(data) {
            angular.copy(data, response.data);
          });
        };

        response.get = function(id) {
          return $http.get(baseURL + "/users/" + id).then(function(res) {
            return res.data;
          });
        };

        response.create = function(object) {
          return $http.post(baseURL + "/users/create", object);
        };

        response.edit = function(object, id) {
          return $http.put(baseURL + "/users/" + id + "/edit", object);
        };

        response.delete = function(id) {
          return $http.delete(baseURL + "/users/" + id + "/destroy");
        };

        response.sendpush = function(id) {
          //return $http.post(baseURL + '/users/' + id + '/sendpush');
          return $http.get(baseURL + "/users/sendpush/" + id);
        };

        return response;
      }
    ])
    .controller("AddUserController", function(
      $scope,
      $rootScope,
      $controller,
      usersFactory
    ) {
      $controller("AuthController", { $scope: $scope });
      $controller("FormStandardController", { $scope: $scope });

      $scope.titleName = "Add new admin user";

      $scope.object = {
        user_type: "admin"
      };

      $scope.showPassword = true; //TODO FIXME

      $scope.submitForm = function() {
        $scope
          .doHttp(usersFactory.create, "New user added successfully")
          .error(function(data, status) {
            if (status === 409) {
              $scope.showUserExistsError();
            }
          });
      };
    })
    .controller("EditUserController", function(
      $scope,
      $rootScope,
      $controller,
      usersFactory,
      user
    ) {
      $controller("AuthController", { $scope: $scope });
      $controller("FormStandardController", { $scope: $scope });

      $scope.titleName = "Edit mobile user";

      $scope.object = {
        complete_name: user.data.complete_name,
        email: user.data.email,
        password: "----"
      };

      $scope.objectId = user.data.id;

      $scope.showPassword = false; //TODO FIXME

      $scope.submitForm = function() {
        $scope.doHttp(usersFactory.edit, "User edited successfully");
      };
    })
    .controller("ViewAllUsersController", function(
      $scope,
      $controller,
      $mdDialog,
      usersFactory,
      users
    ) {
      $controller("AuthController", { $scope: $scope });
      $controller("DynamicTableController", { $scope: $scope });

      $scope.editPath = "/users/edit-admin/";
      $scope.storedData = users.data.data;
      $scope.initDynamicTable();

      $scope.preShowConfirmDelete = function(ev, name, id) {
        $scope.showConfirmDelete(
          ev,
          "Would you like to delete this user?",
          "You will delete: " + name,
          id,
          usersFactory,
          "User deleted successfully"
        );
      };

      $scope.sendPush = function(id, name) {
        var confirm = $mdDialog
          .confirm()
          .title("Send Push Message")
          .content("A push message will be sent to the selected user: " + name)
          .ok("Send")
          .cancel("Cancel");

        $mdDialog.show(confirm).then(function() {
          usersFactory
            .sendpush(id)
            .success(function(data, status) {
              if (status !== 200) {
                $scope.showError(status);
                return;
              }

              $scope.showSuccess("Message sent");
            })
            .error(function(data, status) {
              $scope.showError(status);
            });
        });
      };
      $scope.reloadData = function() {
        usersFactory.getAll().success(function(data) {
          $scope.storedData = data.data;
          $scope.initDynamicTable();
        });
      };
      $scope.verified = function(userMobile) {
        var confirm = $mdDialog
          .confirm()
          .title("Verification User")
          .content(`Verify user ${userMobile.name} ?`)
          .ok("Accept")
          .cancel("Cancel");

        $mdDialog.show(confirm).then(function() {
          userMobile.verified = 1;
          console.log(userMobile)
          usersFactory.edit(userMobile, userMobile.id).success(function() {
            $scope.reloadData();
          });
        });
      };
    });
})();
