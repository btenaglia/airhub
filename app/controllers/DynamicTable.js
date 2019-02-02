(function () {
    'use strict';

    angular.module('app')
    .controller('DynamicTableController' ,function ($scope, $rootScope, $location, $controller, $filter, $mdDialog) {

        $controller('NotificationsController', {$scope: $scope});
        
        $scope.storedData = [];
        $scope.searchKeywords = '';
        $scope.filteredData = [];
        $scope.row = '';
        $scope.select = select;
        $scope.onFilterChange = onFilterChange;
        $scope.onNumPerPageChange = onNumPerPageChange;
        $scope.onOrderChange = onOrderChange;
        $scope.search = search;
        $scope.order = order;
        $scope.numPerPageOpt = [3, 5, 10, 20];
        $scope.numPerPage = $scope.numPerPageOpt[2];
        $scope.currentPage = 1;
        $scope.currentPage = [];
        
        $scope.showEdit = function(id){
            $location.path($scope.editPath + id);
        }
        
        $scope.showConfirmDelete = function(ev, title, content, id, factory, successMessage) {
            var confirm = $mdDialog
            .confirm()
            .title(title)
            .content(content)
            .ariaLabel('Lucky day')
            .targetEvent(ev)
            .ok('Delete')
            .cancel('Cancel');
            
            $mdDialog.show(confirm).then(function(){
                
                $rootScope.$broadcast('preloader:active');
                factory.delete(id).success(function(data, status){

                    if(status !== 200){
                        $scope.showError(status);
                        return;
                    }

                    $scope.showSuccess(successMessage);

                    factory.getAll().success(function(data){
                        $scope.storedData = data.data;
                        $scope.initDynamicTable();
                    });

                }).error(function(data, status){
                    $scope.showError(status);
                });
            });
        };

        function select(page) {
            var end, start;
            start = (page - 1) * $scope.numPerPage;
            end = start + $scope.numPerPage;
            return $scope.currentPageData = $scope.filteredData.slice(start, end);
        };

        function onFilterChange() {
            $scope.select(1);
            $scope.currentPage = 1;
            return $scope.row = '';
        };

        function onNumPerPageChange() {
            $scope.select(1);
            return $scope.currentPage = 1;
        };

        function onOrderChange() {
            $scope.select(1);
            return $scope.currentPage = 1;
        };

        function search() {
            $scope.filteredData = $filter('filter')($scope.storedData, $scope.searchKeywords);
            return $scope.onFilterChange();
        };

        function order(rowName) {
            if ($scope.row === rowName) {
            return;
            }
            $scope.row = rowName;
            $scope.filteredData = $filter('orderBy')($scope.storedData, rowName);
            return $scope.onOrderChange();
        };

        $scope.initDynamicTable = function() {
            $scope.search();
            return $scope.select($scope.currentPage);
        };
    });
})(); 