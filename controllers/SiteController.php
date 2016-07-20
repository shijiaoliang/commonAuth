<?php
/**
 * 后台主框架页面
 */
class SiteController extends Controller {
    private $_cnMenu = array(
        100 => array(
            //知识库<i class="glyphicon glyphicon-th"></i>
            array(
                'itemname' => '知识库管理',
                'icon' => 'glyphicon glyphicon-th',
                'list' => array(
                    101 => array(
                        'name' => '知识库分类管理',
                        'url' => 'app.knowledge.addstoreCategoryList'
                    ),
                    102 => array(
                        'name' => '知识库文章新增',
                        'url' => 'app.knowledge.addStoreKnowledge'
                    ),
                    103 => array(
                        'name' => '知识库文章列表',
                        'url' => 'app.knowledge.listStoreKnowledge'
                    ),
                    104 => array(
                        'name' => '文章评论管理',
                        'url' => 'app.knowledge.listKnowledgeAppraise'
                    ),
                    105 => array(
                        'name' => '我的知识库',
                        'url' => 'app.knowledge.myStoreKnowledge'
                    ),
                    106 => array(
                        'name' => '新版知识库',
                        'url' => 'app.knowledge.myStoreKnowledgeNew'
                    ),
                )
            )
        ),
        //网点
        400 => array(
            array(
                'itemname' => '海外网点管理',
                'icon' => ' icon-screen-desktop',
                'list' => array(
                    401 => array(
                        'name' => 'RMA单据管理',
                        'url' => 'app.site.rmaList'
                    ),
                    402 => array(
                        'name' => '备件申请单管理',
                        'url' => 'app.replacementPart.replacementPartList'
                    ),
                    403 => array(
                        'name' => '保证金查询',
                        'url' => 'app.replacementPart.fundFlow'
                    ),
                    //保证金流水列表
                    404 => array(
                        'name' => 'RMA物流管理',
                        'url' => 'app.logistics.rmaLogisticsList'
                    ),
                )
            )
        ),
        //物流
        500 => array(
            array(
                'itemname' => '物流管理',
                'icon' => 'icon-social-twitter',
                'list' => array(
                    501 => array(
                        'name' => '物流查询',
                        'url' => 'app.logistics.query'
                    ),
                    502 => array(
                        'name' => '运单列表',
                        'url' => 'app.logistics.list'
                    ),
                )
            )
        ),
        //海外订单
        700 => array(
            array(
                'itemname' => '海外订单管理',
                'icon' => 'icon-book-open',
                'list' => array(
                    701 => array(
                        'name' => '订单查询',
                        'url' => 'app.overSeaOrders.orderQuery'
                    ),
                    702 => array(
                        'name' => 'Mainboard IMEI',
                        'url' => 'app.mainboard.imeiQuery'
                    ),
                    703 => array(
                        'name' => '创建订单',
                        'url' => 'app.overSeaOrders.add'
                    ),
                )
            )
        ),
        //Zendesk
        600 => array(
            array(
                'itemname' => 'Zendesk管理',
                'icon' => 'icon-social-tumblr',
                'list' => array(
                    601 => array(
                        'name' => 'Ticket Query',
                        'url' => 'app.ticket.ticketQuery'
                    ),
                    602 => array(
                        'name' => 'Ticket List',
                        'url' => 'app.ticket.ticketList'
                    ),
                )
            )
        ),
        //线下保险
        300 => array(
            array(
                'itemname' => '保险管理',
                'icon' => 'glyphicon glyphicon-th',
                'list' => array(
                    301 => array(
                        'name' => '保险新增',
                        'url' => 'app.insurance.insuranceAdd'
                    ),
                    302 => array(
                        'name' => '保险查询',
                        'url' => 'app.insurance.insuranceList'
                    ),
                    303 => array(
                        'name' => '全球保险查询',
                        'url' => 'app.insurance.allInsuranceList'
                    ),
                )
            )
        ),
        //邀请码
        200 => array(
            array(
                'itemname' => '邀请码管理',
                'icon' => 'icon-credit-card',
                'list' => array(
                    201 => array(
                        'name' => '邀请码列表查询',
                        'url' => 'app.invite.invitetsList'
                    ),
                    202 => array(
                        'name' => '邀请码详情查询',
                        'url' => 'app.invite.invitetsQuery'
                    ),
                )
            )
        ),
    );

