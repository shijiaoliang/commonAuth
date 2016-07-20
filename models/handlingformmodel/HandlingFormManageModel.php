<?php
Yii::import('application.models.common.AfterSalesService', true);
Yii::import('application.models.handlingformmodel.HandlingFormModel', true);

/**
 * 处理单管理Model
 */
class HandlingFormManageModel {
    /**
     * 售后服务对象
     * @var AfterSalesService
     */
    private $_afterSalesService;

    /**
     * 添加日志
     * @param string $msg 日志消息
     * @param int $level 日志等级
     * @return    void
     */
    private function addLog($msg, $level = Log::LEVEL_WARN) {
        Log::addLog($msg, __CLASS__, $level);
    }

    /**
     * 构造函数
     */
    public function __construct() {
        $serviceUrl = AfterSalesService::getServiceUrl(AfterSalesService::AFTER_SALES_SERVICE_KEY);
        $this->_afterSalesService = new AfterSalesService($serviceUrl);
    }

    /**
     * 分页查询处理单
     * @param HandlingFormListForm $condition
     * @return    OnePlusServiceResponse    data为二维数组,每项有以下key:
     *            treatSheetId, imeiNo, orderFlow, type, status, sourceType
     * , receiveUser, receiveTime, reserveTime, engineer, serviceNodeCode
     * , sender, senderTel, surfaceDescr, malfunctionDescr, attachment
     * , inspectResult, initialMemo, receiptNo, recheckTime
     * , softwareVersion, totalPrice, discountPrice, attitude
     * , imeiNew, handleTime, handleMemo, pickupTime, pickuper, pickupMemo
     */
    public function queryList($arrParam, $page, $pageSize) {
        $ret = $this->_afterSalesService->findTreatSheet($arrParam, $page, $pageSize);

        if (true !== $ret->isSuccess()) {
            $this->addLog(__METHOD__ . ':' . __LINE__ . ',分页查询处理单错误,输入参数:' . var_export($arrParam, 1) . ',结果:' . var_export($ret, 1));
            return $ret;
        }
        return $ret;
    }

    /**
     * 分页查询处理单日志
     * @param HandlingFormListForm $condition
     */
    public function findRmaSheetLog($arrParam, $page, $pageSize) {
        $ret = $this->_afterSalesService->findRmaSheetLog($arrParam, $page, $pageSize);

        if (true !== $ret->isSuccess()) {
            $this->addLog(__METHOD__ . ':' . __LINE__ . ',分页查询处理单错误,输入参数:' . var_export($arrParam, 1) . ',结果:' . var_export($ret, 1));
            return $ret;
        }
        return $ret;
    }

    /**
     * 分页查询处理单
     * @param HandlingFormListForm $condition
     * @return    OnePlusServiceResponse    data为二维数组,每项有以下key:
     *            treatSheetId, imeiNo, orderFlow, type, status, sourceType
     * , receiveUser, receiveTime, reserveTime, engineer, serviceNodeCode
     * , sender, senderTel, surfaceDescr, malfunctionDescr, attachment
     * , inspectResult, initialMemo, receiptNo, recheckTime
     * , softwareVersion, totalPrice, discountPrice, attitude
     * , imeiNew, handleTime, handleMemo, pickupTime, pickuper, pickupMemo
     */
    public function queryRepaireReplacementList($arrParam, $page, $pageSize) {
        $ret = $this->_afterSalesService->queryRepaireReplacementList($arrParam, $page, $pageSize);
        if (true !== $ret->isSuccess()) {
            $this->addLog(__METHOD__ . ':' . __LINE__ . ',分页查询处理单非保价项错误,输入参数:' . var_export($arrParam, 1) . ',结果:' . var_export($ret, 1));
            return $ret;
        }
        return $ret;
    }

