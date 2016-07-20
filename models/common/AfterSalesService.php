<?php

/**
 * 售后系统--服务
 */
class AfterSalesService extends OnePlusServiceAbstract {

    /**
     * 当前操作人
     * @var string
     */
    private $_currentUser = '';

    /**
     * 网点信息管理服务的key名
     * @var string
     */
    const AFTER_SALES_SERVICE_KEY = 'afterSales';

    /**
     * 当前操作者的key
     * @var string
     */
    const CURRENT_OPERATOR = 'currentOperator';

    /**
     * 最终调用服务
     * (non-PHPdoc)
     * @see OnePlusServiceAbstract::callService()
     * @return	OnePlusServiceResponse
     */
    protected function callService($method, $param, $notAllowEmptyStr = true) {
        if ($notAllowEmptyStr) {
            $res = parent::callService($method, $param);
        } else {
            $res = parent::callServiceV2($method, $param, $notAllowEmptyStr);
        }

        return $res;
    }

    /**
     * 设置当前操作用户
     * @param string $currentUser	当前操作用户
     */
    public function setCurrentUser($currentUser) {
        $this->_currentUser = $currentUser;
    }

    /**
     * 修改处理单
     * @param	array	$arrParam	参数，可以包含以下key:
     * 			treatSheetId,type,status,receiveUser,reserveTime,engineer,serviceNodeCode
      sender,senderTel,surfaceDescr,malfunctionDescr,attachment,inspectResult
      initialMemo,receiptNo,softwareVersion,discountPrice,attitude,handleTime
      handleMemo,pickuper,pickupTime,pickupMem
     * @return	OnePlusServiceResponse	data中key:result		{result:true}
     */
    public function updateTreatSheet($arrParam) {
        $method = 'updateTreatSheet';
        //过滤一些特殊字符
        if ($arrParam && is_array($arrParam)) {
            foreach ($arrParam as $key => $value) {
                $arrParam[$key] = str_replace(array(',', '"'), array('，', '“'), $value);
            }
        }
        $arrParam[self::CURRENT_OPERATOR] = $this->_currentUser;
        $ret = $this->callService($method, $arrParam);
        $data = $ret->getData();
        if (true !== ParamCheck::checkArray($data, array('result'))) {
            $this->addLog(__CLASS__ . '::' . __METHOD__ . ':' . __LINE__ . ',修改处理单错误,输入参数:'
                    . var_export($arrParam, 1) . ',结果:' . var_export($ret, 1));
            $ret->setFailed();
        }
        return $ret;
    }

    /**
     * 处理单设为初检
     * @param string $treatSheetId	处理单ID
     * @return	OnePlusServiceResponse	data有以下key:treatSheetId, status
     */
    public function initialCheckTreatSheet($treatSheetId) {
        $method = 'initialCheckTreatSheet';
        $arrParam = array('treatSheetId' => $treatSheetId);
        $arrParam[self::CURRENT_OPERATOR] = $this->_currentUser;
        $ret = $this->callService($method, $arrParam);
        $data = $ret->getData();
        if (true !== ParamCheck::checkArray($data, array('treatSheetId', 'status'))) {
            $this->addLog(__CLASS__ . '::' . __METHOD__ . ':' . __LINE__ . ',处理单设为初检错误,输入参数:'
                    . var_export($arrParam, 1) . ',结果:' . var_export($ret, 1));
            $ret->setFailed();
        }
        return $ret;
    }

    /**
     * 处理单设为复检
     * @param	array	$arrParam	可以填以下key:
     * 			treatSheetId,sender,senderTel,surfaceDescr,malfunctionDescr,attachment
      inspectResult,initialMemo,engineer
     * @return	OnePlusServiceResponse	data有以下key:treatSheetId, status
     */
    public function reCheckTreatSheet($arrParam) {
        $method = 'reCheckTreatSheet';
        //过滤一些特殊字符
        if ($arrParam && is_array($arrParam)) {
            foreach ($arrParam as $key => $value) {
                $arrParam[$key] = str_replace(array(',', '"'), array('，', '“'), $value);
            }
        }
        $arrParam[self::CURRENT_OPERATOR] = $this->_currentUser;
        $ret = $this->callService($method, $arrParam);
        $data = $ret->getData();
        if (true !== ParamCheck::checkArray($data, array('treatSheetId', 'status'))) {
            $this->addLog(__CLASS__ . '::' . __METHOD__ . ':' . __LINE__ . ',处理单设为复检错误,输入参数:'
                    . var_export($arrParam, 1) . ',结果:' . var_export($ret, 1));
            $ret->setFailed();
        }
        return $ret;
    }

    /**
     * 处理单设为处理
     * @param array $arrParam	参数,可以填以下key:
     * 			treatSheetId,handleTime,handleMemo,imeiNew
     * @return	OnePlusServiceResponse	data有以下key:treatSheetId, status
     */
    public function handleTreatSheet($arrParam) {
        $method = 'handleTreatSheet';
        //过滤一些特殊字符
        if ($arrParam && is_array($arrParam)) {
            foreach ($arrParam as $key => $value) {
                $arrParam[$key] = str_replace(array(',', '"'), array('，', '“'), $value);
            }
        }
        $arrParam[self::CURRENT_OPERATOR] = $this->_currentUser;
        $ret = $this->callService($method, $arrParam);
        $data = $ret->getData();
        if (true !== ParamCheck::checkArray($data, array('treatSheetId', 'status'))) {
            $this->addLog(__CLASS__ . '::' . __METHOD__ . ':' . __LINE__ . ',处理单设为处理错误,输入参数:'
                    . var_export($arrParam, 1) . ',结果:' . var_export($ret, 1));
            $ret->setFailed();
        }
        return $ret;
    }

