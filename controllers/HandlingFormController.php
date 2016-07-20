<?php
Yii::import('application.models.handlingformmodel.HandlingFormFacadeModel', true);
Yii::import('application.models.handlingformmodel.HandlingFormConfig');
Yii::import('application.models.common.RedisService');
Yii::import('application.models.extendedwarranty.ExtendedWarrantySearchModel', true);
Yii::import('service.models.extendedwarranty.ExtendedWarrantyBillModel', true);
Yii::import('service.models.extendedwarranty.ExtendedWarrantyBaseModel', true);
Yii::import('service.models.extendedwarranty.config.ExtendedWarrantyConfig', true);
Yii::import('application.models.warehouse.WMSAfterSalesService');

Yii::import('application.models.common.RedisModels');
Yii::import('service.models.CommonModel', true);

Yii::import('service.models.logistics.*');
Yii::import('application.models.common.*');

/**
 * 处理单Controller
 */
class HandlingFormController extends Controller {
    private $isShouJi = "01"; //01代表是手机

    const OPERE_TYPE_SAVE = 1; //保存
    const OPERE_TYPE_PASS = 2; //检测通过
    const OPERE_TYPE_NO_PASS = 3; //检测不通过
    /**
     * 检测状态为：退货-正常
     * @var int
     */
    const CHECK_REJECT_OK = 10;

    /**
     * 检测状态为：退货-非保
     * @var int
     */
    const CHECK_REJECT_NOT_GUARANTEE = 15;

    /**
     * 检测状态为：换机-正常
     * @var int
     */
    const CHECK_SWAP_OK = 20;

    /**
     * 检测状态为：换机-非保
     * @var int
     */
    const CHECK_SWAP_NOT_GUARANTEE = 25;

    /**
     * 检测状态为：维修-正常
     * @var int
     */
    const CHECK_REPAIR_OK = 30;

    /**
     * 检测状态为：维修-非保
     * @var int
     */
    const CHECK_REPAIR_NOT_GUARANTEE = 35;

    /**
     * 检测状态为：维修-以换代修
     * @var int
     */
    const CHECK_REPAIR_REPLACE = 38;

    /**
     * 没有权限操作此处理单的错误信息
     * @var string
     */
    const NO_FORM_RIGHT = '你没有权限操作此处理单';

    /**
     * 当前的处理单
     * @var HandlingFormModel
     */
    private $_currentHandlingForm;

    /**
     * 添加日志
     * @param string $msg 日志消息
     * @param int $level 日志等级
     * @return    void
     */
    private function addLog($msg, $level = Log::LEVEL_INFO) {
        Log::addLog($msg, __CLASS__, $level);
    }

    //获取物流商
    public function getAllCarrierNew() {
        $logisticsModel = new LogisticsBaseModel();

        $r = RedisModels::getInstance()->getAftershipCouriers();
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

        $allCarrier = array();
        foreach ($r['data']['couriers'] as $k => $v) {
            $allCarrier[$v['name']] = $v['slug'];
        }

        return $allCarrier;
    }

    //状态转文本
    public function actionGetStatusTxt() {
        //校验权限
        $this->checkPower(PrivilegeCode::PRI_SITE_MANAGEMENTE);
        $allStatuss = HandlingFormModel::getAllStatus();
        if (isset($_REQUEST['status']) && !empty($_REQUEST['status']) && array_key_exists($_REQUEST['status'], $allStatuss)) {
            $statusTxt = $allStatuss[$_REQUEST['status']];
        } else {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'status is invalid!');
        }

