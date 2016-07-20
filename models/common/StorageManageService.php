<?php
/**
 * 售后系统--服务
 */
class StorageManageService extends OnePlusServiceAbstract{	
	/**
	 * 当前操作人
	 * @var string
	 */
	private $_currentUser = '';
	/**
	 * 网点信息管理服务的key名
	 * @var string
	 */
	const STORAGE_MANAGE_SERVICE_KEY = 'storageManage';
	/**
	 * 当前操作者的key
	 * @var string
	 */
	const CURRENT_OPERATOR = 'currentOperator';
	/**
	 * 设置当前操作用户
	 * @param string $currentUser	当前操作用户
	 */
	public function setCurrentUser($currentUser){
		$this->_currentUser = $currentUser;
	}
	/**
	 * 查询WMS是否存在该IMEI号
	 * @param	array	$arrParam	参数，可以包含以下key:
	 * 			imei
	 * @return	OnePlusServiceResponse	data中key:result		{result:true}
	 */
	public function queryImei($arrParam){
		$method = 'queryImei';
		$ret = $this->callService($method, $arrParam);
		/*$data = $ret->getData();
		if (true !== ParamCheck::checkArray($data, array('result'))){
			$this->addLog(__CLASS__ . '::' . __METHOD__ . ':' . __LINE__ . ',查询WMS_IMEI号,输入参数:' 
					. var_export($arrParam, 1) . ',结果:' . var_export($ret, 1) );
			$ret->setFailed();
		}*/
		return $ret;
	}
	
}
