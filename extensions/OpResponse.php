<?php
class OpResponse {
    /**
     * ret字段错误时的值
     * @var string
     */
    const RET_ERROR = '0';

    /**
     * 成功时ret字段的值
     * @var string
     */
    const RET_SUCCESS = '1';

    /**
     * 错误码, 1表示成功，0表示失败
     * @var string
     */
    private $_ret;

    /**
     * 错误码, 一般从100开始
     * @var string
     */
    private $_errorCode;

    /**
     * 错误信息
     * @var string
     */
    private $_errMsg = '';

    /**
     * 数据
     * @var mixed
     */
    private $_data = null;

    /**
     * 分页对象
     * @var object
     */
    private $_page = null;

    /**
     * 构造函数
     * @param string $ret 返回标志
     * @param string $errCode 错误码
     * @param string $errMsg 错误信息
     */
    public function __construct($ret, $errCode, $errMsg, $data = null, $page = null) {
        $this->_ret = strval($ret);
        $this->_errorCode = strval($errCode);
        $this->_errMsg = $errMsg;
        $this->_data = $data;
        if (is_array($page)) {
            $this->_page = (Object)$page;
        } else {
            $this->_page = $page;
        }
    }

    /**
     * 是否成功
     * @return boolean
     */
    public function isSuccess() {
        return self::RET_SUCCESS === $this->_ret;
    }

    /**
     * 获取返回标志
     * @return    string
     */
    public function getRet() {
        return $this->_ret;
    }

    /**
     * 返回错误码
     * @return string
     */
    public function getErrCode() {
        return $this->_errorCode;
    }

    /**
     * 返回错误信息
     * @return    string
     */
    public function getErrMsg() {
        return $this->_errMsg;
    }

    /**
     * 返回数据
     * @return mixed
     */
    public function getData() {
        return $this->_data;
    }

    /**
     * 返回分页信息
     * @return
     */
    public function getPage() {
        return $this->_page;
    }

    /**
     * 设置失败
     * @param    int $errCode 错误码
     */
    public function setFailed($errCode = '') {
        $this->_ret = '0';
        if (is_int($errCode)) {
            $this->_errorCode = strval($errCode);
        } else {
            //默认的错误码是服务数据格式错误
            $this->_errorCode = OpError::ERR_UNKNOW;
        }
    }

    /**
     * 设置错误信息
     * @param string $erMsg 错误信息
     * @return bool
     */
    public function setErrMsg($erMsg) {
        $this->_errMsg = $erMsg;
    }

    /**
     * 设置数据
     * @param mixed $data 相应的数据
     */
    public function setData($data) {
        $this->_data = $data;
    }
}