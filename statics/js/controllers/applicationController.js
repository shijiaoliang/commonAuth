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
                        //statusTxt switchTxt
                        y.statusTxt = '启用';
                        y.switchTxt = '禁用';
                        if (y.app_status == 20) {
                            y.statusTxt = '禁用';
                            y.switchTxt = '启用';
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
        $scope._addUpdate();
    };

    $scope.update = function(item, event) {
        $scope._addUpdate(item);
    };

    $scope._addUpdate = function(item) {
        var instance = dialog.open({
            //backdrop:false,
            templateUrl: 'applicationModal.html',
            size: 'md',
            controller: function ($scope) {
                //appName, appCode, appUrl
                $scope.ngData = item || {};

                if (item) {
                    $scope.ngData.appId = item.app_id;
                }

                $scope.cancel = function() {
                    instance.close();
                };
                $scope.ok = function(formObj) {
                    $http.post('/index.php?r=application/save', $scope.ngData).success(function (res) {
                        tipDialog.tip(res.errMsg);

                        if (res.ret == 1) {
                            var result = res.data;
                            if (item) {
                                item = result;
                            } else {
                                $scope.search();
                            }
                        }
                    });

                    instance.close();
                };
            }
        });
    };

    $scope.switch = function(item, event) {
        //
    };
});