    private $_enMenu = array(
        100 => array(
            //知识库
            array(
                'itemname' => 'Knowledge Mgt',
                'icon' => 'glyphicon glyphicon-th',
                'list' => array(
                    101 => array(
                        'name' => 'Category List',
                        'url' => 'app.knowledge.addstoreCategoryList'
                    ),
                    102 => array(
                        'name' => 'Knowledge Add',
                        'url' => 'app.knowledge.addStoreKnowledge'
                    ),
                    103 => array(
                        'name' => 'Knowledge List',
                        'url' => 'app.knowledge.listStoreKnowledge'
                    ),
                    104 => array(
                        'name' => 'Appraise List',
                        'url' => 'app.knowledge.listKnowledgeAppraise'
                    ),
                    105 => array(
                        'name' => 'My Knowledge',
                        'url' => 'app.knowledge.myStoreKnowledge'
                    ),
                    106 => array(
                        'name' => 'My Knowledge New',
                        'url' => 'app.knowledge.myStoreKnowledgeNew'
                    ),
                )
            )
        ),
        //网点
        400 => array(
            array(
                'itemname' => 'Site Mgt',
                'icon' => ' icon-screen-desktop',
                'list' => array(
                    401 => array(
                        'name' => 'RMA List',
                        'url' => 'app.site.rmaList'
                    ),
                    402 => array(
                        'name' => '备件申请单管理',
                        'url' => 'app.replacementPart.replacementPartList'
                    ),
                    403 => array(
                        'name' => 'Fund Flow',
                        'url' => 'app.replacementPart.fundFlow'
                    ),
                    //保证金流水列表
                    404 => array(
                        'name' => 'RMA Logistic List',
                        'url' => 'app.logistics.rmaLogisticsList'
                    ),
                )
            )
        ),
        //物流
        500 => array(
            array(
                'itemname' => 'Logistics Mgt',
                'icon' => 'icon-social-twitter',
                'list' => array(
                    501 => array(
                        'name' => 'Logistics Query',
                        'url' => 'app.logistics.query'
                    ),
                    502 => array(
                        'name' => 'Logistics List',
                        'url' => 'app.logistics.list'
                    ),
                )
            )
        ),
        //海外订单
        700 => array(
            array(
                'itemname' => 'Order Mgt',
                'icon' => 'icon-book-open',
                'list' => array(
                    701 => array(
                        'name' => 'Order Query',
                        'url' => 'app.overSeaOrders.orderQuery'
                    ),
                    702 => array(
                        'name' => 'Mainboard IMEI',
                        'url' => 'app.mainboard.imeiQuery'
                    ),
                    703 => array(
                        'name' => 'Create Order',
                        'url' => 'app.overSeaOrders.add'
                    ),
                )
            )
        ),
        //Zendesk
        600 => array(
            array(
                'itemname' => 'Zendesk Mgt',
                'icon' => 'icon-social-tumblr',
                'list' => array(
                    601 => array(
                        'name' => 'Ticket Query',
                        'url' => 'app.ticket.ticketQuery'
                    ),
                    602 => array(
                        'name' => 'Ticket List',
                        'url' => 'app.ticket.ticketList'
                    ),
                )
            )
        ),
        //线下保险
        300 => array(
            array(
                'itemname' => 'Insurance Mgt',
                'icon' => 'glyphicon glyphicon-th',
                'list' => array(
                    301 => array(
                        'name' => 'Insurance Add',
                        'url' => 'app.insurance.insuranceAdd'
                    ),
                    302 => array(
                        'name' => 'Insurance List',
                        'url' => 'app.insurance.insuranceList'
                    ),
                    303 => array(
                        'name' => 'G-Insurance List',
                        'url' => 'app.insurance.allInsuranceList'
                    ),
                )
            )
        ),
        //邀请码
        200 => array(
            array(
                'itemname' => 'Invites Mgt',
                'icon' => 'icon-credit-card',
                'list' => array(
                    201 => array(
                        'name' => 'Invites List',
                        'url' => 'app.invite.invitetsList'
                    ),
                    202 => array(
                        'name' => 'Invites Query',
                        'url' => 'app.invite.invitetsQuery'
                    ),
                )
            )
        ),
    );

