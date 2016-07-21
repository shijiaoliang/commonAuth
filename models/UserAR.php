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
                'user_no, user_pwd, captcha',
                'required',
                'on' => 'login'
            ),
            // The following rule is used by search().
            array(
                'user_id, user_name, user_no, user_status, role_codes, last_login_ip, last_login_time, user_create_time',
                'safe',
                'on' => 'search'
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
}