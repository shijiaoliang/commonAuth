<?php
/**
 * 实体类的抽象父类
 */
abstract class EntityAbstract{
	/**
	 * 拷贝对象
	 * @param Object $src	源对象
	 * @param array $keys	要拷贝对象中的key
	 * @return	stdClass
	 */
	public static function copyObject($src, array $keys){
		$res = new stdClass();
		foreach ($keys as $key){
			$res->$key = $src->$key; 
		}
		return $res;
	}
}
