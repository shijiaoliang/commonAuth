<?php
class factoryCode {
    /**
     * 生成验证码
     * @param string $int
     * @return object
     */
    public static function createObj($type = null) {
        $arrCode = array(
            1,
            2,
            3,
            4,
            5
        );
        if (!empty($type) && in_array($type, $arrCode)) {
            $class = 'validateCode' . $type;
        } else {
            $day = date('d', time());
            $type = ($day % 5) + 1;
            $class = 'validateCode' . $type;
        }

        require_once('validateCode/' . $class . '.php');
        return new $class();
    }

    /**
     * 校验验证码
     * @param string $input
     * @param bool $flag
     */
    public static function validate($input, $flag = false) {
        $result = false;
        require_once('validateCode/validateCode1.php');
        $objCode = new validateCode1();
        if (!empty($input)) {
            $result = $objCode->validate($input, $flag);
        }
        return $result;
    }
}