<?php
/**
 * 受理单搜索表单Model
 */
class HandlingFormListForm extends CFormModel {
    /**
     * IMEI
     * @var string
     */
    public $imei;

    /**
     * IMEI
     * @var string
     */
    public $imeiNew;

    /**
     * 受理单类型
     * @var string
     */
    public $formType;

    /**
     * 状态
     * @var string
     */
    public $status;

    /**
     * 领退料状态
     * @var string
     */
    public $applySheetStatus;

    /**
     * 接单开始时间
     * @var string
     */
    public $processStartTime;

    /**
     * 接单结束时间
     * @var string
     */
    public $processEndTime;

    /**
     * 订单号
     * @var string
     */
    public $orderNO;

    /**
     * 网点
     * @var string
     */
    public $branchNO;

    /**
     * 工程师
     * @var string
     */
    public $engineer;

    /**
     * 售后处理单号
     * @var string
     */
    public $asHandlingFormNO;

    /**
     * 取机时间
     * @var string
     */
    public $pickupTimeStart;

    /**
     * 取机时间
     * @var string
     */
    public $pickupTimeEnd;

    /**
     * 更新开始时间
     * @var string
     */
    public $startTime2;

    /**
     * 更新结束时间
     * @var string
     */
    public $endTime2;

    /**
     * 分页信息
     * @var int
     */
    public $page;

    /**
     * 每页的记录数
     * @var int
     */
    public $pageSize;

    /**
     * Declares the validation rules.
     * The rules state that username and password are required,
     * and password needs to be authenticated.
     */
    public function rules() {
        //必须加上safe标签，不然就不起作用了
        return array(array('imei,formType,status,applySheetStatus,processStartTime,processEndTime,
                        orderNO,branchNO,engineer,asHandlingFormNO,pickupTimeEnd,
                        pickupTimeStart,imeiNew,startTime2,endTime2', 'safe'));
    }
}