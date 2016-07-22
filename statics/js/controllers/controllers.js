'use strict';
angular.module('app.controllers', ['pascalprecht.translate', 'ngCookies'])
    .controller('AppCtrl', ['$scope', '$http', '$rootScope', '$translate', '$localStorage', '$window',
        function ($scope, $http, $rootScope, $translate, $localStorage, $window) {
            var isIE = !!navigator.userAgent.match(/MSIE/i);
            isIE && angular.element($window.document.body).addClass('ie');
            isSmartDevice($window) && angular.element($window.document.body).addClass('smart');

            $scope.app = {
                name: 'CommonAuth',
                version: '1.1.3',

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
                //en: 'English'
            };
            var arrCookie = document.cookie.split("; ");
            var langKey;
            for (var i = 0; i < arrCookie.length; i++) {
                var arr = arrCookie[i].split("=");
                if ("langKey" == arr[0]) {
                    langKey = arr[1];
                    break;
                }
            }
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
                document.cookie = "langKey=" + langKey;
                window.location.reload();
            };

            function isSmartDevice($window) {
                var ua = $window['navigator']['userAgent'] || $window['navigator']['vendor'] || $window['opera'];
                return (/iPhone|iPod|iPad|Silk|Android|BlackBerry|Opera Mini|IEMobile/).test(ua);
            }
        }
    ])

    //RouteController
    .controller('RouteController', function ($scope, $http, $state) {
        $scope.load = function () {
            $http.get('/index.php?r=site/ajaxLeftMenu').success(function (data) {
                if (data.data.length > 0) {
                    var urlOtherwise = '/app/application/list';
                    for (var i in data.data[0]['list']) {
                        urlOtherwise = data.data[0]['list'][i]['url'];
                        break;
                    }
                    $state.go(urlOtherwise);
                }
            });
        }
    })

    //headerController
    .controller('headerController', function ($scope, $http, $rootScope, LogoutServer) {
        $scope.logout = function () {
            LogoutServer.logout();
        };
    })

    //NavController
    .controller('NavController', function ($scope, $http, $rootScope) {
        $scope.load = function() {
            //current state
            $scope.currentName = $rootScope.$state.current.name;

            $http.get('/index.php?r=site/ajaxLeftMenu').success(function (data) {
                console.log(data);
                $scope.siteMenuData = data.data;
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
;