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

    public function login($params = array()) {
        if (empty($params['user_no']) || empty($params['user_pwd'])) {
            return false;
        }

        $userNo = $params['user_no'];
        $userPwd = md5($params['user_pwd'] . KEY);

        $criteria = new CDbCriteria;
        $criteria->condition = 'user_no=:user_no AND user_pwd=:user_pwd AND user_status=10';
        $criteria->params = array(':user_no' => $userNo, ':user_pwd' => $userPwd);
        $res = $this->find($criteria);

        return $res;
    }
}