        $this->retJSON(OnePlusServiceResponse::RET_SUCCESS, array(
            'statusTxt' => $statusTxt
        ), '');
    }

    /**
     * 受理单列表
     */
    public function actionSearch() {
        //查询权限
        if ($this->checkPower(PrivilegeCode::PRI_SITE_MANAGEMENTE, false) == false) {
            $this->retJSON(OnePlusServiceResponse::RET_ERROR, null, '您没有RMA单的查询权限');
        }

        $controlPrivilege = false;
        //操作权限
        if ($this->checkPower(PrivilegeCode::PRI_SITE_MANAGEMENTEUPDATE, false) == true) {
            $controlPrivilege = true;
        }

        $arrParam = array();

        //调查接口查询数据
        $model = new HandlingFormFacadeModel();
        //当前第几页
        $page = 1;
        if (isset($_REQUEST['page'])) {
            $page = intval($_REQUEST['page']);
        }
        if ($page < 1) {
            $page = 1;
        }
        $pageSize = 15;

        if (isset($_REQUEST['handlingForm'])) {
            $handlingForms = json_decode($_REQUEST['handlingForm'], true);
            if (is_array($handlingForms)) {
                if (isset($handlingForms['rmaSheetId']) && !empty($handlingForms['rmaSheetId'])) {
                    $arrParam['rmaSheetId'] = $handlingForms['rmaSheetId'];
                }
                if (isset($handlingForms['sourceId']) && !empty($handlingForms['sourceId'])) {
                    $arrParam['sourceId'] = $handlingForms['sourceId'];
                }
                if (isset($handlingForms['ticketNo']) && !empty($handlingForms['ticketNo'])) {
                    $arrParam['ticketNo'] = $handlingForms['ticketNo'];
                }
                if (isset($handlingForms['email']) && !empty($handlingForms['email'])) {
                    $arrParam['rmaAddress']['email'] = $handlingForms['email'];
                }
                if (isset($handlingForms['status']) && !empty($handlingForms['status'])) {
                    $arrParam['status'] = $handlingForms['status'];
                }
                if (isset($handlingForms['houseCode']) && !empty($handlingForms['houseCode'])) {
                    $arrParam['houseCode'] = $handlingForms['houseCode'];
                }
                if (isset($handlingForms['type']) && !empty($handlingForms['type'])) {
                    $arrParam['type'] = $handlingForms['type'];
                }
                if (isset($handlingForms['pendingType']) && !empty($handlingForms['pendingType'])) {
                    $arrParam['pendingType'] = $handlingForms['pendingType'];
                }
                if (isset($handlingForms['returnMethod']) && !empty($handlingForms['returnMethod'])) {
                    $arrParam['returnMethod'] = $handlingForms['returnMethod'];
                }
                if (isset($handlingForms['startTime1']) && !empty($handlingForms['startTime1'])) {
                    $arrParam['startTime1'] = date("Y-m-d", strtotime($handlingForms['startTime1']));
                }
                if (isset($handlingForms['endTime1']) && !empty($handlingForms['endTime1'])) {
                    $arrParam['endTime1'] = date("Y-m-d", strtotime($handlingForms['endTime1']) + 86400);
                }

                if (isset($handlingForms['imei']) && !empty($handlingForms['imei'])) {
                    $arrParam['imei'] = $handlingForms['imei'];
                }
            }
        }
        $arrParam['serviceType'] = 20; //网点单据

        $serviceDataRight = $this->verifyAllDataPrivilege(PrivilegeCode::PRI_DATARIGHT_BRANCH_ID); //获取网点权限
        $arrParam['houseCodes'] = implode(",", $serviceDataRight) . ',0';;
        //搜索
        $searchRet = $model->queryList($arrParam, $page, $pageSize);

        if (true !== $searchRet->isSuccess()) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, '查询异常:' . $searchRet->getErrMsg());
        }
        $handlingFormList = $searchRet->getData();
        //所有类型及状态
        $allTypes = HandlingFormModel::getAllType();
        $allStatuss = HandlingFormModel::getAllStatus();
        $allMethods = HandlingFormModel::getAllMethod();

        //=======物流服务商动态获取aftership接口数据========
        //$allCarrier = HandlingFormModel::getAllCarrier();
        $allCarrier = $this->getAllCarrierNew();
        //===============================================

        $allPendingTypes = HandlingFormModel::getAllPendingType();

        $rmaSheetIdArr = array();
        if (is_array($handlingFormList) && count($handlingFormList) > 0) {
            foreach ($handlingFormList as $k => $v) {
                if (isset($v['status']) && array_key_exists($v['status'], $allStatuss)) {
                    $handlingFormList[$k]['statusTxt'] = $allStatuss[$v['status']];
                } else {
                    $handlingFormList[$k]['statusTxt'] = '';
                }

                if (isset($v['type']) && array_key_exists($v['type'], $allTypes)) {
                    $handlingFormList[$k]['typeTxt'] = $allTypes[$v['type']];
                } else {
                    $handlingFormList[$k]['typeTxt'] = '';
                }
                $handlingFormList[$k]['hasLogistics'] = 0;

                //求得 rmaSheetIdArr
                $rmaSheetIdArr[] = $v['rmaSheetId'];
            }
        }
        $allType = array();
        foreach ($allTypes as $k => $v) {
            $allType[] = array(
                'key' => $k,
                'value' => $v
            );
        }
        $allPendingType = array();
        foreach ($allPendingTypes as $k => $v) {
            $allPendingType[] = array(
                'key' => $k,
                'value' => $v
            );
        }
        $allStatus = array();
        foreach ($allStatuss as $k => $v) {
            $allStatus[] = array(
                'key' => $k,
                'value' => $v
            );
        }
        $allMethod = array();
        foreach ($allMethods as $k => $v) {
            $allMethod[] = array(
                'key' => $k,
                'value' => $v
            );
        }

        //处理 in-house-tat
        $logisticsModel = new LogisticsActiveRecord();
        $inHouseTatArr = $logisticsModel->getInHouseTatByRmaNo($handlingFormList);
        if ($inHouseTatArr && $handlingFormList) {
            foreach ($handlingFormList as $k => $v) {
                $handlingFormList[$k]['inHouseTat'] = null;
                if (isset($inHouseTatArr[$v['rmaSheetId']]) && isset($inHouseTatArr[$v['rmaSheetId']]['inHouseTat'])) {
                    $handlingFormList[$k]['inHouseTat'] = $inHouseTatArr[$v['rmaSheetId']]['inHouseTat'];
                }
            }
        }

        $this->retJSON(OnePlusServiceResponse::RET_SUCCESS, array(
            'pager' => array(
                'totalRecord' => $searchRet->getPage()->totalRecord,
                'pageSize' => $pageSize,
                'currentPage' => $page
            ),
            'handlingFormList' => $handlingFormList,
            'allMethod' => $allMethod,
            'allType' => $allType,
            'allPendingType' => $allPendingType,
            'allStatus' => $allStatus,
            'allCarrier' => $allCarrier,
            'controlPrivilege' => $controlPrivilege
        ), '');
    }

    private function fileExists($url) {
        if (file_get_contents($url, 0, null, 0, 1)) {
            return 1;
        } else {
            return 0;
        }
    }

    private function isImage($filename) {
        $types = array(
            'gif',
            'jpg',
            'jpeg',
            'png',
            'bmp'
        );
        $info = pathinfo($filename);
        if (in_array($info['extension'], $types)) {
            return true;
        }
        return false;
    }

    /**
     * 图片展示
     * @return type
     */
    function actionImageShow() {
        $fileName = isset($_GET['fileName']) ? $_GET['fileName'] : 'download';
        $imgID = isset($_GET['id']) ? $_GET['id'] : '';
        $errMsg = '文件不存在或已经被删除';
        if ($imgID && $fileName) {
            if ($this->isImage($imgID)) {
                Yii::app()->getRequest()->redirect($imgID);
            } else {
                if ($this->fileExists($imgID)) {
                    Yii::app()->request->sendFile($fileName, file_get_contents($imgID));
                } else {
                    header("Content-Type:text/html;charset=utf-8");
                    echo $errMsg;
                }
            }
        } else {
            header("Content-Type:text/html;charset=utf-8");
            echo $errMsg;
        }
    }

    /**
     * 增加pending
     * actionAddPending
     */
    public function actionAddPending() {
        //操作权限
        if ($this->checkPower(PrivilegeCode::PRI_SITE_MANAGEMENTEUPDATE, false) == false) {
            $this->retJSON(OnePlusServiceResponse::RET_ERROR, null, '您没有RMA单的操作权限');
        }

        //获取参数
        $params = Yii::app()->getRequest()->getParam('params');

        //model
        $model = new HandlingFormFacadeModel();

        $detaiRet = $model->findDetailRmaSheet(array('rmaSheetId' => $params['rmaSheetId']));
        if (true !== $detaiRet->isSuccess()) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'Find RMA detail error:' . $detaiRet->getErrMsg());
        }

        //add pending
        $arrParam['logType'] = 378;
        $arrParam['currentOperator'] = $this->adminUser;

        $arrParam['rmaSheetId'] = $params['rmaSheetId'];
        $arrParam['pendingType'] = $params['pendingType'];
        $arrParam['pendingReason'] = $params['pendingReason'];

        $addRet = $model->addPending($arrParam);
        if (true !== $addRet->isSuccess()) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'Add error:' . $addRet->getErrMsg());
        } else {
            $this->retJSON(OnePlusServiceResponse::RET_SUCCESS, array(), '');
        }
    }

    /**
     * 取消pending
     * actionCancelPending
     */
    public function actionCancelPending() {
        //操作权限
        if ($this->checkPower(PrivilegeCode::PRI_SITE_MANAGEMENTEUPDATE, false) == false) {
            $this->retJSON(OnePlusServiceResponse::RET_ERROR, null, '您没有RMA单的操作权限');
        }

        //获取参数
        $params = Yii::app()->getRequest()->getParam('params');

        //model
        $model = new HandlingFormFacadeModel();

        $detaiRet = $model->findDetailRmaSheet(array('rmaSheetId' => $params['rmaSheetId']));
        if (true !== $detaiRet->isSuccess()) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'Find RMA detail error:' . $detaiRet->getErrMsg());
        }

        //cancel pending
        $arrParam['logType'] = 380;
        $arrParam['currentOperator'] = $this->adminUser;

        $arrParam['rmaSheetId'] = $params['rmaSheetId'];

        $cancelRet = $model->cancelPending($arrParam);
        if (true !== $cancelRet->isSuccess()) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'Cancel error:' . $cancelRet->getErrMsg());
        } else {
            $this->retJSON(OnePlusServiceResponse::RET_SUCCESS, array(), '');
        }
    }

    /**
     * 受理单处理页面非保价项查询
     */
    public function actionGetRepaireReplacement() {
        //查询权限
        if ($this->checkPower(PrivilegeCode::PRI_SITE_MANAGEMENTE, false) == false) {
            $this->retJSON(OnePlusServiceResponse::RET_ERROR, null, '您没有RMA单的查询权限');
        }
        //调查接口查询数据
        $model = new HandlingFormFacadeModel();

        $currency = '';
        $language = '';

        $cookie = Yii::app()->request->getCookies();
        if (isset($cookie['langKey'])) {
            $langKey = $cookie['langKey']->value;
        } else {
            $langKey = 'cn';
        }
        if ($langKey == 'en') {
            $language = 'en_US';
        } else {
            $language = 'zh_CN';
        }

        $allQuotationTypeCh = HandlingFormModel::getAllQuotationTypeCh();
        $allQuotationTypeEn = HandlingFormModel::getAllQuotationTypeEn();

        $detaiRet = $model->findDetailRmaSheet(array('rmaSheetId' => $_REQUEST['rmaSheetId']));
        if (true !== $detaiRet->isSuccess()) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'Find RMA detail error:' . $detaiRet->getErrMsg());
        } else {
            $detaiData = $detaiRet->getData();
            $currency = $detaiData['currency'];
        }

        $arrParam = Yii::app()->request->getParam('params', array());
        $page = Yii::app()->request->getParam('page', 1);

        //获取rma单据明细商品类型
        $machineType = $this->getPhoneTypeByRma($detaiData['rmaGoodsList']);
        if (!empty($machineType)) {
            $arrParam['machineTypes'] = $machineType;
        }

        $pageSize = 1000;
        $arrParam['showMode'] = 2; //sku不为空
        $arrParam['currency'] = $currency;
        $arrParam['status'] = '10';
        //        $arrParam['sortByParamNames'] = 'descr';
        //        $arrParam['sortByParamTypes'] = 'asc';

        //搜索
        $searchRet = $model->queryRepaireReplacementList($arrParam, $page, $pageSize);
        if (true !== $searchRet->isSuccess()) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, '查询异常:' . $searchRet->getErrMsg());
        }
        $data['findNonAssurance'] = $searchRet->getData();

        if (count($data['findNonAssurance']) > 0) {
            foreach ($data['findNonAssurance'] as $k => $v) {
                if ($langKey == 'en') {
                    if (isset($v['type']) && array_key_exists($v['type'], $allQuotationTypeEn)) {
                        $data['findNonAssurance'][$k]['typeTxt'] = $allQuotationTypeEn[$v['type']];
                    }
                } else {
                    if (isset($v['type']) && array_key_exists($v['type'], $allQuotationTypeCh)) {
                        $data['findNonAssurance'][$k]['typeTxt'] = $allQuotationTypeCh[$v['type']];
                    }
                }
            }
        }

        $this->retJSON(OnePlusServiceResponse::RET_SUCCESS, array(
            'repaireReplacement' => $data['findNonAssurance']
        ), '');
    }

    /**
     * 受理单日志
     */
    public function actionFindRmaSheetLog() {
        //查询权限
        if ($this->checkPower(PrivilegeCode::PRI_SITE_MANAGEMENTE, false) == false) {
            $this->retJSON(OnePlusServiceResponse::RET_ERROR, null, '您没有RMA单的查询权限');
        }
        //调查接口查询数据
        $arrParam = array();
        $model = new HandlingFormFacadeModel();
        if (!isset($_REQUEST['rmaSheetId']) || empty($_REQUEST['rmaSheetId'])) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'rmaSheetId cannot be null');
        } else {
            $arrParam['rmaSheetId'] = $_REQUEST['rmaSheetId'];
        }
        $page = 1;
        $pageSize = 1000;
        //搜索
        $searchRet = $model->findRmaSheetLog($arrParam, $page, $pageSize);
        if (true !== $searchRet->isSuccess()) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, '查询异常:' . $searchRet->getErrMsg());
        }
        $RamLogs = $searchRet->getData();

        $allLogType = HandlingFormModel::getAllLogType();

        foreach ($RamLogs as $k => $v) {
            if (isset($v['logType']) && array_key_exists($v['logType'], $allLogType)) {
                $RamLogs[$k]['typeTxt'] = $allLogType[$v['logType']];
            } else {
                $RamLogs[$k]['typeTxt'] = 'undefined';
            }
        }

        $this->retJSON(OnePlusServiceResponse::RET_SUCCESS, array(
            'RamLogs' => $RamLogs
        ), '');
    }

    /**
     * sendTrackingNo
     */
    public function actionSendTrackingNo() {
        //操作权限
        if ($this->checkPower(PrivilegeCode::PRI_SITE_MANAGEMENTEUPDATE, false) == false) {
            $this->retJSON(OnePlusServiceResponse::RET_ERROR, null, '您没有RMA单的操作权限');
        }

        $arrParam = array();

        //调查接口查询数据
        $model = new HandlingFormFacadeModel();
        if (!isset($_REQUEST['rmaSheetId']) || empty($_REQUEST['rmaSheetId'])) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'rmaSheetId cannot be null');
        } else {
            $arrParam['rmaSheetId'] = $_REQUEST['rmaSheetId'];
        }
        if (!isset($_REQUEST['returnTrackingNo']) || empty($_REQUEST['returnTrackingNo'])) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'trackingNo cannot be null');
        } else {
            //        $searchRet = $model->findDetailRmaSheet($arrParam);
            //        if (true !== $searchRet->isSuccess()) {
            //            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'Find RMA detail error:' . $searchRet->getErrMsg());
            //        }else{
            //            $data= $searchRet->getData();
            //        }
            $arrParam['returnTrackingNo'] = $_REQUEST['returnTrackingNo'];
        }

        $arrParam['logType'] = 300;
        $arrParam['currentOperator'] = $this->adminUser;
        $searchRet = $model->sendTrackingNo($arrParam);

        if (true !== $searchRet->isSuccess()) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'Input tracking no error:' . $searchRet->getErrMsg());
        } else {
            $this->retJSON(OnePlusServiceResponse::RET_SUCCESS, array('returnTrackingNo' => $arrParam['returnTrackingNo']), '');
        }
    }

    /**
     * 获取rma列表
     * @return string
     */
    public function getRmaList($param, $page, $pageSize) {
        //操作权限
        if ($this->checkPower(PrivilegeCode::PRI_SITE_MANAGEMENTE, false) == false) {
            $this->retJSON(OnePlusServiceResponse::RET_ERROR, null, '您没有RMA单的查询权限');
        }

        $model = new HandlingFormFacadeModel();
        //$this->verifyAllDataPrivilege(PrivilegeCode::PRI_DATARIGHT_BRANCH_ID); //获取网点权限
        //搜索
        $searchRet = $model->queryList($param, $page, $pageSize);

        $param['serviceType'] = 20;

        if (true !== $searchRet->isSuccess()) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, '查询异常:' . $searchRet->getErrMsg());
        }
        $handlingFormList = $searchRet->getData();
        //所有类型及状态
        $allTypes = HandlingFormModel::getAllType();
        $allStatuss = HandlingFormModel::getAllStatus();

        if (is_array($handlingFormList) && count($handlingFormList) > 0) {
            foreach ($handlingFormList as $k => $v) {
                if (isset($v['status']) && array_key_exists($v['status'], $allStatuss)) {
                    $handlingFormList[$k]['statusTxt'] = $allStatuss[$v['status']];
                } else {
                    $handlingFormList[$k]['statusTxt'] = '';
                }

                if (isset($v['type']) && array_key_exists($v['type'], $allTypes)) {
                    $handlingFormList[$k]['typeTxt'] = $allTypes[$v['type']];
                } else {
                    $handlingFormList[$k]['typeTxt'] = '';
                }
            }
        }

        $handlingFormList['page'] = array(
            'totalRecord' => $searchRet->getPage()->totalRecord,
            'pageSize' => $pageSize,
            'currentPage' => $page
        );

        return $handlingFormList;
    }

    /**
     * cancleRma
     */
    public function actionCancleRma() {
        //操作权限
        if ($this->checkPower(PrivilegeCode::PRI_SITE_MANAGEMENTEUPDATE, false) == false) {
            $this->retJSON(OnePlusServiceResponse::RET_ERROR, null, '您没有RMA单的操作权限');
        }
        $arrParam = array();

        //调查接口查询数据
        $model = new HandlingFormFacadeModel();
        if (!isset($_REQUEST['rmaSheetId']) || empty($_REQUEST['rmaSheetId'])) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'rmaSheetId cannot be null');
        } else {
            $arrParam['rmaSheetId'] = $_REQUEST['rmaSheetId'];
        }
        if (!isset($_REQUEST['cancelReason']) || empty($_REQUEST['cancelReason'])) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'reason cannot be null');
        } else {
            $arrParam['cancelReason'] = $_REQUEST['cancelReason'];
        }

        if (!isset($_REQUEST['status']) || empty($_REQUEST['status'])) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'status cannot be null');
        }

        $detaiRet = $model->findDetailRmaSheet(array('rmaSheetId' => $arrParam['rmaSheetId']));
        if (true !== $detaiRet->isSuccess()) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'Find RMA detail error:' . $detaiRet->getErrMsg());
        } else {
            $detaiData = $detaiRet->getData();
            if ($detaiData['status'] != $_REQUEST['status']) {
                $this->retJSON(OnePlusException::PARAM_ERROR, null, 'RMA status has changed!');
            }
        }

        $arrParam['logType'] = 252;
        $arrParam['currentOperator'] = $this->adminUser;
        $arrParam['status'] = 25; //已取消
        $searchRet = $model->cancleRma($arrParam);

        if (true !== $searchRet->isSuccess()) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'Cancle error:' . $searchRet->getErrMsg());
        } else {
            $this->retJSON(OnePlusServiceResponse::RET_SUCCESS, array(
                'status' => 25,
                'statusTxt' => 'Cancel',
                'cancelReason' => $arrParam['cancelReason']
            ), '');
        }
    }

    /**
     * pickup
     */
    public function actionPickup() {
        //操作权限
        if ($this->checkPower(PrivilegeCode::PRI_SITE_MANAGEMENTEUPDATE, false) == false) {
            $this->retJSON(OnePlusServiceResponse::RET_ERROR, null, '您没有RMA单的操作权限');
        }
        $arrParam = array();

        //调查接口查询数据
        $model = new HandlingFormFacadeModel();

        if (!isset($_REQUEST['rmaSheetId']) || empty($_REQUEST['rmaSheetId'])) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'rmaSheetId cannot be null');
        } else {
            $arrParam['rmaSheetId'] = $_REQUEST['rmaSheetId'];
        }

        if (!isset($_REQUEST['status']) || empty($_REQUEST['status'])) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'status cannot be null');
        }

        $detaiRet = $model->findDetailRmaSheet(array('rmaSheetId' => $arrParam['rmaSheetId']));
        if (true !== $detaiRet->isSuccess()) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'Find RMA detail error:' . $detaiRet->getErrMsg());
        } else {
            $detaiData = $detaiRet->getData();
            if ($detaiData['status'] != $_REQUEST['status']) {
                $this->retJSON(OnePlusException::PARAM_ERROR, null, 'RMA status has changed!');
            }
        }

        $arrParam['logType'] = 304;
        $arrParam['currentOperator'] = $this->adminUser;
        $arrParam['status'] = 250; //已取件
        $searchRet = $model->pickup($arrParam);

        if (true !== $searchRet->isSuccess()) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'Pickup error:' . $searchRet->getErrMsg());
        } else {
            $this->retJSON(OnePlusServiceResponse::RET_SUCCESS, array(
                'status' => 250,
                'statusTxt' => 'Picked-up'
            ), '');
        }
    }

    /**
     * dropOff
     */
    public function actionDropOff() {
        //操作权限
        if ($this->checkPower(PrivilegeCode::PRI_SITE_MANAGEMENTEUPDATE, false) == false) {
            $this->retJSON(OnePlusServiceResponse::RET_ERROR, null, '您没有RMA单的操作权限');
        }
        $arrParam = array();

        //调查接口查询数据
        $model = new HandlingFormFacadeModel();

        if (!isset($_REQUEST['rmaSheetId']) || empty($_REQUEST['rmaSheetId'])) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'rmaSheetId cannot be null');
        } else {
            $arrParam['rmaSheetId'] = $_REQUEST['rmaSheetId'];
        }

        if (!isset($_REQUEST['status']) || empty($_REQUEST['status'])) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'status cannot be null');
        }

        $detaiRet = $model->findDetailRmaSheet(array('rmaSheetId' => $arrParam['rmaSheetId']));
        if (true !== $detaiRet->isSuccess()) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'Find RMA detail error:' . $detaiRet->getErrMsg());
        } else {
            $detaiData = $detaiRet->getData();
            if ($detaiData['status'] != $_REQUEST['status']) {
                $this->retJSON(OnePlusException::PARAM_ERROR, null, 'RMA status has changed!');
            }
        }

        $arrParam['logType'] = 306;
        $arrParam['currentOperator'] = $this->adminUser;
        $arrParam['status'] = 260; //已投件
        $searchRet = $model->dropOff($arrParam);

        if (true !== $searchRet->isSuccess()) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'Drop off error:' . $searchRet->getErrMsg());
        } else {
            $this->retJSON(OnePlusServiceResponse::RET_SUCCESS, array(
                'status' => 260,
                'statusTxt' => 'Dropped off'
            ), '');
        }
    }

    /**
     * acceptArrangement
     */
    public function actionAcceptArrangement() {
        //操作权限
        if ($this->checkPower(PrivilegeCode::PRI_SITE_MANAGEMENTEUPDATE, false) == false) {
            $this->retJSON(OnePlusServiceResponse::RET_ERROR, null, '您没有RMA单的操作权限');
        }
        $arrParam = array();

        //调查接口查询数据
        $model = new HandlingFormFacadeModel();

        if (!isset($_REQUEST['rmaSheetId']) || empty($_REQUEST['rmaSheetId'])) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'rmaSheetId cannot be null');
        } else {
            $arrParam['rmaSheetId'] = $_REQUEST['rmaSheetId'];
        }

        if (!isset($_REQUEST['status']) || empty($_REQUEST['status'])) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'status cannot be null');
        }

        $detaiRet = $model->findDetailRmaSheet(array('rmaSheetId' => $arrParam['rmaSheetId']));
        if (true !== $detaiRet->isSuccess()) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'Find RMA detail error:' . $detaiRet->getErrMsg());
        } else {
            $detaiData = $detaiRet->getData();
            if ($detaiData['status'] != $_REQUEST['status']) {
                $this->retJSON(OnePlusException::PARAM_ERROR, null, 'RMA status has changed!');
            }
        }

        $arrParam['logType'] = 302;
        $arrParam['currentOperator'] = $this->adminUser;
        $arrParam['status'] = 240; //接受安排      
        $searchRet = $model->acceptArrangement($arrParam);

        if (true !== $searchRet->isSuccess()) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'Accept arrangement error:' . $searchRet->getErrMsg());
        } else {
            $this->retJSON(OnePlusServiceResponse::RET_SUCCESS, array(
                'status' => 240,
                'statusTxt' => 'Arrangement accepted'
            ), '');
        }
    }

    /**
     * applyCancelDo
     */
    public function actionApplyCancelDo() {
        //操作权限
        if ($this->checkPower(PrivilegeCode::PRI_SITE_MANAGEMENTEUPDATE, false) == false) {
            $this->retJSON(OnePlusServiceResponse::RET_ERROR, null, '您没有RMA单的操作权限');
        }
        $arrParam = array();

        //调查接口查询数据
        $model = new HandlingFormFacadeModel();

        if (!isset($_REQUEST['rmaSheetId']) || empty($_REQUEST['rmaSheetId'])) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'rmaSheetId cannot be null');
        } else {
            $arrParam['rmaSheetId'] = $_REQUEST['rmaSheetId'];
        }
        if (!isset($_REQUEST['cancelReason']) || empty($_REQUEST['cancelReason'])) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'reason cannot be null');
        } else {
            $arrParam['cancelReason'] = $_REQUEST['cancelReason'];
        }

        if (!isset($_REQUEST['status']) || empty($_REQUEST['status'])) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'status cannot be null');
        }

        $detaiRet = $model->findDetailRmaSheet(array('rmaSheetId' => $arrParam['rmaSheetId']));
        if (true !== $detaiRet->isSuccess()) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'Find RMA detail error:' . $detaiRet->getErrMsg());
        } else {
            $detaiData = $detaiRet->getData();
            if ($detaiData['status'] != $_REQUEST['status']) {
                $this->retJSON(OnePlusException::PARAM_ERROR, null, 'RMA status has changed!');
            }
        }

        $arrParam['logType'] = 308;
        $arrParam['currentOperator'] = $this->adminUser;
        $arrParam['status'] = 270; //申请取消
        $searchRet = $model->applyCancelDo($arrParam);

        if (true !== $searchRet->isSuccess()) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'Apply cancel error:' . $searchRet->getErrMsg());
        } else {
            $this->retJSON(OnePlusServiceResponse::RET_SUCCESS, array(
                'status' => 270,
                'statusTxt' => 'Pending cancel',
                'cancelReason' => $arrParam['cancelReason']
            ), '');
        }
    }

    /**
     * receive
     */
    public function actionReceive() {
        //操作权限
        if ($this->checkPower(PrivilegeCode::PRI_SITE_MANAGEMENTEUPDATE, false) == false) {
            $this->retJSON(OnePlusServiceResponse::RET_ERROR, null, '您没有RMA单的操作权限');
        }
        $arrParam = array();

        //调查接口查询数据
        $model = new HandlingFormFacadeModel();

        if (!isset($_REQUEST['rmaSheetId']) || empty($_REQUEST['rmaSheetId'])) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'rmaSheetId cannot be null');
        } else {
            $arrParam['rmaSheetId'] = $_REQUEST['rmaSheetId'];
        }

        if (!isset($_REQUEST['status']) || empty($_REQUEST['status'])) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'status cannot be null');
        }

        $detaiRet = $model->findDetailRmaSheet(array('rmaSheetId' => $arrParam['rmaSheetId']));
        if (true !== $detaiRet->isSuccess()) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'Find RMA detail error:' . $detaiRet->getErrMsg());
        } else {
            $detaiData = $detaiRet->getData();
            if ($detaiData['status'] != $_REQUEST['status']) {
                $this->retJSON(OnePlusException::PARAM_ERROR, null, 'RMA status has changed!');
            }
        }

        $arrParam['logType'] = 310;
        $arrParam['currentOperator'] = $this->adminUser;
        $arrParam['status'] = 280; //已到达网点   
        $searchRet = $model->receive($arrParam);

        if (true !== $searchRet->isSuccess()) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'Receive error:' . $searchRet->getErrMsg());
        } else {
            $this->retJSON(OnePlusServiceResponse::RET_SUCCESS, array(
                'status' => 280,
                'statusTxt' => 'Returned to facility'
            ), '');
        }
    }

    /**
     * inspect
     */
    public function actionInspect() {
        //操作权限
        if ($this->checkPower(PrivilegeCode::PRI_SITE_MANAGEMENTEUPDATE, false) == false) {
            $this->retJSON(OnePlusServiceResponse::RET_ERROR, null, '您没有RMA单的操作权限');
        }
        $arrParam = array();

        //调查接口查询数据
        $model = new HandlingFormFacadeModel();

        if (!isset($_REQUEST['rmaSheetId']) || empty($_REQUEST['rmaSheetId'])) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'rmaSheetId cannot be null');
        } else {
            $arrParam['rmaSheetId'] = $_REQUEST['rmaSheetId'];
        }

        if (!isset($_REQUEST['status']) || empty($_REQUEST['status'])) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'status cannot be null');
        }

        $detaiRet = $model->findDetailRmaSheet(array('rmaSheetId' => $arrParam['rmaSheetId']));
        if (true !== $detaiRet->isSuccess()) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'Find RMA detail error:' . $detaiRet->getErrMsg());
        } else {
            $detaiData = $detaiRet->getData();
            if ($detaiData['status'] != $_REQUEST['status']) {
                $this->retJSON(OnePlusException::PARAM_ERROR, null, 'RMA status has changed!');
            }
        }

        $arrParam['logType'] = 314;
        $arrParam['currentOperator'] = $this->adminUser;
        $arrParam['status'] = 290; //检测中
        $searchRet = $model->inspect($arrParam);

        if (true !== $searchRet->isSuccess()) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'Inspect error:' . $searchRet->getErrMsg());
        } else {
            $this->retJSON(OnePlusServiceResponse::RET_SUCCESS, array(
                'status' => 290,
                'statusTxt' => 'Inspecting'
            ), '');
        }
    }

    /**
     * sendQuotation
     */
    public function actionSendQuotation() {
        //操作权限
        if ($this->checkPower(PrivilegeCode::PRI_SITE_MANAGEMENTEUPDATE, false) == false) {
            $this->retJSON(OnePlusServiceResponse::RET_ERROR, null, '您没有RMA单的操作权限');
        }
        $arrParam = array();

        //调查接口查询数据
        $model = new HandlingFormFacadeModel();

        if (!isset($_REQUEST['rmaSheetId']) || empty($_REQUEST['rmaSheetId'])) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'rmaSheetId cannot be null');
        } else {
            $arrParam['rmaSheetId'] = $_REQUEST['rmaSheetId'];
        }

        if (!isset($_REQUEST['status']) || empty($_REQUEST['status'])) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'status cannot be null');
        }

        $detaiRet = $model->findDetailRmaSheet(array('rmaSheetId' => $arrParam['rmaSheetId']));
        if (true !== $detaiRet->isSuccess()) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'Find RMA detail error:' . $detaiRet->getErrMsg());
        } else {
            $detaiData = $detaiRet->getData();
            if ($detaiData['status'] != $_REQUEST['status']) {
                $this->retJSON(OnePlusException::PARAM_ERROR, null, 'RMA status has changed!');
            }
        }

        $arrParam['logType'] = 316;
        $arrParam['currentOperator'] = $this->adminUser;
        $arrParam['status'] = 310; //检测结束
        $searchRet = $model->sendQuotation($arrParam);

        if (true !== $searchRet->isSuccess()) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'Send quotation error:' . $searchRet->getErrMsg());
        } else {
            $this->retJSON(OnePlusServiceResponse::RET_SUCCESS, array(
                'status' => 310,
                'statusTxt' => 'Pending payment'
            ), '');
        }
    }

    /**
     * replaceDo
     */
    public function actionInputTrackingNoDo() {
        //操作权限
        if ($this->checkPower(PrivilegeCode::PRI_SITE_MANAGEMENTEUPDATE, false) == false) {
            $this->retJSON(OnePlusServiceResponse::RET_ERROR, null, '您没有RMA单的操作权限');
        }
        $arrParam = array();

        //调查接口查询数据
        $model = new HandlingFormFacadeModel();

        if (!isset($_REQUEST['rmaSheetId']) || empty($_REQUEST['rmaSheetId'])) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'rmaSheetId cannot be null');
        } else {
            $arrParam['rmaSheetId'] = $_REQUEST['rmaSheetId'];
        }

        if (!isset($_REQUEST['returnTrackingCarrier']) || empty($_REQUEST['returnTrackingCarrier'])) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'returnTrackingCarrier cannot be null');
        } else {
            $arrParam['returnTrackingCarrier'] = $_REQUEST['returnTrackingCarrier'];
        }
        //if (isset($_REQUEST['returnTrackingCarrier']) || !empty($_REQUEST['returnTrackingCarrier'])) {
        //    $arrParam['returnTrackingCarrier'] = $_REQUEST['returnTrackingCarrier'];
        //}

        if (!isset($_REQUEST['returnTrackingNo']) || empty($_REQUEST['returnTrackingNo'])) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'returnTrackingNo cannot be null');
        } else {
            $arrParam['returnTrackingNo'] = $_REQUEST['returnTrackingNo'];
        }
        //if (isset($_REQUEST['returnTrackingNo']) || !empty($_REQUEST['returnTrackingNo'])) {
        //    $arrParam['returnTrackingNo'] = $_REQUEST['returnTrackingNo'];
        //}

        if (!isset($_REQUEST['status']) || empty($_REQUEST['status'])) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'status cannot be null');
        }

        $detaiRet = $model->findDetailRmaSheet(array('rmaSheetId' => $arrParam['rmaSheetId']));
        if (true !== $detaiRet->isSuccess()) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'Find RMA detail error:' . $detaiRet->getErrMsg());
        } else {
            $detaiData = $detaiRet->getData();
            if ($detaiData['status'] != $_REQUEST['status']) {
                $this->retJSON(OnePlusException::PARAM_ERROR, null, 'RMA status has changed!');
            }
        }

        $arrParam['logType'] = 300;
        $arrParam['currentOperator'] = $this->adminUser;
        $searchRet = $model->inputTrackingNoDo($arrParam);

        if (true !== $searchRet->isSuccess()) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'Replace error:' . $searchRet->getErrMsg());
        } else {
            //新增rma 物流运单 入仓
            $logisticsActiveModel = new LogisticsActiveRecord();
            $r = $logisticsActiveModel->add($arrParam['rmaSheetId'], $arrParam['returnTrackingCarrier'], $arrParam['returnTrackingNo']);
            if ($r['ret'] > 0) {
                $this->retJSON(OnePlusException::PARAM_ERROR, null, $r['errMsg']);
            }

            $this->retJSON(OnePlusServiceResponse::RET_SUCCESS, array(), '');
        }
    }


    /**
     * replaceDo
     */
    public function actionReplaceDo() {
        //操作权限
        if ($this->checkPower(PrivilegeCode::PRI_SITE_MANAGEMENTEUPDATE, false) == false) {
            $this->retJSON(OnePlusServiceResponse::RET_ERROR, null, '您没有RMA单的操作权限');
        }
        $arrParam = array();

        //调查接口查询数据
        $model = new HandlingFormFacadeModel();

        if (!isset($_REQUEST['rmaSheetId']) || empty($_REQUEST['rmaSheetId'])) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'rmaSheetId cannot be null');
        } else {
            $arrParam['rmaSheetId'] = $_REQUEST['rmaSheetId'];
        }

        if (!isset($_REQUEST['carrier']) || empty($_REQUEST['carrier'])) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'carrier cannot be null');
        } else {
            $arrParam['carrier'] = $_REQUEST['carrier'];
        }

        if (!isset($_REQUEST['trackingNo']) || empty($_REQUEST['trackingNo'])) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'trackingNo cannot be null');
        } else {
            $arrParam['trackingNo'] = $_REQUEST['trackingNo'];
        }

        if (isset($_REQUEST['carriagePrice']) || !empty($_REQUEST['carriagePrice'])) {
            $arrParam['carriagePrice'] = $_REQUEST['carriagePrice'] * 100;
        }

        if (!isset($_REQUEST['status']) || empty($_REQUEST['status'])) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'status cannot be null');
        }

        $detaiRet = $model->findDetailRmaSheet(array('rmaSheetId' => $arrParam['rmaSheetId']));
        if (true !== $detaiRet->isSuccess()) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'Find RMA detail error:' . $detaiRet->getErrMsg());
        } else {
            $detaiData = $detaiRet->getData();
            if ($detaiData['status'] != $_REQUEST['status']) {
                $this->retJSON(OnePlusException::PARAM_ERROR, null, 'RMA status has changed!');
            }
        }

        if ($detaiData['status'] != 60) {
            $arrParam['logType'] = 318;
            $arrParam['currentOperator'] = $this->adminUser;
            $arrParam['status'] = 60; //已完成
            $searchRet = $model->replaceDo($arrParam);
            if (true !== $searchRet->isSuccess()) {
                $this->retJSON(OnePlusException::PARAM_ERROR, null, 'Replace error:' . $searchRet->getErrMsg());
            } else {
                //新增rma 物流运单 出仓
                $logisticsActiveModel = new LogisticsActiveRecord();
                $r = $logisticsActiveModel->add($arrParam['rmaSheetId'], $arrParam['carrier'], $arrParam['trackingNo'], 3);
                if ($r['ret'] > 0) {
                    $this->retJSON(OnePlusException::PARAM_ERROR, null, $r['errMsg']);
                }

                $this->retJSON(OnePlusServiceResponse::RET_SUCCESS, array(
                    'status' => 60,
                    'statusTxt' => 'Completed'
                ), '');
            }
        } else {
            //新增rma 物流运单 出仓
            $logisticsActiveModel = new LogisticsActiveRecord();
            $r = $logisticsActiveModel->add($arrParam['rmaSheetId'], $arrParam['carrier'], $arrParam['trackingNo'], 3);
            if ($r['ret'] > 0) {
                $this->retJSON(OnePlusException::PARAM_ERROR, null, $r['errMsg']);
            }

            $this->retJSON(OnePlusServiceResponse::RET_SUCCESS, array(
                'status' => 60,
                'statusTxt' => 'Completed'
            ), '');
        }
    }

    /**
     * sendBackDo
     */
    public function actionSendBackDo() {
        //操作权限
        if ($this->checkPower(PrivilegeCode::PRI_SITE_MANAGEMENTEUPDATE, false) == false) {
            $this->retJSON(OnePlusServiceResponse::RET_ERROR, null, '您没有RMA单的操作权限');
        }
        $arrParam = array();

        //调查接口查询数据
        $model = new HandlingFormFacadeModel();

        if (!isset($_REQUEST['rmaSheetId']) || empty($_REQUEST['rmaSheetId'])) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'rmaSheetId cannot be null');
        } else {
            $arrParam['rmaSheetId'] = $_REQUEST['rmaSheetId'];
        }

        if (!isset($_REQUEST['carrier']) || empty($_REQUEST['carrier'])) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'carrier cannot be null');
        } else {
            $arrParam['carrier'] = $_REQUEST['carrier'];
        }

        if (!isset($_REQUEST['trackingNo']) || empty($_REQUEST['trackingNo'])) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'trackingNo cannot be null');
        } else {
            $arrParam['trackingNo'] = $_REQUEST['trackingNo'];
        }

        if (isset($_REQUEST['carriagePrice']) || !empty($_REQUEST['carriagePrice'])) {
            $arrParam['carriagePrice'] = $_REQUEST['carriagePrice'] * 100;
        }

        if (!isset($_REQUEST['status']) || empty($_REQUEST['status'])) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'status cannot be null');
        }

        $detaiRet = $model->findDetailRmaSheet(array('rmaSheetId' => $arrParam['rmaSheetId']));
        if (true !== $detaiRet->isSuccess()) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'Find RMA detail error:' . $detaiRet->getErrMsg());
        } else {
            $detaiData = $detaiRet->getData();
            if ($detaiData['status'] != $_REQUEST['status']) {
                $this->retJSON(OnePlusException::PARAM_ERROR, null, 'RMA status has changed!');
            }
        }

        if ($detaiData['status'] != 101) {
            $arrParam['logType'] = 262;
            $arrParam['currentOperator'] = $this->adminUser;
            $arrParam['status'] = 101; //已退货拒收
            $searchRet = $model->sendBackDo($arrParam);

            if (true !== $searchRet->isSuccess()) {
                $this->retJSON(OnePlusException::PARAM_ERROR, null, 'SendBack error:' . $searchRet->getErrMsg());
            } else {
                //新增rma 物流运单 出仓
                $logisticsActiveModel = new LogisticsActiveRecord();
                $r = $logisticsActiveModel->add($arrParam['rmaSheetId'], $arrParam['carrier'], $arrParam['trackingNo'], 3);
                if ($r['ret'] > 0) {
                    $this->retJSON(OnePlusException::PARAM_ERROR, null, $r['errMsg']);
                }

                $this->retJSON(OnePlusServiceResponse::RET_SUCCESS, array(
                    'status' => 101,
                    'statusTxt' => 'Sent Back'
                ), '');
            }
        } else {
            //新增rma 物流运单 出仓
            $logisticsActiveModel = new LogisticsActiveRecord();
            $r = $logisticsActiveModel->add($arrParam['rmaSheetId'], $arrParam['carrier'], $arrParam['trackingNo'], 3);
            if ($r['ret'] > 0) {
                $this->retJSON(OnePlusException::PARAM_ERROR, null, $r['errMsg']);
            }

            $this->retJSON(OnePlusServiceResponse::RET_SUCCESS, array(
                'status' => 101,
                'statusTxt' => 'Sent Back'
            ), '');
        }
    }

    /**
     * repair
     */
    public function actionRepair() {
        //操作权限
        if ($this->checkPower(PrivilegeCode::PRI_SITE_MANAGEMENTEUPDATE, false) == false) {
            $this->retJSON(OnePlusServiceResponse::RET_ERROR, null, '您没有RMA单的操作权限');
        }
        $arrParam = array();

        //调查接口查询数据
        $model = new HandlingFormFacadeModel();

        if (!isset($_REQUEST['rmaSheetId']) || empty($_REQUEST['rmaSheetId'])) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'rmaSheetId cannot be null');
        } else {
            $arrParam['rmaSheetId'] = $_REQUEST['rmaSheetId'];
        }

        if (!isset($_REQUEST['status']) || empty($_REQUEST['status'])) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'status cannot be null');
        }

        $detaiRet = $model->findDetailRmaSheet(array('rmaSheetId' => $arrParam['rmaSheetId']));
        if (true !== $detaiRet->isSuccess()) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'Find RMA detail error:' . $detaiRet->getErrMsg());
        } else {
            $detaiData = $detaiRet->getData();
            if ($detaiData['status'] != $_REQUEST['status']) {
                $this->retJSON(OnePlusException::PARAM_ERROR, null, 'RMA status has changed!');
            }
        }

        $arrParam['logType'] = 320;
        $arrParam['currentOperator'] = $this->adminUser;
        $arrParam['status'] = 330; //维修中
        $searchRet = $model->repair($arrParam);

        if (true !== $searchRet->isSuccess()) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'Repair error:' . $searchRet->getErrMsg());
        } else {
            $this->retJSON(OnePlusServiceResponse::RET_SUCCESS, array(
                'status' => 330,
                'statusTxt' => 'Repairing'
            ), '');
        }
    }

    /**
     * applyRefund
     */
    public function actionApplyRefund() {
        //操作权限
        if ($this->checkPower(PrivilegeCode::PRI_SITE_MANAGEMENTEUPDATE, false) == false) {
            $this->retJSON(OnePlusServiceResponse::RET_ERROR, null, '您没有RMA单的操作权限');
        }
        $arrParam = array();

        //调查接口查询数据
        $model = new HandlingFormFacadeModel();

        if (!isset($_REQUEST['rmaSheetId']) || empty($_REQUEST['rmaSheetId'])) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'rmaSheetId cannot be null');
        } else {
            $arrParam['rmaSheetId'] = $_REQUEST['rmaSheetId'];
        }

        if (!isset($_REQUEST['status']) || empty($_REQUEST['status'])) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'status cannot be null');
        }

        $detaiRet = $model->findDetailRmaSheet(array('rmaSheetId' => $arrParam['rmaSheetId']));
        if (true !== $detaiRet->isSuccess()) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'Find RMA detail error:' . $detaiRet->getErrMsg());
        } else {
            $detaiData = $detaiRet->getData();
            if ($detaiData['status'] != $_REQUEST['status']) {
                $this->retJSON(OnePlusException::PARAM_ERROR, null, 'RMA status has changed!');
            }
        }

        $arrParam['logType'] = 322;
        $arrParam['currentOperator'] = $this->adminUser;
        $arrParam['status'] = 340; //等待退款
        $searchRet = $model->applyRefund($arrParam);

        if (true !== $searchRet->isSuccess()) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'Apply refund error:' . $searchRet->getErrMsg());
        } else {
            $this->retJSON(OnePlusServiceResponse::RET_SUCCESS, array(
                'status' => 340,
                'statusTxt' => 'Pending refund'
            ), '');
        }
    }

    /**
     * releaseDo
     */
    public function actionReleaseDo() {
        //操作权限
        if ($this->checkPower(PrivilegeCode::PRI_SITE_MANAGEMENTEUPDATE, false) == false) {
            $this->retJSON(OnePlusServiceResponse::RET_ERROR, null, '您没有RMA单的操作权限');
        }
        $arrParam = array();

        //调查接口查询数据
        $model = new HandlingFormFacadeModel();

        if (!isset($_REQUEST['rmaSheetId']) || empty($_REQUEST['rmaSheetId'])) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'rmaSheetId cannot be null');
        } else {
            $arrParam['rmaSheetId'] = $_REQUEST['rmaSheetId'];
        }

        if (!isset($_REQUEST['carrier']) || empty($_REQUEST['carrier'])) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'carrier cannot be null');
        } else {
            $arrParam['carrier'] = $_REQUEST['carrier'];
        }
        //if (isset($_REQUEST['carrier']) || !empty($_REQUEST['carrier'])) {
        //    $arrParam['carrier'] = $_REQUEST['carrier'];
        //}

        if (!isset($_REQUEST['trackingNo']) || empty($_REQUEST['trackingNo'])) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'trackingNo cannot be null');
        } else {
            $arrParam['trackingNo'] = $_REQUEST['trackingNo'];
        }
        //if (isset($_REQUEST['trackingNo']) || !empty($_REQUEST['trackingNo'])) {
        //    $arrParam['trackingNo'] = $_REQUEST['trackingNo'];
        //}

        if (isset($_REQUEST['carriagePrice']) || !empty($_REQUEST['carriagePrice'])) {
            $arrParam['carriagePrice'] = $_REQUEST['carriagePrice'] * 100;
        }

        if (!isset($_REQUEST['status']) || empty($_REQUEST['status'])) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'status cannot be null');
        }

        $detaiRet = $model->findDetailRmaSheet(array('rmaSheetId' => $arrParam['rmaSheetId']));
        if (true !== $detaiRet->isSuccess()) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'Find RMA detail error:' . $detaiRet->getErrMsg());
        } else {
            $detaiData = $detaiRet->getData();
            if ($detaiData['status'] != $_REQUEST['status']) {
                $this->retJSON(OnePlusException::PARAM_ERROR, null, 'RMA status has changed!');
            }
        }

        if ($_REQUEST['status'] != 60) {
            $arrParam['logType'] = 324;
            $arrParam['currentOperator'] = $this->adminUser;
            $arrParam['status'] = 60; //已完成
            $searchRet = $model->releaseDo($arrParam);
            if (true !== $searchRet->isSuccess()) {
                $this->retJSON(OnePlusException::PARAM_ERROR, null, 'Release error:' . $searchRet->getErrMsg());
            } else {
                //新增rma 物流运单 出仓
                $logisticsActiveModel = new LogisticsActiveRecord();
                $r = $logisticsActiveModel->add($arrParam['rmaSheetId'], $arrParam['carrier'], $arrParam['trackingNo'], 3);
                if ($r['ret'] > 0) {
                    $this->retJSON(OnePlusException::PARAM_ERROR, null, $r['errMsg']);
                }

                $this->retJSON(OnePlusServiceResponse::RET_SUCCESS, array(
                    'status' => 60,
                    'statusTxt' => 'Completed'
                ), '');
            }
        } else {
            $logisticsActiveModel = new LogisticsActiveRecord();
            $r = $logisticsActiveModel->add($arrParam['rmaSheetId'], $arrParam['carrier'], $arrParam['trackingNo'], 3);
            if ($r['ret'] > 0) {
                $this->retJSON(OnePlusException::PARAM_ERROR, null, $r['errMsg']);
            }

            $this->retJSON(OnePlusServiceResponse::RET_SUCCESS, array(
                'status' => 60,
                'statusTxt' => 'Completed'
            ), '');
        }
    }

    /**
     * findDetailRmaSheet
     */
    public function actionFindDetailRmaSheet() {
        //校验权限
        $this->checkPower(PrivilegeCode::PRI_SITE_MANAGEMENTE);
        $controlPrivilege = false;
        //操作权限
        if ($this->checkPower(PrivilegeCode::PRI_SITE_MANAGEMENTEUPDATE, false) == true) {
            $controlPrivilege = true;
        }
        $arrParam = array();

        //调查接口查询数据
        $model = new HandlingFormFacadeModel();

        if (!isset($_REQUEST['rmaSheetId']) || empty($_REQUEST['rmaSheetId'])) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'rmaSheetId cannot be null');
        } else {
            $arrParam['rmaSheetId'] = $_REQUEST['rmaSheetId'];
        }
        $searchRet = $model->findDetailRmaSheet($arrParam);
        if (true !== $searchRet->isSuccess()) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'Find RMA detail error:' . $searchRet->getErrMsg());
        } else {
            $data = $searchRet->getData();
            //所有类型及状态
            $allTypes = HandlingFormModel::getAllType();
            $allStatuss = HandlingFormModel::getAllStatus();
            $allMethods = HandlingFormModel::getAllMethod();
            $allTreatmentTypes = HandlingFormModel::getAllTreatmentTypes();
            $allTreatmentTypes2 = HandlingFormModel::getAllTreatmentTypes2();
            $allTreatmentTypeTxts = HandlingFormModel::getAllTreatmentTypeTxts();
            $allTreatmentTypeTxts2 = HandlingFormModel::getAllTreatmentTypeTxts2();

            //=======物流服务商动态获取aftership接口数据========
            //$allCarrier = HandlingFormModel::getAllCarrier();
            $allCarrier = $this->getAllCarrierNew();
            //===============================================

            $allPendingTypes = HandlingFormModel::getAllPendingType();
            $allRmaReason = HandlingFormModel::getAllRmaReason();
            if (isset($data['pendingType']) && in_array($data['pendingType'], array(
                    10,
                    20,
                    30,
                    40
                ))
            ) {
                $data['pendingReason'] = $allPendingTypes[$data['pendingType']];
            }

            if (isset($data['rmaReason']) && $data['rmaReason']) {
                $data['rmaReason'] = $allRmaReason[$data['rmaReason']];
            }

            if (isset($data['status']) && array_key_exists($data['status'], $allStatuss)) {
                $data['statusTxt'] = $allStatuss[$data['status']];
            }
            if (isset($data['type']) && array_key_exists($data['type'], $allTypes)) {
                $data['typeTxt'] = $allTypes[$data['type']];
            }
            if (isset($data['returnMethod']) && array_key_exists($data['returnMethod'], $allMethods)) {
                $data['returnMethodTxt'] = $allMethods[$data['returnMethod']];
            }
            if (isset($data['treatmentType']) && array_key_exists($data['treatmentType'], $allTreatmentTypeTxts)) {
                $data['treatmentTxt'] = $allTreatmentTypeTxts[$data['treatmentType']];
            }
            if (isset($data['treatmentType2']) && array_key_exists($data['treatmentType2'], $allTreatmentTypeTxts2)) {
                $data['treatmentTxt2'] = $allTreatmentTypeTxts2[$data['treatmentType2']];
            }

            //test data
            //$data['status'] = 290;

            if (isset($data['type']) && array_key_exists($data['type'], $allTreatmentTypes)) {
                $treatmentType = $allTreatmentTypes[$data['type']];
                $allTreatmentTypes = array();
                foreach ($treatmentType as $k => $v) {
                    $allTreatmentTypes[] = array(
                        'key' => $k,
                        'value' => $v
                    );
                }
                $data['treatmentTypes'] = $allTreatmentTypes;
            }

            if (isset($data['type']) && array_key_exists($data['type'], $allTreatmentTypes2)) {
                $treatmentType = $allTreatmentTypes2[$data['type']];
                $allTreatmentTypes2 = array();
                foreach ($treatmentType as $k => $v) {
                    $allTreatmentTypes2[] = array(
                        'key' => $k,
                        'value' => $v
                    );
                }
                $data['treatmentTypes2'] = $allTreatmentTypes2;
            }


            if (count($data['quotationCodes']) > 0) {
                $quotationCodesIns = array();
                $quotationCodess = array();
                foreach ($data['quotationCodes'] as $quotationCodes) {
                    if ($quotationCodes['checkType'] == 10) {
                        $quotationCodesIns[] = $quotationCodes;
                    }

                    if (!empty($quotationCodes['goodsCode'])) {
                        $quotationCodess[] = $quotationCodes;
                    }
                }
                $data['quotationCodesIns'] = $quotationCodesIns;
                $data['quotationCodes'] = $quotationCodess;
            } else {
                $data['quotationCodesIns'] = array();
            }

            if (count($data['rmaGoodsList']) > 0) {
                $imeiRecord = array();
                foreach ($data['rmaGoodsList'] as $rmaGoodsList) {
                    if (!empty($rmaGoodsList['imeiOld1'])) {
                        $imeiRecord[] = $rmaGoodsList;
                    }
                }
                $data['imeiRecord'] = $imeiRecord;
            } else {
                $data['imeiRecord'] = array();
            }

            //attachment
            $attachment = array();
            if (!empty($data['attachment'])) {
                $tmp = $tmpArr = array();
                $tmp = explode(';', $data['attachment']);
                if ($tmp && is_array($tmp)) {
                    foreach ($tmp as $val) {
                        $tmpArr[] = explode('-', $val);
                    }
                }
                $attachment = $tmpArr;
            }
            $data['attachment'] = $attachment;


            $data['controlPrivilege'] = $controlPrivilege;
            $data['allCarrier'] = $allCarrier;

            $this->retJSON(OnePlusServiceResponse::RET_SUCCESS, $data, '');
        }
    }

    /**
     * Inspectiondoing
     */
    public function actionInspectionFinish() {
        $params = Yii::app()->request->getParam('params', array());
        if (empty($params['attachment'])) {
            $params['attachment'] = ' ';
        }

        $model = new HandlingFormFacadeModel();
        $params['currentOperator'] = $this->adminUser;

        if (!isset($params['rmaSheetId']) || empty($params['rmaSheetId'])) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, ' RMA rmaSheetId null');
        }

        if (!isset($params['checkoutPrice']) < 0) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, ' The payment cannot be  less than zero！');
        }

        $detaiRet = $model->findDetailRmaSheet(array('rmaSheetId' => $params['rmaSheetId']));
        if (true !== $detaiRet->isSuccess()) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'Find RMA detail error:' . $detaiRet->getErrMsg());
        } else {
            $detaiData = $detaiRet->getData();
            //迭代1604去掉换货imei必输校验
            //            if ($params['treatmentType'] == 22) {
            //                if (isset($detaiData['rmaGoodsList']) && count($detaiData['rmaGoodsList']) > 0) {
            //                    foreach ($detaiData['rmaGoodsList'] as $rmaGoods) {
            //                        if (!empty($rmaGoods['imeiOld1'])) {
            //                            if (empty($rmaGoods['imeiNew1'])) {
            //                                $this->retJSON(OnePlusException::PARAM_ERROR, null, 'Please fill out the new imei and save .');
            //                            }
            //                        }
            //                    }
            //                }
            //            }

            if ($params['checkoutPrice'] < 0) {
                $this->retJSON(OnePlusException::PARAM_ERROR, null, 'The payment cannot be  less than zero!');
            } else {
                if (!empty($detaiData['depreciatePrice'])) {
                    if ($params['checkoutPrice'] > $detaiData['depreciatePrice']) {
                        $this->retJSON(OnePlusException::PARAM_ERROR, null, 'The payment is not greater than out of warranty!');
                    }
                }
            }

            if (count($detaiData['faultCodeList']) < 1) {
                $this->retJSON(OnePlusException::PARAM_ERROR, null, 'Malfunction Symptom is null!');
            }

            if (count($detaiData['causeCodeList']) < 1) {
                $this->retJSON(OnePlusException::PARAM_ERROR, null, 'Malfunction Cause is null!');
            }
        }

        $params['logType'] = 370;
        $params['status'] = 300;
        $searchRet = $model->inspect($params);
        if (true !== $searchRet->isSuccess()) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'RMA detail error:' . $searchRet->getErrMsg());
        }

        $this->retJSON(OnePlusServiceResponse::RET_SUCCESS, array(
            'status' => 300,
            'statusTxt' => 'Inspected'
        ), '');
    }

    /**
     * Inspectiondoing
     */
    public function actionInspectionSave() {
        $params = Yii::app()->request->getParam('params', array());
        if (empty($params['attachment'])) {
            $params['attachment'] = ' ';
        }

        $model = new HandlingFormFacadeModel();
        $params['currentOperator'] = $this->adminUser;

        if (!isset($params['rmaSheetId']) || empty($params['rmaSheetId'])) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, ' RMA rmaSheetId null');
        }

        $params['logType'] = 374;
        if (isset($params['logAttachment'])) {//附件
            if ($params['logAttachment'] == 1) {
                $params['logType'] = 382;
            } else if ($params['logAttachment'] == 2) {
                $params['logType'] = 384;
            }

            unset($params['logAttachment']);
        }

        $searchRet = $model->inspect($params);
        if (true !== $searchRet->isSuccess()) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'RMA detail error:' . $searchRet->getErrMsg());
        }
        $data = array();
        $this->retJSON(OnePlusServiceResponse::RET_SUCCESS, $data, '');
    }

    /**
     * Inspectiondoing
     */
    public function actionRepairSave() {
        $params = Yii::app()->request->getParam('params', array());
        $model = new HandlingFormFacadeModel();
        $params['currentOperator'] = $this->adminUser;

        if (!isset($params['rmaSheetId']) || empty($params['rmaSheetId'])) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, ' RMA rmaSheetId null');
        }

        $params['logType'] = 376;
        $searchRet = $model->inspect($params);
        if (true !== $searchRet->isSuccess()) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'RMA detail error:' . $searchRet->getErrMsg());
        }
        $data = array();
        $this->retJSON(OnePlusServiceResponse::RET_SUCCESS, $data, '');
    }

    /**
     * Inspectiondoing
     */
    public function actionRepairFinish() {
        $params = Yii::app()->request->getParam('params', array());
        $model = new HandlingFormFacadeModel();
        $params['currentOperator'] = $this->adminUser;

        if (!isset($params['rmaSheetId']) || empty($params['rmaSheetId'])) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, ' RMA rmaSheetId null');
        }
        if (!isset($params['softwareVersion']) || empty($params['softwareVersion'])) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, ' RMA softwareVersion null');
        }
        if (!isset($params['status']) || empty($params['status']) || $params['status'] != 350) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, ' RMA status null');
        }

        $detaiData = array();
        $detaiRet = $model->findDetailRmaSheet(array('rmaSheetId' => $params['rmaSheetId']));
        if (true !== $detaiRet->isSuccess()) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'Find RMA detail error:' . $detaiRet->getErrMsg());
        } else {
            $detaiData = $detaiRet->getData();

            $quotationCodesImei = false;
            if (isset($detaiData['quotationCodes']) && count($detaiData['quotationCodes']) > 0) {
                foreach ($detaiData['quotationCodes'] as $quotationCodes) {
                    if (!empty($quotationCodes['goodsCode']) && (strpos($quotationCodes['goodsCode'], '01') === 0 || strpos($quotationCodes['goodsCode'], '0413') === 0 || strpos($quotationCodes['goodsCode'], '0403') === 0)) {
                        $quotationCodesImei = true;
                        break;
                    }
                }
            }

            if (isset($detaiData['rmaGoodsList']) && count($detaiData['rmaGoodsList']) > 0) {
                foreach ($detaiData['rmaGoodsList'] as $rmaGoods) {
                    if (!empty($rmaGoods['imeiOld1'])) {
                        if (empty($rmaGoods['imeiNew1']) && $quotationCodesImei) {
                            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'Please fill out the new imei and save .');
                        }
                    }
                }
            }
        }

        $params['logType'] = 372;
        $searchRet = $model->inspect($params);
        if (true !== $searchRet->isSuccess()) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'RMA detail error:' . $searchRet->getErrMsg());
        }

        $this->retJSON(OnePlusServiceResponse::RET_SUCCESS, array(
            'status' => 350,
            'statusTxt' => 'Repaired'
        ), '');
    }

    /**
     * switchRmaCheckCode
     */
    public function actionSwitchRmaCheckCode() {
        $params = Yii::app()->request->getParam('params', array());
        $model = new HandlingFormFacadeModel();
        $params['currentOperator'] = $this->adminUser;
        $searchRet = $model->switchRmaCheckCode($params);
        if (true !== $searchRet->isSuccess()) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'Find RMA detail error:' . $searchRet->getErrMsg());
        }
        $data['findNonAssurance'] = $searchRet->getData();

        $this->retJSON(OnePlusServiceResponse::RET_SUCCESS, $data, '');
    }


    public function getPhoneTypeByRma($param) {
        $typeArray = array();
        $_ret = RedisModels::getInstance();
        $PhoneSkus = $_ret->getRdsPhoneSkuProtection(); //清楚缓存
        foreach ($param as $k => $v) {
            if (!empty($v['goodsCode'])) {
                foreach ($PhoneSkus as $k1 => $v1) {
                    if ((strpos($v1['skus'], $v['goodsCode']) !== FALSE) && (array_search($v1['code'], $typeArray) === FALSE)) {
                        $typeArray[] = $v1['code'];
                    }
                }
            }
        }
        $types = '';
        if (!empty($typeArray)) {
            $types = implode(',', $typeArray) . ',100';
        }
        return $types;
    }

    /**
     * InspectionInfo
     */
    public function actionInspectionInfo() {

        //校验权限
        $this->checkPower(PrivilegeCode::PRI_SITE_MANAGEMENTE);

        $type = Yii::app()->request->getParam('sel_type', 'all');
        $arrParam = array();
        //查询保价项
        $model = new HandlingFormFacadeModel();

        $allQuotationTypeCh = HandlingFormModel::getAllQuotationTypeCh();
        $allQuotationTypeEn = HandlingFormModel::getAllQuotationTypeEn();
        $allMalfunctionTypeCh = HandlingFormModel::getAllMalfunctionTypeCh();
        $allMalfunctionTypeEn = HandlingFormModel::getAllMalfunctionTypeEn();

        $currency = '';
        $language = '';

        $cookie = Yii::app()->request->getCookies();
        if (isset($cookie['langKey'])) {
            $langKey = $cookie['langKey']->value;
        } else {
            $langKey = 'cn';
        }
        if ($langKey == 'en') {
            $language = 'en_US';
        } else {
            $language = 'zh_CN';
        }


        $detaiRet = $model->findDetailRmaSheet(array('rmaSheetId' => $_REQUEST['rmaSheetId']));
        if (true !== $detaiRet->isSuccess()) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'Find RMA detail error:' . $detaiRet->getErrMsg());
        } else {
            $detaiData = $detaiRet->getData();
            $currency = $detaiData['currency'];
        }

        if ($type == 'all' || $type == 'qus') {
            $params = Yii::app()->request->getParam('params', array());
            if (empty($params)) {
                $params = array('pageSize' => 1000);
            } else {
                $params['pageSize'] = 1000;
            }

            //获取rma单据明细商品类型
            $machineType = $this->getPhoneTypeByRma($detaiData['rmaGoodsList']);

            if (!empty($machineType)) {
                $params['machineTypes'] = $machineType;
            }

            $params['showMode'] = 1; //金额大于0
            $params['currency'] = $currency;
            $params['status'] = '10';
            //            $params['sortByParamNames'] = 'id';
            //            $params['sortByParamTypes'] = 'asc';

            $searchRet = $model->findQuotationCode($params);
            if (true !== $searchRet->isSuccess()) {
                $this->retJSON(OnePlusException::PARAM_ERROR, null, 'Find RMA detail error:' . $searchRet->getErrMsg());
            }
            $data['findNonAssurance'] = $searchRet->getData();

            if (count($data['findNonAssurance']) > 0) {
                foreach ($data['findNonAssurance'] as $k => $v) {
                    if ($langKey == 'en') {
                        if (isset($v['type']) && array_key_exists($v['type'], $allQuotationTypeEn)) {
                            $data['findNonAssurance'][$k]['typeTxt'] = $allQuotationTypeEn[$v['type']];
                        }
                    } else {
                        if (isset($v['type']) && array_key_exists($v['type'], $allQuotationTypeCh)) {
                            $data['findNonAssurance'][$k]['typeTxt'] = $allQuotationTypeCh[$v['type']];
                        }
                    }
                }
            }
        }
        if ($type == 'all' || $type == 'Symptom') {
            $params = Yii::app()->request->getParam('params', array());
            if (empty($params)) {
                $params = array(
                    'pageSize' => 1000,
                    'codeType' => 10
                );
            } else {
                $params['pageSize'] = 1000;
                $params['codeType'] = 10;
            }

            $params['language'] = $language;
            $params['status'] = '10';
            //            $params['sortByParamNames'] = 'id';
            //            $params['sortByParamTypes'] = 'asc';
            //查询故障代码
            $searchRet = $model->findMalfunctCode($params);
            if (true !== $searchRet->isSuccess()) {
                $this->retJSON(OnePlusException::PARAM_ERROR, null, 'Find RMA detail error:' . $searchRet->getErrMsg());
            }
            $data['MalfunctionSymptom1'] = $searchRet->getData();

            if (count($data['MalfunctionSymptom1']) > 0) {
                foreach ($data['MalfunctionSymptom1'] as $k => $v) {
                    if ($langKey == 'en') {
                        if (isset($v['type']) && array_key_exists($v['type'], $allMalfunctionTypeEn)) {
                            $data['MalfunctionSymptom1'][$k]['typeTxt'] = $allMalfunctionTypeEn[$v['type']];
                        }
                    } else {
                        if (isset($v['type']) && array_key_exists($v['type'], $allMalfunctionTypeCh)) {
                            $data['MalfunctionSymptom1'][$k]['typeTxt'] = $allMalfunctionTypeCh[$v['type']];
                        }
                    }
                }
            }
        }
        if ($type == 'all' || $type == 'Cause') {
            $params = Yii::app()->request->getParam('params', array());
            if (empty($params)) {
                $params = array(
                    'pageSize' => 1000,
                    'codeType' => 20
                );
            } else {
                $params['pageSize'] = 1000;
                $params['codeType'] = 20;
            }
            $params['language'] = $language;
            $params['status'] = '10';
            //            $params['sortByParamNames'] = 'descr';
            //            $params['sortByParamTypes'] = 'asc';
            //查询原因代码
            $searchRet = $model->findMalfunctCode($params);
            if (true !== $searchRet->isSuccess()) {
                $this->retJSON(OnePlusException::PARAM_ERROR, null, 'Find RMA detail error:' . $searchRet->getErrMsg());
            }
            $data['MalfunctionSymptom2'] = $searchRet->getData();

            if (count($data['MalfunctionSymptom2']) > 0) {
                foreach ($data['MalfunctionSymptom2'] as $k => $v) {
                    if ($langKey == 'en') {
                        if (isset($v['type']) && array_key_exists($v['type'], $allMalfunctionTypeEn)) {
                            $data['MalfunctionSymptom2'][$k]['typeTxt'] = $allMalfunctionTypeEn[$v['type']];
                        }
                    } else {
                        if (isset($v['type']) && array_key_exists($v['type'], $allMalfunctionTypeCh)) {
                            $data['MalfunctionSymptom2'][$k]['typeTxt'] = $allMalfunctionTypeCh[$v['type']];
                        }
                    }
                }
            }
        }

        $data['CodeType'] = HandlingFormModel::getAllCodeType();
        $this->retJSON(OnePlusServiceResponse::RET_SUCCESS, $data, '');
    }

    public function ationNotifyRmaSheetValidateResult() {
        $data = array();
        $params = Yii::app()->request->getParam('params', array());
        if (empty($params)) {
            $params = array(
                'pageSize' => 1000,
                'codeType' => 20
            );
        } else {
            $params['pageSize'] = 1000;
            $params['codeType'] = 20;
        }
        $model = new HandlingFormFacadeModel();
        $model->notifyRmaSheetValidateResult($params);
        if (true !== $searchRet->isSuccess()) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'Find RMA detail error:' . $searchRet->getErrMsg());
        }
        $data = $searchRet->getData();
        $this->retJSON(OnePlusServiceResponse::RET_SUCCESS, $data, '');
    }

    /*
     * 中文转UTF-8
     * */

    public function turnCode($param) {
        //return mb_convert_encoding($param,"CP936","UTF-8");
        $param = str_replace('&#039;', '&apos;', htmlspecialchars($param, ENT_QUOTES));
        return $param;
        //return mb_convert_encoding($param, 'gb2312', 'utf-8');
    }

    /**
     * 导出EXCEL
     */
    public function export_csv($filename, $data) {
        header("Content-type:text/csv");
        header("Content-Disposition:attachment;filename=" . $filename);
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header('Expires:0');
        header('Pragma:public');
        echo $data;
        exit;
    }

    /**
     * 更新申请单物料记录-IMei或者条码
     * treatSheetId,materielCode,materielName,materielDescr
     * 系统填充以下字段:serviceNodeCode,quantity
     */
    public function actionAjaxUpdateApplyFormMaterial() {
        //操作权限
        if ($this->checkPower(PrivilegeCode::PRI_SITE_MANAGEMENTEUPDATE, false) == false) {
            $this->retJSON(OnePlusServiceResponse::RET_ERROR, null, '您没有RMA单的操作权限');
        }

        if (empty($_REQUEST['rmaSheetId'])) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'rmaSheetId is null');
        }
        if (empty($_REQUEST['imeiCode'])) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'New IMEI is null');
        }
        if (empty($_REQUEST['imeiOld'])) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'Old IMEI is null');
        }

        $imeiCode = $_REQUEST['imeiCode'];
        //查询WMS是否存在该IMEI号 

        $model = new HandlingFormFacadeModel();
        $detaiData = array();
        $detaiRet = $model->findDetailRmaSheet(array('rmaSheetId' => $_REQUEST['rmaSheetId']));
        if (true !== $detaiRet->isSuccess()) {
            $this->retJSON(OnePlusException::PARAM_ERROR, null, 'Find RMA detail error:' . $detaiRet->getErrMsg());
        } else {
            $detaiData = $detaiRet->getData();
            if (isset($detaiData['rmaGoodsList']) && count($detaiData['rmaGoodsList']) > 0) {
                foreach ($detaiData['rmaGoodsList'] as $rmaGoods) {
                    if ($rmaGoods['imeiOld1'] == $imeiCode || $rmaGoods['imeiOld2'] == $imeiCode) {
                        $this->retJSON(OnePlusException::PARAM_ERROR, null, 'New imei can not equal to Previous IMEI.');
                    }
                }
            }
        }

        //校验wms imei
        //$params = array(
        //    'imei' => $imeiCode,
        //);
        //$imeiDetail = array();
        //$storageRet = Yii::app()->service->storageManage->queryImei($params);
        //if ($storageRet['ret'] == OpError::ERR_NONE) {//ps:java接口返回0是失败,1是成功,这个和OpError.php里的定义是相反的
        //    if (!isset($storageRet['data']) || count($storageRet['data']) == 0) {
        //        $this->retJSON(OnePlusException::PARAM_ERROR, null, '该IMEI号不存在');
        //    } else {
        //        $imeiDetail = $storageRet['data'][0];
        //        if (!isset($imeiDetail['state']) && $imeiDetail['state'] != ExtendedWarrantyConfig::STORAGE_STATUS_OUTSTORAGE) {
        //            $this->retJSON(OnePlusException::PARAM_ERROR, null, '该IMEI号尚未出库!');
        //        }
        //    }
        //} else {
        //    $this->retJSON(OnePlusException::PARAM_ERROR, null, '根据IMEI号查询IMEI详细信息失败');
        //}

        $rmaGoodsList = array();
        foreach ($detaiData['rmaGoodsList'] as $rmaGoods) {
            if ($rmaGoods['imeiOld1'] == $_REQUEST['imeiOld'] || $rmaGoods['imeiOld2'] == $_REQUEST['imeiOld']) {
                //$rmaGoodsList[] = array(
                //    "imeiOld1" => $rmaGoods['imeiOld1'],
                //    "imeiOld2" => $rmaGoods['imeiOld2'],
                //    "imeiNew1" => $imeiDetail['imei'],
                //    "imeiNew2" => $imeiDetail['imei2']);

                $rmaGoodsList[] = array(
                    "imeiOld1" => $rmaGoods['imeiOld1'],
                    "imeiOld2" => $rmaGoods['imeiOld2'],
                    "imeiNew1" => $imeiCode,
                    "imeiNew2" => $imeiCode
                );
            }
        }

        $applyFormMaterial = array(
            'rmaSheetId' => $_REQUEST['rmaSheetId'],
            'rmaGoodsList' => $rmaGoodsList,
            'currentOperator' => $this->adminUser
        );

        //更新imei
        $ret = $model->updateApplyFormMaterial($applyFormMaterial);
        if (true === $ret->isSuccess()) {
            $this->retJSON(OnePlusServiceResponse::RET_SUCCESS, $ret->getData(), '');
        } else {
            $this->retJSON($ret->getErrCode(), null, $ret->getErrMsg());
        }
    }

    /**
     * 翻译错误
     */
    public function errorReturns($mes) {
        $cookie = Yii::app()->request->getCookies();
        if (isset($cookie['langKey'])) {
            $langKey = $cookie['langKey']->value;
        } else {
            $langKey = 'cn';
        }
        if ($langKey == 'en') {
            if ($mes == 'ERROR_VALIDATE') {
                return ERROR_VALIDATE_EN;
            }
            if ($mes == 'ERROR_STATUS') {
                return ERROR_STATUS_EN;
            }
            if ($mes == 'ERROR_LOGWRITE') {
                return ERROR_LOGWRITE_EN;
            }
            if ($mes == 'ERROR_LOGIN') {
                return ERROR_LOGIN_EN;
            }
            if ($mes == 'ERROR_NOT_POWER') {
                return ERROR_NOT_POWER_EN;
            }
        } else {
            if ($mes == 'ERROR_VALIDATE') {
                return ERROR_VALIDATE;
            }
            if ($mes == 'ERROR_STATUS') {
                return ERROR_STATUS;
            }
            if ($mes == 'ERROR_LOGWRITE') {
                return ERROR_LOGWRITE;
            }
            if ($mes == 'ERROR_LOGIN') {
                return ERROR_LOGIN;
            }
            if ($mes == 'ERROR_NOT_POWER') {
                return ERROR_NOT_POWER;
            }
        }
    }
}