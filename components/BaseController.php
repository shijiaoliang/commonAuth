<?php
class BaseController extends CController {
    public $layout = 'main';

    /**
     * 登录例外设置
     */
    public $load_list = array();

    /**
     * 权限code集合
     */
    public $powerCodeList;

    /**
     * 当前登录用户名
     */
    public $adminUser;

    /**
     * 当前登录用户ID
     */
    public $adminUserId;

    /**
     * 存放用户数组
     */
    public $adminUserInfo;

    public function init() {
        $this->checkLoginStatus();
    }


    /**
     * 验证用户是否有登录
     * 验证不通过 将会跳转到登陆页面
     */
    protected function checkLoginStatus() {
        // 无需验证页面 只控制到action处
        $r_url = Yii::app()->request->getParam('r');
        $path_info = explode('/', $r_url);
        if (!isset ($path_info [0])) {
            $path_info [0] = 'site';
        }
        if (!isset ($path_info [1])) {
            $path_info [1] = 'index';
        }
        $path_name = sprintf('%s/%s', $path_info [0], $path_info [1], '');
        $load_list = $this->load_list;

        // 验证用户是否登录
        if (!in_array($path_name, $load_list)) {
            //$userInfo = $this->checkValidate();
            //if (!$userInfo) {
            //    if (strstr($_SERVER["QUERY_STRING"], 'angularjs=true')) {
            //        $data = array('signinUrl' => Yii::app()->request->hostInfo . '/#/access/signin');
            //        $this->retJSON(OpResponse::RET_ERROR, $data, '登录超时，请重新登录！');
            //    } else {
            //        $this->redirect(Yii::app()->request->hostInfo . '/#/access/signin');
            //    }
            //    Yii::app()->end();
            //}
        }
    }

    /**
     * 跳转到指定的登录页面
     * @param string $url
     */
    protected function onLoadToUrl($url = '') {
        if (isset($_SERVER["QUERY_STRING"]) && strpos($_SERVER['QUERY_STRING'], "angularjs")) {
            $msg = OpError::getInstance()->getMessage(OpError::ERR_VERIFY);
            $this->retJSON(OpError::ERR_VERIFY, $url, $msg);
        } else {
            $str = "<script>location.href='" . $url . "'</script>";
            exit($str);
        }
    }

    /**
     * 验证用户状态是否有效
     */
    protected function checkValidate() {
        $isLogin = false;
        $userInfo = $this->checkCookies();

        if (!empty ($userInfo)) {
            $dataList = array();
            $dataList ['userId'] = $userInfo ['userId'];
            $dataList ['key'] = $userInfo ['key'];
            if (!$result = $this->service->privilege->checkValidate($dataList)) {
                throw new CHttpException(500, '权限接口不通!');
            }
            if (isset($result['ret']) && ($result ['ret'] == 1)) {
                $dataList = $result ['data'];
                $this->powerCodeList = $dataList ['menuCode'];
                $this->adminUser = $dataList ['empNameZh'];
                $this->adminUserId = $userInfo ['userId'];
                $this->adminJobNum = $dataList ['empJobNum'];
                $this->adminUserInfo = $dataList;
                $key = 'LoadUserId_' . $this->adminJobNum;
                Yii::app()->redisDB->set($key, 1, AdminLoginForm::LOGIN_SUCCESS_TIME_RATE);
                $dataateList = array();
                $dataateList ['power_userName'] = $dataList ['empNameZh'];
                $dataateList ['power_userId'] = $userInfo ['userId'];
                $dataateList ['power_token'] = $userInfo ['key'];
                $this->setCookiesUserInfo($dataateList);
                $isLogin = true;
            } elseif (isset($result['ret']) && ($result ['ret'] == 3)) {
                $key = 'LoadUserId_' . $this->adminJobNum;
                Yii::app()->redisDB->set($key, 1, AdminLoginForm::LOGIN_OUT_TIME_RATE);
                exit($result ['errMsg']);
            } else {
                $isLogin = false;
            }
        }
        return $isLogin;
    }

    /**
     * 提取cookies信息
     * @return multitype: array
     */
    protected function checkCookies() {
        if (empty (Yii::app()->request->cookies['power_userId']->value)
            || empty (Yii::app()->request->cookies['power_userName']->value)
            || empty (Yii::app()->request->cookies['power_token']->value)) {
            return array();
        } else {
            return array(
                'userName' => Yii::app()->request->cookies ['power_userName']->value,
                'userId' => Yii::app()->request->cookies ['power_userId']->value,
                'key' => Yii::app()->request->cookies ['power_token']->value
            );
        }
    }

    /**
     * 设置用户登录的cookies信息
     * @param unknown $dataList
     */
    protected function setCookiesUserInfo($dataList, $time = 7200) {
        foreach ($dataList as $k => $v) {
            $this->setCookiesAte($k, $v, $time);
        }
    }

    /**
     * 设置cookiese信息
     */
    protected function setCookiesAte($name = '', $value = '', $time = 3600) {
        setcookie($name, $value, time() + $time, "/", STATIC_COOKIES);
    }

    /**
     * 权限单独验证
     * @param string $powerCode
     */
    protected function checkPower($powerCode = '', $returnfalse = true) {
        $isCheck = false;
        $allList = explode(',', $this->powerCodeList);
        $isajax = $this->request->getParam('isajax');
        if (in_array($powerCode, $allList) || $this->adminUserId == 1) {
            $isCheck = true;
        }
        if (empty ($isCheck) && $returnfalse) {
            if (($isajax == 1) || (isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) == "xmlhttprequest") || (isset($_SERVER["QUERY_STRING"]) && strpos($_SERVER['QUERY_STRING'], "angularjs"))) {
                if (strpos($_SERVER['QUERY_STRING'], "angularjs")) {
                    $this->retJSON(OpResponse::RET_ERROR, null, OpError::getInstance()->getMessage(OpError::ERR_VERIFY));
                } else {
                    $this->retJSON(OpResponse::RET_ERROR, array('msg' => OpError::getInstance()->getMessage(OpError::ERR_VERIFY)));
                }
            } else {
                throw new CHttpException (500, "Sorry,您没有权限操作此信息!");
            }
        }
        return $isCheck;
    }

    /**
     * 统一JSON返回接口
     * @param 标志位 $code 1 正常 其他自定义
     * @param array $data
     */
    protected function retJSON($code, $data, $errMsg = '') {
        $jsonArray = array();
        $jsonArray['ret'] = (int)$code;
        $jsonArray['data'] = $data;
        $jsonArray['errMsg'] = $errMsg;

        $callBack = $this->request->getParam('jsonpCallback');
        $strJson = json_encode($jsonArray);
        if ($callBack) {
            $strJson = $callBack . '(' . $strJson . ')';
        }
        Yii::app()->end($strJson);
    }
}
