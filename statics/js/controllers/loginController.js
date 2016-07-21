'use strict';
angular.module('app.controllers.loginController', [])
    //用户登录 controller
    .controller('loginController', function ($scope, $rootScope, $http, $state) {
        $scope.user = {};
        $scope.authError = null;

        //检测用户是否登录
        $http.get('/index.php?r=site/ajaxCheckLogin').success(function (d) {
            if (d.ret == '1') {
                $rootScope.isLogin = true;
                $state.go('app.knowledge.myStoreKnowledge');
            }
        }).error(function (x) {
            $scope.authError = '';
        });

        //验证码
        var captchUrl = '/index.php?r=site/ajaxVeryfy';
        $scope.captchUrl = captchUrl;
        $scope.changeCaptch = function () {
            $scope.captchUrl = captchUrl + '&num=' + Math.random();
        };

        //load
        $scope.load = function () {//切换大小写提示
            function isIE() {
                if (!!window.ActiveXObject || "ActiveXObject" in window) {
                    return true;
                }
                else {
                    return false;
                }
            }

            (function () {
                var inputPWD = document.getElementById('loginPasswd');
                var capital = false;
                var capitalTip = {
                    elem: document.getElementById('capital'),
                    toggle: function (s) {
                        var sy = this.elem.style;
                        var d = sy.display;
                        if (s) {
                            sy.display = s;
                        }
                        else {
                            sy.display = d == 'none' ? '' : 'none';
                        }
                    }
                };
                var detectCapsLock = function (event) {
                    if (capital) {
                        return
                    }
                    var e = event || window.event;
                    var keyCode = e.keyCode || e.which;
                    var isShift = e.shiftKey || (keyCode == 16) || false;
                    if (((keyCode >= 65 && keyCode <= 90) && !isShift) || ((keyCode >= 97 && keyCode <= 122) && isShift)) {
                        capitalTip.toggle('block');
                        capital = true
                    }
                    else {
                        capitalTip.toggle('none');
                    }
                };
                if (!isIE()) {
                    inputPWD.onkeypress = detectCapsLock;
                    inputPWD.onkeyup = function (event) {
                        var e = event || window.event;
                        if (e.keyCode == 20 && capital) {
                            capitalTip.toggle();
                            return false;
                        }
                    }
                }
            })()
        };

        //用户登录
        $scope.login = function () {
            $scope.authError = null;
            // Try to login
            $http.post('/index.php?r=site/ajaxLogin', $scope.user).success(function (d) {
                if (d.ret == '1') {
                    $rootScope.isLogin = true;
                    $state.go('app.knowledge.myStoreKnowledge');
                } else {
                    // 刷新验证码
                    $scope.captchUrl = captchUrl + '&num=' + Math.random();
                    $scope.authError = d.errMsg;
                }
            }).error(function (x) {
                // 刷新验证码
                $scope.captchUrl = captchUrl + '&num=' + Math.random();
                $scope.authError = 'Server Error';
            });
        };
    })
;