    /**
     * 处理单设为取机
     * @param array $arrParam	参数,可以填以下key:
     * 			treatSheetId,handleTime,handleMemo
     * @return	OnePlusServiceResponse	data有以下key:treatSheetId, status
     */
    public function pickupTreatSheet($arrParam) {
        $method = 'pickupTreatSheet';
        //过滤一些特殊字符
        if ($arrParam && is_array($arrParam)) {
            foreach ($arrParam as $key => $value) {
                $arrParam[$key] = str_replace(array(',', '"'), array('，', '“'), $value);
            }
        }
        $arrParam[self::CURRENT_OPERATOR] = $this->_currentUser;
        $ret = $this->callService($method, $arrParam);
        $data = $ret->getData();
        if (true !== ParamCheck::checkArray($data, array('treatSheetId', 'status'))) {
            $this->addLog(__CLASS__ . '::' . __METHOD__ . ':' . __LINE__ . ',处理单设为取机错误,输入参数:'
                    . var_export($arrParam, 1) . ',结果:' . var_export($ret, 1));
            $ret->setFailed();
        }
        return $ret;
    }

    /**
     * 处理单完成
     * @param array $arrParam	参数，可以有以下key:
     * 			treatSheetId,pickupTime,pickuper,pickupMemo
     * @return	OnePlusServiceResponse	data有以下key:treatSheetId, status
     */
    public function finishTreatSheet($arrParam) {
        $method = 'finishTreatSheet';
        //过滤一些特殊字符
        if ($arrParam && is_array($arrParam)) {
            foreach ($arrParam as $key => $value) {
                $arrParam[$key] = str_replace(array(',', '"'), array('，', '“'), $value);
            }
        }
        $arrParam[self::CURRENT_OPERATOR] = $this->_currentUser;
        $ret = $this->callService($method, $arrParam);
        $data = $ret->getData();
        if (true !== ParamCheck::checkArray($data, array('treatSheetId', 'status'))) {
            $this->addLog(__CLASS__ . '::' . __METHOD__ . ':' . __LINE__ . ',完成处理单错误,输入参数:'
                    . var_export($arrParam, 1) . ',结果:' . var_export($ret, 1));
            $ret->setFailed();
        }
        return $ret;
    }

    /**
     * 处理单详情查询
     * @param string $treatSheetId	处理单ID
     * @return	OnePlusServiceResponse	data中有以下key:
     * 				reatSheetId,imeiNo,orderFlow,type,status,sourceType,receiveUser,receiveTime
     * 				,reserveTime,engineer,,serviceNodeCode,sender,senderTel,surfaceDescr
     * 				,malfunctionDescr,attachment,inspectResult,,initialMemo,receiptNo,recheckTime
     * 				,softwareVersion ,totalPrice,discountPrice,attitude,,imeiNew,handleTime
     * ,handleMemo,pickupTime,pickuper,pickupMemo
     */
    public function findDetailTreatSheet($treatSheetId) {
        $method = 'findDetailTreatSheet';
        $arrParam = array('treatSheetId' => $treatSheetId);
        $ret = $this->callService($method, $arrParam);
        $data = $ret->getData();
        $checkKeys = array('treatSheetId', 'orderFlow', 'type', 'status', 'sourceType'
            , 'receiveUser', 'receiveTime', 'reserveTime', 'engineer', 'serviceNodeCode'
            , 'sender', 'senderTel', 'surfaceDescr', 'malfunctionDescr', 'attachment'
            , 'inspectResult', 'initialMemo', 'receiptNo', 'recheckTime'
            , 'softwareVersion', 'totalPrice', 'attitude'
            , 'handleTime', 'handleMemo', 'pickupTime', 'pickuper', 'pickupMemo');
        if (true !== ParamCheck::checkArray($data, $checkKeys)) {
            $this->addLog(__CLASS__ . '::' . __METHOD__ . ':' . __LINE__ . ',处理单详情查询错误,输入参数:'
                    . var_export($arrParam, 1) . ',结果:' . var_export($ret, 1));
            $ret->setFailed();
        }
        return $ret;
    }

    /**
     * 分页查询处理单
     * @param	array	$arrParam	参数	有以下key:
     * 				treatSheetId,imeiNo,orderFlow,type,status,sourceType,receiveUser
      ,receiveTime,reserveTime,engineer,serviceNodeCode,sender,senderTel,receiptNo
      ,softwareVersion,attitude,imeiNew,pickuper,startTime1,endTime1
      ,startTime2,endTime2
     * @param	int	$page	当前页数
     * @param	int	$pageSize	每页记录数
     * @return	OnePlusServiceResponse	data为二维数组,每一项有以下key:
     * 				treatSheetId, imeiNo, orderFlow, type, status, sourceType
      , receiveUser, receiveTime, reserveTime, engineer, serviceNodeCode
      , sender, senderTel, surfaceDescr, malfunctionDescr, attachment
      , inspectResult, initialMemo, receiptNo, recheckTime
      , softwareVersion, totalPrice, discountPrice, attitude
      , imeiNew, handleTime, handleMemo, pickupTime, pickuper, pickupMemo
     */
    public function findTreatSheet($arrParam, $page, $pageSize) {
        $method = 'findRmaSheet';
        $arrParam['currentPage'] = $page;
        $arrParam['pageSize'] = $pageSize;

        $ret = $this->callService($method, $arrParam);
        if (true === $ret->isSuccess()) {

            $checkKeys = array('createTime', 'createUser', 'modifyTime', 'modifyUser', 'rmaSheetId', 'serviceZone'
                , 'customerId', 'customerName', 'ticketNo', 'sourceId', 'sourceCompleteTime'
                , 'status', 'type', 'serviceType', 'swapCreateType', 'swapOrderNo'
                , 'houseSheetId', 'houseCode', 'houseName', 'houseTel'
                , 'returnMethod', 'appointTime', 'currency'
                , 'checkoutPrice', 'depreciatePrice', 'refundAmount', 'payStatus', 'payType'
                , 'reasonDescr', 'rmaAddress', 'rmaGoodsList', 'commonNotesList', 'asLogList'
                , 'causeCodeDtoList', 'nonAssuranceCodes', 'faultCodeDtoList');
            $data = $ret->getData();
//             if (true !== ParamCheck::checkTDArray($data, $checkKeys)) {
//                 $this->addLog(__CLASS__ . '::' . __METHOD__ . ':' . __LINE__ . ',分页查询处理单错误,输入参数:'
//                         . var_export($arrParam, 1) . ',结果:' . var_export($ret, 1));
//                 $ret->setFailed();
//             }
        }
        return $ret;
    }

