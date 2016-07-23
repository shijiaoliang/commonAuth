<?php
class ApplicationController extends BaseController {
    public function actionList() {
        //校验权限
        //$this->checkPower(PrivilegeCode::PRI_Logistics_QUERY);

        $arrParam = array();
        if (isset($_REQUEST['listForm'])) {
            $listForm = json_decode($_REQUEST['listForm'], true);
            if (is_array($listForm)) {
                if (!empty($listForm['app_name'])) {
                    $arrParam['app_name'] = $listForm['app_name'];
                }
            }
        }

        $params = array();
        $params['currentPage'] = $this->page;
        $params['pageSize'] = $this->pageSize;
        $params = array_merge($params, $arrParam);

        $model = new AppAR();
        $searchRet = $model->getList($params);

        if ($searchRet['ret'] > 0) {
            $this->retJSON(OpResponse::RET_ERROR, null, '查询异常:' . $searchRet['errMsg']);
        }

        $getData = $searchRet['data'];
        $pager = $searchRet['pager'];

        //retJSON
        $this->retJSON(OpResponse::RET_SUCCESS, array(
                'pager' => $pager,
                'applications' => $getData,
            )
        );
    }

    //add | update
    public function actionSave() {
        //校验权限
        //$this->checkPower(PrivilegeCode::PRI_Logistics_QUERY);

        if (!ParamCheck::checkArray($_POST, array('app_name', 'app_code', 'app_url'))) {
            $this->retJSON(OpResponse::RET_ERROR, null, '参数缺省!');
        }

        $appId = 0;
        if (!empty($_POST['app_id'])) {
            $appId = (int)$_POST['app_id'];
        }

        $model = new AppAR('save');
        if ($appId) {
            $model->setIsNewRecord(false);
        }
        $model->attributes = $_POST;
        if (!$appId) {
            $model->app_status = 10;
        }

        $res = $model->save();
        if (!$res) {
            $errMsg = BaseModel::getFirstErrMsg($model);
            $this->retJSON(OpResponse::RET_ERROR, null, $errMsg);
        }

        $data = array();
        if ($appId) {
            $data = AppAR::model()->findByPk($appId);
        }
        $this->retJSON(OpResponse::RET_SUCCESS, $data);
    }
}