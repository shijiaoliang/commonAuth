<?php
class UserAR extends BaseModel {
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{user}}';
    }

    /**
     * @return array 对模型的属性验证规则
     */
    public function rules() {
        return array(
            array(
                'user_id, user_name, user_no, user_status, role_codes, last_login_ip, last_login_time, user_create_time',
                'safe'
            ),
        );
    }

    /**
     * @return array 自定义属性标签 (name=>label)
     */
    public function attributeLabels() {
        return array(
            'user_id' => '用户id'
        );
    }

    /**
     * password
     * @param string $string
     * @return string
     */
    public static function password($string = '') {
        return md5($string . KEY);
    }

    /**
     * login
     * @param array $params
     * @return bool|CActiveRecord
     */
    public function login($params = array()) {
        if (empty($params['user_no']) || empty($params['user_pwd'])) {
            return false;
        }

        $userNo = $params['user_no'];
        $userPwd = $this->password($params['user_pwd']);

        $criteria = new CDbCriteria;
        $criteria->condition = 'user_no=:user_no AND user_pwd=:user_pwd AND user_status=10';
        $criteria->params = array(':user_no' => $userNo, ':user_pwd' => $userPwd);
        $res = $this->find($criteria);

        return $res;
    }

    /**
     * changePwd
     * @param array $params
     * @return bool
     */
    public function changePwd($params = array()) {
        if (!ParamCheck::checkArray($params, array('newPwd', 'oldPwd', 'userId'))) {
            return false;
        }

        $userId = $params['userId'];

        //根据userId查询用户信息
        $info = $this->findByPk($userId);
        //用户不存在
        if (!$info) {
            return $this->defaultResult(OpError::ERR_NONE, OpError::ERR_NONE, '该用户不存在或已被删除!');
        }

        //旧密码错误
        if (isset($info['user_pwd']) && $info['user_pwd'] != $this->password($params['oldPwd'])) {
            return $this->defaultResult(OpError::ERR_NONE, OpError::ERR_NONE, '旧密码错误!');
        }

        //update
        $data = array(
            'user_pwd' => $this->password($params['newPwd'])
        );
        $r = $this->updateByPk($userId, $data);
        if (!$r) {
            return $this->defaultResult(OpError::ERR_NONE, OpError::ERR_NONE, '更改密码失败!');
        }

        return $this->defaultResult(OpError::OK, OpError::OK, 'Success!');
    }
}