    public function findRmaSheetLog($arrParam, $page, $pageSize) {
        $method = 'findRmaSheetLog';
        $arrParam['currentPage'] = $page;
        $arrParam['pageSize'] = $pageSize;
        $ret = $this->callService($method, $arrParam);
        return $ret;
    }

    public function queryRepaireReplacementList($arrParam, $page, $pageSize) {
        $method = 'findQuotationCode';
        $arrParam['currentPage'] = $page;
        $arrParam['pageSize'] = $pageSize;

        $ret = $this->callService($method, $arrParam);
        return $ret;
    }

    public function sendTrackingNo($arrParam) {
        $method = 'updateRmaSheet';
        $ret = $this->callService($method, $arrParam);
        return $ret;
    }

    public function cancleRma($arrParam) {
        $method = 'updateRmaSheet';
        $ret = $this->callService($method, $arrParam);
        return $ret;
    }

    public function acceptArrangement($arrParam) {
        $method = 'updateRmaSheet';
        $ret = $this->callService($method, $arrParam);
        return $ret;
    }

    public function pickup($arrParam) {
        $method = 'updateRmaSheet';
        $ret = $this->callService($method, $arrParam);
        return $ret;
    }

    public function dropOff($arrParam) {
        $method = 'updateRmaSheet';
        $ret = $this->callService($method, $arrParam);
        return $ret;
    }

    public function applyCancelDo($arrParam) {
        $method = 'updateRmaSheet';
        $ret = $this->callService($method, $arrParam);
        return $ret;
    }

    public function receive($arrParam) {
        $method = 'updateRmaSheet';
        $ret = $this->callService($method, $arrParam, false);
        return $ret;
    }

    public function sendQuotation($arrParam) {
        $method = 'updateRmaSheet';
        $ret = $this->callService($method, $arrParam);
        return $ret;
    }

    public function replaceDo($arrParam) {
        //$method = 'updateRmaSheet';
        $method = 'finishRmaSheet';
        $ret = $this->callService($method, $arrParam);
        return $ret;
    }

    public function repair($arrParam) {
        $method = 'updateRmaSheet';
        $ret = $this->callService($method, $arrParam, false);
        return $ret;
    }

    public function inspect($arrParam) {
        $method = 'updateRmaSheet';
        $ret = $this->callService($method, $arrParam, false);
        return $ret;
    }

    public function updateApplyFormMaterial($arrParam) {
        $method = 'setRmaGoodsImeiNew';
        $ret = $this->callService($method, $arrParam);
        return $ret;
    }

    public function applyRefund($arrParam) {
        $method = 'applyRefund4ServiceNode';
        $ret = $this->callService($method, $arrParam);
        return $ret;
    }

    public function sendBackDo($arrParam) {
        $method = 'updateRmaSheet';
        $ret = $this->callService($method, $arrParam);
        return $ret;
    }

    public function releaseDo($arrParam) {
        $method = 'finishRmaSheet';
        $ret = $this->callService($method, $arrParam);
        return $ret;
    }

    public function inputTrackingNoDo($arrParam) {
        $method = 'updateRmaSheet';
        $ret = $this->callService($method, $arrParam);
        return $ret;
    }

    public function findDetailRmaSheet($arrParam) {
        $method = 'findDetailRmaSheet';
        $ret = $this->callService($method, $arrParam);
        return $ret;
    }

    public function findMalfunctCode($arrParam) {
        $method = 'findMalfunctCode';
        $ret = $this->callService($method, $arrParam);
        return $ret;
    }

    public function findQuotationCode($arrParam) {
        $method = 'findQuotationCode';
        $ret = $this->callService($method, $arrParam);
        return $ret;
    }

    public function notifyRmaSheetValidateResult($arrParam) {
        $method = 'notifyRmaSheetValidateResult';
        $ret = $this->callService($method, $arrParam);
        return $ret;
    }

    public function switchRmaCheckCode($arrParam) {
        $method = 'switchRmaCheckCode';
        $ret = $this->callService($method, $arrParam);
        return $ret;
    }

    /**
     * 新增处理单收银记录
     * @param array $arrParam	可以有以下key:
     * 			treatSheetId,checkItemCode,checkoutPrice
     * @return	OnePlusServiceResponse	data中有以下key:invoiceId,createTime
     */
    public function addTreatInvoice($arrParam) {
        $method = 'addTreatInvoice';
        $arrParam[self::CURRENT_OPERATOR] = $this->_currentUser;
        $ret = $this->callService($method, $arrParam);
        if (true === $ret->isSuccess()) {
            $data = $ret->getData();
            if (true !== ParamCheck::checkArray($data, array('invoiceId', 'createTime'))) {
                $this->addLog(__CLASS__ . '::' . __METHOD__ . ':' . __LINE__
                        . ',新增处理单收银记录错误,输入参数:' . var_export($arrParam, 1) . ',结果:'
                        . var_export($ret, 1));
                $ret->setFailed();
            }
        }
        return $ret;
    }