    public function sendTrackingNo($arrParam) {
        $ret = $this->_afterSalesService->sendTrackingNo($arrParam);
        if (true !== $ret->isSuccess()) {
            $this->addLog(__METHOD__ . ':' . __LINE__ . ',RMA send TrackingNo错误,输入参数:' . var_export($arrParam, 1) . ',结果:' . var_export($ret, 1));
            return $ret;
        }
        return $ret;
    }

    public function cancleRma($arrParam) {
        $ret = $this->_afterSalesService->cancleRma($arrParam);
        if (true !== $ret->isSuccess()) {
            $this->addLog(__METHOD__ . ':' . __LINE__ . ',RMA cancle错误,输入参数:' . var_export($arrParam, 1) . ',结果:' . var_export($ret, 1));
            return $ret;
        }
        return $ret;
    }

    public function acceptArrangement($arrParam) {
        $ret = $this->_afterSalesService->acceptArrangement($arrParam);
        if (true !== $ret->isSuccess()) {
            $this->addLog(__METHOD__ . ':' . __LINE__ . ',RMA accept Arrangement错误,输入参数:' . var_export($arrParam, 1) . ',结果:' . var_export($ret, 1));
            return $ret;
        }
        return $ret;
    }

    public function pickup($arrParam) {
        $ret = $this->_afterSalesService->pickup($arrParam);
        if (true !== $ret->isSuccess()) {
            $this->addLog(__METHOD__ . ':' . __LINE__ . ',RMA pickup错误,输入参数:' . var_export($arrParam, 1) . ',结果:' . var_export($ret, 1));
            return $ret;
        }
        return $ret;
    }

    public function dropOff($arrParam) {
        $ret = $this->_afterSalesService->dropOff($arrParam);
        if (true !== $ret->isSuccess()) {
            $this->addLog(__METHOD__ . ':' . __LINE__ . ',RMA dropOff错误,输入参数:' . var_export($arrParam, 1) . ',结果:' . var_export($ret, 1));
            return $ret;
        }
        return $ret;
    }

    public function applyCancelDo($arrParam) {
        $ret = $this->_afterSalesService->applyCancelDo($arrParam);
        if (true !== $ret->isSuccess()) {
            $this->addLog(__METHOD__ . ':' . __LINE__ . ',RMA apply Cancel错误,输入参数:' . var_export($arrParam, 1) . ',结果:' . var_export($ret, 1));
            return $ret;
        }
        return $ret;
    }

    public function receive($arrParam) {
        $ret = $this->_afterSalesService->receive($arrParam);
        if (true !== $ret->isSuccess()) {
            $this->addLog(__METHOD__ . ':' . __LINE__ . ',RMA receive错误,输入参数:' . var_export($arrParam, 1) . ',结果:' . var_export($ret, 1));
            return $ret;
        }
        return $ret;
    }

    public function sendQuotation($arrParam) {
        $ret = $this->_afterSalesService->sendQuotation($arrParam);
        if (true !== $ret->isSuccess()) {
            $this->addLog(__METHOD__ . ':' . __LINE__ . ',RMA send quotation错误,输入参数:' . var_export($arrParam, 1) . ',结果:' . var_export($ret, 1));
            return $ret;
        }
        return $ret;
    }

    public function replaceDo($arrParam) {
        $ret = $this->_afterSalesService->replaceDo($arrParam);
        if (true !== $ret->isSuccess()) {
            $this->addLog(__METHOD__ . ':' . __LINE__ . ',RMA replace错误,输入参数:' . var_export($arrParam, 1) . ',结果:' . var_export($ret, 1));
            return $ret;
        }
        return $ret;
    }

    public function repair($arrParam) {
        $ret = $this->_afterSalesService->repair($arrParam);
        if (true !== $ret->isSuccess()) {
            $this->addLog(__METHOD__ . ':' . __LINE__ . ',RMA repair错误,输入参数:' . var_export($arrParam, 1) . ',结果:' . var_export($ret, 1));
            return $ret;
        }
        return $ret;
    }

