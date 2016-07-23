<?php
class AppAR extends BaseModel {
    public static $equalKeys = array(
        'app_id',
    );

    public static $likeKeys = array(
        'app_name'
    );

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
            
            array(
                'app_create_time',
                'default',
                'on' => 'save'
            ),
            array(
                'app_name, app_code, app_url',
                'required',
                'on' => 'save'
            ),
            array(
                'app_url',
                'url',
                'on' => 'save'
            ),
            array(
                'app_code',
                'unique',
                'on' => 'save'
            ),
        );
    }

    public function getList($params = array()) {
        $arrParams = array(
            'condition' => '1 = 1',
            'params' => array(),
        );

        $objPager = new CPagination(0);

        //处理查询条件
        if (is_array($params) && count($params) > 0) {
            $zfw_params = array();
            foreach ($params as $k => $v) {
                if ($v === '' || $v === null) {
                    unset($params[$k]);
                } else {
                    $zfw_k = ":" . (string) $k;
                    $zfw_v = $v;
                    if (in_array($k, self::$equalKeys)) {
                        $arrParams['condition'] .= " and t." . (string) $k . " = " . $zfw_k . " ";
                    } else if (in_array($k, self::$likeKeys)) {
                        $arrParams['condition'] .= " and " . (string) $k . " like " . $zfw_k . " ";
                        $zfw_v = "%" . $v . "%";
                    } else {
                        continue;
                    }
                    $zfw_params[$zfw_k] = $zfw_v;
                }
            }
            $arrParams['params'] = $zfw_params;
        }

        //处理分页
        $pageSize = !empty($params['pageSize']) ? (int)$params['pageSize'] : 15;
        $currentPage = !empty($params['currentPage']) ? (int)$params['currentPage'] : 1;
        $offset = $pageSize * ($currentPage - 1);

        //CDbCriteria
        $criteria = new CDbCriteria;
        $criteria->select = '*';
        $criteria->limit = $pageSize;
        $criteria->offset = $offset;
        $criteria->order = 'app_id DESC';
        $criteria->condition = $arrParams['condition'];
        $criteria->params = $arrParams['params'];

        //$result
        $result = AppAR::model()->findAll($criteria);

        //$count
        $count = AppAR::model()->count($criteria);

        //分页条信息
        $objPager->setItemCount($count);
        $objPager->setPageSize($pageSize);

        $returnData = array();
        foreach ($result as $key => $value) {
            $returnData[$key] = $value->attributes;
        }

        return $this->defaultResult(OpError::OK, OpError::OK, "Success!", $returnData, $objPager);
    }
}