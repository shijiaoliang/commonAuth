<?php

Yii::import('application.models.common.RedisService');

/**
 * 读取redis服务
 */
class RedisModels extends RedisService {

    const RDS_KNOWLEDGEVIEW = '_thx_knowledgeView'; //redis KEY
    //手机sku配置
    const PHONESKUPP = '_thx_phoneSkuPP';
    const AFTERSHIP_COURIERS = '_thx_couriers'; //aftership couriers 列表
    const AFTERSHIP_TRACKINGS = '_thx_trackings'; //快递单数据
    const KNOWLEDGE_RECENTLY_READ = '_thx_knowledgeRencentlyRead_'; //文章最近被阅读 key 前缀 +ID

    private static $_int = null;
    private static $time_out = 30; //过期时间30秒

    /**
     * 单例方法
     */

    public static function getInstance() {
        if (!self::$_int) {
            self::$_int = new RedisModels();
        }
        return self::$_int;
    }

    /**
     * 添加日志
     * @param string $msg 日志消息
     * @param int $level 日志等级
     * @return    void
     */
    public function addLog($msg, $level = Log::LEVEL_WARN) {
        Log::addLog($msg, __CLASS__, $level);
    }

    /**
     * 根据id获取文章缓存
     * @return array
     */
    public function getRdsKnowledgeView($id) {
        $exists = $this->exists(self::RDS_KNOWLEDGEVIEW . '_' . $id);
        if ($exists) {
            $getData = $this->getData(self::RDS_KNOWLEDGEVIEW . '_' . $id);
            $data = unserialize($getData);
            return $data;
        } else {
            return array();
        }
//        $hExists = $this->hExists(self::RDS_KNOWLEDGEVIEW, $id);
//        if ($hExists) {
//            $hGetData = $this->hGet(self::RDS_KNOWLEDGEVIEW, $id);
//            $data = unserialize($hGetData);
//            return $data;
//        } else {
//            return array();
//        }
    }

    /**
     * 根据id设置文章缓存
     * @return array
     */
    public function setRdsKnowledgeView($id, $knowledgeViewData) {
        $exists = $this->exists(self::RDS_KNOWLEDGEVIEW . '_' . $id);
        if (!$exists) {
            $data = serialize($knowledgeViewData);
            $timeout = 3600 * 24 * 10; //缓存10天
            $this->setexData(self::RDS_KNOWLEDGEVIEW . '_' . $id, $timeout, $data);
        }
//        $hExists = $this->hExists(self::RDS_KNOWLEDGEVIEW, $id);
//        if (!$hExists) {
//            $data = serialize($knowledgeViewData);
//            $hExists = $this->hSet(self::RDS_KNOWLEDGEVIEW, $id, $data);
//        }
    }

    /**
     * 根据id删除文章缓存
     * @return array
     */
    public function deleteRdsKnowledgeViewById($id) {
        $exists = $this->exists(self::RDS_KNOWLEDGEVIEW . '_' . $id);
        if ($exists) {
            $this->delete(self::RDS_KNOWLEDGEVIEW . '_' . $id);
        }
    }

    /**
     * 删除所有文章缓存
     * @return array
     */
    public function deleteRdsKnowledgeView() {
        $keys = $this->getKeys(self::RDS_KNOWLEDGEVIEW . '_*');
        if (!empty($keys) && is_array($keys)) {
            foreach ($keys as $key){
               $this->delete($key); 
            }
        }
    }

    /**
     * 获取文章缓存数量
     * @return array
     */
    public function getRdsKnowledgeViewNum() {
        $keys = $this->getKeys(self::RDS_KNOWLEDGEVIEW . '_*');
        if (!empty($keys) && is_array($keys)) {
            return count($keys);
        }
        return 0;
//        $hExists = $this->exists(self::RDS_KNOWLEDGEVIEW);
//        if ($hExists) {
//            $num = $this->hLen(self::RDS_KNOWLEDGEVIEW);
//            return $num;
//        } else {
//            return 0;
//        }
    }

    /**
     * 获取手机类型和SKU配置
     * @return array
     */
    public function getRdsPhoneSkuProtection() {
        $data = $this->getData(self::PHONESKUPP);
        if ($data) {
            $data = unserialize($data);
            $tmp = array();
            foreach ($data as $key => $val) {
                $tmp[$val['code']] = $val;
            }
            return $tmp;
        }
        return array();
    }

    /**
     * getAftershipCouriers 获取缓存拥有的aftership 快递公司列表
     * @return mixed
     */
    public function getAftershipCouriers() {
        $data = $this->getData(self::AFTERSHIP_COURIERS);
        if ($data) {
            $data = json_decode($data, true);
        }

        return $data;
    }

    public function setAftershipCouriers($data) {
        $data = json_encode($data);
        $timeout = 3600 * 24 * 15; //缓存15天
        $this->setexData(self::AFTERSHIP_COURIERS, $timeout, $data);

        return true;
    }

    public function delAftershipCouriers() {
        $this->delete(self::AFTERSHIP_COURIERS);

        return true;
    }

    /**
     * getAftershipTracking
     * $id = {$slug}_{$tracking_number}
     * @return mixed
     */
    public function getAftershipTracking($id) {
        $data = $this->hGet(self::AFTERSHIP_TRACKINGS, $id);
        $data = json_decode($data, true);
        //校验过期
        if ($data['expire_time'] < time()) {
            $this->hDel(self::AFTERSHIP_TRACKINGS, $id);
            $data = false;
        }
        return true;
    }

    //最近浏览文章的agent
    public function addRecentlyRead($knowledgeId, $uname) {
        if ($knowledgeId && $uname) {
            $key = (self::KNOWLEDGE_RECENTLY_READ) . '_' . $knowledgeId;
            $expire = 3600 * 24 * 10; //缓存时间10天

            Yii::app()->redisDB->sAdd($key, $uname);
            Yii::app()->redisDB->expire($key, $expire);
        }
        return true;
    }

    public function getRecentlyRead($knowledgeId) {
        $key = (self::KNOWLEDGE_RECENTLY_READ) . '_' . $knowledgeId;
        return Yii::app()->redisDB->sMembers($key);
    }

    public function setAftershipTracking($id, $data) {
        $this->hDel(self::AFTERSHIP_TRACKINGS, $id);

        $timeout = 3600 * 5; //缓存5小时
        $data['expire_time'] = time() + $timeout;

        $data = json_encode($data);
        $this->hSet(self::AFTERSHIP_TRACKINGS, $id, $data);

        return true;
    }

    public function delAftershipTracking($id) {
        $this->hDel(self::AFTERSHIP_TRACKINGS, $id);

        return true;
    }

    public function delAllAftershipTracking() {
        Yii::app()->redisDB->del(self::AFTERSHIP_TRACKINGS);

        return true;
    }

}
