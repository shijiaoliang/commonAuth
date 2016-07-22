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
}