'use strict';
/* collect Controllers */
app.controller('orderStep2Controller', function($scope, $rootScope, $http, $state, $stateParams, $sce, $location, $timeout, $compile,
    dialog, tipDialog, serviceValid, NgMap, orderServicesBase, orderServicesOrder) {
    //事件处理
    $scope.$on('callStep2', function(event, data) {
        //获取orderTypes
        $scope.fOrderTypes();

        //获取store列表
        $scope.fStoreList();
    });

    //======================================step2======================================
    //fOrderTypes
    $scope.fOrderTypes = function() {
        $scope.g.orderTypes = orderServicesBase.orderTypes;
    };

    //fStoreList 获取商店列表
    $scope.fStoreList = function() {
        var promise = orderServicesOrder.storeList();
        promise.then(function (res) {
            if (!res.ret) {
                tipDialog.open({title: $scope.msg.Notice, template: $scope.msg.msg5, isOk: true, timeOut: 3000});
                return false;
            }

            $scope.g.stores = res.data;
        });
    };

    //fChangeStore 更改store
    $scope.fChangeStore = function() {
        if ($scope.ngmodel.store > 0) {
            var promise = orderServicesOrder.goodsList($scope.ngmodel.store);
            promise.then(function (res) {
                if (!res.ret) {
                    tipDialog.open({title: $scope.msg.Notice, template: 'There is no goods!', isOk: true, timeOut: 3000});
                    return false;
                }

                $scope.g.goods = res.data;

                //初始化每个goods的quantity
                if ($scope.g.goods.length > 0) {
                    angular.forEach($scope.g.goods, function(y, x) {
                        y.quantity = 1;
                    });
                }
            });
        }
    };

    //fChooseGoods 选中商品
    $scope.fChooseGoods = function (event, item) {
        var $_this = $(event.target);
        var isChecked = $_this.prop('checked');

        if (isChecked) {
            //校验quantity
            if (item.quantity <= 0) {
                tipDialog.open({title: $scope.msg.Notice, template: $scope.msg.msg6, isOk: true, timeOut: 3000});
                $_this.prop('checked', false);
                return false;
            }
            item.quantity = parseInt(item.quantity);

            $scope.ngmodel.goods.push(item);
            $scope.g.goods = orderServicesBase.stick($scope.g.goods, item.id, 'id', 1);
        } else {
            //删除一个item
            $scope._delOneItem(item);
        }
    };

    //fQuantityBlur
    $scope.fQuantityBlur = function(event, item) {
        var $_this = $(event.target);
        var $tr = $_this.parents("tr:first");
        var $checkItem = $tr.find('input[name="checkItem"]');
        var isChecked = $checkItem.prop('checked');

        if (item.quantity <= 0) {
            tipDialog.open({title: $scope.msg.Notice, template: $scope.msg.msg6, isOk: true, timeOut: 3000});
            if (isChecked) {
                $checkItem.prop('checked', false);

                //删除一个item
                $scope._delOneItem(item);
            }

            return false;
        }
    };

    //私有方法, 删除一个item
    $scope._delOneItem = function(item) {
        angular.forEach($scope.ngmodel.goods, function(y, x) {
            if (y.id == item.id) {
                //删除数组中这一项
                $scope.ngmodel.goods.splice(x, 1);
            }
        });

        $scope.g.goods = orderServicesBase.stick($scope.g.goods, item.id, 'id', 0);
    };
});