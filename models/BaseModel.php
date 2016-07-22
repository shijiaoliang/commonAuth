<?php
class BaseModel extends CActiveRecord {
    //$this->defaultResult(OpError::ERR_NONE, OpError::ERR_NONE, 'Error!');
    public function defaultResult($ret = OpError::ERR_NONE, $errCode = OpError::ERR_NONE, $errMsg = "", $data = array(), $pagerObj = array()) {
        $ret = array(
            'ret' => $ret,
            'errCode' => $errCode,
            'errMsg' => $errMsg,
            'data' => $data,
            'pager' => $this->frontPagerObj($pagerObj)
        );

        return $ret;
    }

    public function frontPagerObj(CPagination $pagerObj) {
        return array(
            'pageSize' => $pagerObj->getPageSize(),
            'currentPage' => $pagerObj->getCurrentPage(),
            'itemCount' => $pagerObj->getItemCount()
        );
    }
}