    /**
     * 后台左边导航菜单
     */
    public function actionAjaxLeftMenu() {
        $cookie = Yii::app()->request->getCookies();
        if (isset($cookie['langKey'])) {
            $langKey = $cookie['langKey']->value;
        } else {
            $langKey = 'cn';
        }
        $language = $langKey;
        $arrMenu = $this->getPrivilegeMenu($language);//$this->_menu;
        $adminUser = $this->adminUser;
        $retunMenu = array();
        if (is_array($arrMenu) && count($arrMenu) > 0) {
            foreach ($arrMenu as $key => $value) {
                $v = $value[0];
                $v['count'] = count($v['list']);
                $v['empNameZh'] = $adminUser;
                $retunMenu[] = $v;
            }
        }
        $this->retJSON(1, $retunMenu);
    }

    /**
     * 获取有权限的菜单
     * @author ellan
     */
    public function getPrivilegeMenu($language) {
        switch ($language) {
            case 'cn'://中文
                $menu = $this->_cnMenu;
                break;
            case 'en'://英文
                $menu = $this->_enMenu;
                break;
            default:
                $menu = $this->_cnMenu;
                break;
        }
        foreach ($menu as $_key => $_lmenu) {
            $tmp_menu = $this->getPrivilegeLeftMenu($_key, $_lmenu);//查询子菜单权限
            if (count($tmp_menu) <= 0) {
                unset($menu[$_key]);
                //unset($menu['top'][$_key]);
                continue;
            }

            $menu[$_key] = $tmp_menu;
        }

        return $menu;
    }

    /**
     * 获取子菜单有权限的菜单
     * @author ellan
     */
    public function getPrivilegeLeftMenu($menuId, array $_lmenu) {//PrivilegeCode::$arrMenuPri
        $priCode = PrivilegeCode::$arrMenuPri;
        $__lmenu = $_lmenu;
        foreach ($_lmenu as $i => $item) {
            foreach ($item['list'] as $k => $m) {
                //如果没有权限设置，直接跳过
                if (!isset($priCode[$menuId]) || !isset($priCode[$menuId][$k])) {
                    continue;
                }

                if (!$this->checkPower($priCode[$menuId][$k], false)) {//没有子菜单对应的权限则删除menu
                    unset($__lmenu[$i]['list'][$k]);
                }
            }

            if (count($__lmenu[$i]['list']) <= 0) {
                unset($__lmenu[$i]);
            }
        }

        return $__lmenu;
    }

    /**
     * 后台登陆后主页
     */
    public function actionMain() {
        $this->render('main');
    }

