'use strict';
/* collect Controllers */
app.controller('orderController', function($scope, $rootScope, $q, $http, $state, $stateParams, $sce, $location, $timeout, $compile,
    dialog, tipDialog, serviceValid, NgMap, orderServicesBase, orderServicesOrder) {
    //中英文切换
    $scope.msg = {};
    if ($scope.selectLang == "English") {
        $scope.msg = {
            "Notice" : "Notice",
            "almsg"  : "Successfully",
            "msg1"   : "Please input a correct Customer Email!",
            "msg2"   : "We can not find this user, please check again.",
            "msg3"   : "Please choose a user and then click next.",
            "msg4"   : "Please at least select one item.",
            "msg5"   : "There is no stores!",
            "msg6"   : "The quantity of goods is wrong!",
            "msg7"   : "You can only buy up to 4 devices per order, sorry!",
            "msg8"   : "Price can not be less than 0!"
        };
    } else {
        $scope.msg = {
            "Notice" : "提示信息",
            "almsg"  : "操作成功",
            "msg1"   : "Please input a correct Customer Email!",
            "msg2"   : "We can not find this user, please check again.",
            "msg3"   : "Please choose a user and then click next.",
            "msg4"   : "Please at least select one item.",
            "msg5"   : "There is no stores!",
            "msg6"   : "The quantity of goods is wrong!",
            "msg7"   : "You can only buy up to 4 devices per order, sorry!",
            "msg8"   : "Price can not be less than 0!"
        };
    }

    /**
     * 根据orderNo查询order
     * @returns {undefined}
     */
    $scope.orderNo = '';
    $scope.queryDetail = function(orderNo) {
        if (orderNo == '' && $scope.orderNo == '') {
            tipDialog.open({
                title: $scope.msg.Notice,
                template: 'Please enter the correct order number.',
                isOk: true,
                timeOut: 3000
            });
            return false;
        }

        if (orderNo != '') {
            $scope.orderNo = orderNo;
        }

        var params = {orderNo: $scope.orderNo};
        $http.get('/index.php?r=order/ajaxGetOrderDetail', {params: params}).success(function (res) {
            if (res.ret == true) {
                var result = res.data;
                if (typeof(result.order) == "undefined") {
                    tipDialog.open({title: $scope.msg.Notice, template: result.errmsg, isOk: true, timeOut: 3000});
                    $scope.orderDetail = '';
                } else {
                    $scope.orderDetail = result.order;
                }
                $scope.orderDetail = result.order;
            } else {
                tipDialog.open({title: $scope.msg.Notice, template: res.errMsg, isOk: true, timeOut: 3000});
            }
        });
    };

    /*===============海外订单创建===============*/
    //static vars
    $scope.s = {};
    $scope.s.stepTitleArr = {
        1: '1 Customer Email',
        2: '2 Items',
        3: '3 Order Detail'
    };
    $scope.s.stepTitle = $scope.s.stepTitleArr[1];
    $scope.s.step = 1;
    $scope.s.stepArr = [1, 2, 3];

    //ng-models
    $scope.ngmodel = {};
    $scope.ngmodel.customerEmail = '';
    $scope.ngmodel.user = {
        uid:0,
        email:''
    };
    $scope.ngmodel.orderType = '';
    $scope.ngmodel.department = '';
    $scope.ngmodel.store = 0;
    $scope.ngmodel.goods = [];
    $scope.ngmodel.shippingAddress = {};
    $scope.ngmodel.billingAddress = {};
    $scope.ngmodel.sameAsShipping = true;

    //global vars
    $scope.g = {};
    $scope.g.emails = [];
    $scope.g.orderTypes = [];
    $scope.g.departments = [];
    $scope.g.stores = [];
    $scope.g.goods = [];
    $scope.g.addressList = [];

    //load
    $scope.load = function() {
        //权限验证
        var promise = orderServicesBase.checkPower(true);
        promise.then(function (res) {
            if (res.ret) {
                //全局watch
                $scope.fWatch();

                //初始化step
                $scope.s.step = parseInt($stateParams.step);
                if (!$stateParams.step || ($.inArray($scope.s.step, $scope.s.stepArr) == -1)) {
                    $scope.s.step = 1;
                }
                $scope.fCreateOrderStep($scope.s.step);
            }
        });
    };

    //fCreateOrderStep back|next
    $scope.fCreateOrderStep = function(step) {
        step = parseInt(step);

        if (-1 != $.inArray(step, $scope.s.stepArr)) {
            //滚动到页面顶部
            $(window).scrollTop(0);

            var bool = true;
            if (2 == step) {
                if ($scope.ngmodel.user.uid <= 0) {
                    bool = false;
                    tipDialog.open({title: $scope.msg.Notice, template: $scope.msg.msg3, isOk: true, timeOut: 3000});
                }

                if (!bool) {
                    if ($scope.s.step != 1) {
                        $scope.fCreateOrderStep(1);
                    }
                    return false;
                }

                //事件广播
                $scope.$broadcast('callStep2');
            } else if (3 == step) {
                if ($scope.ngmodel.goods.length <= 0) {
                    bool = false;
                    tipDialog.open({title: $scope.msg.Notice, template: $scope.msg.msg4, isOk: true, timeOut: 3000});
                } else {
                    angular.forEach($scope.ngmodel.goods, function(y, x) {
                        if (y.quantity <= 0) {
                            bool = false;
                            tipDialog.open({title: $scope.msg.Notice, template: $scope.msg.msg6, isOk: true, timeOut: 3000});
                        }
                    });
                }

                if (!bool) {
                    if ($scope.s.step != 1) {
                        $scope.fCreateOrderStep(1);
                    }
                    return false;
                }

                //事件广播
                $scope.$broadcast('callStep3');
            }

            $scope.s.step = step;
            $scope.s.stepTitle = $scope.s.stepTitleArr[$scope.s.step];
        }
    };

    //fWatch 监控数据变更
    $scope.fWatch = function() {
        //s.step
        $scope.$watch('s.step', function (newValue, oldValue) {
            //从2 返回到 1
            if (newValue == 1 && oldValue == 2) {
                $scope.ngmodel.orderType = '';
                $scope.ngmodel.department = '';
                $scope.ngmodel.store = 0;
                $scope.ngmodel.goods = [];
                $scope.ngmodel.shippingAddress = {};
                $scope.ngmodel.billingAddress = {};
                $scope.ngmodel.sameAsShipping = true;
            }
            //从3 返回到 2
            else if (newValue == 2 && oldValue == 3) {
                $scope.ngmodel.goods = [];
                $scope.ngmodel.shippingAddress = {};
                $scope.ngmodel.billingAddress = {};
                $scope.ngmodel.sameAsShipping = true;
            }
        });

        //ngmodel.user
        $scope.$watch('ngmodel.user', function(newValue, oldValue) {
            $scope.ngmodel.orderType = '';
            $scope.ngmodel.department = '';
            $scope.ngmodel.store = 0;
            $scope.ngmodel.goods = [];
            $scope.g.stores = [];
            $scope.g.goods = [];
            $scope.g.addressList = [];
        });

        //ngmodel.orderType
        $scope.$watch('ngmodel.orderType', function(newValue, oldValue) {
            $scope.ngmodel.department = '';
            $scope.ngmodel.store = 0;
            $scope.ngmodel.goods = [];
            $scope.g.goods = [];
        });

        //ngmodel.store
        $scope.$watch('ngmodel.store', function(newValue, oldValue) {
            $scope.ngmodel.goods = [];
            $scope.g.goods = [];
        });

        //ngmodel.sameAsShipping
        $scope.$watch('ngmodel.sameAsShipping', function (newValue, oldValue) {
            //
        });
    };
});