'use strict';
app.controller('applicationController', function($scope, $rootScope, $q, $http, $state, $stateParams, $sce, $location, $timeout, $compile,
    dialog, tipDialog) {
    var vm = $scope.vm = {
        currentPage: 1
    };

    //load
    $scope.load = function() {
        $scope.search();
    };

    $scope.listForm = {};
    $scope.reset = function () {
        $scope.listForm = {};
    };

    $scope.search = function () {
        var params = {page: vm.currentPage, listForm: $scope.listForm};
        $http.get('/index.php?r=application/list', {params: params}).success(function (res) {
            if (res.ret == true) {
                var result = res.data;
                $scope.applications = result.applications;
                $scope.pager = result.pager;
            } else {
                tipDialog.tip(res.errMsg);
            }
        });
    };
});