    /**
     * 修改处理单收银记录
     * @param array $arrParam	参数,可以有以下key:
     * 				invoiceId,treatSheetId,materielCode,materielName,materielPrice
      checkoutPrice,paymentType,status
     * @return	OnePlusServiceResponse
     */
    public function updateTreatInvoice($arrParam) {
        $method = 'updateTreatInvoice';
        $arrParam[self::CURRENT_OPERATOR] = $this->_currentUser;
        $ret = $this->callService($method, $arrParam);
        if (true === $ret->isSuccess()) {
            $data = $ret->getData();
            if (true !== ParamCheck::checkArray($data, array('result'))) {
                $this->addLog(__CLASS__ . '::' . __METHOD__ . ':' . __LINE__
                        . ',修改处理单收银记录错误,输入参数:' . var_export($arrParam, 1) . ',结果:'
                        . var_export($ret, 1));
                $ret->setFailed();
            }
        }
        return $ret;
    }

    /**
     * 分页查询处理单收银记录
     * @param	array	$arrParam	参数,要以有以下key:
     * 			invoiceId,imeiNo,type,status,receiveTime,treatSheetId,materielCode,materielName
      materielPrice,checkoutPrice,paymentType,status,startTime1,endTime1
      ,startTime2,endTime2
     * @param	int	$page	当前第几页
     * @param	int	$pageSize	每页记录数
     * @return	OnePlusServiceResponse	data为二维数组,每项有以下key:
     * 				invoiceId,tsImeiNo,tsType,tsStatus,tsReceiveTime,treatSheetId
     * 				,materielCode,materielName,checkoutPrice,paymentType,status
     * 				,createTime,createUser,tsImeiNew,serviceNodeName, tsReceiveUser
     */
    public function findTreatInvoice($arrParam, $page, $pageSize) {
        //currentPage,pageSize
        $method = 'findTreatInvoice';
        $arrParam['currentPage'] = $page;
        $arrParam['pageSize'] = $pageSize;
        $ret = $this->callService($method, $arrParam);
        if (true === $ret->isSuccess()) {
            $data = $ret->getData();
// 			$checkKeys = array('invoiceId','tsImeiNo','tsType','tsStatus','tsReceiveTime'
// 					,'treatSheetId', 'materielCode', 'materielName', 'checkoutPrice'
// 					, 'checkoutPrice', 'paymentType', 'status', 'createTime', 'createUser'
// 					,'serviceNodeName', 'serviceNodeCode', 'tsReceiveUser', 'tsImeiNew'
// 			);
            $checkKeys = array('invoiceId', 'tsSourceId', 'tsType'
                , 'tsStatus', 'tsReceiveTime', 'tsReceiveUser', 'serviceNodeName'
                , 'serviceNodeCode', 'treatSheetId', 'checkItemCode', 'checkoutPrice'
                , 'status', 'createTime', 'createUser'
            );
            if (true !== ParamCheck::checkTDArray($data, $checkKeys)) {
                $this->addLog(__CLASS__ . '::' . __METHOD__ . ':' . __LINE__
                        . ',分页查询处理单收银记录错误,输入参数:' . var_export($arrParam, 1) . ',结果:'
                        . var_export($ret, 1));
                $ret->setFailed();
            }
        }
        return $ret;
    }

    /**
     * 修改物料申请单
     * @param array $arrParam	可以有以下key:
     * 			applysheetId,status,wmsLinkedId
     * @return	OnePlusServiceResponse	data中有以下key:applysheetId, status
     */
    public function updateApplySheet($arrParam) {
        $method = 'updateApplySheet';
        $arrParam[self::CURRENT_OPERATOR] = $this->_currentUser;
        $ret = $this->callService($method, $arrParam);
        if (true === $ret->isSuccess()) {
            $data = $ret->getData();
            $checkKeys = array('applysheetId', 'modifyTime');
            if (true !== ParamCheck::checkArray($data, $checkKeys)) {
                $this->addLog(__CLASS__ . '::' . __METHOD__ . ':' . __LINE__
                        . ',修改物料申请单错误,输入参数:' . var_export($arrParam, 1) . ',结果:'
                        . var_export($ret, 1));
                $ret->setFailed();
            }
        }
        return $ret;
    }

    /**
     * 审核通过申请单
     * @param string $applysheetId	申请单ID
     * @return	OnePlusServiceResponse	data中有以下key:applysheetId, status
     */
    public function approveApplySheet($arrParam) {
        $method = 'approveApplySheet';
        $arrParam[self::CURRENT_OPERATOR] = $this->_currentUser;

        $ret = $this->callService($method, $arrParam);
        if (true === $ret->isSuccess()) {
            $data = $ret->getData();
            $checkKeys = array('applysheetId', 'status');
            if (true !== ParamCheck::checkArray($data, $checkKeys)) {
                $this->addLog(__CLASS__ . '::' . __METHOD__ . ':' . __LINE__
                        . ',审核通过申请单错误,输入参数:' . var_export($arrParam, 1) . ',结果:'
                        . var_export($ret, 1));
                $ret->setFailed();
            }
        }
        return $ret;
    }

    /**
     * 根据员工编号查询员工部门
     */
    public function getDepNameByEmpId() {
        
    }

