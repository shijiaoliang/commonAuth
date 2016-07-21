<?php
class PermissionAR extends BaseModel {
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{permission}}';
    }

    /**
     * @return array 对模型的属性验证规则
     */
    public function rules() {
        return array(
            // The following rule is used by search().
            array(
                'p_id, p_name, p_code, p_type, p_status, p_app_id, p_module_id, p_data_url, p_data_id, p_create_time',
                'safe',
                'on' => 'search'
            ),
        );
    }
}