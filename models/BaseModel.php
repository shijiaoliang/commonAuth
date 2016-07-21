<?php
class BaseModel extends CActiveRecord {
    /**
     * 检测用户密码
     *
     * @return boolean
     */
    public function validatePassword($password) {
        return $this->hashPassword($this->password) === $password;
    }

    /**
     * 密码进行加密
     * @return string password
     */
    public function hashPassword($password) {
        return md5($password);
    }
}