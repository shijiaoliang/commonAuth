'use strict';
app.controller('userController', function($scope, $rootScope, $q, $http, $state, $stateParams, $sce, $location, $timeout, $compile,
    dialog, tipDialog) {
    var vm = $scope.vm = {
        currentPage: 1,
        oldPssword: '',
        newPssword: '',
        newPssword2: ''
    };

    var msg = {
        Notice : '提示信息',
        almsg  : '操作成功',
        msg1 : '密码不能为空，请重新输入！',
        msg2 : '密码长度不能小于8，请重新输入！',
        msg3 : '两次密码输入不一致，请重新输入！',
        msg4 : '原密码不正确！',
        msg5 : '密码必须包含大写字母，小写字母，数字，特殊字符',
        msg6 : '修改密码失败'
    };

    //提示信息
    var alerts = [{
        type: 'success',
        msg: msg.almsg
    }];

    //changePwd
    $scope.changePwd = function () {
        if (vm.oldPssword == '' || vm.newPssword == '' || vm.newPssword2 == '') {
            tipDialog.tip(msg.msg1);
            return false;
        }
        if (vm.newPssword.length < 8 || vm.newPssword2.length < 8) {
            tipDialog.tip(msg.msg2);
            return false;
        }

        //var pattern = /^(?=.*?[a-z])(?=.*?[A-Z])(?=.*?\d)(?=.*?[#@*$!$&^~_.]).*$/;
        //var password = vm.newPssword;
        //if (!password.match(pattern)) {
        //    tipDialog.tip(msg.msg5);
        //    return false;
        //}

        if (vm.newPssword != vm.newPssword2) {
            tipDialog.tip(msg.msg3);
            return false;
        }

        var data = {
            oldPssword: vm.oldPssword,
            newPssword: vm.newPssword
        };
        $http.post('/index.php?r=user/changePwd', data).success(function (res) {
            tipDialog.tip(res.errMsg);

            if (res.ret == 1) {
                vm.oldPssword = '';
                vm.newPssword = '';
                vm.newPssword2 = '';
                $scope.alerts = alerts;

                $timeout(function () {
                    $scope.alerts = [];
                }, 2000);
            }
        });
    };

    //load
    $scope.load = function() {
        $scope.search();
    };

    $scope.listForm = {};
    $scope.reset = function () {
        $scope.listForm = {};
    };
});