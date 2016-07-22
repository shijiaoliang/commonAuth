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
                $scope.pager = result.pager;
                $scope.applications = result.applications;

                if ($scope.applications) {
                    angular.forEach($scope.applications, function(y, x) {
                        //statusTxt
                        y.statusTxt = '启用';
                        if (y.app_status == 20) {
                            y.statusTxt = '禁用';
                        }

                        //app_create_time
                        y.app_create_time = y.app_create_time * 1000;
                    });
                }
            } else {
                tipDialog.tip(res.errMsg);
            }
        });
    };

    $scope.add = function() {
        //
    };

    $scope.switch = function(item, event) {
        //
    };

    $scope.update = function(item, event) {
        //
    };
});