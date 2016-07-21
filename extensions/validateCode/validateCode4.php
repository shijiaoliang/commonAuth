<?php
//动画验证码类
include "GifCreator.php";
class validateCode4 {
	private $charset = 'abcdefghkmnprstuvwxyzABCDEFGHKMNPRSTUVWXYZ23456789';
    private $code;                           //验证码
    private $codelen = 5;                    //验证码长度
    private $width = 140;                    //宽度
    private $height = 50;                    //高度
    private $img;                            //图形资源句柄
    private $font;                           //指定的字体
    private $fontsize = 24;                  //指定字体大小
    private $fontcolor;                      //指定字体颜色
    private $sessionName = '__verify';
	private $frames      = array();          //存储动画帧数
    //构造方法初始化
    public function __construct() {
        $this->font = dirname(__FILE__) . '/ttfs/' . mt_rand(2,5).'.ttf';
    }
    //生成随机码
    private function createCode() {
        $_len = strlen($this->charset)-1;
        for ($i=0;$i<$this->codelen;$i++) {
            $this->code .= $this->charset[mt_rand(0,$_len)];
        }
    }

    //生成背景
    private function createBg() {
        $this->img = imagecreatetruecolor($this->width, $this->height);
		$color = imagecolorallocate($this->img, 250, 250, 250);
        imagefilledrectangle($this->img,0,$this->height,$this->width,0,$color);
    }

	//生成动画
	public function createGif(){
		$this->createCode();
		
		$color1 = mt_rand(0, 157);
		$color2 = mt_rand(0, 157);
		$color3 = mt_rand(0, 157);
		
		$_x = ($this->width) / ($this->codelen*2);
		$_begin = $this->width /($this->codelen*1.5);
		
		$arrDx = array(
			array(30, -20, 10, 30, 10),
			array(20, -10,  0, 10, 20),
			array(10,  -0, 10, -5, 10),
			array(0,  -10, 20,-10, 30),
			array(10,  20, 30, 20, 10),
		);
		
		//大循环生成每一帧
		for($j = 0; $j < $this->codelen; $j++){
		
			$this->createBg();
			$this->fontcolor = imagecolorallocate($this->img, $color1, $color2, $color3);

			//小循环
			for ($i=0;$i<$this->codelen;$i++) {
				imagettftext($this->img,$this->fontsize, $arrDx[$j][$i], $_x*$i+$_begin, $this->height / 1.4,$this->fontcolor,$this->font,$this->code[$i]);
			}
			
			//干扰线条
			for ($i=0;$i<6;$i++) {
				$color = imagecolorallocate($this->img, $color1, $color2, $color3);
				imageline($this->img,mt_rand(0,$this->width),mt_rand(0,$this->height),mt_rand(0,$this->width),mt_rand(0,$this->height),$color);
			}
			
			$this->frames[] = $this->img;
		}
	}
	
	
    //输出
    private function outPut() {
		//动画展示
		$gc = new GifCreator();
		$durations = array(50, 50, 50, 50, 50);;//开始延迟时间 
		$gc->create($this->frames, $durations);
		$gifBinary = $gc->getGif();
		
		header('Cache-Control: private, max-age=0, no-store, no-cache, must-revalidate');
		header('Cache-Control: post-check=0, pre-check=0', false);
		header('Pragma: no-cache');
		header('Content-type: image/gif');
		header('Content-Disposition: filename="butterfly.gif"');
		echo $gifBinary;
		exit;
    }

    //对外生成
    public function doimg() {
    	$this->clearCode();
        $this->createGif();
        $this->setCode();
        $this->outPut();
    }
    
    //设置验证码
    public function setCode(){
    	$session = Yii::app()->session;
    	$session->open();
    	$name = $this->sessionName;
    	$session[$name] = strtolower($this->code);
    }

    //获取验证码
    public function getCode() {
    	
    	$session = Yii::app()->session;
    	$session->open();
    	$code = null;
    	
    	if(!empty($session[$this->sessionName])){
    		$code = $session[$this->sessionName];
    	}else{
    		$code = $this->code;
    	}
    	return $code;
    }
     
    /**
     * 如果验证码验证失败，则立即清除，即要求验证码立即刷新
     */
    public function clearCode(){
    	$session = Yii::app()->session;
    	$session->open();
    	$name = $this->sessionName;
    	unset($session[$name]);
    }
    
    /**
     * 验证验证码是否正确
     * @param string $input
     * @param string $flag
     * @return boolean
     */
    public function validate($input, $flag = false)
    {
    	$code = $this->getCode();
    	$valid = (strtolower($input) == $code) ? true : false;
    	if($valid && $flag){
    		$this->clearCode();
    	}
    	return $valid;
    }
}