    /**
     * 后台登陆页
     */
    public function actionAjaxLogin() {
        //验证码验证
        $flag = factoryCode::validate(trim($_REQUEST['verifyCode']), true);
        if (!$flag) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'Captcha incorrect');
        }
        $model = new LoginForm;
        $LoginForm = array(
            'userName' => $_REQUEST['userName'],
            'password' => $_REQUEST['password']
        );
        $allErrorHTML = '';
        $HTTP_REFERER = isset($_SERVER ['HTTP_REFERER']) ? $_SERVER ['HTTP_REFERER'] : '';

        if (!empty($LoginForm)) {
            $model->attributes = $LoginForm;
            $dataList = array(
                'userName' => $model->userName,
                'userPwd' => $model->password
            );
            if ($this->loadLogin($dataList)) {
                Yii::app()->session['userName'] = $model->userName;
                //Yii::app()->session['password'] = $model->password;
                Yii::app()->session['userId'] = $model->userId;
                $key = 'LoadUserIdpassword_' . $model->userName;
                if ($model->password == '000000') {
                    Yii::app()->redisDB->set($key, 1);
                } else {
                    Yii::app()->redisDB->set($key, 0);
                }
                $this->retJSON(OnePlusServiceResponse::RET_SUCCESS, array(), '');
                //                //判断用户选择的应用参数
                //                if ($model->application) {
                //                    $this->onLoadToUrl($model->application);
                //                }
                //                if (empty($model->tarGet)) {
                //                    //如果未设置登录跳转，直接跳转上一地址
                //                    $this->redirect(Yii::app()->user->returnUrl);
                //                } else {
                //                    $this->onLoadToUrl($model->tarGet);
                //                }
            } else {
                $this->retJSON(OnePlusException::PARAM_ERROR, null, 'User Name or Password incorrect');
            }
        } else {
            $this->redirect(Yii::app()->request->hostInfo . '/#/access/signin');
        }
        //        $appList = array();
        //        $ateurl = "http://" . $_SERVER['HTTP_HOST'];
        //        $isget = strstr($HTTP_REFERER, $ateurl);
        //        if ((empty($model->tarGet) && !empty($isget)) || empty($HTTP_REFERER)) {//如果请求没有来源，则可勾选应用
        //            $result = $this->service->privilege->querySysAppList();
        //            if ($result ['ret'] == SUCCESS) {
        //                $appdate = $result ['data'];
        //                $appdate = array_reverse($appdate);
        //                foreach ($appdate as $appOne) {
        //                    if (!empty($appOne['applicationCallbackUrl']) && $appOne['applicationCallbackUrl'] != '') {
        //                        $appList[$appOne['applicationCallbackUrl']] = $appOne['applicationName'];
        //                    }
        //                }
        //            }
        //        }
        //        $this->render('login', array('model' => $model, 'appList' => $appList, 'allErrorHTML' => $allErrorHTML));
    }

    /**
     * 注销登陆
     */
    public function actionAjaxLoginout() {
        $this->logout();
    }

    public function actionAjaxCheckoutRedis() {
        $key = 'LoadUserIdpassword_' . $this->adminJobNum;
        $checkload = Yii::app()->redisDB->get($key);
        if ($checkload == 1) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'User Name or Password incorrect');
        }
    }

    /**
     * 错误提示页
     */
    public function actionError() {
        $error = Yii::app()->errorHandler->error;
        if ($error) {
            if (Yii::app()->request->isAjaxRequest) echo $error['message']; else
                $this->render('error', $error);
        }
    }
    /**
     * 检测是否登录
     */
    public function actionAjaxCheckLogin() {
        $userInfo = $this->checkValidate();

        if ($userInfo) {
            $this->retJSON(OnePlusServiceResponse::RET_SUCCESS, array(), '');
        } else {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, '');
        }
    }

    /**
     * 注销登陆
     */
    public function logout() {
        $userInfo = $this->checkCookies();
        if (!empty($userInfo)) {
            $dataList = array();
            $dataList ['power_userId'] = '';
            $dataList ['power_token'] = '';
            $this->setCookiesUserInfo($dataList, 0);
            $result = Yii::app()->service->privilege->logout($userInfo);
            //var_dump($result);exit;
        }
        $this->retJSON(OnePlusServiceResponse::RET_SUCCESS, array(), '');
        //Yii::app()->user->logout();
        //$this->redirect(Yii::app()->request->hostInfo.'/#/access/signin');
    }

    /**
     * 生成验证码
     */
    public function actionAjaxVeryfy() {
        $captcha = factoryCode::createObj(1);
        $captcha->doimg();
        Yii::app()->end();
    }

    /**
     * 修改用户信息
     */
    public function actionUpdateUserMsg() {
        $arrSearch = array();
        if (!isset($_REQUEST['params']['oldPssword']) || !isset($_REQUEST['params']['newPssword'])) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, '必要参数未输入！');
        }
        if (empty($_REQUEST['params']['newPssword'])) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, '新密码不能为空！');
        }
        if (empty($this->adminUserId)) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, '必要参数错误！');
        }
        $arrSearch ['newPwd'] = $_REQUEST['params']['newPssword'];
        $arrSearch ['oldPwd'] = $_REQUEST['params']['oldPssword'];
        $arrSearch ['empId'] = $this->adminUserId;
        $result = $this->service->privilege->updatePwd($arrSearch);
        //输出
        if ($result['ret'] == '1') {
            $key = 'LoadUserIdpassword_' . $this->adminJobNum;
            Yii::app()->redisDB->set($key, 0);
            $this->retJSON(OnePlusServiceResponse::RET_SUCCESS, null);
        } else {
            $this->retJSON($result['errCode'], '', $result['errMsg']);
        }
    }
}