    /**
     * 物料申请单详情查询
     * @param string $applysheetId	申请单ID
     * @return	OnePlusServiceResponse	data为数组,有以下key:
     * 				applysheetId,serviceNodeCode,serviceNodeName,treatSheetId,applyType
     * 				,status,createTime,createUser,modifyTime,modifyUser,applyMaterielList
     * 				applyMaterielList为二维数组，有以下key
     * 				applyMaterielId,applysheetId,materielCode,materielName
     * 				,materielDescr,qualityDepot,quantity
     */
    public function findDetailApplySheet($applysheetId) {
        $method = 'findDetailApplySheet';
        $arrParam = array('applysheetId' => $applysheetId);
        $arrParam[self::CURRENT_OPERATOR] = $this->_currentUser;
        $ret = $this->callService($method, $arrParam);
        if (true === $ret->isSuccess()) {
            $data = $ret->getData();
            $checkKeys = array('applysheetId', 'serviceNodeCode', 'serviceNodeName', 'treatSheetId'
                , 'applyType', 'status', 'createTime', 'createUser', 'modifyTime', 'modifyUser'
                , 'applyMaterielList'
            );
            if (true !== ParamCheck::checkArray($data, $checkKeys)) {
                $this->addLog(__CLASS__ . '::' . __METHOD__ . ':' . __LINE__
                        . ',物料申请单详情查询错误,输入参数:' . var_export($arrParam, 1) . ',结果:'
                        . var_export($ret, 1));
                $ret->setFailed();
            }
            //检查物料
            $applyMaterielList = $data['applyMaterielList'];
            $checkKeys = array('applyMaterielId', 'applysheetId', 'materielCode', 'materielName'
                , 'materielDescr', 'quantity', 'qualityDepot');
            if (true !== ParamCheck::checkTDArray($applyMaterielList, $checkKeys)) {
                $this->addLog(__CLASS__ . '::' . __METHOD__ . ':' . __LINE__
                        . ',物料申请单详情时，物料明细无指定的key,输入参数:' . var_export($arrParam, 1) . ',结果:'
                        . var_export($ret, 1));
                $ret->setFailed();
            }
        }
        return $ret;
    }

    /**
     * 分页查询物料申请单
     * @param	array	$arrParam	参数	可以有以下key:
     * 				applysheetId,applyType,treatSheetId,serviceNodeCode,createUser
      status,startTime1,endTime1,currentPage,pageSize
     * @param	int	$page	当前第几页
     * @param	int	$pageSize	每页记录数
     * @return	OnePlusServiceResponse	data为二维数组,有以下key:
     * 			applysheetId,serviceNodeCode,serviceNodeName,treatSheetId
     * 			,applyType,status,createTime,createUser,modifyTime,modifyUser
     */
    public function findApplySheet($arrParam, $page, $pageSize) {
        $method = 'findApplySheet';
        $arrParam['currentPage'] = $page;
        $arrParam['pageSize'] = $pageSize;
        $ret = $this->callService($method, $arrParam);
        if (true === $ret->isSuccess()) {
            $data = $ret->getData();
            if (is_array($data)) {
                //@todo:totalPrice不用了?
                $checkKeys = array('applysheetId', 'treatSheetId', 'applyType'
                    , 'status', 'createTime', 'createUser', 'modifyTime', 'modifyUser');
                if (true !== ParamCheck::checkTDArray($data, $checkKeys)) {
                    $this->addLog(__CLASS__ . '::' . __METHOD__ . ':' . __LINE__
                            . ',分页查询物料申请单错误,输入参数:' . var_export($arrParam, 1) . ',结果:'
                            . var_export($ret, 1));
                    $ret->setFailed();
                }
            } else {
                $ret->setData(array());
            }
        }
        return $ret;
    }

    /**
     * 新增申请或者退料单物料记录
     * @param array $arrParam	可以有以下key:
     * 			treatSheetId,serviceNodeCode,materielCode,materielName
     * 			,quantity,materielDescr,applyType
     * @return	OnePlusServiceResponse	data为2个对象:'applySheet', 'applyMateriel'
     * 			
     */
    public function addApplyMateriel($arrParam) {
        $method = 'addApplyMateriel';
        $arrParam[self::CURRENT_OPERATOR] = $this->_currentUser;
        $ret = $this->callService($method, $arrParam);
        if (true === $ret->isSuccess()) {
            $data = $ret->getData();
            $checkKeys = array('applySheet', 'applyMateriel');
            if (true !== ParamCheck::checkArray($data, $checkKeys)) {
                $this->addLog(__CLASS__ . '::' . __METHOD__ . ':' . __LINE__
                        . ',新增申请或者退货单物料记录错误,输入参数:' . var_export($arrParam, 1) . ',结果:'
                        . var_export($ret, 1));
                $ret->setFailed();
            }
        }
        return $ret;
    }

    /**
     * 修改申请单物料记录
     * @param	$arrParam	参数	可以有以下key:
     * 			applyMaterielId,applysheetId,materielCode,qualityDepot,materielName,materielPrice,materielDescr
     * @return	OnePlusServiceResponse	data中有以下key:result
     */
    public function updateApplyMateriel($arrParam) {
        $method = 'updateApplyMateriel';
        $arrParam[self::CURRENT_OPERATOR] = $this->_currentUser;
        $ret = $this->callService($method, $arrParam);
        if (true === $ret->isSuccess()) {
            $data = $ret->getData();
            $checkKeys = array('result');
            if (true !== ParamCheck::checkArray($data, $checkKeys)) {
                $this->addLog(__CLASS__ . '::' . __METHOD__ . ':' . __LINE__
                        . ',修改申请单物料记录错误,输入参数:' . var_export($arrParam, 1) . ',结果:'
                        . var_export($ret, 1));
                $ret->setFailed();
            }
        }
        return $ret;
    }

