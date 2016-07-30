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

        $ngData = Yii::app()->getRequest()->getPost('ngData');
        if (!ParamCheck::checkArray($ngData, array('app_name', 'app_code', 'app_url'))) {
            $this->retJSON(OpResponse::RET_ERROR, null, '参数缺省!');
        }

        $appId = 0;
        if (!empty($ngData['app_id'])) {
            $appId = (int)$ngData['app_id'];
        }

        if ($appId) {
            $model = AppAR::model()->findByPk($appId);
        } else {
            $model = new AppAR();
        }

        $model->attributes = $ngData;

        $res = $model->save();
        if (!$res) {
            $errMsg = BaseModel::getFirstErrMsg($model);
            $this->retJSON(OpResponse::RET_ERROR, null, $errMsg);
        }

        $data = array();
        $msg = '添加成功';
        if ($appId) {
            $msg = '更新成功';
        }
        $this->retJSON(OpResponse::RET_SUCCESS, $data, $msg);
    }

    //切换应用是否可用
    public function actionSwitch() {
        //校验权限
        //$this->checkPower(PrivilegeCode::PRI_Logistics_QUERY);

        $ngData = Yii::app()->getRequest()->getPost('ngData');
        if (!ParamCheck::checkArray($ngData, array('app_id'))) {
            $this->retJSON(OpResponse::RET_ERROR, null, '参数缺省!');
        }

        $appId = (int)$ngData['app_id'];
        $model = AppAR::model()->findByPk($appId);
        $agoStatus = $model->app_status;
        $updateStatus = 10;
        if ($agoStatus == 10) {
            $updateStatus = 20;
        }
        $model->app_status = $updateStatus;

        $res = $model->save();
        if (!$res) {
            $errMsg = BaseModel::getFirstErrMsg($model);
            $this->retJSON(OpResponse::RET_ERROR, null, $errMsg);
        }

        $data = array();
        $msg = '操作成功';
        $this->retJSON(OpResponse::RET_SUCCESS, $data, $msg);
    }
}