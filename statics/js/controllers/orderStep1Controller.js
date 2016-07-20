'use strict';
/* collect Controllers */
app.controller('orderStep1Controller', function($scope, $rootScope, $http, $state, $stateParams, $sce, $location, $timeout, $compile, dialog, tipDialog, serviceValid, NgMap, orderServicesBase, orderServicesOrder) {
    //======================================step1======================================
    //fQueryCustomerEmail 根据email查询用户
    $scope.fQueryCustomerEmail = function() {
        if (! $scope.ngmodel.customerEmail || !serviceValid.checkEmail($scope.ngmodel.customerEmail)) {
            tipDialog.open({title: $scope.msg.Notice, template: $scope.msg.msg1, isOk: true, timeOut: 3000});
            return false;
        }

        var promise = orderServicesOrder.queryEmail($scope.ngmodel.customerEmail);
        promise.then(function (res) {
            if (!res.ret) {
                tipDialog.open({title: $scope.msg.Notice, template: $scope.msg.msg2, isOk: true, timeOut: 3000});
                return false;
            }

            //赋值emails
            $scope.g.emails = res.data;
        });
    };

    //fChooseUser 选择用户
    $scope.fChooseUser = function(event, item) {
        var checked = $(event.target).is(':checked');
        if (checked) {
            if (item && item.uid > 0 || item.name != '') {
                $scope.ngmodel.user = item;
            }
        } else {
            $scope.ngmodel.user = {};
        }
    };
});