<?php
class ModuleAR extends BaseModel {
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{module}}';
    }

    /**
     * @return array 对模型的属性验证规则
     */
    public function rules() {
        return array(
            // The following rule is used by search().
            array(
                'module_id, module_name, module_code, module_status, module_create_time, app_id',
                'safe',
                'on' => 'search'
            ),
        );
    }
}