    /**
     * 删除申请单物料记录
     * @param	string	$applyMaterielId	申请ID
     * @return	OnePlusServiceResponse	data中有以下key:result
     */
    public function deleteApplyMateriel($applyMaterielId) {
        $method = 'deleteApplyMateriel';
        $arrParam = array('applyMaterielId' => $applyMaterielId);
        $arrParam[self::CURRENT_OPERATOR] = $this->_currentUser;
        $ret = $this->callService($method, $arrParam);
        if (true === $ret->isSuccess()) {
            $data = $ret->getData();
            $checkKeys = array('result');
            if (true !== ParamCheck::checkArray($data, $checkKeys)) {
                $this->addLog(__CLASS__ . '::' . __METHOD__ . ':' . __LINE__
                        . ',删除申请单物料记录错误,输入参数:' . var_export($arrParam, 1) . ',结果:'
                        . var_export($ret, 1));
                $ret->setFailed();
            }
        }
        return $ret;
    }

    /**
     * 查询申请单所有物料记录
     * @param	string	$applysheetId	申请单ID
     * @return	OnePlusServiceResponse	data为二维数组,每项有以下key:
     * 			applyMaterielId,applysheetId,materielCode,materielName,materielPrice
     * 			,materielDescr,currentOperator,qualityDepot
     */
    public function listApplyMateriel($applysheetId) {
        $method = 'listApplyMateriel';
        $arrParam = array('applysheetId' => $applysheetId);
        $arrParam[self::CURRENT_OPERATOR] = $this->_currentUser;
        $ret = $this->callService($method, $arrParam);
        if (true === $ret->isSuccess()) {
            $data = $ret->getData();
            //@todo:materielPrice不用了?
            $checkKeys = array('applyMaterielId', 'applysheetId', 'materielCode', 'imeiBarcode'
                , 'materielName', 'qualityDepot', 'quantity', 'materielDescr');
            if (true !== ParamCheck::checkTDArray($data, $checkKeys)) {
                $this->addLog(__CLASS__ . '::' . __METHOD__ . ':' . __LINE__
                        . ',查询申请单所有物料记录错误,输入参数:' . var_export($arrParam, 1) . ',结果:'
                        . var_export($ret, 1));
                $ret->setFailed();
            }
        }
        return $ret;
    }

    /**
     * 删除收银记录
     * @param string $invoiceId	收银ID
     * @return	OnePlusServiceResponse
     */
    public function deleteTreatInvoice($invoiceId) {
        $method = 'deleteTreatInvoice';
        $arrParam = array('invoiceId' => $invoiceId);
        $arrParam[self::CURRENT_OPERATOR] = $this->_currentUser;
        $ret = $this->callService($method, $arrParam);
        if (true === $ret->isSuccess()) {
            $data = $ret->getData();
            $checkKeys = array('result');
            if (true !== ParamCheck::checkArray($data, $checkKeys)) {
                $this->addLog(__CLASS__ . '::' . __METHOD__ . ':' . __LINE__
                        . ',删除收银记录,输入参数:' . var_export($arrParam, 1) . ',结果:'
                        . var_export($ret, 1));
                $ret->setFailed();
            }
        }
        return $ret;
    }

    /**
     * 检测不通过处理单
     * @param array $arrParam	参数,有以下key:treatSheetId,pickupTime,pickuper,pickupMemo
      delayReasonA,delayReasonB
      @return	OnePlusServiceResponse
     */
    public function refuseTreatSheet($arrParam) {
        $method = 'refuseTreatSheet';
        $arrParam[self::CURRENT_OPERATOR] = $this->_currentUser;
        $ret = $this->callService($method, $arrParam);
        if (true === $ret->isSuccess()) {
            $data = $ret->getData();
            $checkKeys = array('status');
            if (true !== ParamCheck::checkArray($data, $checkKeys)) {
                $this->addLog(__CLASS__ . '::' . __METHOD__ . ':' . __LINE__
                        . ',检测不通过处理单,输入参数:' . var_export($arrParam, 1) . ',结果:'
                        . var_export($ret, 1));
                $ret->setFailed();
            }
        }
        return $ret;
    }

    /**
     * 取消处理单
     * @param array $arrParam	参数,有以下key:treatSheetId,pickupTime,pickuper,pickupMemo
      delayReasonA,delayReasonB
      @return	OnePlusServiceResponse
     */
    public function cancelTreatSheet($arrParam) {
        $method = 'cancelTreatSheet';
        $arrParam[self::CURRENT_OPERATOR] = $this->_currentUser;
        $ret = $this->callService($method, $arrParam);
        return $ret;
    }

    /**
     * 1.19	处理单商品记录设置新IMEI号
     * @param array $arrParam	可以有以下key:
     * 			tsGoodsId,imeiNew,currentOperator
     * @return	OnePlusServiceResponse	data中有以下key:tsGoodsId, modifyTime
     */
    public function setTsGoodsImeiNew($arrParam) {
        $method = 'setTsGoodsImeiNew';
        $arrParam[self::CURRENT_OPERATOR] = $this->_currentUser;
        $ret = $this->callService($method, $arrParam, true);
        if (true === $ret->isSuccess()) {
            $data = $ret->getData();
            $checkKeys = array('tsGoodsId', 'modifyTime');
            if (true !== ParamCheck::checkArray($data, $checkKeys)) {
                $this->addLog(__CLASS__ . '::' . __METHOD__ . ':' . __LINE__
                        . ',处理单商品记录设置新IMEI号或者增加修改、故障代码错误,输入参数:' . var_export($arrParam, 1) . ',结果:'
                        . var_export($ret, 1));
                $ret->setFailed();
            }
        }
        return $ret;
    }

