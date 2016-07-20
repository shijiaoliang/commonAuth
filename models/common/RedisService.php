<?php

/**
 * 读取redis服务
 */
class RedisService{
    /**
     * $key: hjl_*
     * 获取指定前缀的rediskey
     */
    public function getKeys($key) {
        return Yii::app()->redisDB->keys($key);
    }
    /**
     * 获取redis数据
     */
    public function getData($key) {
        return Yii::app()->redisDB->get($key);
    }
    /**
     * 设置redis数据
     * 同时给多个key赋值
     * $redis->mset(array('key0' => 'value0', 'key1' => 'value1'));
     */
    public function setData($key,$data) {
        return Yii::app()->redisDB->set($key,$data);
    }
    
    /**
     * 设置redis数据带有效时间，单位秒
     */
    public function setexData($key,$time_out,$data) {
        $this->multi();
        Yii::app()->redisDB->setex($key,$time_out,$data);
        $this->exec();
    }
    
    /**
     * 判断是否存在
     */
    public function exists($key) {
        return Yii::app()->redisDB->exists($key);
    }
    
    /**
     * delete  删除指定key的值
     */
    public function delete($key) {
        return Yii::app()->redisDB->delete($key);
    }
    /**
     * 移除生存时间到期的key
     */
    public function persist($key) {
        return Yii::app()->redisDB->persist($key);
    }
    
    /**
     * 开始事物
     */
    public function multi() {
        return Yii::app()->redisDB->multi();
    }
    /**
     * 结束事物
     */
    public function exec() {
        return Yii::app()->redisDB->exec();
    }

    /*************************Hash操作***********************/ 
    /**
     * hSet
     * $redis->hSet('h', 'key1', 'hello');
     * 向名称为h的hash中添加元素key1—>hello
     */
    public function hSet($h,$k,$v) {
        $this->multi();
        Yii::app()->redisDB->hSet($h,$k,$v);
        $this->exec();
    }
    /**
     * hmSet
     * $redis->(‘hash1′,array(‘key3′=>’v3′,’key4′=>’v4′));
     * 向名称为h的hash1中批量添加元素
     */
    public function hmSet($h,$kvArray) {
        return Yii::app()->redisDB->hmSet($h,$kvArray);
    }
    
    /**
     * hGet
     * $redis->hGet('h', 'key1');
     * 返回名称为h的hash中key1对应的value（hello）
     */
    public function hGet($h,$k) {
        return Yii::app()->redisDB->hGet($h,$k);
    }
    /**
     * hMGet
     * $redis->hmGet('h', array('field1', 'field2'));
     *返回名称为h的hash中field1,field2对应的value
     */
    public function hMGet($h,$kArray) {
        return Yii::app()->redisDB->hMGet($h,$kArray);
    }
    /**
     * hLen
     * $redis->hLen('h');
     * 返回名称为h的hash中元素个数
     */
    public function hLen($h) {
        return Yii::app()->redisDB->hLen($h);
    }
    /**
     * hExists
     * $redis->hExists('h', 'a');
     * 名称为h的hash中是否存在键名字为a的域
     */
    public function hExists($h,$k) {
        return Yii::app()->redisDB->hExists($h,$k);
    }
    /**
     * hDel
     * $redis->hDel('h', 'key1');
     * 删除名称为h的hash中键为key1的域
     */
    public function hDel($h,$k) {
        return Yii::app()->redisDB->hDel($h,$k);
    }
    /**
     * hKeys
     * $redis->hKeys('h');
     * 返回名称为key的hash中所有键
     */
    public function hKeys($h) {
        return Yii::app()->redisDB->hKeys($h);
    }
    /**
     * hVals
     * $redis->hVals('h');
     * 返回名称为key的hash中所有值
     */
    public function hVals($h) {
        return Yii::app()->redisDB->hVals($h);
    }
    /**
     * hGetAll
     * $redis->hGetAll('h');
     * 返回名称为key的hash中所有键-值
     */
    public function hGetAll($h) {
        return Yii::app()->redisDB->hGetAll($h);
    }
  }
