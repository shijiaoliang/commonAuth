<?php
class ApplicationController extends BaseController {
    public function actionList() {
        //校验权限
        //$this->checkPower(PrivilegeCode::PRI_Logistics_QUERY);

        $arrParam = array();
        if (isset($_REQUEST['listForm'])) {
            $listForm = json_decode($_REQUEST['listForm'], true);
            if (is_array($listForm)) {
                if (!empty($listForm['keyword'])) {
                    $arrParam['keyword'] = $listForm['keyword'];
                }
            }
        }
        print_r($_REQUEST);exit;

        $params = array();
        $params['page'] = $this->page;
        $params['limit'] = $this->pageSize;
        $params = array_merge($params, $arrParam);

        $model = new AppAR();
        $searchRet = $model->search($params);

        if ($searchRet['ret'] > 0 || !isset($searchRet['data'])) {
            $this->retJSON(OpResponse::RET_ERROR, null, '查询异常:' . $searchRet['errMsg']);
        }

        $getData = $searchRet['data'];

        new OnePlusServiceResponse($this->_callRes['ret'], $this->_callRes['errCode']
            , $this->_callRes['errMsg'], $data, $page);

        $this->retJSON(OpResponse::RET_SUCCESS, array(
                'pager' => array(
                    'totalRecord' => $getData['count'],
                    'pageSize' => $pageSize,
                    'currentPage' => $page
                ),
                'applications' => $getData['trackings'],
                ''
            )
        );
    }
}