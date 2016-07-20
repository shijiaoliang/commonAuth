<?php
Yii::import('application.models.acceptmodel.entity.AddressEntity');
Yii::import('application.models.common.UserService');

/**
 * 处理单Model
 */
class HandlingFormModel extends OPAdminModelBase {
    /**
     * 类型为退货的处理单
     * @var int
     */
    const TYPE_REJECT = 10;

    /**
     * 类型为换货的处理单
     * @var int
     */
    const TYPE_SWAP = 20;

    /**
     * 类型为维修的退理单
     * @var int
     */
    const TYPE_REPAIR = 30;

    /**
     * 网点自建
     * @var int
     */
    const SOURCETYPE_BRANCH_CREATE = 10;

    /**
     * 客服推送
     * @var int
     */
    const SOURCETYPE_CS_PUSH = 20;

    /**
     * 用户自建
     * @var int
     */
    const SOURCETYPE_USER_CREATE = 30;

    /*     * **************	客户意见是否同意	********************* */

    /**
     * 客户同意
     * @var int
     */
    const CUSTOMER_AGREE = 10;

    /**
     * 客户不同意
     * @var int
     */
    const CUSTOMER_NOT_AGREE = 20;

    /**
     * 已在线支付
     * @var int
     */
    const CUSTOMER_HAS_PAID_ONLINE = 30;

    /*     * **************	状态常量开始	********************* */

    /**
     * 待初检
     * @var int
     */
    const STATUS_WAIT_FIRST_CHECK = 10;

    /**
     * 初检中
     * @var int
     */
    const STATUS_FIRST_CHECKING = 15;

    /**
     * 复检中
     * @var int
     */
    const STATUS_SECOND_CHECKING = 20;

    /**
     * 检测不通过-已取机
     * @var int
     */
    const STATUS_NOT_PASSED = 25;

    /**
     * 处理中
     * @var int
     */
    const STATUS_PROCESSING = 30;

    /**
     * 待取机
     * @var int
     */
    const STATUS_WAIT_TAKE_MACHINE = 35;

    /*
     * 处理完成-已取机
     * @var int
     */
    const STATUS_PROCESSED = 40;

    /*
     * 已取消
     * @var int
     */
    const STATUS_CABCEKED = 45;

    /*
     * 未处理
     * @var int
     */
    const APPLYSHEETSTATUS_UNTREATED_CHECKING = 0;

    /*
     * 已领料
     * @var int
     */
    const APPLYSHEETSTATUS_PICK_CHECK = 1;

    /*
     * 已退料
     * @var int
     */
    const APPLYSHEETSTATUS_REJECT_CHECK = 3;

    /*
     * 已退领料
     * @var int
     */
    //const APPLYSHEETSTATUS_PICK_REJECT_CHECK	=	3;

    /**
     * 手机大类的前缀
     * @var string
     */
    const MOBILE_BIG_CATEGORY_CODE = '01';

    /**
     * 处理单号
     * @var string
     */
    private $_handlingFormNO;

    /**
     * 受理单号
     * @var string
     */
    private $_acceptFormNO;

    /**
     * 销售订单号
     * @var string
     */
    private $_salesOrderNO;

    /**
     * 网点编号
     * @var string
     */
    private $_branchNO;

    /**
     * 受理员
     * @var string
     */
    private $_acceptor;

    /**
     * 处理时间
     * @var string
     */
    private $_acceptTime;

    /**
     * 1:退货, 2:换货, 3;维修
     * @var int
     */
    private $_type;

    /**
     * 来源类型, 1:客服推送，2：新建
     * @var int
     */
    private $_sourceType;

    /**
     * 处理单状态
     * @var int
     */
    private $_status;

    /**
     * 退领料单状态
     * @var int
     */
    private $_applySheetStatus;

    /**
     * 预约时间
     * @var string
     */
    private $_appointTime;

    /**
     * 送修人
     * @var string
     */
    private $_sendRepairUser;

    /**
     * 送修人电话
     * @var string
     */
    private $_sendRepairUserPhone;

    /**
     * 外观描述
     * @var string
     */
    private $_surfaceDescription;

    /**
     * 故障描述
     * @var string
     */
    private $_troubleDescription;

    /**
     * 随机附件
     * @var string
     */
    private $_accesories;

    /**
     * 检查结果, 1:退机-正常, 2:退机-非保, 3:换机-正常, 4:换机-非保, 5:维修-正常, 6:维修-非保, 7:维修-以换代修
     * @var int
     */
    private $_checkStatus;

    /**
     * 检测时间
     * @var string
     */
    private $_checkTime;

    /**
     * 发票号
     * @var string
     */
    private $_invoceNO;

    /**
     * 初检备注
     * @var string
     */
    private $_memo;

    /**
     * 工程师名称
     * @var string
     */
    private $_engineer;

    /**
     * 软件版本
     * @var string
     */
    private $_softVersion;

    /**
     * 处理时间
     * @var string
     */
    private $_processTime;

    /**
     * 处理内容
     * @var string
     */
    private $_processContent;

    /**
     * imei号
     * @var string
     */
    private $_imei;

    /**
     * 新IMEI号
     * @var string
     */
    private $_newIMEI;

    /**
     * 取机时间
     * @var string
     */
    private $_takeMachineTime;

    /**
     * 取机人
     * @var string
     */
    private $_takeMachineUser;

    /**
     * 取机备注
     * @var string
     */
    private $_takeMachineMemo;

    /**
     * 受理单信息
     * @var mixed
     */
    private $_acceptOrder;

    /**
     * 接单时间
     * @var string
     */
    private $_receiveTime;

    /**
     * 折扣金额
     * @var number
     */
    private $_discountPrice = 0;

    /**
     * 客户意见, 10:同意，20：拒绝
     * @var int
     */
    private $_customerViewpoint;

    /**
     * 售后的处理单号
     * @var string
     */
    private $_asHandlingFormNO;

    /**
     * 应收金额
     * @var number
     */
    private $_totalPrice;

    /**
     * 处理方式,1:网点内处理,2:工程师上门
     * @var int
     */
    private $_handleType;

    /**
     * 网点名称
     * @var string
     */
    private $_serviceNodeName;

    /**
     * 服务类型
     * @var string
     */
    private $_serviceCode;

    /**
     * 维修等级
     * @var string
     */
    private $_repairLevel;

    /**
     * 网点编号
     * @var string
     */
    private $_serviceNodeCode;

    /**
     * 受理单创建时间
     * @var string
     */
    private $_acceptFormCreateTime;

    //收货地址DTO对象
    private $addressDto;

    private $_userId; //用户ID

    /**
     * 是否为第三方售后
     */
    private $_isThirdParty;

    /**
     * 初检时间
     */
    private $_firstCheckTime;

    /**
     * 取消原因1
     * @var int
     */
    private $_delayReasonA;

    /**
     * 取消原因2
     * @var int
     */
    private $_delayReasonB;

    /**
     * 处理单商品保险号对应集
     * @var array
     */
    private $_goodsCodeIMEIs;

    /**
     * 网点备注
     * @var string
     */
    private $_serviceNodeDescr;

    /**
     * 最后更新人
     * @var string
     */
    private $_modifyUser;

    /**
     * 最后更新时间
     * @var string
     */
    private $_modifyTime;

    /**
     * 处理方案
     * @var string
     */
    private $_handleSolution;

    /**
     * 结算金额
     */
    private $_settlementPrice;

    /**
     * 受理单备注
     */
    private $sourceRemark;

    /**
     * 商品寄回物流公司
     */
    private $expressCompany;

    /**
     * 商品寄回物流单号
     */
    private $expressNo;

    /**
     * 受理单备注
     * @return type
     */
    public function getSourceRemark() {
        return $this->sourceRemark;
    }

    public function setSourceRemark($sourceRemark) {
        $this->sourceRemark = $sourceRemark;
    }

    /**
     * 商品寄回物流公司
     * @return type
     */
    public function getExpressCompany() {
        return $this->expressCompany;
    }

    public function setExpressCompany($expressCompany) {
        $this->expressCompany = $expressCompany;
    }

    /**
     * 商品寄回物流单号
     * @return type
     */
    public function getExpressNo() {
        return $this->expressNo;
    }

    public function setExpressNo($expressNo) {
        $this->expressNo = $expressNo;
    }

    /**
     * 维修等级
     * @return type
     */
    public function getRepairLevel() {
        return $this->_repairLevel;
    }

    public function setRepairLevel($repairLevel) {
        $this->_repairLevel = $repairLevel;
    }

    /**
     * 结算金额
     */
    public function getSettlementPrice() {
        return $this->_settlementPrice;
    }

    public function setSettlementPrice($settlementPrice) {
        $this->_settlementPrice = $settlementPrice;
    }

    /**
     * 最后更新人
     * @return string
     */
    public function getModifyUser() {
        return $this->_modifyUser;
    }

    /**
     * 最后更新时间
     * @return string
     */
    public function getModifyTime() {
        return $this->_modifyTime;
    }

    /**
     * 处理方案
     * @return string
     */
    public function getHandleSolution() {
        return $this->_handleSolution;
    }

    public function setModifyUser($user) {
        $this->_modifyUser = $user;
    }

    public function setModifyTime($time) {
        $this->_modifyTime = $time;
    }

    public function setHandleSolution($solution) {
        $this->_handleSolution = $solution;
    }

    public function setIsThirdParty($isThirdParty) {
        $this->_isThirdParty = $isThirdParty;
    }

    public function getIsThirdParty() {
        return $this->_isThirdParty;
    }

    /**
     * 服务类型
     * @return string
     */
    public function getServiceCode() {
        return $this->_serviceCode;
    }

    public function setServiceCode($serviceCode) {
        $this->_serviceCode = $serviceCode;
    }

    /**
     * 设置处理单商品保险号对应集
     * @return    bool
     */
    public function setGoodsCodeIMEIs($goodsCodeIMEIs) {
        $this->_goodsCodeIMEIs = $goodsCodeIMEIs;
    }

    /**
     * 获取处理单商品保险号对应集
     * @param AddressEntity $addressDto 收货地址对象
     * @return    bool
     */
    public function getGoodsCodeIMEIs() {
        $arrList = $this->_goodsCodeIMEIs;
        return $arrList && is_array($arrList) ? $arrList : array();
    }

    /**
     * @return the $_delayReasonB
     */
    public function getDelayReasonB() {
        return $this->_delayReasonB;
    }

    /**
     * @param number $_delayReasonB
     */
    public function setDelayReasonB($_delayReasonB) {
        $this->_delayReasonB = $_delayReasonB;
    }

    /**
     * @return the $_delayReasonA
     */
    public function getDelayReasonA() {
        return $this->_delayReasonA;
    }

    /**
     * @param number $_delayReasonA
     */
    public function setDelayReasonA($_delayReasonA) {
        $this->_delayReasonA = $_delayReasonA;
    }

    /**
     * 获取受理单创建时间
     * @return the $_acceptFormCreateTime
     */
    public function getAcceptFormCreateTime() {
        return $this->_acceptFormCreateTime;
    }

    /**
     * 设置受理单创建时间
     * @param string $acceptFormCreateTime 受理单创建时间
     */
    public function setAcceptFormCreateTime($acceptFormCreateTime) {
        if (is_numeric($acceptFormCreateTime)) {
            $this->_acceptFormCreateTime = Func::javaDate('Y-m-d H:i:s', $acceptFormCreateTime);
            ;
        } else {
            $this->_acceptFormCreateTime = $acceptFormCreateTime;
        }
    }

    //设置用户ID
    public function setUserId($userId) {
        $this->_userId = $userId;
    }

    //获取用户ID
    public function getUserId() {
        return $this->_userId;
    }

    /**
     * 获取用户名
     * @return string
     */
    public function getCustomerName() {
        //获取订单接口中的用户名信息 
        $userModel = UserService::getInstance();
        if ($this->_userId) {
            $arr_param = array('userId' => $this->_userId);
            $user_ret = $userModel->getUserInfo($arr_param);
            if (true !== $user_ret->isSuccess()) {
                $this->addLog(__METHOD__ . ':' . __LINE__ . ',查询用户信息错误:' . var_export($arr_param, 1) . '错误,结果:' . var_export($user_ret, 1));
                $this->error('查询用户信息错误:' . $user_ret->getErrMsg());
            }
            $user_data = $user_ret->getData();
            return $user_data['userName'];
        } else {
            if (!$this->_salesOrderNO) {//第三方售后
                $handlingFormFacadeModel = new HandlingFormFacadeModel();
                $ret = $handlingFormFacadeModel->getASHandlingFormFromSiteHandlingForm($this, null);
                if ($ret->isSuccess() == true) {
                    $data = $ret->getData();
                    return isset($data['addressDto']['name']) ? $data['addressDto']['name'] : '未知';
                } else {
                    $this->addLog(__METHOD__ . ':' . __LINE__ . ',查询第三方售后用户信息错误:' . var_export((array) $this, 1) . '错误,结果:' . var_export($ret, 1));
                    $this->error('查询第三方售后用户信息错误:' . $ret->getErrMsg());
                }
            }
            return '未知';
        }
    }

