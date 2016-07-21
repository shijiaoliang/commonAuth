<?php
class validateCode2 {
	private $charset = 'ABCDEFGHKMNPRSTUVWXYZ';
    private $code;                           //验证码
    private $codelen = 4;                    //验证码长度
    private $width = 120;                    //宽度
    private $height = 50;                    //高度
    private $img;                            //图形资源句柄
    private $font;                           //指定的字体
    private $fontsize = 28;                  //指定字体大小
    private $fontcolor;                      //指定字体颜色
    private $sessionName = '__verify';
	private $frames      = array();          //存储动画帧数
	private $bgcolor;
    //构造方法初始化
    public function __construct() {
        $this->font = dirname(__FILE__) . '/ttfs/3.ttf';
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
		$this->bgcolor = imagecolorallocate($this->img, 255, 255, 255);
        imagefilledrectangle($this->img,0,$this->height,$this->width,0,$this->bgcolor);
    }

    //生成文字
    private function createFont() {    
        $_x = $this->width/($this->codelen*1.1) ;
		$_begin = abs(($this->width -($this->codelen*$this->fontsize)))/2;
		$this->fontcolor = imagecolorallocate($this->img,0, 0, 0);
        for ($i=0;$i<$this->codelen;$i++) {
			$rand = mt_rand(1, ($this->codelen - 2));
			if($rand == $i){
				$_begin = $_begin - 5;
				imagettftext($this->img,$this->fontsize,mt_rand(-30,30), $_begin + $_x*$i,$this->height / 1.2,$this->fontcolor,$this->font,$this->code[$i]);
			}else{
				imagettftext($this->img,$this->fontsize,mt_rand(-20,20), $_begin + $_x*$i,$this->height / 1.4,$this->fontcolor,$this->font,$this->code[$i]);
			} 
        }
		
		//生成线条、雪花
		$line = mt_rand(2, 4);
		imagesetthickness($this->img, $line);
		imageline($this->img, 0, $this->height/2, $this->width, $this->height/2, $this->bgcolor);
		imageline($this->img, 0, $this->height/3, $this->width, $this->height/3, $this->bgcolor);
    }
	

	
	
    //输出
    private function outPut() {
    	header('Cache-Control: private, max-age=0, no-store, no-cache, must-revalidate');
    	header('Cache-Control: post-check=0, pre-check=0', false);
    	header('Pragma: no-cache');
        header('Content-type:image/png');
        imagepng($this->img); 
        imagedestroy($this->img);
    }

    //对外生成
    public function doimg() {
    	$this->clearCode();
		$this->createCode();
		$this->createBg();
		$this->createFont();
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