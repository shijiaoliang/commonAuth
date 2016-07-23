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
    public $userName;

    /**
     * 当前登录用户ID
     */
    public $userId;

    /**
     * 当前登录账号
     */
    public $userNo;

    /**
     * 存放用户数组
     */
    public $userInfo;

    public $hostUrl = '';

    /*======page======*/
    public $page = 1;
    public $pageSize = 15;
    
    //init
    public function init() {
        parent::init();
        $this->hostUrl = Yii::app()->getRequest()->hostInfo;

        $this->checkLoginStatus();
        $this->_initPage();
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
            $userInfo = $this->checkValidate();
            if (!$userInfo) {
                if (strstr($_SERVER["QUERY_STRING"], 'angularjs=true')) {
                    $data = array('signinUrl' => Yii::app()->request->hostInfo . '/#/access/signin');
                    $this->retJSON(OpResponse::RET_ERROR, $data, '登录超时，请重新登录！');
                } else {
                    $this->redirect($this->hostUrl . '/#/access/signin');
                }
                Yii::app()->end();
            }
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

        if (Yii::app()->session['userId'] > 0) {
            $this->userId = Yii::app()->session['userId'];
            $this->userInfo = Yii::app()->session['userInfo'];
            $this->userName = Yii::app()->session['userInfo']['user_name'];
            $this->userNo = Yii::app()->session['userInfo']['user_no'];
        }

        if ($this->userId > 0) {
            $isLogin = true;
        }

        return $isLogin;
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

        $callBack = Yii::app()->getRequest()->getParam('jsonpCallback');
        $strJson = json_encode($jsonArray);
        if ($callBack) {
            $strJson = $callBack . '(' . $strJson . ')';
        }
        Yii::app()->end($strJson);
    }

    //初始化分页
    protected function _initPage() {
        if (isset($_REQUEST['page'])) {
            $this->page = intval($_REQUEST['page']);
        }
        if ($this->page < 1) {
            $this->page = 1;
        }
    }
}