    /**
     * 领退料状态数组
     * @var array
     */
    private static $applySheetStatus = array(
        self::APPLYSHEETSTATUS_UNTREATED_CHECKING => '未处理',
        self::APPLYSHEETSTATUS_PICK_CHECK => '已领料',
        self::APPLYSHEETSTATUS_REJECT_CHECK => '已完成',
            //self::APPLYSHEETSTATUS_PICK_REJECT_CHECK=> '已领退料'
    );

    /**
     * 所有状态数组
     * @var array
     */
    private static $allStatus = array(
        //         10=> 'Awaiting approve'
        //        , 20 => 'Withdrawing'
        25 => 'Canceled',
        //        , 30 => 'Pending'
        //        , 35 => 'Validated'
        //        , 40 => 'Awaiting Return'
        //        , 45 => 'Normal Returned'
        //        , 50 => 'To Depreciatory Receive'
        60 => 'Completed',
        100 => 'To Send Back',
        101 => 'Sent Back',
        //        , 105 => 'To Abnormally Receive'
        //        , 110 => 'Abnormal Returned'
        210 => 'Pending return to facility',
        220 => 'Pending arrangement',
        //      , 230 => 'Canceled'
        240 => 'Arrangement accepted',
        250 => 'Picked-up',
        260 => 'Dropped off',
        270 => 'Pending cancel',
        280 => 'Returned to facility',
        290 => 'Inspecting',
        300 => 'Inspected',
        310 => 'Pending payment',
        //       , 320 => 'Completed'
        330 => 'Repairing',
        340 => 'Pending refund',
        350 => 'Repaired'
    );

    /**
     * @var array 全部状态[java]
     */
    private static $_allStatus = array(
        10 => 'Awaiting approve',
        20 => 'Withdrawing',
        25 => 'Canceled',
        30 => 'Pending',
        35 => 'Validated',
        40 => 'Awaiting Return',
        45 => 'Normal Returned',
        50 => 'To Depreciatory Receive',
        60 => 'Completed',
        100 => 'To Send Back',
        101 => 'Sent Back',
        105 => 'To Abnormally Receive',
        110 => 'Abnormal Returned',
        210 => 'Pending return to facility',
        220 => 'Pending arrangement',
        230 => 'Canceled',
        240 => 'Arrangement accepted',
        250 => 'Picked-up',
        260 => 'Dropped off',
        270 => 'Pending cancel',
        280 => 'Returned to facility',
        290 => 'Inspecting',
        300 => 'Inspected',
        310 => 'Pending payment',
        320 => 'Completed',
        330 => 'Repairing',
        340 => 'Pending refund',
        350 => 'Repaired'
    );

    /**
     * 所有状态数组
     * @var array
     */
    private static $allMethod = array(
        10 => 'Self-return',
        20 => 'Pick-up',
        30 => 'Drop off'
    );

    /**
     * 所有状态数组
     * @var array
     */
    private static $allCarrier = array(
        'Fedex' => 'FEDEX',
        'UPS' => 'UPS',
        'TNT' => 'TNT',
        'DHL' => 'DHL'
    );

    /**
     * @var array
     */
    private static $allTreatmentTypes = array(
        10 => array(
            12 => 'return',
            14 => 'inspection fails'
        ),
        20 => array(
            22 => 'DOA',
            24 => 'inspection fails',
        ),
        30 => array(
            32 => 'in warranty repair',
            34 => 'out of warranty repair',
        )
    );

    /**
     * @var array
     */
    private static $allTreatmentTypeTxts = array(
        12 => 'return',
        14 => 'inspection fails',
        22 => 'DOA',
        24 => 'inspection fails',
        32 => 'in warranty repair',
        34 => 'out of warranty repair',
        36 => 'user cancel repair'
    );

    /**
     * @var array
     */
    private static $allTreatmentTypes2 = array(
        30 => array(
            10 => 'software update',
            20 => 'parts replacement',
        )
    );

    /**
     * @var array
     */
    private static $allTreatmentTypeTxts2 = array(
        10 => 'software update',
        20 => 'parts replacement',
    );

    /**
     * @var array
     */
    private static $allLogType = array(
        240 => 'Creat RMA',
        242 => 'Update RMA',
        244 => 'Approve RMA',
        248 => 'Withdraw',
        250 => 'Reset',
        254 => 'Pending',
        256 => 'Pay',
        258 => 'Refund',
        260 => 'Depreciatory Receive',
        264 => 'Normal Return',
        266 => 'Normal Returned',
        268 => 'Complete RMA',
        270 => 'Abnormally Receive',
        272 => 'Abnormally Return',
        300 => 'Input Tracking no',
        252 => 'Cancel RMA',
        302 => 'Accept Arrangement',
        304 => 'Pick-up',
        306 => 'Drop Off',
        308 => 'Apply to Cancel',
        310 => 'Receive',
        314 => 'Inspect',
        316 => 'Send Quotation',
        318 => 'Replace',
        320 => 'Repair',
        322 => 'Apply Refund',
        262 => 'Send Back',
        324 => 'Release',
        326 => 'Change Status',
        328 => 'Change Type',
        330 => 'Inform Customer',
        360 => 'Set New IMEI',
        362 => 'Modify Inspection quotation',
        364 => 'Modify Repair replacement parts',
        366 => 'Modify Malfunction Symptom',
        368 => 'Modify Malfunction Cause',
        370 => 'Finish inspection',
        372 => 'Finish repair',
        374 => 'Save inspection',
        376 => 'Save repair',
        378 => 'Add Pending',
        380 => 'Cancel Pending',
        382 => 'Upload Attachment',
        384 => 'Remove Attachment',
        386 => 'Create New Order',
        388 => 'Exception Approve',
        402 => 'Update New Order No',
        403 => 'Update New Order No And Complete',
        404 => 'Paid And Complete',
        406 => 'Refuse Quotation An Complete',
        408 => 'Customer Agree Quotation',
        410 => 'Customer Refuse Quotation',
        412 => 'Customer Paid Quotation',
    );

    /**
     * 所有类型数组
     * @var array
     */
    private static $allType = array(
        10 => 'Return',
        20 => 'Replacement',
        30 => 'Repair'
    );

    /**
     * 所有pending类型
     * @var array
     */
    private static $allPendingType = array(
        10 => 'spare parts'
        //缺备件
        ,
        20 => 'clarification with OnePlus'
        //需要跟客服确认
        ,
        30 => 'clarification with customer'
        //需要跟用户确认
        ,
        40 => 'Notification Customer（EA）',
        90 => 'other'
            //其它
    );

    /**
     * 所有 RMA Reason
     * @var array
     */
    private static $allRmaReason = array(
        100 => "15-Day Return",
        101 => "15-Day Replacement",
        102 => "DOA Return",
        103 => "DOA Replacement",
        104 => "Within Warranty",
        105 => "Out of Warranty",
        106 => "In-warranty Replacement",
        107 => "Accessories Return",
        108 => "Accessories Replacement",
        109 => "Paypal Case",
        999 => "Other"
    );

    /**
     * 故障代码类型
     * @var array
     */
    private static $allCodeType = array(
        10 => 'Fault code',
        20 => 'Reason code'
    );

    /**
     * 非保价项中文
     * @var array
     */
    private static $allQuotationTypeCh = array(
        'BC' => '包材',
        'CDQ' => '充电器',
        'ZJ' => '整机',
        'RXXL' => '柔性线路',
        'DC' => '电池',
        'BHT' => '保护套',
        'LCD' => 'LCD',
        'QT' => '其他',
        'SJX' => '数据线',
        'BHK' => '保护壳',
        'ZB' => '主板',
        'SXT' => '摄像头',
        'EJ' => '耳机',
        'JT' => '机头',
        'HC' => '耗材',
        'BHM' => '保护膜',
        'DSQJ' => '电声器件',
        'JGJ' => '结构件',
        'YDDY' => '移动电源'
    );

    /**
     * 非保价项英文
     * @var array
     */
    private static $allQuotationTypeEn = array(
        'BHM' => 'Screen Protector',
        'QT' => 'Others',
        'SXT' => 'Camera',
        'SJX' => 'Cable',
        'BHK' => 'Protective Case',
        'YDDY' => 'Power Bank',
        'RXXL' => 'Flexible circuit',
        'BHT' => 'Flip Cover',
        'ZJ' => 'Mobile Phone',
        'DSQJ' => 'Electroacoustic device',
        'LCD' => 'LCD',
        'BC' => 'Packaging materials',
        'ZB' => 'Main board',
        'HC' => 'consumable',
        'EJ' => 'Earphone',
        'JGJ' => 'Structure',
        'JT' => 'Handset',
        'DC' => 'Battery',
        'CDQ' => 'Adapter'
    );

    /**
     * c错误原因代码中文
     * @var array
     */
    private static $allMalfunctionTypeCh = array(
        'XQT' => '其它类',
        'SHQ' => '送话类不良',
        'XSP' => '显示屏功能不良',
        'XH' => '信号网络/天线类不良',
        'QTRJ' => '其他软件类不良',
        'ZD' => '振动类故障',
        'ROOT' => '手机',
        'RJ' => '耳机类不良',
        'FR' => '发热',
        'YSQ' => '扬声器类不良',
        'ZW' => '指纹模组不良',
        'KD' => '卡顿/闪退',
        'KGJ' => '开关机故障',
        'DC' => '电池故障',
        'SJCQ' => '死机重启',
        'JGJ' => '结构件外观不良',
        'KZ' => '卡座类不良',
        'PWG' => '屏外观类不良',
        'CMP' => '触摸屏类不良',
        'PZWG' => '拍照/摄像外观类不良',
        'PJ' => '附配件故障',
        'CD' => '充电类不良',
        'WGZ' => '无故障',
        'DJD' => '待机短',
        'TX' => '无线连接类故障',
        'AJ' => '按键类不良',
        'TT' => '受话类不良',
        'PZ' => '拍照/摄像功能类不良',
        'ZBH' => '主板坏',
        'YSQH' => '扬声器组件坏',
        'YWGZ' => '检测无故障',
        'JHC' => '进灰尘',
        'SJXH' => '数据线坏',
        'JY' => '进液',
        'RJH' => '耳机坏',
        'JGJH' => '结构件坏',
        'TTH' => '受话器坏',
        'ZWH' => '指纹模组坏',
        'RJSJ' => '软件升级',
        'PZJH' => '屏组件坏',
        'CDQH' => '充电器坏',
        'DCH' => '电池坏',
        'SXTH' => '摄像头坏',
        'FBH' => '副板坏',
        'JC' => '接触不良',
        'AJH' => '按键坏',
        'YQT' => '其他',
        'FPCH' => 'FPC坏',
        'SCCM' => 'Structural component cosmetic malfunction',
        'CHM' => 'Card Holder malfunction ',
        'BM' => 'Button malfunction',
        'PM' => 'Power on / off malfunction',
        'SF' => 'Stuck / Flash quit',
        'CR' => 'Crash / reboot',
        'HEAT' => 'HEAT',
        'SST' => 'Short standby time',
        'DSFM' => 'Display Screen functional malfunction',
        'DSCM' => 'Display screen cosmetic malfunction',
        'TPM' => 'Touch panel malfunction',
        'CAM' => 'Camera malfunction',
        'RM' => 'Receiver malfunction',
        'MM' => 'MIC malfunction',
        'SM' => 'Speaker malfunction',
        'NAM' => 'Network signal / Antenna malfunction',
        'WCM' => 'Wireless connection malfunction',
        'CGM' => 'Charging malfunction',
        'EM' => 'Earphone malfunction',
        'OM' => 'Other malfunction',
        'AM' => 'Accessories malfunction',
        'SWM' => 'Software malfunction',
        'NFF' => 'No fault found',
        'FPSM' => 'Fingerprint module malfunction',
        'SWU' => 'Software upgrade',
        'MD' => 'Mainboard defects',
        'SAD' => 'Screen assembly defects',
        'UCD' => 'USB cable defects',
        'ED' => 'Earphone defects',
        'CHD' => 'Charger defects',
        'RD' => 'Receiver defects',
        'SPAD' => 'Speaker assembly defects',
        'BD' => 'Battery defects',
        'BTD' => 'Button defects',
        'SCD' => 'Structural component defects',
        'ABD' => 'Antenna board defects',
        'CAD' => 'Camera defects',
        'FPCD' => 'FPC defects',
        'DI' => 'Dust infiltration',
        'PC' => 'Poor contact',
        'OD' => 'Other defects',
        'NFD' => 'No fault detected',
        'LI' => 'Liquid infiltration',
        'FPS' => 'Fingerprint module defects  ',
    );

