<?php
Yii::import('application.models.common.*');

class LogisticsController extends Controller {
    //清除缓存[快递公司列表][快递单数据]
    public function actionDelCache() {
        //校验权限
        $this->checkPower(PrivilegeCode::PRI_Logistics_QUERY);

        $type = trim(Yii::app()->getRequest()->getParam('type', 1));
        $id   = trim(Yii::app()->getRequest()->getParam('id'));

        if ($type == 1) {
            RedisModels::getInstance()->delAftershipCouriers();
        } else if ($type == 2) {
            if ($id) {
                RedisModels::getInstance()->delAftershipTracking($id);
            } else {
                RedisModels::getInstance()->delAllAftershipTracking();
            }
        } else if ($type == 3) {
            RedisModels::getInstance()->delAftershipCouriers();
            RedisModels::getInstance()->delAllAftershipTracking();
        }

        echo 'Success~';
    }

    public function actionAjaxCouriers() {
        //校验权限
        $this->checkPower(PrivilegeCode::PRI_Logistics_QUERY);

        $r = RedisModels::getInstance()->getAftershipCouriers();

        $logisticsModel = new LogisticsBaseModel();
        if (!$r) {
            $r = $logisticsModel->couriersGet();

            //写缓存
            if ($r) {
                RedisModels::getInstance()->setAftershipCouriers($r);
            }
        }

        if ($r['ret'] > 0) {
            $this->retJSON(OnePlusServiceResponse::RET_ERROR, null, $r['errMsg']);
        }

        $this->retJSON(OnePlusServiceResponse::RET_SUCCESS, array(
            'couriers' => $r['data']['couriers']
        ), 'ok');
    }

    public function actionAjaxQuery() {
        //校验权限
        $this->checkPower(PrivilegeCode::PRI_Logistics_QUERY);

        $slug = Yii::app()->getRequest()->getParam('slug');
        $tracking_number = Yii::app()->getRequest()->getParam('tracking_number');

        if (!$slug || !$tracking_number) {
            $this->retJSON(OnePlusServiceResponse::RET_ERROR, null, 'params error');
        }

        $logisticsModel = new LogisticsBaseModel();

        //$id = $slug . '_' . $tracking_number;
        //$r = RedisModels::getInstance()->getAftershipTracking($id);
        //if (!$r) {
        //    $r = $logisticsModel->trackingsGet($slug, $tracking_number);
        //
        //    //写缓存
        //    if ($r && isset($r['ret']) && $r['ret'] == 0) {
        //        RedisModels::getInstance()->setAftershipTracking($id, $r);
        //    }
        //}

        $r = $logisticsModel->trackingsGet($slug, $tracking_number);
        if ($r['ret'] > 0) {
            $this->retJSON(OnePlusServiceResponse::RET_ERROR, null, $r['errMsg']);
        }
        $trackingInfo = $r['data'];

        $this->retJSON(OnePlusServiceResponse::RET_SUCCESS, array(
            'trackingInfo' => $trackingInfo['tracking']
        ), 'ok');
    }

