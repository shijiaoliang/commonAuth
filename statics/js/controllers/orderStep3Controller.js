'use strict';
/* collect Controllers */
app.controller('orderStep3Controller', function($scope, $rootScope, $http, $state, $stateParams, $sce, $location, $timeout, $compile,
    dialog, tipDialog, serviceValid, NgMap, orderServicesBase, orderServicesOrder) {
    //事件处理
    $scope.$on('callStep3', function(event, data) {
        //处理items 是否显示 "更改价格" 按钮
        $scope.fInitEditPrice();

        //获取address列表
        $scope.fAddressList();
    });

    //======================================step3======================================
    //fInitEditPrice 处理goods里item是否可以显示更改价格按钮
    $scope.fInitEditPrice = function() {
        if ($scope.ngmodel.goods.length > 0) {
            angular.forEach($scope.ngmodel.goods, function(y, x) {
                //默认值
                y.canEdited = 0;
                y.canEdit = 0;

                //判断条件...
                if (x == 1) {
                    y.canEdit = 1;
                }
            });
        }
    };

    //fEditPriceOK
    $scope.fEditPriceBlur = function(event, item) {
        var target = event.target;
        if ($(target).val() < 0) {
            tipDialog.open({title: $scope.msg.Notice, template: $scope.msg.msg8, isOk: true, timeOut: 3000});
            return false;
        }
    };

    //fEditPrice 变更价格
    $scope.fEditPriceOK = function(event, item) {
        var target = event.target;
        var itemPriceHand = $(target).parents('td:first').find('input[name="itemPrice"]');
        var _tmpPrice = itemPriceHand.val();
        if (_tmpPrice >= 0) {
            item.price = _tmpPrice;
            tipDialog.open({title: $scope.msg.Notice, template: $scope.msg.almsg, timeOut: 1000});
        }
    };

    //fAddressList 获取地址列表
    $scope.fAddressList = function() {
        var promise = orderServicesOrder.addressList();
        promise.then(function (res) {
            if (!res.ret) {
                tipDialog.open({title: $scope.msg.Notice, template: res.errMsg, isOk: true, timeOut: 3000});
                return false;
            }

            $scope.g.addressList = res.data;
        });
    };

    //fAddAddress 添加收货地址
    $scope.fAddAddress = function() {
        var instance = dialog.open({
            backdrop:false,
            templateUrl: 'addAddressModal.html',
            size: 'md',
            controller: function ($scope) {
                $scope.addAddress = {};

                //无法改变环境就改变自己:将弹出从z-index设置为1,适应google place autocomplate
                $scope.placeFocused = function(event) {
                    var dialogHand = $(event.target).parents('div[role="dialog"]:first');
                    dialogHand.css({"z-index":1});
                };

                //google地址自动补全服务
                this.types = "['geocode']";
                $scope.placeChanged = function() {
                    $scope.place = this.getPlace();

                    if ($scope.place && $scope.place.address_components.length > 0) {
                        $scope.addressComponents = $scope.place.address_components;

                        angular.forEach($scope.addressComponents, function(y) {
                            var type = y.types[0];
                            switch (type) {
                                case 'country':
                                    $scope.addAddress.country = y.short_name;
                                    break;
                                case "administrative_area_level_1":
                                    $scope.addAddress.address = y.short_name;
                                    break;
                                case "locality":
                                    $scope.addAddress.city = y.short_name;
                                    break;
                                case "postal_code":
                                    $scope.addAddress.postCode = y.short_name;
                                    break;
                            }
                        });
                        console.log($scope.addAddress);
                    }
                };

                $scope.cancel = function() {
                    instance.close();//关闭当前弹出框
                };
                $scope.ok = function(obj) {
                    if (!obj.$invalid) {
                        console.log($scope);
                        console.log(obj);
                        console.log($scope.addAddress);

                        //todo...

                        instance.close();//关闭当前弹出框
                    }
                };
            }
        });
    };

    //funcApplyCouponCode 使用优惠卷
    $scope.funcApplyCouponCode = function() {
        //
    };

    //fSubmitOrder 提交订单
    $scope.fSubmitOrder = function() {
        //
    };
});