    /**
     * c错误原因代码中文
     * @var array
     */
    private static $allMalfunctionTypeEn = array(
        'XQT' => 'Other malfunctions',
        'SHQ' => 'Microphone malfunctions',
        'XSP' => 'Display malfunctions',
        'XH' => 'Signal/  Antenna malfunctions',
        'QTRJ' => 'Other software defects',
        'ZD' => 'Vibration malfunction',
        'ROOT' => 'Rooting issues',
        'RJ' => 'Earphone malfunctions',
        'FR' => 'Heat',
        'YSQ' => 'Speaker malfunctions',
        'ZW' => 'Home button and fingerprint assembly malfunctions',
        'KD' => 'Freezing/Crashing',
        'KGJ' => 'Startup defects',
        'DC' => 'Battery malfunctions',
        'SJCQ' => 'Power-off/Restarting',
        'JGJ' => 'Structural component malfunctions',
        'KZ' => 'Card holder malfunctions',
        'PWG' => 'Screen appearance malfunctions',
        'CMP' => 'Touchscreen assembly malfunctions',
        'PZWG' => 'Camera appearance malfunctions',
        'PJ' => 'Accessories defects',
        'CD' => 'Charging malfunctions',
        'WGZ' => 'NFF(No Fault Found)',
        'DJD' => 'Short standby time',
        'TX' => 'Wireless connection malfunctions',
        'AJ' => 'Button malfunctions',
        'TT' => 'Receiver malfunctions',
        'PZ' => 'Camera malfunctions',
        'ZBH' => 'Mainboard defects',
        'YSQH' => 'Speaker assembly malfunctions',
        'YWGZ' => 'NFF(No Fault Found)',
        'JHC' => 'Dust infiltration',
        'SJXH' => 'USB cable malfunctions',
        'JY' => 'Liquid damage',
        'RJH' => 'Earphone malfunctions',
        'JGJH' => 'Structural component malfunctions',
        'TTH' => 'Receiver malfunctions',
        'ZWH' => 'Home button and fingerprint assembly malfunctions',
        'RJSJ' => 'Software upgrade',
        'PZJH' => 'Screen assembly malfunctions',
        'CDQH' => 'Charger malfunctions',
        'DCH' => 'Battery malfunctions',
        'SXTH' => 'Camera malfunctions',
        'FBH' => 'Small board malfunctions',
        'JC' => 'Poor contact',
        'AJH' => 'Button malfunctions',
        'YQT' => 'Others',
        'FPCH' => 'FPC malfunctions',
        'SCCM' => 'Structural component cosmetic malfunction',
        'CHM' => 'Card Holder malfunction ',
        'BM' => 'Button malfunction',
        'PM' => 'Power on / off malfunction',
        'SF' => 'Stuck / Flash quit',
        'CR' => 'Crash / reboot',
        'HEAT' => 'HEAT',
        'SST' => 'Short standby time',
        'DSFM' => 'Display Screen functional malfunction',
        'DSCM' => 'Display screen cosmetic malfunction',
        'TPM' => 'Touch panel malfunction',
        'CAM' => 'Camera malfunction',
        'RM' => 'Receiver malfunction',
        'MM' => 'MIC malfunction',
        'SM' => 'Speaker malfunction',
        'NAM' => 'Network signal / Antenna malfunction',
        'WCM' => 'Wireless connection malfunction',
        'CGM' => 'Charging malfunction',
        'EM' => 'Earphone malfunction',
        'OM' => 'Other malfunction',
        'AM' => 'Accessories malfunction',
        'SWM' => 'Software malfunction',
        'NFF' => 'No fault found',
        'FPSM' => 'Fingerprint module malfunction',
        'SWU' => 'Software upgrade',
        'MD' => 'Mainboard defects',
        'SAD' => 'Screen assembly defects',
        'UCD' => 'USB cable defects',
        'ED' => 'Earphone defects',
        'CHD' => 'Charger defects',
        'RD' => 'Receiver defects',
        'SPAD' => 'Speaker assembly defects',
        'BD' => 'Battery defects',
        'BTD' => 'Button defects',
        'SCD' => 'Structural component defects',
        'ABD' => 'Antenna board defects',
        'CAD' => 'Camera defects',
        'FPCD' => 'FPC defects',
        'DI' => 'Dust infiltration',
        'PC' => 'Poor contact',
        'OD' => 'Other defects',
        'NFD' => 'No fault detected',
        'LI' => 'Liquid infiltration',
        'FPS' => 'Fingerprint module defects  ',
    );

    /**
     * 所有来源类型数组
     * @var array
     */
    private static $allSourceType = array(
        self::SOURCETYPE_BRANCH_CREATE => '网点自建',
        self::SOURCETYPE_CS_PUSH => '客服推送',
        self::SOURCETYPE_USER_CREATE => '用户自建'
    );

    //======================rmaLogistics======================
    /**
     * 时间范围
     */
    private static $rl_timeScope = array(
        1 => 'Last 24 hours',
        7 => 'Last 7 days',
        30 => 'Last 30 days'
    );

    /**
     * 类型
     */
    private static $rl_type = array(
        1 => 'All',
        2 => 'Inbound',
        3 => 'Outbound'
    );
    private static $rl_sync_status = array(
        1 => 'Not synchronized',
        2 => 'Synchronized'
    );

    /**
     * 状态
     */
    private static $rl_status = array(
        1 => 'Pending',
        2 => 'InfoReceived',
        3 => 'InTransit',
        4 => 'OutForDelivery',
        5 => 'AttemptFail',
        6 => 'Delivered',
        7 => 'Exception',
        8 => 'Expired'
    );

    /**
     * aftership 返回状态 详见官网[https://www.aftership.com/docs/api/4/delivery-status]
     */
    private static $rl_aftershipStatus = array(
        'Pending' => 1,
        'InfoReceived' => 2,
        'InTransit' => 3,
        'OutForDelivery' => 4,
        'AttemptFail' => 5,
        'Delivered' => 6,
        'Exception' => 7,
        'Expired' => 8
    );

    public static function getRlTimeScope() {
        return self::$rl_timeScope;
    }

    public static function getRlType() {
        return self::$rl_type;
    }

    public static function getSyncStatus() {
        return self::$rl_sync_status;
    }

    public static function getRlStatus() {
        return self::$rl_status;
    }

    public static function getRlAftershipStatus() {
        return self::$rl_aftershipStatus;
    }
    //======================end of rmaLogistics===============

    /**
     * @return the $_serviceNodeName
     */
    public function getServiceNodeName() {
        return $this->_serviceNodeName;
    }

    /**
     * 设置网点名称
     * @param string $serviceNodeName
     */
    public function setServiceNodeName($serviceNodeName) {
        $this->_serviceNodeName = $serviceNodeName;
    }

    /**
     * @return the $_handleType
     */
    public function getHandleType() {
        return $this->_handleType;
    }

    /**
     * 设置处理方式
     * @param number $handleType
     */
    public function setHandleType($handleType) {
        $this->_handleType = $handleType;
    }

    /**
     * @return the $_totalPrice
     */
    public function getTotalPrice() {
        return $this->_totalPrice;
    }

    /**
     * 设置总价
     * @param number $totalPrice
     */
    public function setTotalPrice($totalPrice) {
        $this->_totalPrice = $totalPrice;
    }

    /**
     * 获取售后系统的处理单号
     * @return string $_asHandlingFormNO
     */
    public function getAsHandlingFormNO() {
        return $this->_asHandlingFormNO;
    }

    /**
     * 设置售后处理单号
     * @param string $asHandlingFormNO 售后处理单号
     */
    public function setAsHandlingFormNO($asHandlingFormNO) {
        $this->_asHandlingFormNO = $asHandlingFormNO;
    }

    /**
     * 获取客户意见
     * @return the $_viewpoint
     */
    public function getCustomerViewpoint() {
        return $this->_customerViewpoint;
    }

    /**
     * 设置客户意见
     * @todo:检测是否标准化
     * @param number $customerViewpoint 客户意见
     */
    public function setCustomerViewpoint($customerViewpoint) {
        $this->_customerViewpoint = intval($customerViewpoint);
    }

    /**
     * 设置收货地址对象
     * @return    bool
     */
    public function setAddressDto($addressInfo) {
        $addressEntity = new AddressEntity();
        //拷贝
        foreach ($addressInfo as $key => $val) {
            $addressEntity->$key = $val;
        }
        $this->addressDto = $addressEntity;
    }

    /**
     * 获取收货地址对象
     * @param AddressEntity $addressDto 收货地址对象
     * @return    bool
     */
    public function getAddressDto() {
        return $this->addressDto;
    }

    /**
     * 获取折扣金额
     * @return number $_discountPrice
     */
    public function getDiscountPrice() {
        return $this->_discountPrice;
    }

    /**
     * 设置折扣金额
     * @param number $discountPrice 折扣金额
     */
    public function setDiscountPrice($discountPrice) {
        if (is_numeric($discountPrice) && (floatval($discountPrice) >= 0)) {
            $this->_discountPrice = floatval($discountPrice);
        }
    }

    /**
     * 获取接单时间
     * @return string $_receiveTime
     */
    public function getReceiveTime() {
        return $this->_receiveTime;
    }

    /**
     * 设置接单时间
     * @param string $receiveTime
     */
    public function setReceiveTime($receiveTime) {
        if (is_numeric($receiveTime)) {
            $this->_receiveTime = Func::javaDate('Y-m-d H:i:s', $receiveTime);
        } else {
            $this->_receiveTime = $receiveTime;
        }
    }

    /**
     * 获取所有类型信息
     * @return    array    key为类型编码，val为类型名称
     */
    public static function getAllType() {
        return self::$allType;
    }

    /**
     * 获取所有pending类型
     * getAllPendingType
     * @return array
     */
    public static function getAllPendingType() {
        return self::$allPendingType;
    }

    /**
     * 获取所有 RMA Reason
     * getAllRmaReason
     * @return mixed
     */
    public static function getAllRmaReason() {
        return self::$allRmaReason;
    }

    /**
     * 故障代码分类
     * @return    array    key为类型编码，val为类型名称
     */
    public static function getAllCodeType() {
        return self::$allCodeType;
    }

    /**
     * 获取所有状态信息
     * @return    array    key为状态，val为状态信息
     */
    public static function getAllStatus() {
        return self::$allStatus;
    }

    /**
     * 获取所有状态信息[java]
     * getAllStatusJava
     * @return array
     */
    public static function getAllStatusJava() {
        return self::$_allStatus;
    }

    /**
     * 获取所有状态信息
     * @return    array    key为状态，val为状态信息
     */
    public static function getAllMethod() {
        return self::$allMethod;
    }

    /**
     * 获取所有状态信息
     * @return    array    key为状态，val为状态信息
     */
    public static function getAllCarrier() {
        return self::$allCarrier;
    }

    /**
     * 获取所有日志类型
     * @return    array    key为状态，val为状态信息
     */
    public static function getAllLogType() {
        return self::$allLogType;
    }

    /**
     * 获取所有状态信息
     * @return    array    key为状态，val为状态信息
     */
    public static function getAllTreatmentTypes() {
        return self::$allTreatmentTypes;
    }

    /**
     * 获取所有状态信息2
     * @return    array    key为状态，val为状态信息
     */
    public static function getAllTreatmentTypes2() {
        return self::$allTreatmentTypes2;
    }

    /**
     * 获取所有状态信息
     * @return    array    key为状态，val为状态信息
     */
    public static function getAllTreatmentTypeTxts() {
        return self::$allTreatmentTypeTxts;
    }

    /**
     * 获取所有状态信息2
     * @return    array    key为状态，val为状态信息
     */
    public static function getAllTreatmentTypeTxts2() {
        return self::$allTreatmentTypeTxts2;
    }

    public static function getAllQuotationTypeCh() {
        return self::$allQuotationTypeCh;
    }

    public static function getAllQuotationTypeEn() {
        return self::$allQuotationTypeEn;
    }

    public static function getAllMalfunctionTypeCh() {
        return self::$allMalfunctionTypeCh;
    }

    public static function getAllMalfunctionTypeEn() {
        return self::$allMalfunctionTypeEn;
    }

    /**
     * 获取所有状态信息
     * @return    array    key为状态，val为状态信息
     */
    public static function getAllApplySheetStatus() {
        return self::$applySheetStatus;
    }

    /**
     * 获取处理单号
     * @return the $_handlingFormNO
     */
    public function getHandlingFormNO() {
        return $this->_handlingFormNO;
    }

    /**
     * 获取受理单
     */
    public function getAcceptOrder() {
        return $this->_acceptOrder;
    }