    public function inspect($arrParam) {
        $ret = $this->_afterSalesService->inspect($arrParam);
        if (true !== $ret->isSuccess()) {
            $this->addLog(__METHOD__ . ':' . __LINE__ . ',RMA inspect错误,输入参数:' . var_export($arrParam, 1) . ',结果:' . var_export($ret, 1));
            return $ret;
        }
        return $ret;
    }

    public function updateApplyFormMaterial($arrParam) {
        $ret = $this->_afterSalesService->updateApplyFormMaterial($arrParam);
        if (true !== $ret->isSuccess()) {
            $this->addLog(__METHOD__ . ':' . __LINE__ . ',RMA update IMEI错误,输入参数:' . var_export($arrParam, 1) . ',结果:' . var_export($ret, 1));
            return $ret;
        }
        return $ret;
    }

    public function switchRmaCheckCode($arrParam) {
        $ret = $this->_afterSalesService->switchRmaCheckCode($arrParam);
        if (true !== $ret->isSuccess()) {
            $this->addLog(__METHOD__ . ':' . __LINE__ . ',RMA inspect选中错误,输入参数:' . var_export($arrParam, 1) . ',结果:' . var_export($ret, 1));
            return $ret;
        }
        return $ret;
    }

    public function applyRefund($arrParam) {
        $ret = $this->_afterSalesService->applyRefund($arrParam);
        if (true !== $ret->isSuccess()) {
            $this->addLog(__METHOD__ . ':' . __LINE__ . ',RMA apply refund错误,输入参数:' . var_export($arrParam, 1) . ',结果:' . var_export($ret, 1));
            return $ret;
        }
        return $ret;
    }

    public function sendBackDo($arrParam) {
        $ret = $this->_afterSalesService->sendBackDo($arrParam);
        if (true !== $ret->isSuccess()) {
            $this->addLog(__METHOD__ . ':' . __LINE__ . ',RMA sendBack错误,输入参数:' . var_export($arrParam, 1) . ',结果:' . var_export($ret, 1));
            return $ret;
        }
        return $ret;
    }

    public function releaseDo($arrParam) {
        $ret = $this->_afterSalesService->releaseDo($arrParam);
        if (true !== $ret->isSuccess()) {
            $this->addLog(__METHOD__ . ':' . __LINE__ . ',RMA releaseDo错误,输入参数:' . var_export($arrParam, 1) . ',结果:' . var_export($ret, 1));
            return $ret;
        }
        return $ret;
    }

    public function inputTrackingNoDo($arrParam) {
        $ret = $this->_afterSalesService->inputTrackingNoDo($arrParam);
        if (true !== $ret->isSuccess()) {
            $this->addLog(__METHOD__ . ':' . __LINE__ . ',RMA inputTrackingNoDo错误,输入参数:' . var_export($arrParam, 1) . ',结果:' . var_export($ret, 1));
            return $ret;
        }
        return $ret;
    }

    public function findDetailRmaSheet($arrParam) {
        $ret = $this->_afterSalesService->findDetailRmaSheet($arrParam);
        if (true !== $ret->isSuccess()) {
            $this->addLog(__METHOD__ . ':' . __LINE__ . ',RMA findDetail错误,输入参数:' . var_export($arrParam, 1) . ',结果:' . var_export($ret, 1));
            return $ret;
        }
        return $ret;
    }

    public function findMalfunctCode($arrParam) {
        $ret = $this->_afterSalesService->findMalfunctCode($arrParam);
        if (true !== $ret->isSuccess()) {
            $this->addLog(__METHOD__ . ':' . __LINE__ . ',RMA findDetail错误,输入参数:' . var_export($arrParam, 1) . ',结果:' . var_export($ret, 1));
            return $ret;
        }
        return $ret;
    }

