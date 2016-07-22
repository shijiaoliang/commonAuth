'use strict';
//路由配置
app.config(function ($stateProvider, $urlRouterProvider, $controllerProvider, $compileProvider, $filterProvider, $provide) {
    // lazy controller, directive and service
    app.controller = $controllerProvider.register;
    app.directive = $compileProvider.directive;
    app.filter = $filterProvider.register;
    app.factory = $provide.factory;
    app.service = $provide.service;
    app.constant = $provide.constant;
    app.value = $provide.value;

    //模板将被插入哪里?状态被激活时，它的模板会自动插入到父状态对应的模板中包含ui-view属性的元素内部。如果是顶层的状态，那么它的父模板就是index.html
    $urlRouterProvider.otherwise('/app/route');//规则之外的跳转

    $stateProvider
        .state('app', {
            abstract: true,
            url: '/app',
            templateUrl: 'statics/tpl/app.html'
        })
        .state('app.route', {
            url: '/route',
            templateUrl: 'statics/tpl/route.html'
        })

        //------------------------------------------------------模块级路由设置------------------------------------------------------
        //用户登录页模块
        .state('access', {
            url: '/access',
            template: '<div ui-view class="fade-in-right-big smooth"></div>'
        })
        //application
        .state('app.application', {
            url: '/application',
            template: '<div ui-view class="fade-in-right-big smooth"></div>'
        })
        //module
        .state('app.module', {
            url: '/module',
            template: '<div ui-view class="fade-in-right-big smooth"></div>'
        })
        //role
        .state('app.role', {
            url: '/role',
            template: '<div ui-view class="fade-in-right-big smooth"></div>'
        })
        //user
        .state('app.user', {
            url: '/user',
            template: '<div ui-view class="fade-in-right-big smooth"></div>'
        })
        //------------------------------------------------------页面级路由展示------------------------------------------------------
        .state('access.signin', {
            url: '/signin',
            templateUrl: 'statics/tpl/site/signin.html',
            resolve: {
                deps: ['uiLoad',
                    function (uiLoad) {
                        return uiLoad.load([
                            'statics/js/controllers/loginController.js'
                        ]);
                    }
                ]
            }
        })
        .state('app.application.list', {
            url: '/list',
            templateUrl: 'statics/tpl/application/list.html',
            resolve: {
                deps: ['uiLoad',
                    function (uiLoad) {
                        return uiLoad.load([
                            'statics/js/controllers/applicationController.js'
                        ]);
                    }
                ]
            }
        })
        .state('app.module.list', {
            url: '/list',
            templateUrl: 'statics/tpl/module/list.html',
            resolve: {
                deps: ['uiLoad',
                    function (uiLoad) {
                        return uiLoad.load([
                            'statics/js/controllers/moduleController.js'
                        ]);
                    }
                ]
            }
        })
        .state('app.role.list', {
            url: '/list',
            templateUrl: 'statics/tpl/role/list.html',
            resolve: {
                deps: ['uiLoad',
                    function (uiLoad) {
                        return uiLoad.load([
                            'statics/js/controllers/roleController.js'
                        ]);
                    }
                ]
            }
        })
        .state('app.user.list', {
            url: '/list',
            templateUrl: 'statics/tpl/user/list.html',
            resolve: {
                deps: ['uiLoad',
                    function (uiLoad) {
                        return uiLoad.load([
                            'statics/js/controllers/userController.js'
                        ]);
                    }
                ]
            }
        })
    }
);
