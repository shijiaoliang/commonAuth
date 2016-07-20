'use strict';
/* Controllers */
angular.module('app.controllers', ['pascalprecht.translate', 'ngCookies'])
    .controller('AppCtrl', ['$scope', '$http', '$rootScope', '$translate', '$localStorage', '$window', 'tipDialog',
        function ($scope, $http, $rootScope, $translate, $localStorage, $window, tipDialog) {
            // add 'ie' classes to html
            var isIE = !!navigator.userAgent.match(/MSIE/i);
            isIE && angular.element($window.document.body).addClass('ie');
            isSmartDevice($window) && angular.element($window.document.body).addClass('smart');

            // config
            $scope.app = {
                name: 'OnePlus Knowledge',
                version: '1.1.3',

                // for chart colors
                color: {
                    primary: '#7266ba',
                    info: '#23b7e5',
                    success: '#27c24c',
                    warning: '#fad733',
                    danger: '#f05050',
                    light: '#e8eff0',
                    dark: '#3a3f51',
                    black: '#1c2b36'
                },
                settings: {
                    themeID: 1,
                    navbarHeaderColor: 'bg-black',
                    navbarCollapseColor: 'bg-white-only',
                    asideColor: 'bg-black',
                    headerFixed: true,
                    asideFixed: false,
                    asideFolded: false
                }
            };

            // save settings to local storage
            if (angular.isDefined($localStorage.settings)) {
                $scope.app.settings = $localStorage.settings;
            } else {
                $localStorage.settings = $scope.app.settings;
            }
            $scope.$watch('app.settings', function () {
                $localStorage.settings = $scope.app.settings;
            }, true);

            //angular translate
            $scope.lang = {
                isopen: false
            };
            $scope.langs = {
                cn: 'Chinese',
                en: 'English'
            };
            var arrCookie = document.cookie.split("; ");
            var langKey;
            //遍历cookie数组，处理每个cookie对
            for (var i = 0; i < arrCookie.length; i++) {
                var arr = arrCookie[i].split("=");
                //找到名称为userId的cookie，并返回它的值
                if ("langKey" == arr[0]) {
                    langKey = arr[1];
                    break;
                }
            }
            //console.info(langKey);
            if (langKey == 'en') {
                $scope.selectLang = "English";
                $scope.Lang = "en";
                langKey = 'en';
            }
            else {
                $scope.selectLang = "Chinese";
                $scope.Lang = "zh-cn";
                langKey = 'cn';
            }
            $translate.use(langKey);
            $scope.setLang = function (langKey, $event) {
                // set the current lang
                document.cookie = "langKey=" + langKey;
                window.location.reload();
            };

            function isSmartDevice($window) {
                // Adapted from http://www.detectmobilebrowsers.com
                var ua = $window['navigator']['userAgent'] || $window['navigator']['vendor'] || $window['opera'];
                // Checks for iOs, Android, Blackberry, Opera Mini, and Windows mobile devices
                return (/iPhone|iPod|iPad|Silk|Android|BlackBerry|Opera Mini|IEMobile/).test(ua);
            }
        }
    ])

    .controller('AccordionDemoCtrl', ['$scope', function ($scope) {
        $scope.oneAtATime = true;

        $scope.groups = [{
            title: 'Accordion group header - #1',
            content: 'Dynamic group body - #1'
        }, {
            title: 'Accordion group header - #2',
            content: 'Dynamic group body - #2'
        }];

        $scope.items = ['Item 1', 'Item 2', 'Item 3'];

        $scope.addItem = function () {
            var newItemNo = $scope.items.length + 1;
            $scope.items.push('Item ' + newItemNo);
        };

        $scope.status = {
            isFirstOpen: true,
            isFirstDisabled: false
        };
    }])

    //菜单权限列表及登录用户信息 controller
    .controller('IndexController', function ($scope, $http, $rootScope) {
        $scope.load = function() {
            //current state
            $scope.currentName = $rootScope.$state.current.name;

            $http.get('/index.php?r=site/ajaxLeftMenu').success(function (data) {
                $scope.siteMenuData = data.data;

                $rootScope.user = {};
                if (data.data && data.data[0]) {
                    $rootScope.user.empNameZh = data.data[0].empNameZh;
                }
            });
        };

        $scope.checkIsActive = function(menuList) {
            var urlArr = [];
            angular.forEach(menuList, function(y) {
                urlArr.push(y.url);
            });

            if (-1 != $.inArray($scope.currentName, urlArr)) {
                return true;
            }

            return false;
        };
    })

    //菜单权限列表及登录用户信息 controller
    .controller('RouteController', function ($scope, $http, $state) {
        $scope.load = function () {
            $http.get('/index.php?r=site/ajaxLeftMenu').success(function (data) {
                if (data.data.length > 0) {
                    var urlOtherwise = '/app/knowledge/myStoreKnowledge';
                    for (var i in data.data[0]['list']) {
                        urlOtherwise = data.data[0]['list'][i]['url'];
                        break;
                    }
                    $state.go(urlOtherwise);
                }
            });
        }
    })