    public function findQuotationCode($arrParam) {
        //$startTime1 = Func::javaStrToTime($startTime1);
        $ret = $this->_afterSalesService->findQuotationCode($arrParam);
        if (true !== $ret->isSuccess()) {
            $this->addLog(__METHOD__ . ':' . __LINE__ . ',RMA findDetail错误,输入参数:' . var_export($arrParam, 1) . ',结果:' . var_export($ret, 1));
            return $ret;
        }
        return $ret;
    }

    public function notifyRmaSheetValidateResult($arrParam) {
        $ret = $this->_afterSalesService->notifyRmaSheetValidateResult($arrParam);
        if (true !== $ret->isSuccess()) {
            $this->addLog(__METHOD__ . ':' . __LINE__ . ',RMA 通知RMA单据检测结果失败:' . var_export($arrParam, 1) . ',结果:' . var_export($ret, 1));
            return $ret;
        }
        return $ret;
    }

    /**
     * 根据处理单号查询处理单
     * @param string $handlingFormNO 处理单号
     * @return    OnePlusServiceResponse
     */
    public function queryHandlingFormNO($handlingFormNO) {
        $ret = $this->_afterSalesService->findDetailTreatSheet($handlingFormNO);
        if (true !== $ret->isSuccess()) {
            $this->addLog(__METHOD__ . ':' . __LINE__ . ',根据处理单号查询处理单错误,输入参数:' . $handlingFormNO . ',结果:' . var_export($ret, 1));
            return $ret;
        }
        //转成标准对象
        $object = (Object)$ret->getData();
        $data = ModelConverter::convertFromObject('HandlingFormModel', $object);
        $ret->setData($data);
        return $ret;
    }

    /**
     * 通过物料申请单ID查询
     * @param string $applyFormID 申请单ID
     * @return    OnePlusServiceResponse
     */
    public function queryByApplyFormID($applyFormID) {
        //先根据物料ID查询处理单ID
        $ret = $this->_afterSalesService->findDetailApplySheet($applyFormID);
        if (true !== $ret->isSuccess()) {
            $this->addLog(__METHOD__ . ':' . __LINE__ . ',根据物料申请单ID查询物料申请单错误,输入参数:' . $applyFormID . ',结果:' . var_export($ret, 1));
            return $ret;
        }
        $data = $ret->getData();
        if ((true !== is_array($data)) || (count($data) < 1)) {
            $this->addLog(__METHOD__ . ':' . __LINE__ . ',根据物料申请单ID查询物料申请单不存在,输入参数:' . $applyFormID . ',结果:' . var_export($ret, 1));
            $ret->setFailed(OnePlusException::PARAM_ERROR);
            $ret->setErrMsg('物料申请单不存在');
            return $ret;
        }
        $handlingFormNO = $data['treatSheetId'];
        //查询详情
        $ret = $this->queryHandlingFormNO($handlingFormNO);
        return $ret;
    }

    /**
     * 增加pending
     * addPending
     * @param $arrParam
     * @return OnePlusServiceResponse
     */
    public function addPending($arrParam) {
        $ret = $this->_afterSalesService->addPending($arrParam);
        if (true !== $ret->isSuccess()) {
            $this->addLog(__METHOD__ . ':' . __LINE__ . ',新增pending错误,输入参数:' . var_export($arrParam, 1) . ',结果:' . var_export($ret, 1));
            return $ret;
        }
        return $ret;
    }

    /**
     * 取消pending
     * cancelPending
     * @param $arrParam
     * @return OnePlusServiceResponse
     */
    public function cancelPending($arrParam) {
        $ret = $this->_afterSalesService->cancelPending($arrParam);
        if (true !== $ret->isSuccess()) {
            $this->addLog(__METHOD__ . ':' . __LINE__ . ',取消pending错误,输入参数:' . var_export($arrParam, 1) . ',结果:' . var_export($ret, 1));
            return $ret;
        }
        return $ret;
    }
}