    /**
     * 获取网点编号
     * @return the $_branchNO
     */
    public function getBranchNO() {
        return $this->_branchNO;
    }

    /**
     * 获取受理员
     * @return the $_acceptor
     */
    public function getAcceptor() {
        return $this->_acceptor;
    }

    /**
     * 获取处理时间
     * @return the $_acceptTime
     */
    public function getAcceptTime() {
        return $this->_acceptTime;
    }

    /**
     * @return the $_type
     */
    public function getType() {
        return $this->_type;
    }

    /**
     * @return the $_sourceType
     */
    public function getSourceType() {
        return $this->_sourceType;
    }

    /**
     * @return the $_status
     */
    public function getStatus() {
        return $this->_status;
    }

    /**
     * @return the $_status
     */
    public function getApplySheetStatus() {
        return $this->_pplySheetStatus;
    }

    /**
     * 获取预约时间
     * @return the $_appointTime
     */
    public function getAppointTime() {
        return $this->_appointTime;
    }

    /**
     * @return the $_sendRepairUser
     */
    public function getSendRepairUser() {
        return $this->_sendRepairUser;
    }

    /**
     * @return the $_sendRepairUserPhone
     */
    public function getSendRepairUserPhone() {
        return $this->_sendRepairUserPhone;
    }

    /**
     * @return the $_surfaceDescription
     */
    public function getSurfaceDescription() {
        return $this->_surfaceDescription;
    }

    /**
     * 获取故障描述
     * @return the $_troubleDescription
     */
    public function getTroubleDescription() {
        return $this->_troubleDescription;
    }

    /**
     * @return the $_accesories
     */
    public function getAccesories() {
        return $this->_accesories;
    }

    /**
     * 检查结果
     * @return the $_checkStatus
     */
    public function getCheckStatus() {
        return $this->_checkStatus;
    }

    /**
     * @return the $_checkTime
     */
    public function getCheckTime() {
        return $this->_checkTime;
    }

    /**
     * @return the $_invoceNO
     */
    public function getInvoceNO() {
        return $this->_invoceNO;
    }

    /**
     * 获取初检备注
     * @return the $_memo
     */
    public function getMemo() {
        return $this->_memo;
    }

    /**
     * @return the $_engineer
     */
    public function getEngineer() {
        return $this->_engineer;
    }

    /**
     * @return the $_softVersion
     */
    public function getSoftVersion() {
        return $this->_softVersion;
    }

    /**
     * 获取处理时间
     * @return the $_processTime
     */
    public function getProcessTime() {
        return $this->_processTime;
    }

    /**
     * 获取处理内容
     * @return the $_processContent
     */
    public function getProcessContent() {
        return $this->_processContent;
    }

    /**
     * 获取新IMEI
     * @return the $_newIMEI
     */
    public function getNewIMEI() {
        return $this->_newIMEI;
    }

    /**
     * @return the $_takeMachineTime
     */
    public function getTakeMachineTime() {
        return $this->_takeMachineTime;
    }

    /**
     * @return the $_takeMachineUser
     */
    public function getTakeMachineUser() {
        return $this->_takeMachineUser;
    }

    /**
     * @return the $_takeMachineMemo
     */
    public function getTakeMachineMemo() {
        return $this->_takeMachineMemo;
    }

    /**
     * @param string $_handlingFormNO
     */
    public function setHandlingFormNO($_handlingFormNO) {
        $this->_handlingFormNO = $_handlingFormNO;
    }

    /**
     * @param string $_branchNO
     */
    public function setBranchNO($_branchNO) {
        $this->_branchNO = $_branchNO;
    }

    /**
     * @param string $_acceptor
     */
    public function setAcceptor($_acceptor) {
        $this->_acceptor = $_acceptor;
    }

    /**
     * 设置处理时间
     * @param string $acceptTime 接单时间
     */
    public function setAcceptTime($acceptTime) {
        if (is_numeric($acceptTime)) {
            $this->_acceptTime = Func::javaDate('Y-m-d H:i:s', $acceptTime);
            ;
        } else {
            $this->_acceptTime = $acceptTime;
        }
    }

    /**
     * 设置类型
     * @param int $type
     */
    public function setType($type) {
        $this->_type = intval($type);
    }

    /**
     * 设置来源类型
     * @param int $sourceType
     */
    public function setSourceType($sourceType) {
        $this->_sourceType = intval($sourceType);
    }

    /**
     * 设置状态
     * @param int $status 状态值
     */
    public function setStatus($status) {
        $this->_status = intval($status);
    }

    /**
     * 设置状态
     * @param int $status 状态值
     */
    public function setApplySheetStatus($applySheetStatus) {
        $this->_applySheetStatus = intval($applySheetStatus);
    }

    /**
     * 设置预约时间
     * @param string $appointTime
     */
    public function setAppointTime($appointTime) {
        if (is_numeric($appointTime)) {
            $this->_appointTime = Func::javaDate('Y-m-d', $appointTime);
            ;
        } else {
            $this->_appointTime = $appointTime;
        }
    }

    /**
     * @param string $_sendRepairUser
     */
    public function setSendRepairUser($_sendRepairUser) {
        $this->_sendRepairUser = $_sendRepairUser;
    }

    /**
     * @param string $_sendRepairUserPhone
     */
    public function setSendRepairUserPhone($_sendRepairUserPhone) {
        $this->_sendRepairUserPhone = $_sendRepairUserPhone;
    }

    /**
     * @param string $_surfaceDescription
     */
    public function setSurfaceDescription($_surfaceDescription) {
        $this->_surfaceDescription = $_surfaceDescription;
    }

    /**
     * @param string $_troubleDescription
     */
    public function setTroubleDescription($_troubleDescription) {
        $this->_troubleDescription = $_troubleDescription;
    }

    /**
     * @param string $_accesories
     */
    public function setAccesories($_accesories) {
        $this->_accesories = $_accesories;
    }

    /**
     * 设置检测状态
     * @param int $checkStatus
     */
    public function setCheckStatus($checkStatus) {
        $this->_checkStatus = intval($checkStatus);
    }

    /**
     * 设置复检时间
     * @param string $checkTime
     */
    public function setCheckTime($checkTime) {
        if (is_numeric($checkTime)) {
            $this->_checkTime = Func::javaDate('Y-m-d H:i:s', $checkTime);
        } else {
            $this->_checkTime = $checkTime;
        }
    }

    /**
     * 设置发票号
     * @param string $invoceNO
     */
    public function setInvoceNO($invoceNO) {
        $this->_invoceNO = $invoceNO;
    }

    /**
     * @param string $_memo
     */
    public function setMemo($_memo) {
        $this->_memo = $_memo;
    }

    /**
     * @param string $_engineer
     */
    public function setEngineer($_engineer) {
        $this->_engineer = $_engineer;
    }

    /**
     * @param string $_softVersion
     */
    public function setSoftVersion($_softVersion) {
        $this->_softVersion = $_softVersion;
    }

    /**
     * @param string $_processTime
     */
    public function setProcessTime($_processTime) {
        $this->_processTime = $_processTime;
    }

    /**
     * @param string $_processContent
     */
    public function setProcessContent($_processContent) {
        $this->_processContent = $_processContent;
    }

    /**
     * @param string $_newIMEI
     */
    public function setNewIMEI($_newIMEI) {
        $this->_newIMEI = $_newIMEI;
    }

    /**
     * 设置取机时间
     * @param string $takeMachineTime
     */
    public function setTakeMachineTime($takeMachineTime) {
        if (is_numeric($takeMachineTime)) {
            $this->_takeMachineTime = Func::javaDate('Y-m-d H:i:s', $takeMachineTime);
        } else {
            $this->_takeMachineTime = $takeMachineTime;
        }
    }

    /**
     * @param string $_takeMachineUser
     */
    public function setTakeMachineUser($_takeMachineUser) {
        $this->_takeMachineUser = $_takeMachineUser;
    }

    /**
     * @param string $_takeMachineMemo
     */
    public function setTakeMachineMemo($_takeMachineMemo) {
        $this->_takeMachineMemo = $_takeMachineMemo;
    }

    /**
     * 网点备注
     * @return type
     */
    public function getDescr() {
        return $this->_serviceNodeDescr;
    }

    public function setDescr($_serviceNodeDescr) {
        $this->_serviceNodeDescr = $_serviceNodeDescr;
    }

    /**
     * 获取销售订单号
     * @return    string
     */
    public function getSalesOrderNO() {
        return $this->_salesOrderNO;
    }

    /**
     * 获取受理单号
     * @return    string
     */
    public function getAcceptFormNO() {
        return $this->_acceptFormNO;
    }

    /**
     * 设置销售订单号
     * @param string $salesOrderNO
     */
    public function setSalesOrderNO($salesOrderNO) {
        $this->_salesOrderNO = $salesOrderNO;
    }

    /**
     * 设置受理单号
     * @param string $acceptFormNO
     */
    public function setAcceptFormNO($acceptFormNO) {
        $this->_acceptFormNO = $acceptFormNO;
    }

    /**
     * 获取IMEI号
     * @return string
     */
    public function getImei() {
        return $this->_imei;
    }

    /**
     * 设置IMEI号
     * @param string $imei imei号
     */
    public function setImei($imei) {
        $this->_imei = $imei;
    }

    /**
     * 设置受理单
     * @param mixed $acceptOrder
     */
    public function setAcceptOrder($acceptOrder) {
        $this->_acceptOrder = $acceptOrder;
    }

    /**
     * 设置受理单初检时间
     * @param mixed $firstCheckTime
     */
    public function setFirstCheckTime($firstCheckTime) {
        if (is_numeric($firstCheckTime)) {
            $this->_firstCheckTime = $firstCheckTime ? Func::javaDate('Y-m-d H:i:s', $firstCheckTime) : null;
        } else {
            $this->_firstCheckTime = $firstCheckTime;
        }
    }

    public function getFirstCheckTime() {
        return $this->_firstCheckTime;
    }

    /*     * ******************** 以下为转换方法			************************ */

    /**
     * (non-PHPdoc)
     * @see OPAdminModelBase::getRules()
     */
    public static function getRules() {
        return array(
            'treatSheetId' => array('method' => 'setHandlingFormNO')
            //销售订单
            ,
            'receiveUser' => array('method' => 'setAcceptor')
            //接单人员(受理员)
            ,
            'imeiNo' => array('method' => 'setImei')
            //imie
            ,
            'orderFlow' => array('method' => 'setSalesOrderNO')
            //销售订单号
            ,
            'reserveTime' => array('method' => 'setAppointTime')
            //预约时间
            ,
            'serviceNodeCode' => array('method' => 'setBranchNO')
            //网点编号
            ,
            'sender' => array('method' => 'setSendRepairUser')
            //送修人
            ,
            'senderTel' => array('method' => 'setSendRepairUserPhone')
            //送修人电话
            ,
            'surfaceDescr' => array('method' => 'setSurfaceDescription')
            //外观描述
            ,
            'malfunctionDescr' => array('method' => 'setTroubleDescription')
            //故障描述
            ,
            'attachment' => array('method' => 'setAccesories')
            //随机附件
            ,
            'inspectResult' => array('method' => 'setCheckStatus')
            //检测结果
            ,
            'applySheetStatus' => array('method' => 'setApplySheetStatus')
            //领退料结果
            ,
            'initialMemo' => array('method' => 'setMemo')
            //初检备注
            ,
            'receiptNo' => array('method' => 'setInvoceNO')
            //发票号码
            ,
            'recheckTime' => array('method' => 'setCheckTime')
            //复检时间
            ,
            'softwareVersion' => array('method' => 'setSoftVersion')
            //软件版本
            ,
            'totalPrice' => array('method' => 'setTotalPrice')
            //报价总额
            ,
            'attitude' => array('method' => 'setCustomerViewpoint')
            //客户意见
            //,'imeiNew'	=> 	array('method'	=> 'setNewIMEI')	//新IMEI号[失效]
            ,
            'handleTime' => array('method' => 'setAcceptTime')
            //处理时间
            ,
            'handleMemo' => array('method' => 'setProcessContent')
            //处理内容
            ,
            'pickupTime' => array('method' => 'setTakeMachineTime')
            //取机时间
            ,
            'pickuper' => array('method' => 'setTakeMachineUser')
            //取机人
            ,
            'pickupMemo' => array('method' => 'setTakeMachineMemo')
            //取机备注
            ,
            'sourceId' => array('method' => 'setAsHandlingFormNO')
            //售后处理单号
            ,
            'customerId' => array('method' => 'setUserId')
            //用户ID
            ,
            'acceptTime' => array('method' => 'setAcceptFormCreateTime')
            //受理单创建时间
            ,
            'firstCheckTime' => array('method' => 'setFirstCheckTime')
            //受理单初检时间
            ,
            'goodsCodeIMEIs' => array('method' => 'setGoodsCodeIMEIs')
            //手机保险信息
            ,
            'discountPrice' => array('method' => 'setDiscountPrice')
            //折扣信息
            ,
            'serviceNodeDescr' => array('method' => 'setDescr')
            //折扣信息
            ,
            'modifyUser' => array('method' => 'setModifyUser')
            //最后更新人
            ,
            'modifyTime' => array('method' => 'setModifyTime')
            //最后更新时间
            ,
            'handleSolution' => array('method' => 'setHandleSolution')
            //处理方案
            ,
            'serviceCode' => array('method' => 'setServiceCode')
            //处理方案
            ,
            'isThirdParty' => array('method' => 'setIsThirdParty')
            //是否为第三方售后
            ,
            'repairLevel' => array('method' => 'setRepairLevel')
            //维修等级
            ,
            'settlementPrice' => array('method' => 'setSettlementPrice')
                //结算金额
        );
    }

