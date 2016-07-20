<?php
/**
 * 日志类
 */
class Log {
    /**
     * 错误信息
     * @var    int
     */
    const LEVEL_ERROR = 0;
    /**
     * 警告
     * @var int
     */
    const LEVEL_WARN = 1;
    /**
     * 正常信息
     * @var int
     */
    const LEVEL_INFO = 2;
    /**
     * 调试信息
     * @var int
     */
    const LEVEL_DEBUG = 3;
    /**
     * 配置等级字符串
     * @var array
     */
    private static $arrLevelStr = array(
        'error',
        'warning',
        'info',
        'trace'
    );
    /**
     * 添加日志
     * @param    string $content 日志内容
     * @param    string $category 日志类别
     * @param    int $level 日志等级
     * @return    void
     */
    public static function addLog($content, $category = 'application', $level = self::LEVEL_INFO) {
        if (true !== is_string($content)) {
            return;
        }
        if (!is_int($level)) {
            return;
        }
        if (($level > self::LEVEL_DEBUG) || ($level < self::LEVEL_ERROR)) {
            $level = self::LEVEL_INFO;
        }
        $levelStr = self::$arrLevelStr[$level];
        //目前调用的是yii框架的实现
        Yii::log($content, $levelStr, $category);
    }
}