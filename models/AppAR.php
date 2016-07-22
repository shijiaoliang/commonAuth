<?php
class AppAR extends BaseModel {
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{app}}';
    }

    /**
     * @return array 对模型的属性验证规则
     */
    public function rules() {
        return array(
            array(
                'app_id, app_name, app_code, app_status, app_url, app_create_time',
                'safe',
            ),
        );
    }
}