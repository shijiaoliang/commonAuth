<?php
class BaseModel extends CActiveRecord {
    public static $timestamp = '';

    public function init() {
        parent::init();
        self::$timestamp = $_SERVER['REQUEST_TIME'];
    }

    //$this->defaultResult(OpError::ERR_NONE, OpError::ERR_NONE, 'Error!');
    public static function defaultResult($ret = OpError::ERR_NONE, $errCode = OpError::ERR_NONE, $errMsg = "", $data = array(), $pagerObj = array()) {
        $ret = array(
            'ret' => $ret,
            'errCode' => $errCode,
            'errMsg' => $errMsg,
            'data' => $data,
            'pager' => self::frontPagerObj($pagerObj)
        );

        return $ret;
    }

    /**
     * frontPagerObj
     * @param CPagination $pagerObj
     * @return array
     */
    public static function frontPagerObj(CPagination $pagerObj) {
        return array(
            'pageSize' => $pagerObj->getPageSize(),
            'currentPage' => $pagerObj->getCurrentPage(),
            'itemCount' => $pagerObj->getItemCount()
        );
    }

    /**
     * getFirstErrMsg
     * @param CModel $model
     * @return string
     */
    public static function getFirstErrMsg(CModel $model) {
        $errMsg = '';

        $errors = $model->getErrors();
        if ($errors) {
            foreach ($errors as $k => $v) {
                if ($v[0]) {
                    $errMsg = $v[0];
                    break;
                }
            }
        }

        return $errMsg;
    }
}