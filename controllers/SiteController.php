<?php
class SiteController extends BaseController {
    private $_cnMenu = array();

    //不校验登录
    public $load_list = array(
        'site/ajaxLogin',
        'site/ajaxCheckLogin',
        'site/ajaxVeryfy',
    );

    public function init() {
        parent::init();
        $this->_cnMenu = Yii::app()->params['menu'];
    }

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

        $this->retJSON(OpResponse::RET_SUCCESS, $arrMenu);
    }

    /**
     * 获取有权限的菜单
     * @author ellan
     */
    protected function getPrivilegeMenu($language) {
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
        //foreach ($menu as $_key => $_lmenu) {
        //    $tmp_menu = $this->getPrivilegeLeftMenu($_key, $_lmenu);//查询子菜单权限
        //    if (count($tmp_menu) <= 0) {
        //        unset($menu[$_key]);
        //        continue;
        //    }
        //
        //    $menu[$_key] = $tmp_menu;
        //}

        return $menu;
    }

    /**
     * 获取子菜单有权限的菜单
     * @author ellan
     */
    protected function getPrivilegeLeftMenu($menuId, array $_lmenu) {
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

        $model = new UserAR();
        $params = array(
            'user_no' => $_REQUEST['userName'],
            'user_pwd' => $_REQUEST['password']
        );
        if (!empty($params)) {
            if ($res = $model->login($params)) {
                Yii::app()->session['userId'] = $res->user_id;
                Yii::app()->session['userInfo'] = $res->attributes;

                $data = array(
                    'userId' => $_SESSION['userId'],
                    'userName' => $_SESSION['userInfo']['user_name']
                );
                $this->retJSON(OpResponse::RET_SUCCESS, $data, 'ok');
            } else {
                $this->retJSON(OpResponse::RET_ERROR, null, 'User Name or Password incorrect');
            }
        } else {
            $this->redirect($this->hostUrl . '/#/access/signin');
        }
    }

    /**
     * 检测是否登录
     */
    public function actionAjaxCheckLogin() {
        $userInfo = $this->checkValidate();

        if ($userInfo) {
            $data = array(
                'userId' => $_SESSION['userId'],
                'userName' => $_SESSION['userInfo']['user_name']
            );
            $this->retJSON(OpResponse::RET_SUCCESS, $data, '');
        } else {
            $this->retJSON(OpResponse::RET_ERROR, null, '');
        }
    }

    /**
     * 注销登陆
     */
    public function actionAjaxLoginout() {
        session_destroy();
        $this->retJSON(OpResponse::RET_SUCCESS, array(), '');
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