    /*     * ******************* 	辅助方法				************************ */

    /**
     * 获取状态对应的文本
     * @return    string
     */
    public function getStatusText() {
        if (isset(self::$allStatus[$this->_status])) {
            return self::$allStatus[$this->_status];
        } else {
            return '未知';
        }
    }

    /**
     * 获取状态对应的文本
     * @return    string
     */
    public function getApplySheetStatusText() {
        if (isset(self::$applySheetStatus[$this->_applySheetStatus])) {
            return self::$applySheetStatus[$this->_applySheetStatus];
        } else {
            return '未知';
        }
    }

    /**
     * 获取类型对应的文本
     * @return    string
     */
    public function getTypeText() {
        if (isset(self::$allType[$this->_type])) {
            return self::$allType[$this->_type];
        } else {
            return '未知';
        }
    }

    /**
     * 获取来源类型对应的文本
     * @return    string
     */
    public function getSourceTypeText() {
        if (isset(self::$allSourceType[$this->_sourceType])) {
            return self::$allSourceType[$this->_sourceType];
        } else {
            return '未知';
        }
    }

    /**
     * 是否处理完成
     * @return    bool
     */
    public function isFinished() {
        return in_array($this->_status, array(
            self::STATUS_NOT_PASSED,
            self::STATUS_PROCESSED,
            self::STATUS_CABCEKED
        ));
    }

    /**
     * 检测结果是否为非保
     * @todo:先写死状态码
     * @return    bool
     */
    public function isCheckStatusNotInsure() {
        return in_array($this->getCheckStatus(), array(
            15,
            25,
            35
        ));
    }

    /**
     * 转换成数组
     * @return    array
     */
    public function toArray() {
        return get_object_vars($this);
    }

    /*     * ******************** 以下开始为业务逻辑相关方法	************************ */

    /**
     * 获取服务
     * @return    AfterSalesService
     */
    private function getService() {
        static $service;
        if (true !== ($service instanceof AfterSalesService)) {
            Yii::import('application.models.common.AfterSalesService', true);
            $serviceUrl = AfterSalesService::getServiceUrl(AfterSalesService::AFTER_SALES_SERVICE_KEY);
            $service = new AfterSalesService($serviceUrl);
        }
        return $service;
    }

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
     * 获取退换货管理器model
     * @return    AcceptManageModel
     */
    private function getAcceptManager() {
        static $model;
        if (true !== ($model instanceof AcceptManageModel)) {
            Yii::import('application.models.acceptmodel.AcceptManageModel', true);
            $model = new AcceptManageModel();
        }
        return $model;
    }

    /**
     * 设置初检中
     * @param    string $currentUser 当前操作人
     * @return    OnePlusServiceResponse
     */
    public function changeToFirstChecking($currentUser) {
        $service = $this->getService();
        $service->setCurrentUser($currentUser);
        $ret = $service->initialCheckTreatSheet($this->getHandlingFormNO());
        if (true !== $ret->isSuccess()) {
            $this->addLog(__METHOD__ . ':' . __LINE__ . '，设置处理单为初检中错误,处理单:' . $this->_handlingFormNO . ',结果:' . var_export($ret, 1));
        }
        return $ret;
    }

    /**
     * 保存初检信息
     * @return    OnePlusServiceResponse
     */
    public function saveFirstCheckInfo($currentUser) {
        $service = $this->getService();
        $service->setCurrentUser($currentUser);
        $arrParam = array(
            'treatSheetId' => $this->getHandlingFormNO(),
            'sender' => $this->getSendRepairUser()
            //送修人
            ,
            'senderTel' => $this->getSendRepairUserPhone()
            //送修人电话
            ,
            'surfaceDescr' => $this->getSurfaceDescription()
            //外观描述
            ,
            'malfunctionDescr' => $this->getTroubleDescription()
            //故障描述
            ,
            'attachment' => $this->getAccesories()
            //随机附件
            ,
            'inspectResult' => $this->getCheckStatus()
            //检测结果
            ,
            'receiptNo' => $this->getInvoceNO()
            //发票号
            ,
            'initialMemo' => $this->getMemo()
            //备注
            ,
            'engineer' => $this->getEngineer()
                //工程师
        );
        $ret = $service->updateTreatSheet($arrParam);
        if (true !== $ret->isSuccess()) {
            $this->addLog(__METHOD__ . ':' . __LINE__ . ',保存初检信息错误,参数:' . var_export($arrParam, 1) . ',结果:' . var_export($ret, 1));
            return $ret;
        }
        return $ret;
    }

    /**
     * 设置为复检中
     * @param    string $currentUser 当前操作用户
     * @return    OnePlusServiceResponse
     */
    public function changeToSecondChecking($currentUser) {
        $service = $this->getService();
        $service->setCurrentUser($currentUser);
        $arrParam = array(
            'treatSheetId' => $this->_handlingFormNO,
            'sender' => $this->getSendRepairUser()
            //送修人
            ,
            'senderTel' => $this->getSendRepairUserPhone()
            //送修人电话
            ,
            'surfaceDescr' => $this->getSurfaceDescription()
            //外观描述
            ,
            'malfunctionDescr' => $this->getTroubleDescription()
            //故障描述
            ,
            'attachment' => $this->getAccesories()
            //随机附件
            ,
            'inspectResult' => $this->getCheckStatus()
            //检测结果
            ,
            'receiptNo' => $this->getInvoceNO()
            //发票号
            ,
            'initialMemo' => $this->getMemo()
            //备注
            ,
            'engineer' => $this->getEngineer()
                //工程师
        );
        $ret = $service->reCheckTreatSheet($arrParam);
        if (true !== $ret->isSuccess()) {
            $this->addLog(__METHOD__ . ':' . __LINE__ . ',将处理单设置为复检中错误,参数:' . var_export($arrParam, 1) . ',结果:' . var_export($ret, 1));
        }
        return $ret;
    }

    /**
     * 保存复检信息
     * @param    string $currentUser 当前用户
     * @return    OnePlusServiceResponse
     */
    public function saveSecondCheckInfo($currentUser) {
        $service = $this->getService();
        $service->setCurrentUser($currentUser);
        //检测时间
        $checkTime = $this->getCheckTime();
        if (!empty($checkTime)) {
            $checkTime = Func::javaStrToTime($checkTime);
        }
        $arrParam = array(
            'treatSheetId' => $this->getHandlingFormNO(),
            'recheckTime' => $checkTime
            //复检时间
            ,
            'surfaceDescr' => $this->getSurfaceDescription()
            //外观描述
            ,
            'malfunctionDescr' => $this->getTroubleDescription()
            //故障描述
            ,
            'softwareVersion' => $this->getSoftVersion()
            //软件版本
            ,
            'inspectResult' => $this->getCheckStatus()
            //检测结果
            ,
            'attitude' => $this->getCustomerViewpoint()
            //客户意见
            ,
            'goodsCodeIMEIs' => $this->getGoodsCodeIMEIs()
            //保险信息对应手机IMEI号的数组//
            ,
            'discountPrice' => $this->getDiscountPrice(),
            'handleSolution' => $this->getHandleSolution()
            //处理方案
            ,
            'repairLevel' => $this->getRepairLevel(),
            'settlementPrice' => $this->getSettlementPrice()
        );
        $ret = $service->updateTreatSheet($arrParam);
        if (true !== $ret->isSuccess()) {
            $this->addLog(__METHOD__ . ':' . __LINE__ . ',保存复检信息错误,参数:' . var_export($arrParam, 1) . ',结果:' . var_export($ret, 1));
        }
        return $ret;
    }

    /**
     * 设置为处理中
     * @param    string $currentUser 当前用户
     * @param    string $userId 用户ID
     * @return    OnePlusServiceResponse
     */
    public function changeToProcessing($currentUser, $userId) {
        $service = $this->getService();
        $service->setCurrentUser($currentUser);
        //时间要作转换
        $checkTime = $this->getCheckTime();
        if (!empty($checkTime)) {
            $checkTime = Func::javaStrToTime($checkTime);
        }
        $arrParam = array(
            'treatSheetId' => $this->getHandlingFormNO(),
            'recheckTime' => $checkTime
            //复检时间
            ,
            'surfaceDescr' => $this->getSurfaceDescription()
            //外观描述
            ,
            'malfunctionDescr' => $this->getTroubleDescription()
            //故障描述
            ,
            'softwareVersion' => $this->getSoftVersion()
            //软件版本
            ,
            'inspectResult' => $this->getCheckStatus()
            //检测结果
            //,'attitude'	=> $this->getCustomerViewpoint()	//客户意见
            ,
            'attitude' => self::CUSTOMER_AGREE
            //必须同意
            ,
            'goodsCodeIMEIs' => $this->getGoodsCodeIMEIs()
            //获取处理单商品保险号对应集
            ,
            'discountPrice' => $this->getDiscountPrice(),
            'handleSolution' => $this->getHandleSolution(),
            'repairLevel' => $this->getRepairLevel(),
            'settlementPrice' => $this->getSettlementPrice()
        );

        $ret = $service->handleTreatSheet($arrParam);
        if (true !== $ret->isSuccess()) {
            $this->addLog(__METHOD__ . ':' . __LINE__ . ',将处理单设置为处理中错误,参数:' . var_export($arrParam, 1) . ',结果:' . var_export($ret, 1));
            return $ret;
        }
        //根据条件来设置不同的状态
        switch ($this->_type) {
            //     		case self::TYPE_SWAP:
            //     			//换货
            //     			$ret = $this->getAcceptManager()->checkSwapFormPassed($this->_asHandlingFormNO, $userId);
            //     			break;
            case self::TYPE_REPAIR:
                //维修
                $ret = $this->getAcceptManager()->checkRepairFormPassed($this->_asHandlingFormNO);
                break;
        }
        return $ret;
    }

    /**
     * 保存处理信息
     * @param    string $currentUser 当前操作用户
     * @return    OnePlusServiceResponse
     */
    public function saveProcessInfo($currentUser) {
        $service = $this->getService();
        $service->setCurrentUser($currentUser);
        //处理时间
        $processTime = $this->getProcessTime();
        if (!empty($processTime)) {
            $processTime = Func::javaStrToTime($processTime);
        }
        $arrParam = array(
            'treatSheetId' => $this->getHandlingFormNO(),
            'handleTime' => $processTime
            //处理时间
            ,
            'handleMemo' => $this->getProcessContent()
            //处理内容
            //,'imeiNew'	=> $this->getNewIMEI()	//新imei号@作废
            ,
            'handleType' => $this->getHandleType()
                //处理方式
        );

        $ret = $service->updateTreatSheet($arrParam);
        if (true !== $ret->isSuccess()) {
            $this->addLog(__METHOD__ . ':' . __LINE__ . ',保存处理单的处理信息错误,参数:' . var_export($arrParam, 1) . ',结果:' . var_export($ret, 1));
            return $ret;
        }
        return $ret;
    }

    /**
     * 设置状态为取机中
     * @param    string $currentUser 当前用户
     * @return    OnePlusServiceResponse
     */
    public function changeToTakeMachine($currentUser) {
        $service = $this->getService();
        $service->setCurrentUser($currentUser);
        //时间转换
        $processTime = $this->getProcessTime();
        if (!empty($processTime)) {
            $processTime = Func::javaStrToTime($processTime);
        } else {
            $processTime = time();
        }
        $arrParam = array(
            'treatSheetId' => $this->getHandlingFormNO(),
            'handleTime' => $processTime
            //处理时间
            ,
            'handleMemo' => $this->getProcessContent()
            //处理内容
            ,
            'handleType' => $this->getHandleType()
                //,'imeiNew'	=> $this->getNewIMEI()	//新imei号@作废
        );
        $ret = $service->pickupTreatSheet($arrParam);
        if (true !== $ret->isSuccess()) {
            $this->addLog(__METHOD__ . ':' . __LINE__ . ',将处理单状态置为取机时错误,参数:' . var_export($arrParam, 1) . ',结果:' . var_export($ret, 1));
            return $ret;
        }
        return $ret;
    }

