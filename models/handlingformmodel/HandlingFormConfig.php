<?php
/**
 * 处理单配置类
 */
class HandlingFormConfig {
    /**
     * 单例对象
     * @var HandlingFormConfig
     */
    private static $instance;

    /**
     * 所有检查状态数组
     * @var array
     */
    private $_allCheckStatus = array();

    /**
     * 非保价项
     * @var array
     */
    private $_rmaNoPriceProtection = array();

    /**
     * 超时原因
     * @var array
     */
    private $_timeoutReason = array();

    /**
     * 超时间隔
     * @var int
     */
    private $_timeoutInterval;

    /**分级结算**/
    private $_balanceClassification = array();

    /**
     * 获取实例
     */
    public static function getInstance() {
        if (true !== (self::$instance instanceof HandlingFormConfig)) {
            self::$instance = new HandlingFormConfig();
        }
        return self::$instance;
    }

    /**
     * 构造函数
     */
    private function __construct() {
        $this->init();
    }

    /**
     * 初始化函数
     */
    private function init() {
        $appRootPath = Yii::app()->getBasePath();
        if ('/' !== substr($appRootPath, strlen($appRootPath) - 1)) {
            $appRootPath .= '/';
        }
        $configFile = $appRootPath . 'config/handlingformconfig.php';
        if (true !== is_file($configFile)) {
            $this->addLog(__METHOD__ . ':' . __LINE__ . ',没有找到配置文件:' . $configFile);
            return;
        }
        $config = include $configFile;
        foreach ($config as $key => $val) {
            $thisKey = '_' . $key;
            if (property_exists($this, $thisKey)) {
                $this->$thisKey = $val;
            }
        }
    }

    /**
     * 获取所有检测状态
     * @return array
     */
    public function getAllCheckStatus() {
        return $this->_allCheckStatus;
    }

    /**
     * 获取非保价项
     * @return array    二维数组，每项有以下信息:code,type,desc,element,explain,price
     */
    public function getRmaNoPriceProtection() {
        return $this->_rmaNoPriceProtection;
    }

    /**
     * 获取超时原因
     * @return array
     */
    public function getTimeoutReason() {
        return $this->_timeoutReason;
    }

    /**
     * 获取超时时间间隔
     * @return    int
     */
    public function getTimeoutInterval() {
        return $this->_timeoutInterval;
    }

    public function getBalanceClassification() {
        return $this->_balanceClassification;
    }
}