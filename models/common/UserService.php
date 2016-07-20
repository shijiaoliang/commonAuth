<?php
/**
 * 会员系统--服务
 */
class UserService extends OnePlusServiceAbstract{	
	/**
	 * 当前操作人
	 * @var string
	 */
	
	/**
	 * 网点信息管理服务的key名
	 * @var string
	 */
	const SSO_USER_SERVICE_KEY = 'ssoUser';
	
        private static $_userService = null;
        /**
         * 单例方法
         */
        public static function getInstance(){
           if(!self::$_userService){
             self::$_userService = new UserService(self::getServiceUrl(self::SSO_USER_SERVICE_KEY));
           }
           return self::$_userService;
        }
	/**
	 * 添加日志
	 * @param string $msg	日志消息
	 * @param int $level	日志等级
	 * @return	void
	 */
	public function addLog($msg, $level=Log::LEVEL_WARN){
		Log::addLog($msg, __CLASS__,  $level);
	}
	
	/**
	 * 根据用户编号获取用户信息
	 * @param	array	$arrParam 参数，可以包含以下key:
	 * userId	否	Integer	用户id
         * userName	否	String	用户名
         * email	否	String	电子邮箱
         * mobile	否	String	手机号码
	 * @return	OnePlusServiceResponse	data中key:result
         *   userId:用户id，如：10086
         *   userName:用户名，如：oneplus
         *   email:邮箱，如：oneplus@one.com
         *   mobile:手机，如：13419649028
         *   registerTime:注册时间，如：2013-12-30 12:34:32
         *   registerType:注册类型，如：1（手机注册）
         *   userSource:用户来源，如：1（官网）
         *   status:用户状态，如：1（正常）
         *   emailVerified:邮箱验证状态，如：1（已验证）
         *   mobileVerified:手机验证状态，如：0（未验证）
         *   sessionKey:用户登录后的sessionKey，如：dhsfsfsdftuyyjcbcvn
         *   idCard:身份证号
         *   avatar:头像链接
         *   changeTimes:用户修改次数 
	 */
	
	public function getUserInfo($arrParam){
		$method = 'getUserInfo';
		$ret = $this->callService($method, $arrParam);
		return $ret;
	}
}