    /**
     * 保存取机信息
     * @param    string $currentUser 当前用户
     * @return    OnePlusServiceResponse
     */
    public function saveTakeMachineInfo($currentUser) {
        $service = $this->getService();
        $service->setCurrentUser($currentUser);
        $arrParam = array(
            'treatSheetId' => $this->getHandlingFormNO(),
            'pickupTime' => Func::javaStrtotime($this->getTakeMachineTime())
            //取机时间
            ,
            'pickuper' => $this->getTakeMachineUser()
            //取机人
            ,
            'pickupMemo' => $this->getTakeMachineMemo()
            //取机备注
            ,
            'expressCompany' => $this->getExpressCompany()
            //商品寄回物流公司
            ,
            'expressNo' => $this->getExpressNo()
                //商品寄回物流单号
        );
        $ret = $service->updateTreatSheet($arrParam);
        if (true !== $ret->isSuccess()) {
            $this->addLog(__METHOD__ . ':' . __LINE__ . ',保存取机信息错误,参数:' . var_export($arrParam, 1) . ',结果:' . var_export($ret, 1));
            return $ret;
        }
        return $ret;
    }

    /**
     * 处理完成
     * @param    string $currentUser 当前用户
     * @param    AddressEntity $addressDto 地址DTO信息，现在没有用了
     * @param    stdClass $asHandlingForm 售后处理单信息,其中depreciateRepairDto成员为折旧维修单信息
     * @return    OnePlusServiceResponse
     */
    public function finish($currentUser, $addressDto, $asHandlingForm, $logArray = array()) {
        $service = $this->getService();
        $service->setCurrentUser($currentUser);

        //更新折旧维修单信息

        $ret = $this->updateDepreciateRepair($currentUser, $asHandlingForm);
        if (true !== $ret->isSuccess()) {
            return $ret;
        }

        //如果类型为换货的，要将本处理单的imei号同步到订单系统中
        if ($this->getSalesOrderNO()) {//第三方售后不需要更新订单IMEI号
            $ret = $this->updateSalesOrderIMEI();
            if (true !== $ret->isSuccess()) {
                return $ret;
            }
        }

        //根据类型调用售后接口，改变成相应的状态
        switch ($this->_type) {
            case self::TYPE_REJECT:
                //退货
                //$ret = $this->getAcceptManager()->checkRejectFormPassed($this->_asHandlingFormNO, $addressDto);
                //实际上css作了退货处理
                $ret = $this->getAcceptManager()->checkRejectFormPassed($this->_asHandlingFormNO, null);
                break;
            case self::TYPE_REPAIR:
                $ret = $this->getAcceptManager()->finishRepairForm($this->_asHandlingFormNO);
                //维修
                break;
            case self::TYPE_SWAP:
                //换货
                $isThirdParty = $this->getSalesOrderNO() ? 0 : 1; //是否是第三方售后
                $ret = $this->getAcceptManager()->finishSwapForm($this->_asHandlingFormNO, $isThirdParty);
                break;
        }
        //是否成功
        if (true !== $ret->isSuccess()) {
            $this->addLog(__METHOD__ . ':' . __LINE__ . ',完成处理单，修改售后的处理单信息错误,' . $this->_asHandlingFormNO . ',结果:' . var_export($ret, 1));
            return $ret;
        }

        //修改本地处理单的状态
        $arrParam = array(
            'treatSheetId' => $this->getHandlingFormNO(),
            'pickupTime' => Func::javaStrtotime($this->getTakeMachineTime())
            //取机时间
            ,
            'pickuper' => $this->getTakeMachineUser()
            //取机人
            ,
            'pickupMemo' => $this->getTakeMachineMemo()
            //取机备注
            ,
            'expressCompany' => $this->getExpressCompany()
            //商品寄回物流公司
            ,
            'expressNo' => $this->getExpressNo()
                //商品寄回物流单号
        );

        $ret = $service->finishTreatSheet($arrParam);
        if (true !== $ret->isSuccess()) {
            $this->addLog(__METHOD__ . ':' . __LINE__ . ',完成处理单错误,参数:' . var_export($arrParam, 1) . ',结果:' . var_export($ret, 1));
            return $ret;
        }
        if ($this->_type == self::TYPE_SWAP && $this->getSalesOrderNO()) {//第三方不用更新订单状态
            $orderModel = new OrderModel();
            $orderStatus = 63;
            $ret = $orderModel->updateSwapOrderStatus($this->getAsHandlingFormNO(), $orderStatus);
            if (true !== $ret->isSuccess()) {
                $this->addLog(__METHOD__ . ':' . __LINE__ . ',更新换货处理单产生的新订单的状态出错,参数:处理单单号' . $this->getAsHandlingFormNO() . '- 新订单状态：' . $orderStatus . ',结果:' . var_export($ret, 1));
                return $ret;
            }
        }
        return $this->updateGoodsWarranty($logArray);
    }

    ////更新处理单中商品保险使用状态
    public function updateGoodsWarranty($logArray) {
        $nowTime = time();
        $salesOrderNO = $this->getHandlingFormNO();
        $asHandlingFormNO = $this->getAsHandlingFormNO();
        $ret = $tsGoodsRet = $this->listTreatSheetGoods($salesOrderNO);
        if (true !== $ret->isSuccess()) {
            $this->addLog(__METHOD__ . ':' . __LINE__ . ',查询申请单所有商品记录:' . $salesOrderNO . '错误,结果:' . var_export($ret, 1));
            return $ret;
        }
        $useEnsureArray = array();
        //获取已使用的保险信息
        $orderGoodsInfo = $ret->getData();
        //新iMEI号对应旧IMEI号数据
        $newImeiList = array();
        if ($orderGoodsInfo && is_array($orderGoodsInfo)) {
            $_tmpOrderGoodsInfo = array();
            $_handType = $this->getType();
            $_newImei = '';
            foreach ($orderGoodsInfo as $value) {
                if (empty($value['imeiOld'])) {
                    continue;
                }
                if ($value['imeiNew']) {
                    $_newImei = $newImeiList[$value['imeiOld']] = $value['imeiNew'];
                }
                if (empty($value['insuranceIds'])) {
                    continue;
                }
                $_tmpArray = explode(',', $value['insuranceIds']);
                foreach ($_tmpArray as $_value) {
                    if (empty($_value))
                        continue;
                    $params = array(
                        'warranty_code' => $_value,
                        'handle_type' => $_handType,
                    );
                    //如果是换货类型处理单
                    if ($_handType == HandlingFormModel::TYPE_SWAP) {
                        $params['new_imei'] = $_newImei;
                        $params['deliver_over_time'] = $nowTime; //换货生成新的IMEI号的订单投档时间
                        $params['order_flow'] = $asHandlingFormNO;
                    }
                    //保存已经使用的保险
                    $useEnsureArray[] = $_value;
                    $ret = $this->useGoodsWarranty($params, $logArray);
                    if (true !== $ret->isSuccess()) {
                        $this->addLog(__METHOD__ . ':' . __LINE__ . ',完成处理单，更新售后的处理单保险信息发生错误,' . var_export($params, 1) . ',结果:' . var_export($ret, 1));
                        //return $ret;
                    }
                }
            }
        } else {
            return new OnePlusServiceResponse(OnePlusServiceResponse::RET_SUCCESS, '', null);
        }

        //是换货的时候(或者主板维修)才需要将处理单上未使用的保险信息传递保险使用接口
        if ($_handType == HandlingFormModel::TYPE_SWAP || $_handType == HandlingFormModel::TYPE_REPAIR) {
            //获取订单上所有属于处理单商品的保险信息
            $billModel = new ExtendedWarrantyBillModel();
            $pageParam = array(
                'pageSize' => 100,
                'currentPage' => 1
            );

            foreach ($newImeiList as $oldImei => $newImei) {
                $allEnsureArray = array();
                $arrSearch = array('goods_imei' => $oldImei);
                $insure_ret = $billModel->queryList($arrSearch, $pageParam);
                if ($insure_ret['ret'] === 0) {//查询成功切有数据返回
                    if (isset($insure_ret['data']) && is_array($insure_ret['data'])) {
                        $insures = $insure_ret['data'];
                        foreach ($insures as $ins_val) {
                            $goodsImei = $newImei;
                            //该保险如果没有被勾选
                            if (!in_array($ins_val['warranty_code'], $useEnsureArray)) {
                                if (!isset($allEnsureArray[$goodsImei]))
                                    $allEnsureArray[$goodsImei] = array();
                                $allEnsureArray[$goodsImei][] = $ins_val['warranty_code'];
                            }
                        }
                    }

                    foreach ($allEnsureArray as $newImei => $ensureCodeArray) {
                        foreach ($ensureCodeArray as $ensureCode) {
                            if (empty($ensureCode))
                                continue;
                            $params = array(
                                'warranty_code' => $ensureCode,
                                'handle_type' => $_handType,
                                'new_imei' => $newImei,
                                'deliver_over_time' => $nowTime,
                                'order_flow' => ($_handType == HandlingFormModel::TYPE_REPAIR ? $this->getSalesOrderNO() : $asHandlingFormNO)
                            );
                            $ret = $this->passUnUseWarranty($params, $logArray, (!$this->getSalesOrderNO() ? true : false));
                            if (true !== $ret->isSuccess()) {
                                $this->addLog(__METHOD__ . ':' . __LINE__ . ',完成处理单，传递处理单未使用的保险信息时发生错误,' . var_export($params, 1) . ',结果:' . var_export($ret, 1));
                                //return $ret;
                            }
                        }
                    }
                } else {//查询失败
                    Log::addLog(__METHOD__ . ':' . __LINE__ . ',通过imei号查找保险信息失败! imei:' . var_export($arrSearch, true));
                    //return new OnePlusServiceResponse(OnePlusServiceResponse::RET_ERROR, OnePlusException::PARAM_ERROR, '传递处理单未使用的保险信息时，通过imei号查找保险信息失败');
                }
            }
        }
        return new OnePlusServiceResponse(OnePlusServiceResponse::RET_SUCCESS, '', null);
    }

