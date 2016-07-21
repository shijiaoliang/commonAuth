<?php

class SiteController extends BaseController {
    private $_cnMenu = array(
        100 => array(
            array(
                'itemname' => '知识库管理',
                'icon' => 'glyphicon glyphicon-th',
                'list' => array(
                    101 => array(
                        'name' => '知识库分类管理',
                        'url' => 'app.knowledge.addstoreCategoryList'
                    ),
                )
            )
        )
    );

    private $_enMenu = array(
        100 => array(
            array(
                'itemname' => 'Knowledge Mgt',
                'icon' => 'glyphicon glyphicon-th',
                'list' => array(
                    101 => array(
                        'name' => 'Category List',
                        'url' => 'app.knowledge.addstoreCategoryList'
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
        $arrMenu = $this->getPrivilegeMenu($language);

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
    public function getPrivilegeLeftMenu($menuId, array $_lmenu) {
        $priCode = PrivilegeCode::$arrMenuPri;
        $__lmenu = $_lmenu;
        foreach ($_lmenu as $i => $item) {
            foreach ($item['list'] as $k => $m) {
                //如果没有权限设置，直接跳过
                if (!isset($priCode[$menuId]) || !isset($priCode[$menuId][$k])) {
                    continue;
                }

                if (!$this->checkPower($priCode[$menuId][$k], false)) {
                    unset($__lmenu[$i]['list'][$k]);
                }
            }

            if (count($__lmenu[$i]['list']) <= 0) {
                unset($__lmenu[$i]);
            }
        }

        return $__lmenu;
    }

    /*=============login============*/
    /**
     * 后台登陆页
     */
    public function actionAjaxLogin() {
        //验证码验证
        $flag = factoryCode::validate(trim($_REQUEST['verifyCode']), true);
        if (!$flag) {
            $this->retJSON(OpResponse::RET_ERROR, null, 'Captcha incorrect');
        }

        $model = new LoginForm();
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
                Yii::app()->session['userId'] = $model->userId;
                $key = 'LoadUserIdpassword_' . $model->userName;
                if ($model->password == '000000') {
                    Yii::app()->redisDB->set($key, 1);
                } else {
                    Yii::app()->redisDB->set($key, 0);
                }
                $this->retJSON(OnePlusServiceResponse::RET_SUCCESS, array(), '');
            } else {
                $this->retJSON(OnePlusException::PARAM_ERROR, null, 'User Name or Password incorrect');
            }
        } else {
            $this->redirect(Yii::app()->request->hostInfo . '/#/access/signin');
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
    public function actionAjaxLoginout() {
        $userInfo = $this->checkCookies();
        if (!empty($userInfo)) {
            $dataList = array();
            $dataList ['power_userId'] = '';
            $dataList ['power_token'] = '';
            $this->setCookiesUserInfo($dataList, 0);
        }
        $this->retJSON(OnePlusServiceResponse::RET_SUCCESS, array(), '');
    }

    /**
     * 生成验证码
     */
    public function actionAjaxVeryfy() {
        $captcha = factoryCode::createObj(1);
        $captcha->doimg();
        Yii::app()->end();
    }

    /*==============Error============*/
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
}