    /**
     * 1.19	查询申请单所有商品记录
     * @param	string	$treatSheetId	申请单ID
     * @return	OnePlusServiceResponse	data为二维数组,每项有以下key:
     * 			tsGoodsId, treatSheetId, orderFlow, 
     *                              goodsCode, imeiOld, imeiNew, createTime
     */
    public function listTreatSheetGoods($arrParam) {
        $method = 'listTreatSheetGoods';
        $ret = $this->callService($method, $arrParam);
        if (true === $ret->isSuccess()) {
            $data = $ret->getData();
            $checkKeys = array('tsGoodsId', 'treatSheetId', 'orderFlow'
                , 'goodsCode', 'imeiOld', 'imeiNew', 'createTime');
            if (true !== ParamCheck::checkTDArray($data, $checkKeys)) {
                $this->addLog(__CLASS__ . '::' . __METHOD__ . ':' . __LINE__
                        . ',查询申请单所有商品记录,输入参数:' . var_export($arrParam, 1) . ',结果:'
                        . var_export($ret, 1));
                $ret->setFailed();
            }
        }
        return $ret;
    }

    /**
     * 查询预约单详情
     * @return	OnePlusServiceResponse	data为二维数组,每项有以下key:
     * 		reserveSheetId/serviceNodeCode/serviceNodeName/snWorkingDays
     *              snOpenTime/snPhoneNumber/customerId/mobileNumber/
     *              customerName/reserveTime/handleTime/type:00
     *              status/memo/cancelReason/createUser/createTime
     */
    public function findDetailReserveSheet($arrParam) {
        $method = 'findDetailReserveSheet';
        $ret = $this->callService($method, $arrParam);
        if (true === $ret->isSuccess()) {
            $data = $ret->getData();
            $checkKeys = array('reserveSheetId', 'serviceNodeCode', 'serviceNodeName'
                , 'snWorkingDays', 'snOpenTime', 'snPhoneNumber', 'customerId');
            if (true !== ParamCheck::checkArray($data, $checkKeys)) {
                $this->addLog(__CLASS__ . '::' . __METHOD__ . ':' . __LINE__
                        . ',查询预约单,输入参数:' . var_export($arrParam, 1) . ',结果:'
                        . var_export($ret, 1));
                $ret->setFailed();
            }
        }
        return $ret;
    }

    /**
     * 分页查询预约单
     * @return	OnePlusServiceResponse	data为二维数组,每项有以下key:
     * 		reserveSheetId/serviceNodeCode/serviceNodeName/snWorkingDays
     *              snOpenTime/snPhoneNumber/customerId/mobileNumber
     *              customerName/reserveTime/handleTime/type
     *              status/memo/cancelReason/createUser/createTime
     */
    public function findReserveSheet($arrParam) {
        $method = 'findReserveSheet';
        $ret = $this->callService($method, $arrParam);
        if (true === $ret->isSuccess()) {
            $data = $ret->getData();
            $checkKeys = array('reserveSheetId', 'serviceNodeCode', 'serviceNodeName'
                , 'snWorkingDays', 'snOpenTime', 'snPhoneNumber', 'customerId');
            if (true !== ParamCheck::checkTDArray($data, $checkKeys)) {
                $this->addLog(__CLASS__ . '::' . __METHOD__ . ':' . __LINE__
                        . ',分页查询预约单,输入参数:' . var_export($arrParam, 1) . ',结果:'
                        . var_export($ret, 1));
                $ret->setFailed();
            }
        }
        return $ret;
    }

    /**
     * 预约单设为取消
     * @param array $arrParam	参数,有以下key:reserveSheetId/cancelReason/currentOperator
      @return	OnePlusServiceResponse
     */
    public function cancelReserveSheet($arrParam) {
        $method = 'cancelReserveSheet';
        $arrParam[self::CURRENT_OPERATOR] = $this->_currentUser;
        $ret = $this->callService($method, $arrParam);
        return $ret;
    }

    /**
     * 预约单设为完成
     * @param array $arrParam	参数,有以下key:reserveSheetId/serviceNodeCode/customerId/mobileNumber/customerName
     *                                             type/memo/currentOperator
     * @return	OnePlusServiceResponse
     */
    public function finishReserveSheet($arrParam) {
        $method = 'finishReserveSheet';
        $arrParam[self::CURRENT_OPERATOR] = $this->_currentUser;
        $ret = $this->callService($method, $arrParam);
        return $ret;
    }

    /**
     * 修改预约单
     * @param array $arrParam	参数,有以下key:reserveSheetId/serviceNodeCode/reserveTime/customerId
     *                                             mobileNumber/customerName/type/memo/cancelReason/currentOperator
     * @return	OnePlusServiceResponse
     */
    public function updateReserveSheet($arrParam) {
        $method = 'updateReserveSheet';
        $arrParam[self::CURRENT_OPERATOR] = $this->_currentUser;
        $ret = $this->callService($method, $arrParam);
        return $ret;
    }

    /**
     * 修改预约单
     * @param array $arrParam	参数,有以下key:serviceNodeCode/reserveTime/customerId/mobileNumber
     *                                             customerName/type/memo/currentOperator
     * @return	OnePlusServiceResponse
     */
    public function addReserveSheet($arrParam) {
        $method = 'addReserveSheet';
        $arrParam[self::CURRENT_OPERATOR] = $this->_currentUser;
        $ret = $this->callService($method, $arrParam);
        return $ret;
    }