    /**
     * 更新或添加折旧维修单信息
     * @param    string $currentUser 当前操作人
     * @param    stdClass    stdClass    $asHandlingForm    售后处理单信息,其中depreciateRepairDto成员为折旧维修单信息
     * @return    OnePlusServiceResponse
     */
    private function updateDepreciateRepair($currentUser, $asHandlingForm) {
        //是否有折旧金额?
        if (in_array($this->getType(), array(
                    self::TYPE_REJECT,
                    self::TYPE_SWAP
                ))) {
            //没有折旧维修单，则创建，有则修改
            Yii::import('application.models.acceptmodel.AcceptManageModel');
            $acceptManageModel = new AcceptManageModel();

            if (is_numeric($this->_totalPrice)) {
                $nonDefendFee = floatval($this->_totalPrice);
            } else {
                $nonDefendFee = 0;
            }
            if (is_numeric($this->_discountPrice)) {
                $discountFee = floatval($this->_discountPrice);
            } else {
                $discountFee = 0;
            }
            //目前非保价项费用 = 非保价项费用总额 - 折扣金额
            $nonDefendFee = $nonDefendFee - $discountFee > 0 ? floatval($nonDefendFee - $discountFee) : 0;
            //$nonDefendFee = Func::pricePHP2Java($nonDefendFee);
            //是否为空?
            if (empty($asHandlingForm->depreciateRepairDto)) {
                $checkStatus = $this->getCheckStatus(); //检测结果为退货、换货正常的不需要新增折旧维修单
                if (in_array($checkStatus, array(
                            20,
                            10
                        ))) {//20 换机-正常 10 退机-正常
                    return new OnePlusServiceResponse(OnePlusServiceResponse::RET_SUCCESS, 0, '');
                }
                $repairForm = new DepreciateRepairEntity();
                //来源单号
                switch ($this->getType()) {
                    case self::TYPE_REJECT:
                        $repairForm->sourceNo = $asHandlingForm->rejectAcceptanceNo;
                        break;
                    case self::TYPE_SWAP:
                        $repairForm->sourceNo = $asHandlingForm->swapAcceptanceNo;
                        break;
                }
                $repairForm->sourceChannel = $this->getType();
                $repairForm->createUser = $currentUser;
                switch ($this->getType()) {
                    case self::TYPE_REJECT:
                        $repairForm->payType = AcceptManageModel::PAY_TYPE_DISCOUNT;
                        break;
                    case self::TYPE_SWAP:
                        $repairForm->payType = AcceptManageModel::PAY_TYPE_BRANCH;
                        break;
                }
                $repairForm->remark = '网点自动生成';
                $repairForm->userId = $asHandlingForm->userId;
                $repairForm->delayFee = 0;
                $repairForm->nonDefendFee = $nonDefendFee;
                //@todo:写死
                $repairForm->state = 3;
                //调用服务
                $repairFormRet = $acceptManageModel->addDepreciateRepair($repairForm);
                if (true !== $repairFormRet->isSuccess()) {
                    $this->addLog(__METHOD__ . ':' . __LINE__ . ',完成处理单，添加折旧维修单错误，参数:' . var_export($repairForm, 1) . ',结果:' . var_export($repairFormRet, 1));
                }
                return $repairFormRet;
            } else {
                // 折旧维修单为已支付且类型为换货 就不需要在更新其状态了
                if ($asHandlingForm->depreciateRepairDto['state'] == 3 && $asHandlingForm->depreciateRepairDto['sourceChannel'] == self::TYPE_SWAP) {
                    $this->addLog(__METHOD__ . ':' . __LINE__ . ',完成处理单，更新折旧维修单错误，参数:' . var_export(array(
                                $this->getAsHandlingFormNO(),
                                $nonDefendFee
                                    ), 1) . ',结果:因为折旧维修单已为支付了,且为换货，所以就不用再对其更新了。');
                    return new OnePlusServiceResponse(OnePlusServiceResponse::RET_SUCCESS, 0, '');
                }

                //这时取已经到网点的，要不改成取售后中的受理单号?
                $repairNO = $asHandlingForm->depreciateRepairDto['repairNo'];
                if ($asHandlingForm->depreciateRepairDto['state'] == 3 && $this->_type != HandlingFormModel::TYPE_REJECT) {//折旧维修单状态为已支付,并且不为退货，就不需要任何更新了
                    $this->addLog(__METHOD__ . ':' . __LINE__ . ',完成处理单，更新折旧维修单提示，参数:' . var_export(array(
                                $this->getAsHandlingFormNO(),
                                $nonDefendFee
                                    ), 1) . ',结果:因为折旧维修单已为支付了,且为不为退货，所以就不用再对其更新了。');
                    return new OnePlusServiceResponse(OnePlusServiceResponse::RET_SUCCESS, 0, '');
                }
                $state = $nonDefendFee > 0 ? 3 : 2; //如果非保价金额等于0，那么状态为待支付,否则为已支付 
                //处理单-换货正常、退货正常，不用修改支付状态
                $checkStatus = $this->getCheckStatus(); //处理单-换货正常、退货正常，不用修改支付状态
                if (in_array($checkStatus, array(
                            20,
                            10
                        ))) {//20 换机-正常 10 退机-正常 折旧费用为零
                    $nonDefendFee = 0;
                    if ($checkStatus == 10) {//退货正常的仍然为已支付，因为其支付方式为抵扣
                        $state = 3;
                    }
                }
                $repairFormRet = $acceptManageModel->updateDepreciateRepair($repairNO, $nonDefendFee, $state);
                if (true !== $repairFormRet->isSuccess()) {
                    $this->addLog(__METHOD__ . ':' . __LINE__ . ',完成处理单，更新折旧维修单错误，参数:' . var_export(array(
                                $this->getAsHandlingFormNO(),
                                $nonDefendFee
                                    ), 1) . ',结果:' . var_export($repairFormRet, 1));
                }
                return $repairFormRet;
            }
        } else {
            return new OnePlusServiceResponse(OnePlusServiceResponse::RET_SUCCESS, 0, '');
        }
    }

    /**
     * 更新销售订单的IMEI号
     * @return OnePlusServiceResponse
     * @notice 处理单修改为多IMEI号 2014.0806
     */
    private function updateSalesOrderIMEI() {
        $type = $this->getType();
        if ($type !== self::TYPE_SWAP) {
            return new OnePlusServiceResponse(OnePlusServiceResponse::RET_SUCCESS, 0, '');
        }
        $handingFormNo = $this->getAsHandlingFormNO();
        $salesOrderNO = $this->getHandlingFormNO();
        $ret = $this->listTreatSheetGoods($salesOrderNO);
        if (true !== $ret->isSuccess()) {
            $this->addLog(__METHOD__ . ':' . __LINE__ . ',查询申请单所有商品记录:' . $salesOrderNO . '错误,结果:' . var_export($ret, 1));
            return $ret;
        }
        $goodsListArray = $ret->getData();
        $bool = false;
        $goodsImeiArray = array();
        //只处理手机
        foreach ($goodsListArray as $goods) {
            if (is_string($goods['imeiNew']) && strlen($goods['imeiNew']) > 0 && is_string($goods['imeiOld']) && strlen($goods['imeiOld']) > 0) {
                $bool = true;
                $goodsImeiArray[$goods['imeiOld']] = $goods['imeiNew'];
            }
        }

        if ($bool) {
            //获取销售订单信息,找出哪个为手机
            Yii::import('application.models.order.OrderModel');
            Yii::import('application.models.acceptmodel.AcceptCommon');
            $salesOrderModel = new OrderModel();
            //销售订单号,要用新订单的
            //$salesOrderNO = $this->getSalesOrderNO();
            //处理单号是新订单号，这个规则只有换货单适用
            $salesOrderNO = $handingFormNo;
            $ret = $salesOrderModel->searchByOrderID($salesOrderNO);
            if (true !== $ret->isSuccess()) {
                $this->addLog(__METHOD__ . ':' . __LINE__ . ',通过销售订单号搜索订单:' . $salesOrderNO . '错误,结果:' . var_export($ret, 1));
                return $ret;
            }
            //销售订单信息
            $salesOrderData = $ret->getData();

            if (count($salesOrderData) < 1) {
                $ret->setFailed(OnePlusException::PARAM_ERROR);
                $this->addLog(__METHOD__ . ':' . __LINE__ . ',修改销售订单的imei时订单不存在,订单:' . $salesOrderNO . ',处理单:' . $this->getHandlingFormNO());
                $ret->setErrMsg('找不到销售订单:' . $salesOrderNO);
                return $ret;
            }
            $salesOrder = (Object) $salesOrderData[0];
            $orderGoodsDetailDTOList = $salesOrder->orderGoodsDetailDTOList;
            //商品信息map
            $goodsInfoList = array();

            foreach ($orderGoodsDetailDTOList as $goodsItem) {
                //是否为手机
                $goodsItem = (Object) $goodsItem;
                //根据商品类型来，1：普通商品，2：套装商品；普通商品找外层的catId，套装商品找里面的catId
                //goodsType,pkgGoodsDetailDTOList
                $good_imei = $goodsItem->oldimei;
                if (!$good_imei) {//不是手机则跳过
                    continue;
                }
                $goodCode = $goodsItem->goodsCode;
                $goodsType = intval($goodsItem->goodsType);
                $imeiArray = explode(',', $good_imei);
                $newImei = '';
                foreach ($imeiArray as $item) {
                    if (!empty($goodsImeiArray[$item])) {
                        $newImei .= ($newImei == '' ? '' : ',') . $goodsImeiArray[$item];
                    } else {
                        continue;
                    }
                }
                if (empty($newImei)) {
                    continue;
                }
                switch ($goodsType) {
                    case AcceptCommon::GOODS_TYPE_NORMAL:
                        $categoryID = $goodsItem->catId;
                        if ($this->isMobileCategory($categoryID)) {
                            //设置IMEI号
                            $goodsItem->imei = $newImei;
                            $goodsInfoList[] = $goodsItem;
                        }
                        break;
                    /* 套装暂时没有手机所以不用更新IMEI号
                     * case AcceptCommon::GOODS_TYPE_PACKET:
                      //套装商品要遍历
                      for($j=0; $j<count($goodsItem->pkgGoodsDetailDTOList); $j++){
                      $categoryID = $goodsItem->pkgGoodsDetailDTOList[$j]['catId'];
                      if ($this->isMobileCategory($categoryID)){
                      $goodsItem->pkgGoodsDetailDTOList[$j]['imei'] = $newImei;
                      //$goodsInfoList[$goodCode] = $goodsItem;冯之浩，订单接口已经修改
                      $goodsInfoList[] = $goodsItem;
                      break;
                      }
                      }
                      break;
                     */
                }
            }
            if (count($goodsInfoList) < 1) {
                $this->addLog(__METHOD__ . ':' . __LINE__ . ',完成处理单，类型为换货，imei号不为空，但销售订单中无手机信息:处理单编号' . $handingFormNo . ',订单明细:' . var_export($salesOrder, 1) . ',不修改');
                return $ret;
            }
            //调用销售订单接口修改
            $ret = $salesOrderModel->updateOrderIMEI($handingFormNo, $goodsInfoList);

            if (true !== $ret->isSuccess()) {
                $this->addLog(__METHOD__ . ':' . __LINE__ . ',完成处理单，类型为换货，修改imei号错误,参数:' . var_export(array(
                            $salesOrderNO,
                            $goodsInfoList
                                ), 1) . ',参数:' . var_export($ret, 1));
                return $ret;
            }
        } else {
            $this->addLog(__METHOD__ . ':' . __LINE__ . ',完成处理单，类型为换货，但imei号为空:' . var_export($goodsImeiArray, 1) . ',不修改');
            $ret = new OnePlusServiceResponse(OnePlusServiceResponse::RET_SUCCESS, 0, '');
        }
        return $ret;
    }

