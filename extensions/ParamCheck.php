<?php

class ParamCheck {
    /**
     * 值的key
     * @var string
     */
    const VAL = 'val';

    /**
     * 是否包含相等值的key
     * @var string
     */
    const INCEQ = 'inceq';

    /**
     * 检查数组
     * @param    mixed $val 要检查的数组变量
     * @param    array $keys 要检查的变量中是否存在指定的key
     * @return    bool
     */
    public static function checkArray($array, array $keys = array()) {
        if (true !== is_array($array)) {
            return false;
        }
        //key数组为空则返回true
        if (0 === count($keys)) {
            return true;
        }
        foreach ($keys as $key) {
            if (true !== array_key_exists($key, $array)) {
                return false;
            }
        }
        return true;
    }

    /**
     * 检查二维数组
     * @param    mixed $val 要检查的数组变量
     * @param    array $keys 要检查的变量中是否存在指定的key
     * @return    bool
     */
    public static function checkTDArray($array, array $keys = array()) {
        if (true !== is_array($array)) {
            return false;
        }
        for ($i = 0; $i < count($array); $i++) {
            if (true !== is_array($array[$i])) {
                return false;
            }
            //检查每项是否包含key
            foreach ($keys as $key) {
                if (true !== array_key_exists($key, $array[$i])) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * 对数组的key进行检查
     * @param array $array 要检查的数组
     * @param array $arrKeys 所有符合要求的key,每一项为一个key
     * @return    bool
     */
    public static function checkArrayKey($array, array $arrKeys) {
        if (true !== is_array($array)) {
            return false;
        }
        foreach ($array as $key => $val) {
            if (true !== in_array($key, $arrKeys)) {
                return false;
            }
        }
        return true;
    }

    /**
     * 检查整型
     * @param mixed $val
     * @param array $min 最小值信息,key有val(int):值, inceq(bool)是否包含相等的值
     * @param array $max 最大值信息,key有val(int):值, inceq(bool)是否包含相等的值
     * @return    bool
     */
    public static function checkInt($val, array $min = array(), array $max = array()) {
        if (true !== is_int($val)) {
            return false;
        }
        //检查最小值
        if (array_key_exists(self::VAL, $min) && is_int($min[self::VAL])) {
            $minVal = $min[self::VAL];
            $isIncEQ = false;
            if (array_key_exists(self::INCEQ, $min) && is_bool($min[self::INCEQ])) {
                $isIncEQ = $min[self::INCEQ];
            }
            if ($isIncEQ) {
                //[1,
                if ($val < $minVal) {
                    return false;
                }
            } else {
                //(1,
                if ($val <= $minVal) {
                    return false;
                }
            }
        }
        //检查最大值
        if (array_key_exists(self::VAL, $max) && is_int($min[self::VAL])) {
            $maxVal = $min[self::VAL];
            $isIncEQ = false;
            if (array_key_exists(self::INCEQ, $max) && is_bool($max[self::INCEQ])) {
                $isIncEQ = $max[self::INCEQ];
            }
            if ($isIncEQ) {
                //,100]
                if ($val > $maxVal) {
                    return false;
                }
            } else {
                //,100)
                if ($val >= $minVal) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * 检查日期时间
     * @param string $strDateTime 日期的时间字符串
     * @return    bool
     */
    public static function checkDateTime($strDateTime) {
        if (is_string($strDateTime)) {
            return false;
        }
        $parseRes = strtotime($strDateTime);
        if (false === $parseRes) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * 检查对象
     * @param    mixed $obj 要检查的对象
     * @param    array $keys 要检查对象中的key
     */
    public static function checkObject($obj, array $keys = array()) {
        if (true !== is_object($obj)) {
            return false;
        }
        if (0 === count($keys)) {
            return true;
        }
        foreach ($keys as $key) {
            if (true !== property_exists($obj, $key)) {
                return false;
            }
        }
        return true;
    }

    /**
     * 检查数组中每一项中的对象
     * @param    mixed $array 要检查的数组
     * @param    array $keys 要检查数组每一项对象中的key
     * @return    bool
     */
    public static function checkArrayObject($array, array $keys = array()) {
        if (true !== is_array($array)) {
            return false;
        }
        foreach ($array as $obj) {
            if (true !== self::checkObject($obj, $keys)) {
                return false;
            }
        }
        return true;
    }

    /**
     * 检查对象中是否有指定的key，并且是否为空
     * @param Object $obj 要检查的对象
     * @param array $keys 要检查的key列表
     * @param    bool $checkKeyExist 是否检查key存在
     * @return    bool
     */
    public static function checkObjectValIsEmpty($obj, array $keys, $checkKeyExist = FALSE) {
        if (true !== is_object($obj)) {
            return false;
        }
        if (0 === count($keys)) {
            return false;
        }
        foreach ($keys as $key) {
            if (true === $checkKeyExist) {
                //检查key是否存在
                if (true !== property_exists($obj, $key)) {
                    return false;
                }
            }
            if (empty($obj->$key)) {
                return false;
            }
        }
        return true;
    }
}