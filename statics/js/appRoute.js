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
        //知识库模块
        .state('app.knowledge', {
            url: '/knowledge',//如果你使用绝对 url 匹配的方式，那么你需要给你的url字符串加上特殊符号"^"
            template: '<div ui-view class="fade-in-up"></div>'
        })
        //用户登录页模块
        .state('access', {
            url: '/access',
            template: '<div ui-view class="fade-in-right-big smooth"></div>'
        })

        //------------------------------------------------------页面级路由展示------------------------------------------------------
        .state('access.signin', {
            url: '/signin',
            templateUrl: 'statics/tpl/knowledge/signin.html'
        })
        .state('access.signup', {
            url: '/signup',
            templateUrl: 'statics/tpl/knowledge/signin.html'
        })
        .state('access.forgotpwd', {
            url: '/forgotpwd',
            templateUrl: 'statics/tpl/page_forgotpwd.html'
        })
        .state('access.404', {
            url: '/404',
            templateUrl: 'statics/tpl/page_404.html'
        })

        //修改用户密码
        .state('app.knowledge.changeUserMsgKnowledge', {
            url: '/changeUserMsgKnowledge',
            templateUrl: 'statics/tpl/knowledge/storeKnowledge_changeUserMsg.html'
        })

        //物流管理 查询
        .state('app.logistics.query', {
            url: '/query?slug&tracking_number',
            templateUrl: 'statics/tpl/logistics/logistics_query.html',
            resolve: {
                deps: ['uiLoad',
                    function (uiLoad) {
                        return uiLoad.load([
                            'https://maps.googleapis.com/maps/api/js?key=AIzaSyCWr2dCA34EdhN0VvautHfk70nlN0rOLXQ',
                            'statics/js/controllers/logisticsController.js'
                        ]);
                    }
                ]
            }
        })
    }
);