    /**
     * 是否为手机类别
     * @param string $catID 手机类别ID
     * @return    bool
     */
    private function isMobileCategory($catID) {
        if (!empty($catID) && self::MOBILE_BIG_CATEGORY_CODE === substr($catID, 0, strlen(self::MOBILE_BIG_CATEGORY_CODE))) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 是否为手机类别
     * @param string $catID 手机类别ID
     * @return    bool
     */
    public static function isMobile2Category($catID) {
        if (!empty($catID) && self::MOBILE_BIG_CATEGORY_CODE === substr($catID, 0, strlen(self::MOBILE_BIG_CATEGORY_CODE))) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 保存超时原因
     * @return    OnePlusServiceResponse
     */
    public function saveDelayedReason($currentUser) {
        $service = $this->getService();
        $service->setCurrentUser($currentUser);
        $arrParam = array(
            'treatSheetId' => $this->getHandlingFormNO(),
            'delayReasonA' => $this->getDelayReasonA(),
            'delayReasonB' => $this->getDelayReasonB()
        );
        $ret = $service->updateTreatSheet($arrParam);
        if (true !== $ret->isSuccess()) {
            $this->addLog(__METHOD__ . ':' . __LINE__ . ',保存超时原因错误,参数:' . var_export($arrParam, 1) . ',结果:' . var_export($ret, 1));
        }
        return $ret;
    }

    /**
     * 检测不通过
     * @param    string $currentUser 当前操作用户
     * @param    string $userId 用户DI
     * @return    OnePlusServiceResponse
     */
    public function cancle($currentUser, $cancelReason, $isThirdParty) {
        $service = $this->getService();
        $service->setCurrentUser($currentUser);
        //
        //    	//2014-5-19:加上取消原因
        //    	$arrParam = array('treatSheetId'	=> $this->getHandlingFormNO()
        //    			,'cancelReason'	=>$cancelReason
        //    	);
        //    	$ret = $service->cancelTreatSheet($arrParam);
        //    	if (true !== $ret->isSuccess()){
        //    		$this->addLog(__METHOD__ . ':' . __LINE__ . ',取消处理单错误,参数:'
        //    				. var_export($arrParam, 1) . ',结果:' . var_export($ret, 1) );
        //    		return $ret;
        //    	}
        //根据类型调用售后接口，改变成相应的状态
        switch ($this->_type) {
            case self::TYPE_REJECT:
                //退货
                $ret = $this->getAcceptManager()->cancelRejectForm($this->_asHandlingFormNO, $currentUser, $cancelReason);
                break;
            case self::TYPE_REPAIR:
                $ret = $this->getAcceptManager()->cancelRepairForm($this->_asHandlingFormNO, $currentUser, $cancelReason);
                //$ret = $this->getAcceptManager()->cancelRepairForm($this->_asHandlingFormNO
                //	, $currentUser, $this->_troubleDescription);
                //维修
                break;
            case self::TYPE_SWAP:
                //换货
                if ($isThirdParty == 1) {
                    $arrParam = array(
                        'treatSheetId' => $this->_handlingFormNO,
                        'cancelReason' => $cancelReason
                    );
                    $ret = $service->cancelTreatSheet($arrParam);
                    break;
                } else {
                    $ret = $this->getAcceptManager()->cancelSwapForm($this->_asHandlingFormNO, $currentUser, $cancelReason);
                    break;
                }
        }
        if (true !== $ret->isSuccess()) {
            $this->addLog(__METHOD__ . ':' . __LINE__ . ',审核不通过处理单错误:' . $this->_asHandlingFormNO . ',结果:' . var_export($ret, 1));
        }
        return $ret;
    }

    /**
     * 检测不通过
     * @param    string $currentUser 当前操作用户
     * @param    string $userId 用户DI
     * @return    OnePlusServiceResponse
     */
    public function notPassed($currentUser, $userId, $isThirdParty = false) {
        $service = $this->getService();
        $service->setCurrentUser($currentUser);

        //2014-5-19:加上取消原因
        $arrParam = array(
            'treatSheetId' => $this->getHandlingFormNO(),
            'pickupTime' => Func::javaStrtotime($this->getTakeMachineTime())
            //取机时间
            ,
            'pickuper' => $this->getTakeMachineUser()
            //取机人
            ,
            'pickupMemo' => $this->getTakeMachineMemo()
            //取机备注
            ,
            'expressCompany' => $this->getExpressCompany()
            //商品寄回物流公司
            ,
            'expressNo' => $this->getExpressNo()
                //商品寄回物流单号
        );
        $ret = $service->refuseTreatSheet($arrParam);
        if (true !== $ret->isSuccess()) {
            $this->addLog(__METHOD__ . ':' . __LINE__ . ',取消处理单错误,参数:' . var_export($arrParam, 1) . ',结果:' . var_export($ret, 1));
            return $ret;
        }

        //根据类型调用售后接口，改变成相应的状态
        switch ($this->_type) {
            case self::TYPE_REJECT:
                //退货
                $ret = $this->getAcceptManager()->checkRejectFormNotPassed($this->_asHandlingFormNO, $currentUser, $this->_troubleDescription);
                break;
            case self::TYPE_REPAIR:
                $ret = $this->getAcceptManager()->checkRepairFormNotPassed($this->_asHandlingFormNO);
                //$ret = $this->getAcceptManager()->cancelRepairForm($this->_asHandlingFormNO
                //	, $currentUser, $this->_troubleDescription);
                //维修
                break;
            case self::TYPE_SWAP:
                //换货
                //@todo:换货原因字段要添加
                $ret = $this->getAcceptManager()->checkSwapFormNotPassed($this->_asHandlingFormNO, $currentUser, $userId, $this->_troubleDescription, $isThirdParty);
                break;
        }
        if (true !== $ret->isSuccess()) {
            $this->addLog(__METHOD__ . ':' . __LINE__ . ',审核不通过处理单错误:' . $this->_asHandlingFormNO . ',结果:' . var_export($ret, 1));
        }
        return $ret;
    }

    /**
     * 处理单商品记录设置新IMEI号
     * @param type $tsGoodsId 处理单物料编号
     * @param type $imeiNew 新的ＩＭＥＩ号
     * @param type $user 　用户名
     * @return OnePlusServiceResponse
     */
    public function setTsGoodsImeiNew($tsGoodsId, $imeiNew, $currentUser) {
        //查找IMEI2
        $imeiNew2 = "";
        if (!empty($imeiNew)) {//如果$imeiNew为空，则为清楚IMIE操作。imeiNew，imeiNew2设置为空即可
            Yii::import('service.models.extendedwarranty.ExtendedWarrantyBaseModel');
            $baseModel = new ExtendedWarrantyBaseModel();
            $imeiDetail = $baseModel->queryImei($imeiNew);
            $imeiNew = (isset($imeiDetail['imei']) && !empty($imeiDetail['imei'])) ? $imeiDetail['imei'] : $imeiNew;
            $imeiNew2 = (isset($imeiDetail['imei2']) && !empty($imeiDetail['imei2'])) ? $imeiDetail['imei2'] : $imeiNew2;
        }
        $service = $this->getService();
        $arrParam = array(
            'tsGoodsId' => $tsGoodsId,
            'imeiNew' => $imeiNew,
            'imeiNew2' => $imeiNew2,
        );
        $service->setCurrentUser($currentUser);
        $ret = $service->setTsGoodsImeiNew($arrParam);
        if (true !== $ret->isSuccess()) {
            $this->addLog(__METHOD__ . ':' . __LINE__ . ',处理单商品记录设置新IMEI号,输入参数:' . var_export($arrParam) . ',结果:' . var_export($ret, 1));
            return $ret;
        }
        return $ret;
    }

    /**
     * 给处理单物品增加维修、故障代码
     * @param type $tsGoodsId
     * @param type $isRepair
     * @param type $code
     * @param type $currentUser
     * @return type
     */
    public function setTsGoodsServiceCode($tsGoodsId, $isRepair, $code, $currentUser) {
        $service = $this->getService();
        $arrParam = array('tsGoodsId' => $tsGoodsId,);
        if ($isRepair) {
            $arrParam['repairs'] = $code;
        } else {
            $arrParam['malfunctions'] = $code;
        }

        $service->setCurrentUser($currentUser);
        $ret = $service->setTsGoodsImeiNew($arrParam);
        if (true !== $ret->isSuccess()) {
            $this->addLog(__METHOD__ . ':' . __LINE__ . ',处理单商品记录设置维修、故障代码,输入参数:' . var_export($arrParam) . ',结果:' . var_export($ret, 1));
            return $ret;
        }
        return $ret;
    }

    /**
     * 查询申请单所有商品记录
     * @param type $treatSheetId 申请单编号
     * @return OnePlusServiceResponse
     */
    public function listTreatSheetGoods($treatSheetId) {
        $service = $this->getService();
        $arrParam = array('treatSheetId' => $treatSheetId,);
        $ret = $service->listTreatSheetGoods($arrParam);
        if (true !== $ret->isSuccess()) {
            $this->addLog(__METHOD__ . ':' . __LINE__ . ',查询申请单所有商品记录,输入参数:' . var_export($arrParam) . ',结果:' . var_export($ret, 1));
            return $ret;
        }
        return $ret;
    }

    /**
     * 查询用户添加的IMEI号是否正确
     * @return OnePlusServiceResponse
     * @notice 处理单修改为多IMEI号 2014.0806
     */
    public function checkoutGoodsIMEI() {
        $type = $this->getType();
        if ($type !== self::TYPE_SWAP) {
            return new OnePlusServiceResponse(OnePlusServiceResponse::RET_SUCCESS, 0, '');
        }
        $salesOrderNO = $this->getAsHandlingFormNO();
        $ret = $this->listTreatSheetGoods($salesOrderNO);
        if (true !== $ret->isSuccess()) {
            $this->addLog(__METHOD__ . ':' . __LINE__ . ',查询申请单所有商品记录:' . $salesOrderNO . '错误,结果:' . var_export($ret, 1));
            return $ret;
        }
        $goodsListArray = $ret->getData();
        $bool = false;
        $goodsImeiArray = array();
        foreach ($goodsListArray as $goods) {
            if (is_string($goods['imeiNew']) && strlen($goods['imeiNew']) > 0) {
                $bool = true;
                $goodsImeiArray[$goods['goodsCode']] = $goods['imeiNew'];
            }
        }
        if ($bool) {
            //获取销售订单信息,找出哪个为手机
            Yii::import('application.models.order.OrderModel');
            Yii::import('application.models.acceptmodel.AcceptCommon');
            $salesOrderModel = new OrderModel();
            //销售订单号,要用新订单的
            $ret = $salesOrderModel->searchByOrderID($salesOrderNO);
            if (true !== $ret->isSuccess()) {
                $this->addLog(__METHOD__ . ':' . __LINE__ . ',通过销售订单号搜索订单:' . $salesOrderNO . '错误,结果:' . var_export($ret, 1));
                return $ret;
            }
            //销售订单信息
            $salesOrderData = $ret->getData();
            if (count($salesOrderData) < 1) {
                $ret->setFailed(OnePlusException::PARAM_ERROR);
                $this->addLog(__METHOD__ . ':' . __LINE__ . ',查询销售订单的imei时订单不存在,订单:' . $salesOrderNO . ',处理单:' . $this->getHandlingFormNO());
                $ret->setErrMsg('找不到销售订单:' . $salesOrderNO);
                return $ret;
            }
            $salesOrder = (Object) $salesOrderData[0];
            $orderGoodsDetailDTOList = $salesOrder->orderGoodsDetailDTOList;
            //商品信息map
            $goodsInfoList = array();
            foreach ($orderGoodsDetailDTOList as $goodsItem) {
                //是否为手机
                $goodsItem = (Object) $goodsItem;
                //根据商品类型来，1：普通商品，2：套装商品；普通商品找外层的catId，套装商品找里面的catId
                //goodsType,pkgGoodsDetailDTOList
                $goodsType = intval($goodsItem->goodsType);
                $newImei = $goodsImeiArray[$goodsItem->goodsCode];
                switch ($goodsType) {
                    case AcceptCommon::GOODS_TYPE_NORMAL:
                        $categoryID = $goodsItem->catId;
                        if ($this->isMobileCategory($categoryID)) {
                            //设置IMEI号
                            $goodsItem->imei = $newImei;
                            $goodsInfoList[$goodsItem->goodsCode] = $goodsItem;
                        }
                        break;
                    case AcceptCommon::GOODS_TYPE_PACKET:
                        //套装商品要遍历
                        for ($j = 0; $j < count($goodsItem->pkgGoodsDetailDTOList); $j++) {
                            $categoryID = $goodsItem->pkgGoodsDetailDTOList[$j]['catId'];
                            if ($this->isMobileCategory($categoryID)) {
                                $goodsItem->pkgGoodsDetailDTOList[$j]['imei'] = $newImei;
                                $goodsInfoList[$goodsItem->goodsCode] = $goodsItem;
                                break;
                            }
                        }
                        break;
                }

                //找到了手机要退出
                if (count($goodsInfoList) > 0) {
                    break;
                }
            }
            if (count($goodsInfoList) < 1) {
                $ret = new OnePlusServiceResponse(OnePlusServiceResponse::RET_ERROR, 0, '请替换处理单中手机的旧IMEI号');
                return $ret;
            }
        } else {
            $ret = new OnePlusServiceResponse(OnePlusServiceResponse::RET_ERROR, 0, '请替换处理单中手机的旧IMEI号');
        }
        return $ret;
    }

    /**
     * 将保险接口返回的结果的从数组转化为对象
     * @param type $ret
     * @return \OnePlusServiceResponse
     */
    private function array2OjbectMsg($ret) {
        if (is_array($ret)) {
            if ($ret['ret'] == OpError::OK)
                return new OnePlusServiceResponse(OnePlusServiceResponse::RET_SUCCESS, 0, '');
            else
                return new OnePlusServiceResponse(OnePlusServiceResponse::RET_ERROR, 0, $ret['errMsg']);
        }
        return $ret;
    }

    /**
     * 处理单处理完成的时候使用保险
     * @param array $params
     */
    public function useGoodsWarranty($params, $logArray) {
        Yii::import('service.models.extendedwarranty.ExtendedWarrantyBillModel', true);
        $billModel = new ExtendedWarrantyBillModel();
        $ret = $billModel->useWarranty($params, $logArray);
        $ret = $this->array2OjbectMsg($ret);
        if (true !== $ret->isSuccess()) {
            $this->addLog(__METHOD__ . ':' . __LINE__ . ',处理单-处理完成时，使用保险时发生错误:' . var_export($params, 1) . '错误,结果:' . var_export($ret, 1));
        }
        return $ret;
    }

    public function passUnUseWarranty($params, $logArray, $isThirdParty = false) {
        Yii::import('service.models.extendedwarranty.ExtendedWarrantyBillModel', true);
        $billModel = new ExtendedWarrantyBillModel();
        $ret = $billModel->swapUnTickWarranty($params, $logArray, $isThirdParty);

        $ret = $this->array2OjbectMsg($ret);
        if (true !== $ret->isSuccess()) {
            $this->addLog(__METHOD__ . ':' . __LINE__ . ',处理单-处理完成时，传递未使用保险信息时发生错误:' . var_export($params, 1) . '错误,结果:' . var_export($ret, 1));
        }
        return $ret;
    }

    /*
     * 检查新IMEI在网点系统中是否是正确的
     */
    public function isNewImeiAvaiable($imeiNew) {
        $service = $this->getService();
        $arrParam = array(
            'imeiNew' => $imeiNew,
            'verifyType' => '1,0,0'
        );
        $ret = $service->verifyImei4ServicenodeSwap($arrParam);
        return $ret;
    }

    /*
     * 获取默认的非报价项
     */
    public function getDefaultWR() {
        $dataS = array();
        $dataList = Yii::app()->redisDB->get(RedisKeyConfig::RDS_EXTENDEDWARRANTYPP);
        if (!empty($dataList)) {
            $dataS = unserialize($dataList);
        }
        $arrParam = '';
        foreach ($dataS as $k => $v) {
            if ($v['rma'] != '无' && !empty($v['rma'])) {
                $arrParam = $arrParam . $v['virtural_code'] . '-' . $v['rma'] . ';';
            }
        }
        return $arrParam;
    }
}