    public function actionList() {
        //校验权限
        $this->checkPower(PrivilegeCode::PRI_Logistics_QUERY);

        //当前第几页
        $page = 1;
        if (isset($_REQUEST['page'])) {
            $page = intval($_REQUEST['page']);
        }
        if ($page < 1) {
            $page = 1;
        }
        $pageSize = 15;

        $arrParam = array();
        if (isset($_REQUEST['listForm'])) {
            $listForm = json_decode($_REQUEST['listForm'], true);
            if (is_array($listForm)) {
                if (isset($listForm['slug']) && !empty($listForm['slug'])) {
                    $arrParam['slug'] = $listForm['slug'];
                }
                if (isset($listForm['keyword']) && !empty($listForm['keyword'])) {
                    $arrParam['keyword'] = trim($listForm['keyword']);
                }
            }
        }

        $params = array();
        $params['page'] = $page;
        $params['limit'] = $pageSize;
        $params = array_merge($params, $arrParam);

        $logisticsModel = new LogisticsBaseModel();
        $searchRet = $logisticsModel->trackingsList($params);

        if ($searchRet['ret'] > 0 || !isset($searchRet['data'])) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, '查询异常:' . $searchRet['errMsg']);
        }

        $getData = $searchRet['data'];
        $this->retJSON(OnePlusServiceResponse::RET_SUCCESS, array(
            'pager' => array(
                'totalRecord' => $getData['count'],
                'pageSize' => $pageSize,
                'currentPage' => $page
            ),
            'logisticsList' => $getData['trackings'],
            ''
            )
        );
    }

    //删除aftership上一个运单
    public function actionAjaxDeleteAftershipByid() {
        //权限校验
        $this->checkPower(PrivilegeCode::PRI_Logistics_QUERY);

        $id = Yii::app()->getRequest()->getParam('id');
        if (!$id) {
            $this->retJSON(OnePlusServiceResponse::RET_ERROR, null, 'Params Error');
        }

        //实例化模型
        $logisticsModel = new LogisticsBaseModel();
        $res = $logisticsModel->trackingsDeleteById($id);

        if ($res['ret'] > 0) {
            $this->retJSON(OnePlusServiceResponse::RET_ERROR, null, $res['errMsg']);
        }

        $this->retJSON(OnePlusServiceResponse::RET_SUCCESS, null, 'Delete Success');
    }

    //==================RMA Logistics===============
    public function actionAjaxRmaLogisticsLoad() {
        //权限校验
        $this->checkPower(PrivilegeCode::PRI_RMA_LOGISTICS_MANAGEMENTE);

        //实例化model
        $activeModel = new LogisticsActiveRecord();

        $_rl_timeScope = HandlingFormModel::getRlTimeScope();
        $_rl_type = HandlingFormModel::getRlType();
        $_rl_status = HandlingFormModel::getRlStatus();
        $_serviceNodePartner = Yii::app()->params['servicenodeMgt']['serviceNodePartner'];

        //网点数据权限
        $serviceDataRight = $this->verifyAllDataPrivilege(PrivilegeCode::PRI_DATARIGHT_BRANCH_ID); //获取网点权限
        $partnerArr = $activeModel->getServiceCenterByServiceNodecode($serviceDataRight);
        $serviceNodePartner = array_keys($_serviceNodePartner);
        $partnerArr = array_intersect($serviceNodePartner, $partnerArr);

        $partnerArr = array_values($partnerArr);
        $partnerArrNew = array();
        if ($partnerArr) {
            foreach ($partnerArr as $x => $y) {
                $partnerArrNew[$y] = isset($_serviceNodePartner[$y]) ? $_serviceNodePartner[$y] : '';
            }
        }
        $_serviceNodePartner = $partnerArrNew;

        $rl_timeScope = array();
        foreach ($_rl_timeScope as $k => $v) {
            $rl_timeScope[] = array('key' => $k, 'value' => $v);
        }

        $rl_type = array();
        foreach ($_rl_type as $k => $v) {
            $rl_type[] = array('key' => $k, 'value' => $v);
        }

        $rl_status = array();
        foreach ($_rl_status as $k => $v) {
            $rl_status[] = array('key' => $k, 'value' => $v);
        }

        $allServiceNodePartner = array();
        foreach ($_serviceNodePartner as $k => $v) {
            $allServiceNodePartner[] = array('key' => $k, 'value' => $v);
        }

        //统计
        $statistics = $activeModel->statistics($_serviceNodePartner);
        if ($statistics) {
            foreach ($statistics as $k => $v) {
                $statistics[] = array(
                    'id' => $k,
                    'name' => $v['name'],
                    'num' => $v['num'],
                    'avgTat' => $v['avgTat']
                );
                unset($statistics[$k]);
            }
        }
        //print_r($statistics);exit;

        $this->retJSON(OnePlusServiceResponse::RET_SUCCESS, array(
                'rl_timeScope' => $rl_timeScope,
                'rl_type' => $rl_type,
                'rl_status' => $rl_status,
                'allServiceNodePartner' => $allServiceNodePartner,
                'statistics' => $statistics
            )
        );
    }

    public function actionRmaLogisticsList() {
        //权限校验
        $this->checkPower(PrivilegeCode::PRI_RMA_LOGISTICS_MANAGEMENTE);

        //分页数据
        $page = 1;
        if (isset($_REQUEST['page'])) {
            $page = intval($_REQUEST['page']);
        }
        if ($page < 1) {
            $page = 1;
        }
        $pageSize = 15;

        //实例化模型
        $activeModel = new LogisticsActiveRecord();

        //==========查询条件==========
        $arrParam = array();
        if (isset($_REQUEST['rmaLogisticsListForm'])) {
            $rmaLogisticsListForm = json_decode($_REQUEST['rmaLogisticsListForm'], true);
            if (is_array($rmaLogisticsListForm)) {
                if (isset($rmaLogisticsListForm['inTime']) && !empty($rmaLogisticsListForm['inTime'])) {
                    $arrParam['inTime'] = $rmaLogisticsListForm['inTime'];
                }
                if (isset($rmaLogisticsListForm['send_type']) && $rmaLogisticsListForm['send_type'] > 1) {
                    $arrParam['send_type'] = $rmaLogisticsListForm['send_type'];
                }
                if (isset($rmaLogisticsListForm['delivery_status']) && !empty($rmaLogisticsListForm['delivery_status'])) {
                    $arrParam['delivery_status'] = $rmaLogisticsListForm['delivery_status'];
                }
                if (isset($rmaLogisticsListForm['tracking_number']) && !empty($rmaLogisticsListForm['tracking_number'])) {
                    $arrParam['tracking_number'] = $rmaLogisticsListForm['tracking_number'];
                }
                if (isset($rmaLogisticsListForm['source_num']) && !empty($rmaLogisticsListForm['source_num'])) {
                    $arrParam['source_num'] = $rmaLogisticsListForm['source_num'];
                }
                if (isset($rmaLogisticsListForm['service_center']) && !empty($rmaLogisticsListForm['service_center'])) {
                    $arrParam['service_center'] = $rmaLogisticsListForm['service_center'];
                }
                if (isset($rmaLogisticsListForm['timeStart']) && !empty($rmaLogisticsListForm['timeStart'])) {
                    $arrParam['timeStart'] = $rmaLogisticsListForm['timeStart'];
                }
                if (isset($rmaLogisticsListForm['timeEnd']) && !empty($rmaLogisticsListForm['timeEnd'])) {
                    $arrParam['timeEnd'] = $rmaLogisticsListForm['timeEnd'];
                }
            }
        }

        //网点数据权限
        $serviceDataRight = $this->verifyAllDataPrivilege(PrivilegeCode::PRI_DATARIGHT_BRANCH_ID); //获取网点权限
        $arrParam['partnerArr'] = $activeModel->getServiceCenterByServiceNodecode($serviceDataRight);
        $serviceNodePartner = array_keys(Yii::app()->params['servicenodeMgt']['serviceNodePartner']);
        $arrParam['partnerArr'] = array_intersect($serviceNodePartner, $arrParam['partnerArr']);//求交集

        //查询
        $result = $activeModel->queryList($arrParam, array('pageSize' => $pageSize, 'currentPage' => $page));
        $rmaLogisticsList = $result['data'];
        //print_r($rmaLogisticsList);exit;

        $this->retJSON(OnePlusServiceResponse::RET_SUCCESS, array(
            'pager' => array(
                'totalRecord' => $result['pagerArray']['itemCount'],
                'pageSize' => $pageSize,
                'currentPage' => $page
            )
        , 'rmaLogisticsList' => $rmaLogisticsList
        ));
    }

    //删除本地一个运单
    public function actionAjaxDelOne() {
        //权限校验
        $this->checkPower(PrivilegeCode::PRI_RMA_LOGISTICS_MANAGEMENTE);

        $id = (int)Yii::app()->getRequest()->getParam('id');
        if (!$id) {
            $this->retJSON(OnePlusServiceResponse::RET_ERROR, null, 'Params Error');
        }

        //实例化模型
        $activeModel = new LogisticsActiveRecord();
        $res = $activeModel->delOne($id);
        if ($res['ret'] > 0) {
            $this->retJSON(OnePlusServiceResponse::RET_ERROR, null, $res['errMsg']);
        }

        $this->retJSON(OnePlusServiceResponse::RET_SUCCESS, null, 'Delete Success');
    }

    public function actionAjaxChangeActive() {
        //权限校验
        $this->checkPower(PrivilegeCode::PRI_RMA_LOGISTICS_MANAGEMENTE);

        $id = (int)Yii::app()->getRequest()->getParam('id');
        $active = (int)Yii::app()->getRequest()->getParam('active');

        if (!$id || !in_array($active, array(0, 1))) {
            $this->retJSON(OnePlusServiceResponse::RET_ERROR, null, 'Params Error');
        }

        //实例化模型
        $activeModel = new LogisticsActiveRecord();
        $res = $activeModel->changeActive($id, $active);
        if ($res['ret'] > 0) {
            $this->retJSON(OnePlusServiceResponse::RET_ERROR, null, $res['errMsg']);
        }

        $this->retJSON(OnePlusServiceResponse::RET_SUCCESS, null, 'Change Success');
    }

    public function actionAjaxUpdate() {
        //权限校验
        $this->checkPower(PrivilegeCode::PRI_RMA_LOGISTICS_MANAGEMENTE);

        $idArr = json_decode($_REQUEST['id'], true);
        if ($idArr) {
            foreach ($idArr as $k => $v) {
                $idArr[] = $v;
            }

            $activeModel = new LogisticsActiveRecord();
            $activeModel->updateById($idArr);
        }

        $this->retJSON(OnePlusServiceResponse::RET_SUCCESS, null, 'Success');
    }
    
    /**
     * 根据rma单号，批量查询是否存在寄出的物流信息
     */
    public function actionAjaxGetRmaLogistics() {
        //查询权限
        if ($this->checkPower(PrivilegeCode::PRI_SITE_MANAGEMENTE, false) == false) {
            $this->retJSON(OnePlusServiceResponse::RET_ERROR, null, '您没有相关权限');
        }
        $res = array();
        if(isset($_POST['params']) && !empty($_POST['params'])){
            //实例化模型
            $activeModel = new LogisticsActiveRecord();
            $res = $activeModel->queryLogisticsByRmaNos($_POST['params']);
        }
        $this->retJSON(OnePlusServiceResponse::RET_SUCCESS, $res, 'Success');
    }
}