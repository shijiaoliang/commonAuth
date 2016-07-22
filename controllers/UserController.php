<?php
class UserController extends BaseController {
    public function actionChangePwd() {
        $params = array();

        if (!isset($_REQUEST['oldPssword']) || !isset($_REQUEST['newPssword'])) {
            $this->retJSON(OpResponse::RET_ERROR, null, '必要参数未输入！');
        }
        if (empty($_REQUEST['newPssword'])) {
            $this->retJSON(OpResponse::RET_ERROR, null, '新密码不能为空！');
        }
        if (empty($this->userId)) {
            $this->retJSON(OpResponse::RET_ERROR, null, '必要参数错误！');
        }

        $params['newPwd'] = $_REQUEST['newPssword'];
        $params['oldPwd'] = $_REQUEST['oldPssword'];
        $params['userId'] = $this->userId;
        $model = new UserAR();
        $result = $model->changePwd($params);

        if ($result['ret'] == 0) {
            $this->retJSON(OpResponse::RET_SUCCESS, null, $result['errMsg']);
        } else {
            $this->retJSON(OpResponse::RET_ERROR, null, $result['errMsg']);
        }
    }
}