    /**
     * 查询IMEI是否已被使用，WMS是否出库，订单中是否已使用，客服系统重是否已经使用
     * @params array( 'imeiNew'=>'232334334')
     * @return	$ret bool 是否已经使用
     *
     */
    public function verifyImei4ServicenodeSwap($arrParam) {
        $method = 'verifyImei4ServicenodeSwap';
        $ret = $this->callService($method, $arrParam);
        return $ret;
    }

    /**
     * 查询备件商品列表
     * @params 
     * @return	
     *
     */
    public function findSparePartsPrice($arrParam) {
        $method = 'findSparePartsPrice';
        $ret = $this->callService($method, $arrParam);
        return $ret;
    }

    /**
     * 查询备件商品列表
     * @params 
     * @return	
     *
     */
    public function findDetailWarehouseFund($arrParam) {
        $method = 'findDetailWarehouseFund';
        $ret = $this->callService($method, $arrParam);
        return $ret;
    }

    /**
     * 新增备件申请单
     * @params 
     * @return	
     *
     */
    public function addSparePartsApplySheet($arrParam) {
        $method = 'addSparePartsApplySheet';
        $ret = $this->callService($method, $arrParam);
        return $ret;
    }

    /**
     * 备件申请单列表
     * @params 
     * @return	
     *
     */
    public function findSparePartsApplySheet($arrParam) {
        $method = 'findSparePartsApplySheet';
        $ret = $this->callService($method, $arrParam);
        return $ret;
    }
    
    /**
     * 查询备件申请单详情
     * cancelPending
     * @param $arrParam
     * @return OnePlusServiceResponse
     */
    public function findDetailSparePartsApplySheet($arrParam) {
        $method = 'findDetailSparePartsApplySheet';
        $ret = $this->callService($method, $arrParam);
        return $ret;
    }
    
    /**
     * 查询出入库单列表
     * cancelPending
     * @param $arrParam
     * @return OnePlusServiceResponse
     */
    public function findSparePartsSkuStockSheet($arrParam) {
        $method = 'findSparePartsSkuStockSheet';
        $ret = $this->callService($method, $arrParam);
        return $ret;
    }   
    
    /**
     * 查询保证金流水
     * findWarehouseFundFlow
     * @param $arrParam
     * @return OnePlusServiceResponse
     */
    public function findWarehouseFundFlow($arrParam) {
        $method = 'findWarehouseFundFlow';
        $ret = $this->callService($method, $arrParam);
        return $ret;
    }
    /**
     * 新增出库单
     * cancelPending
     * @param $arrParam
     * @return OnePlusServiceResponse
     */
    public function addSparePartsSkuStockSheet($arrParam) {
        $method = 'addSparePartsSkuStockSheet';
        $ret = $this->callService($method, $arrParam);
        return $ret;
    }
    
    /**
     * 入库确认
     * cancelPending
     * @param $arrParam
     * @return OnePlusServiceResponse
     */
    public function confirmSparePartsSkuStockSheet($arrParam) {
        $method = 'confirmSparePartsSkuStockSheet';
        $ret = $this->callService($method, $arrParam);
        return $ret;
    }
    
    /**
     * 申请单取消
     * cancelPending
     * @param $arrParam
     * @return OnePlusServiceResponse
     */
    public function cancelSparePartsApplySheet($arrParam) {
        $method = 'cancelSparePartsApplySheet';
        $ret = $this->callService($method, $arrParam);
        return $ret;
    }
    
    /**
     * 增加pending
     * addPending
     * @param $arrParam
     * @return OnePlusServiceResponse
     */
    public function addPending($arrParam) {
        $method = 'updateRmaSheet';
        $ret = $this->callService($method, $arrParam);
        return $ret;
    }
    
    /**
     * 取消pending
     * cancelPending
     * @param $arrParam
     * @return OnePlusServiceResponse
     */
    public function cancelPending($arrParam) {
        $method = 'updateRmaSheet';
        $ret = $this->callService($method, $arrParam);
        return $ret;
    }
    
    /**
     * 取消pending
     * cancelPending
     * @param $arrParam
     * @return OnePlusServiceResponse
     */
    public function findSparePartsApplyGoods($arrParam) {
       
        $method = 'findSparePartsApplyGoods';             
        $ret = $this->callService($method, $arrParam);
        return $ret;
    }
    
    /**
     * 查询备件申请单(退料)实际退料商品价格和数量
     * cancelPending
     * @param $arrParam
     * @return OnePlusServiceResponse
     */
    public function findSparePartsApplyGoodsPrice4Return($arrParam) {
        $method = 'findSparePartsApplyGoodsPrice4Return';
        $ret = $this->callService($method, $arrParam);
        return $ret;
    }
    
    /**
     * 查询备件申请单日志
     * cancelPending
     * @param $arrParam
     * @return OnePlusServiceResponse
     */
    public function findSparePartsApplySheetLog($arrParam) {
        $method = 'findSparePartsApplySheetLog';
        $ret = $this->callService($method, $arrParam);
        return $ret;
    }
    /**
     * 查询出入库单详情
     * cancelPending
     * @param $arrParam
     * @return OnePlusServiceResponse
     */
    public function findDetailSparePartsSkuStockSheet($arrParam) {
        $method = 'findDetailSparePartsSkuStockSheet';
        $ret = $this->callService($method, $arrParam);
        return $ret;
    }
    /**
     * 查询仓库价格表
     * cancelPending
     * @param $arrParam
     * @return OnePlusServiceResponse
     */
    public function findDetailSparePartsPriceConfig($arrParam) {
        $method = 'findSparePartsPriceConfig';
        $ret = $this->callService($method, $arrParam);
        return $ret;
    }
    

}
