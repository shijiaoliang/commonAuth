<?php
Yii::import('application.models.handlingformmodel.HandlingFormManageModel', true);
Yii::import('application.models.handlingformmodel.HandlingFormListForm', true);
Yii::import('application.models.order.OrderModel', true);

/**
 * 处理单门面Model
 */
class HandlingFormFacadeModel {
    /**
     * 处理单管理model
     * @var HandlingFormManageModel
     */
    private $_handleFormManageModel;

    /**
     * 当前用户
     * @var string
     */
    private $_currentUser = '';

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
        $this->_handleFormManageModel = new HandlingFormManageModel();
    }

    /**
     * 设置当前用户
     * @param string $currentUser 当前用户
     */
    public function setCurrentUser($currentUser) {
        $this->_currentUser = $currentUser;
    }

    /**
     * 分页查询处理单
     * @param HandlingFormListForm $condition
     * @return OnePlusServiceResponse    data为multitype:HandlingFormModel
     */
    public function queryList($arrParam, $page, $pageSize) {
        return $this->_handleFormManageModel->queryList($arrParam, $page, $pageSize);
    }

    /**
     * 查询处理单日志
     * @param HandlingFormListForm $condition
     * @return OnePlusServiceResponse    data为multitype:HandlingFormModel
     */
    public function findRmaSheetLog($arrParam, $page, $pageSize) {
        return $this->_handleFormManageModel->findRmaSheetLog($arrParam, $page, $pageSize);
    }

    /**
     * 分页处理单非保价项
     * @param HandlingFormListForm $condition
     * @return OnePlusServiceResponse    data为multitype:HandlingFormModel
     */
    public function queryRepaireReplacementList($arrParam, $page, $pageSize) {
        return $this->_handleFormManageModel->queryRepaireReplacementList($arrParam, $page, $pageSize);
    }

    /**
     * sendTrackingNo
     * @param HandlingFormListForm $condition
     * @return OnePlusServiceResponse    data为multitype:HandlingFormModel
     */
    public function sendTrackingNo($arrParam) {
        return $this->_handleFormManageModel->sendTrackingNo($arrParam);
    }

    /**
     * cancleRma
     * @param HandlingFormListForm $condition
     * @return OnePlusServiceResponse    data为multitype:HandlingFormModel
     */
    public function cancleRma($arrParam) {
        return $this->_handleFormManageModel->cancleRma($arrParam);
    }

    /**
     * acceptArrangement
     * @param HandlingFormListForm $condition
     * @return OnePlusServiceResponse    data为multitype:HandlingFormModel
     */
    public function acceptArrangement($arrParam) {
        return $this->_handleFormManageModel->acceptArrangement($arrParam);
    }

    /**
     * pickup
     * @param HandlingFormListForm $condition
     * @return OnePlusServiceResponse    data为multitype:HandlingFormModel
     */
    public function pickup($arrParam) {
        return $this->_handleFormManageModel->pickup($arrParam);
    }

    /**
     * dropOff
     * @param HandlingFormListForm $condition
     * @return OnePlusServiceResponse    data为multitype:HandlingFormModel
     */
    public function dropOff($arrParam) {
        return $this->_handleFormManageModel->dropOff($arrParam);
    }

    /**
     * applyCancelDo
     * @param HandlingFormListForm $condition
     * @return OnePlusServiceResponse    data为multitype:HandlingFormModel
     */
    public function applyCancelDo($arrParam) {
        return $this->_handleFormManageModel->applyCancelDo($arrParam);
    }

    /**
     * receive
     * @param HandlingFormListForm $condition
     * @return OnePlusServiceResponse    data为multitype:HandlingFormModel
     */
    public function receive($arrParam) {
        return $this->_handleFormManageModel->receive($arrParam);
    }

    /**
     * sendQuotation
     * @param HandlingFormListForm $condition
     * @return OnePlusServiceResponse    data为multitype:HandlingFormModel
     */
    public function sendQuotation($arrParam) {
        return $this->_handleFormManageModel->sendQuotation($arrParam);
    }

    /**
     * replaceDo
     * @param HandlingFormListForm $condition
     * @return OnePlusServiceResponse    data为multitype:HandlingFormModel
     */
    public function replaceDo($arrParam) {
        return $this->_handleFormManageModel->replaceDo($arrParam);
    }

    /**
     * repair
     * @param HandlingFormListForm $condition
     * @return OnePlusServiceResponse    data为multitype:HandlingFormModel
     */
    public function repair($arrParam) {
        return $this->_handleFormManageModel->repair($arrParam);
    }

    /**
     * inspect
     * @param HandlingFormListForm $condition
     * @return OnePlusServiceResponse    data为multitype:HandlingFormModel
     */
    public function inspect($arrParam) {
        return $this->_handleFormManageModel->inspect($arrParam);
    }

    /**
     * inspect
     * @param HandlingFormListForm $condition
     * @return OnePlusServiceResponse    data为multitype:HandlingFormModel
     */
    public function updateApplyFormMaterial($arrParam) {
        return $this->_handleFormManageModel->updateApplyFormMaterial($arrParam);
    }

    /**
     * inspect
     * @param HandlingFormListForm $condition
     * @return OnePlusServiceResponse    data为multitype:HandlingFormModel
     */
    public function switchRmaCheckCode($arrParam) {
        return $this->_handleFormManageModel->switchRmaCheckCode($arrParam);
    }

    /**
     * applyRefund
     * @param HandlingFormListForm $condition
     * @return OnePlusServiceResponse    data为multitype:HandlingFormModel
     */
    public function applyRefund($arrParam) {
        return $this->_handleFormManageModel->applyRefund($arrParam);
    }

    /**
     * sendBackDo
     * @param HandlingFormListForm $condition
     * @return OnePlusServiceResponse    data为multitype:HandlingFormModel
     */
    public function sendBackDo($arrParam) {
        return $this->_handleFormManageModel->sendBackDo($arrParam);
    }

    /**
     * sendBackDo
     * @param HandlingFormListForm $condition
     * @return OnePlusServiceResponse    data为multitype:HandlingFormModel
     */
    public function releaseDo($arrParam) {
        return $this->_handleFormManageModel->releaseDo($arrParam);
    }

    /**
     * inputTrackingNoDo
     * @param HandlingFormListForm $condition
     * @return OnePlusServiceResponse    data为multitype:HandlingFormModel
     */
    public function inputTrackingNoDo($arrParam) {
        return $this->_handleFormManageModel->inputTrackingNoDo($arrParam);
    }

    /**
     * sendBackDo
     * @param HandlingFormListForm $condition
     * @return OnePlusServiceResponse    data为multitype:HandlingFormModel
     */
    public function findDetailRmaSheet($arrParam) {
        return $this->_handleFormManageModel->findDetailRmaSheet($arrParam);
    }

    /**
     * sendBackDo
     * @param HandlingFormListForm $condition
     * @return OnePlusServiceResponse    data为multitype:HandlingFormModel
     */
    public function findMalfunctCode($arrParam) {
        return $this->_handleFormManageModel->findMalfunctCode($arrParam);
    }

    /**
     * sendBackDo
     * @param HandlingFormListForm $condition
     * @return OnePlusServiceResponse    data为multitype:HandlingFormModel
     */
    public function findQuotationCode($arrParam) {
        return $this->_handleFormManageModel->findQuotationCode($arrParam);
    }

    /**
     * sendBackDo
     * @param HandlingFormListForm $condition
     * @return OnePlusServiceResponse    data为multitype:HandlingFormModel
     */
    public function notifyRmaSheetValidateResult($arrParam) {
        return $this->_handleFormManageModel->notifyRmaSheetValidateResult($arrParam);
    }

    /**
     * 根据处理单号查询
     * @todo:检测每项是否有保修月数的信息
     * @param    string $handlingFormNO 处理单号
     * @param    bool $getOtherOrder 是否获取其它如销售订单和售后处理单信息
     * @return    OnePlusServiceResponse    data为对象,有以下key:
     *                    handlingForm    =>    处理单
     *                    salesOrder        =>    销售订单
     *                    asHandlingForm    =>    售后处理单
     */
    public function queryByHandlingFormNO($handlingFormNO, $getOtherOrder = true) {
        //调用HandlingFormManageModel
        $ret = $this->_handleFormManageModel->queryHandlingFormNO($handlingFormNO);
        if (true !== $ret->isSuccess()) {
            $this->addLog(__METHOD__ . ':' . __LINE__ . ',通过处理单号搜索处理单:' . $handlingFormNO . '错误,结果:' . var_export($ret, 1));
            return $ret;
        }
        //处理单信息
        $handlingForm = $ret->getData();
        //不获取其它信息，直接返回
        if (true !== $getOtherOrder) {
            $data = new stdClass();
            $data->handlingForm = $handlingForm;
            $data->salesOrder = null;
            $data->asHandlingForm = null;
            $ret->setData($data);

            return $ret;
        }

        //销售订单号
        $salesOrderNO = $ret->getData()->getSalesOrderNO();
        if ($salesOrderNO) {
            //到订单中心获取销售订单信息
            $salesOrderModel = new OrderModel();
            $ret = $salesOrderModel->searchByOrderID($salesOrderNO);
            if (true !== $ret->isSuccess()) {
                $this->addLog(__METHOD__ . ':' . __LINE__ . ',通过销售订单号搜索订单:' . $salesOrderNO . '错误,结果:' . var_export($ret, 1));
                return $ret;
            }
            //销售订单信息
            $salesOrderData = $ret->getData();
            if (count($salesOrderData) < 1) {
                $ret->setFailed(OnePlusException::PARAM_ERROR);
                $ret->setErrMsg('找不到销售订单:' . var_export($salesOrderNO, 1));
                return $ret;
            }
            $salesOrder = (Object)$salesOrderData[0];

            //返回退换货信息
            $userID = $salesOrder->userId;
            $ret = $this->getASHandlingFormFromSiteHandlingForm($handlingForm, $userID);
            if (true !== $ret->isSuccess()) {
                $this->addLog(__METHOD__ . ':' . __LINE__ . ',获取售后处理单时出错:' . var_export(array($handlingForm->getAsHandlingFormNO(), $userID), 1) . ',结果:' . var_export($ret, 1));
                return $ret;
            }
            //是否有数据
            $data = $ret->getData();
            if (!is_array($data) || (count($data) < 1)) {
                $ret->setFailed(OnePlusException::PARAM_ERROR);
                $ret->setErrMsg('找不到售后处理单信息');
                return $ret;
            }
            $asHandlingForm = (Object)$data;
            if (!is_array($asHandlingForm->checkItemDtoList)) {
                $asHandlingForm->checkItemDtoList = array();
            }
            //返回结果
            $data = new stdClass();
            $data->handlingForm = $handlingForm;
            $data->salesOrder = $salesOrder;
            $data->asHandlingForm = $asHandlingForm;
            $data->userId = $userID;
            $data->isThirdParty = false;
        } else {//第三方收货没有订单
            $ret = $this->getASHandlingFormFromSiteHandlingForm($handlingForm, null);
            if (true !== $ret->isSuccess()) {
                $this->addLog(__METHOD__ . ':' . __LINE__ . ',获取售后处理单时出错:' . var_export(array($handlingForm->getAsHandlingFormNO(), $userID), 1) . ',结果:' . var_export($ret, 1));
                return $ret;
            }
            //是否有数据
            $data = $ret->getData();
            if (!is_array($data) || (count($data) < 1)) {
                $ret->setFailed(OnePlusException::PARAM_ERROR);
                $ret->setErrMsg('找不到售后处理单信息');
                return $ret;
            }
            $asHandlingForm = (Object)$data;
            if (!is_array($asHandlingForm->checkItemDtoList)) {
                $asHandlingForm->checkItemDtoList = array();
            }
            $data = new stdClass();
            $data->handlingForm = $handlingForm;
            $data->salesOrder = null;
            $data->asHandlingForm = $asHandlingForm;
            $data->userId = null;
            $data->isThirdParty = true;
        }
        $ret->setData($data);
        return $ret;
    }

    /**
     * 根据网点受理单获取售后处理单信息
     * @todo:折旧商品数量没有
     * 2014-4-30,返回remark字段
     * @param    HandlingFormModel $siteHandlingForm 网点受理单信息
     * @param    string $userID 用户ID
     * @return    OnePlusServiceResponse
     */
    public function getASHandlingFormFromSiteHandlingForm($siteHandlingForm, $userID) {
        Yii::import('application.models.acceptmodel.AcceptManageModel', true);
        $isThirdParty = $siteHandlingForm->getIsThirdParty() || !$siteHandlingForm->getSalesOrderNo();
        $userID = $userID ? $userID : null;
        $acceptManageModel = new AcceptManageModel();
        $sourceFormNO = $siteHandlingForm->getAsHandlingFormNO();
        switch ($siteHandlingForm->getType()) {
            case HandlingFormModel::TYPE_REJECT:
                //退货
                $ret = $acceptManageModel->searchRejectOrderByNO($sourceFormNO, $userID);
                // 				var_dump($ret);die();
                //为了接口兼容用rejectAcceptanceDto字段填充下
                //商品明细在rejectGoodsDetailDtoList
                if (true === $ret->isSuccess()) {
                    $data = $ret->getData();
                    if (!is_array($data)) {
                        $this->addLog(__METHOD__ . ':' . __LINE__ . ',找不到退货单:' . $sourceFormNO . ',结果:' . var_export($ret, 1));
                        throw new Exception('找不到退货单:' . $sourceFormNO, OnePlusException::PARAM_ERROR);
                    }
                    $newData = $data['rejectAcceptanceDto'];
                    $newData['goodsDetailDtoList'] = $newData['rejectGoodsDetailDtoList'];
                    //取消原因
                    $newData['cancelReason'] = $data['cancelReason'];
                    $ret->setData($newData);
                }
                break;
            case HandlingFormModel::TYPE_REPAIR:
                //维修
                $ret = $acceptManageModel->searchRepairOrderByNO($sourceFormNO, $userID);
                if (true === $ret->isSuccess()) {
                    $data = $ret->getData();
                    if (!is_array($data)) {
                        $this->addLog(__METHOD__ . ':' . __LINE__ . ',找不到维修单:' . $sourceFormNO . ',结果:' . var_export($ret, 1));
                        throw new Exception('找不到维修单:' . $sourceFormNO, OnePlusException::PARAM_ERROR);
                    }

                    $ret->setData($data);
                }
                break;
            case HandlingFormModel::TYPE_SWAP:
                //换货
                $ret = $acceptManageModel->getSwapAcceptanceDetail($sourceFormNO, $userID, $isThirdParty);
                //商品明细在swapGoodDetailList中
                if (true === $ret->isSuccess()) {
                    $data = $ret->getData();
                    if (!is_array($data)) {
                        $this->addLog(__METHOD__ . ':' . __LINE__ . ',找不到换货单:' . $sourceFormNO . ',结果:' . var_export($ret, 1));
                        throw new Exception('找不到换货单:' . $sourceFormNO, OnePlusException::PARAM_ERROR);
                    }
                    $data['goodsDetailDtoList'] = $data['goodsDetailList'];
                    //取消原因
                    $data['cancelReason'] = $data['swapOrderDto']['cancelReason'];
                    $ret->setData($data);
                }
                break;
            default:
                throw new Exception('错误的类型', OnePlusException::PARAM_ERROR);
                break;
        }
        $asHandlingForm = $ret->getData();
        if (!isset($asHandlingForm['checkItemDtoList']) || !is_array($asHandlingForm['checkItemDtoList'])) {
            $asHandlingForm['checkItemDtoList'] = array();
            $ret->setData($asHandlingForm);
        }
        return $ret;
    }

    /**
     * 通过物料申请单ID查询
     * @param string $applyFormID 申请单ID
     * @return    OnePlusServiceResponse
     */
    public function queryByApplyFormID($applyFormID) {
        return $this->_handleFormManageModel->queryByApplyFormID($applyFormID);
    }

    /**
     * 增加pending
     * addPending
     * @param $arrParam
     * @return OnePlusServiceResponse
     */
    public function addPending($arrParam) {
        return $this->_handleFormManageModel->addPending($arrParam);
    }

    /**
     * 取消pending
     * cancelPending
     * @param $arrParam
     * @return OnePlusServiceResponse
     */
    public function cancelPending($arrParam) {
        return $this->_handleFormManageModel->cancelPending($arrParam);
    }
}