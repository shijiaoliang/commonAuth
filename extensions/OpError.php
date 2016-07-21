<?php
class OpError {
    protected static $_INSTANCE = null;

    //0.无错误
    const OK = 0;
    const ERR_NONE = 1;
    const ERR_VERIFY = 401;

    const ERR_UNKNOW = 900000;

    protected $_MESSAGES = array();

    /**
     * 初始化相关的数据
     */
    protected function _init() {
        $this->_MESSAGES = array(
            self::OK => "",
            self::ERR_NONE => "无错误",
            self::ERR_VERIFY => "对不起，您无权限执行此操作",

            self::ERR_UNKNOW => "很抱歉，由于系统繁忙，请您稍候再试！",
        );
    }

    /**
     * 获取实例
     * @return self
     */
    public static function getInstance() {
        if (!(self::$_INSTANCE instanceof self)) {
            $strClassName = __CLASS__;
            self::$_INSTANCE = new $strClassName();
        }

        return self::$_INSTANCE;
    }

    /**
     * 根据错误码获取相关的信息
     * @param int $intErrno 错误号
     * @param array $arrData 相关数据
     * @return string
     */
    public function getMessage($intErrno, $arrData = array(), &$debug = array()) {
        $strRet = "";

        if (!isset($this->_MESSAGES[$intErrno])) {
            $intErrno = self::ERR_UNKNOW;
        }

        $strMessageTpl = $this->_MESSAGES[$intErrno];

        if (preg_match('/<list>(.*?)<\/list>/', $strMessageTpl, $arrOut)) {
            $strListTpl = $arrOut[1];
            $strListMsg = $this->_list($strListTpl, $arrData);

            $strMessageTpl = preg_replace('/<list>.*?<\/list>/', $strListMsg, $strMessageTpl);
        }

        eval("\$strRet = \"{$strMessageTpl}\";");

        $debug = $arrData;

        return $strRet;
    }

    /**
     * 获取错误信息的列表
     * @param string $strTpl 消息模板
     * @param array $arrData 消息数据
     * @return string
     */
    protected function _list($strTpl, $arrData) {
        $strRet = "";

        do {
            if (is_array($arrData) && !empty($arrData)) {
                foreach ($arrData as $intId => $arrRow) {
                    $strMsg = '';
                    eval("\$strMsg = \"{$strTpl}\";");
                    $strRet .= $strMsg;
                }
            }
        } while (false);

        return $strRet;
    }

    /**
     * 入口
     */
    protected function __construct() {
        $this->_init();
    }
}
