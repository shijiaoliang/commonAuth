<?php
class RoleAR extends BaseModel {
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{role}}';
    }

    /**
     * @return array 对模型的属性验证规则
     */
    public function rules() {
        return array(
            array(
                'role_id, role_name, role_code, role_status, permission_codes, data_role_codes, role_create_time',
                'safe',
            ),
        );
    }
}