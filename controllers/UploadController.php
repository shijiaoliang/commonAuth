<?php
Yii::import('service.models.extendedwarranty.ExtendedWarrantyBillModel', true);
/**
 * 用户管理相关
 */
class UploadController extends Controller {
    
    private $_language = 'cn';
    
    public function __construct() {
        $cookie = Yii::app()->request->getCookies();
        if (isset($cookie['langKey'])) {
            $this->_language = $cookie['langKey']->value;
        } else {
            $this->_language = 'cn';
        }
    }

    public function actionAjaxUeditorFileUpload() {

        $action = $_GET['action'];

        $CONFIG = Yii::app()->params['uEditorConfig']['config'];
        switch ($action) {
            case 'config':
                $result = json_encode($CONFIG);
                break;
            /* 上传图片 */
            case 'uploadimage':
                $result = include("action_upload.php");
                break;
            default:
                $result = json_encode(array(
                    'state' => '请求地址出错'
                ));
                break;
        }
        /* 输出结果 */
        if (isset($_GET["callback"])) {
            if (preg_match("/^[\w_]+$/", $_GET["callback"])) {
                echo htmlspecialchars($_GET["callback"]) . '(' . $result . ')';
            } else {
                echo json_encode(array(
                    'state' => 'callback参数不合法'
                ));
            }
        } else {
            echo $result;
        }
    }

    // 上传目录读写权限
    public function actionAjaxFileUpload() {
        // 传递和下载图片路径
        $down = $this->request->getParam('down');
        if ($down == "down") {
            $type = $this->request->getParam('type');
            $value = $_GET;
            $value['imgFile'] = $_FILES;
            $value ['maxSize'] = 1024;
            $value ['uploads'] = '';
            if ($type == "fileManager") {
                $this->fileManager($value);
            } elseif ($type == "if") {
                $this->index($value);
            }
            exit();
        }
    }

    //导入excel共用组建
    public function actionUploadExcel() {
        $usedtype = array(
            'userlist' => array(
                'user_id',
                'user_name',
                'mobile',
                'email'
            ),
            'goodslist' => array(
                'goods_code',
                'goods_name',
            ),
            'goodsSpcelist' => array(
                'goods_code',
                'goods_name',
                'goods_price',
                'sale_price',
            )
        );

        $type_name = $this->request->getParam('type_name');
        if (empty($type_name) || empty($usedtype [$type_name])) {
            $this->retJSON(2, array(
                'msg' => '导入方式异常'
            ));
        } else {
            $numName = $usedtype [$type_name];
        }
        // 文件导入处理
        set_time_limit(3000); // 设置程序响应超时时长0
        // 获取上传的XLS文件
        $file = $_FILES ['fullview']; // 获取post传递参数文件流
        if ($file ['error'] == 0) {
            $tmpfile = $file ['tmp_name']; // 获取文件名称

            $type = explode('.', $file ['name']); // 获取文件后缀名
            $filetype = array(
                'xls',
                'xlsx'
            );
            if (!in_array($type [1], $filetype)) {
                $this->retJSON(2, array(
                    'msg' => '文件格式异常'
                ));
            }
            if ($file ['name'] != md5('uploadExcel') . '.xls') {
                $this->retJSON(2, array(
                    'msg' => '请使用上传按钮右侧提供的模版文件上传数据'
                ));
            }
            $name = time() . "." . $type [1]; // 组建新名称
            $resultname = PROJECT_PATH . "service/extensions/Excel/" . $name; // 组建新路径
            @chmod(PROJECT_PATH . "service/extensions/Excel/", 0777);
// 			system( 'sudo chmod -R 777 '.$tmpfile);
            //move_uploaded_file ( $tmpfile, $resultname ); // 移动缓存文件到指定目录
            $resultname = $tmpfile;
            @chmod($resultname, 0777);
            $xls = new Spreadsheet_Excel_Reader (); // 创建工作对象
            $xls->setOutputEncoding('UTF-8');
            $result = $xls->read($resultname);
            if (!empty($result)) {
                $this->retJSON(2, array(
                    'msg' => $result,
                ));
            }
            // 获取数据并处理
            if (empty($xls->sheets[0] ['cells'])) {
                $this->retJSON(2, array(
                    'msg' => '上传数据为空',
                ));
            }
            $data = $xls->sheets [0] ['cells'];
            if (!empty($data) && is_array($data)) {
                unset($data[1]);
                $new_data = array();
                foreach ($data as $val) {
                    if (!empty($val [1])) {
                        $new_one = array();
                        $i = 1;
                        foreach ($numName as $typeval) {
                            if (empty($val [$i])) {
                                $new_one [$typeval] = '';
                            } else {
                                $new_one [$typeval] = $val [$i];
                            }
                            $i ++;
                        }
                        $new_data [] = $new_one;
                    }
                }
                //数据校验
// 				$new_data=$this->checkExcelValue($new_data ,$type_name);
// 				if(empty($new_data)){
// 					$this->retJSON ( 2, array (
// 							'msg' => '导入的数据经过验证，无符合条件数据'
// 					) );
// 				}else{
                $result = array(
                    'data' => $new_data
                );
// 				}
                $this->retJSON(1, $result);
            } else {
                $this->retJSON(2, array(
                    'msg' => '导入的数据为空'
                ));
            }
        } else {
            $this->retJSON(2, array(
                'msg' => '导入文件异常'
            ));
        }
    }

    /**
     * 验证数据
     */
    public function checkExcelValue($data, $type) {
        $newDate = array();
        if ($type == 'goodslist') {
            $goodCodeList = array();
            foreach ($data as $one) {
                $result = $this->service->goods->findDetailGoodsInfo(array('goodsCodes' => $one['goods_code']));
                if ($result ['ret'] == 1) {
                    // 赋值分页对象
                    $arrOne = $result['data'];
                    if ($arrOne['isDiscount'] == 1) {
                        $dataOne = array();
                        $dataOne['goods_code'] = $arrOne['goodsCode'];
                        $dataOne['goods_name'] = $arrOne['goodsName'];
                        $newDate[] = $dataOne;
                    }
                }
            }
        } elseif ($type == 'userlist') {
            foreach ($data as $one) {
                if (!is_num($one['user_id'])) {
                    continue;
                }
                $result = $this->service->user->getUserInfo(array('userId' => $one['user_id']));
                if ($result ['ret'] == 1) {
                    // 赋值分页对象
                    $arrOne = $result['data'];
                    if (!empty($arrOne)) {
                        $dataOne = array();
                        $dataOne['user_id'] = $arrOne['userId'];
                        $dataOne['user_name'] = $arrOne['userName'];
                        $newDate[] = $dataOne;
                    }
                }
            }
        }
        return $newDate;
    }

    //上传图片组建
    public function index($value) {
        // 文件保存url
        $save_url = $value ['uploads'];
        // 存储目录
        $save_path = $value ['updir'];
        $save_url .= $save_path . '/';
        // 类型
        $dir = $value ['dir'];
        $save_path = UPLOAD_DIR . '/' . $save_url;
        if (!is_dir($save_path)) {
            mkdir($save_path, 0777, true);
        }
        // 客户端传递允许大小,单位为K
        // $max_size = $this->getRequest()->getParam('maxSize');
        $max_size = $value ['maxSize'];
        $max_size = floatval($max_size);
        // 默认为100M，转为字节大小
        if (empty($max_size)) {
            $max_size = 100 * 1024 * 1024;
        } else {
            $max_size = $max_size * 1024;
        }

        // 定义允许上传的文件扩展名
        $ext_arr = array(
            'image' => array(
                'gif',
                'jpg',
                'jpeg',
                'png',
                'bmp'
            ),
            'flash' => array(
                'swf',
                'flv'
            ),
            'media' => array(
                'swf',
                'flv',
                'mp3',
                'wav',
                'wma',
                'wmv',
                'mid',
                'avi',
                'mpg',
                'asf',
                'rm',
                'rmvb'
            ),
            'file' => array(
                'doc',
                'docx',
                'xls',
                'xlsx',
                'ppt',
                'pptx',
                'txt',
                'zip',
                'rar',
                'gz',
                'bz2',
                'gif',
                'jpg',
                'jpeg',
                'png',
                'bmp',
                'pdf'
            )
        );
        // 有上传文件时
        if (empty($_FILES) === false) {
            // 原文件名
            $file_name = $_FILES ['file'] ['name'];
            // 服务器上临时文件名
            $tmp_name = $_FILES ['file'] ['tmp_name'];
            // 文件大小
            $file_size = $_FILES ['file'] ['size'];
            // 检查文件名
            // 检查目录名
            $dir_name = empty($dir) ? 'image' : trim($dir);
            if (empty($ext_arr [$dir_name])) {
                $this->retJSON(0, array(), "目录名不正确。");
            }
            if (strpos($file_name, '-') || strpos($file_name, '/') || strpos($file_name, ' ')) {
                if ($this->_language == 'en') {
                    $this->retJSON(0, array(), "File name cannot have special characters - / space ");
                } else {
                    $this->retJSON(0, array(), "文件名不能有特殊字符 - / 空格");
                }
                
            }

            // 获得文件扩展名
            $temp_arr = explode(".", $file_name);
            $file_ext = array_pop($temp_arr);
            $file_ext = trim($file_ext);
            $file_ext = strtolower($file_ext);
            // 检查扩展名
            if (in_array($file_ext, $ext_arr [$dir_name]) === false) {
                if ($this->_language == 'en') {
                    $this->retJSON(0, array(), "Can only upload with the extensions of " . implode(", ", $ext_arr [$dir_name]) . ".");
                } else {
                    $this->retJSON(0, array(), "上传文件扩展名是不允许的扩展名。只允许" . implode(", ", $ext_arr [$dir_name]) . "格式。");
                }
                
            }
            $upArr = array(
                'VerifyKey' => "4f38b3113b147054e04019977e5070c9", //验证的key值
                'FromType' => 4     //图片用途类型：1商城，2论坛    3	=>'user'//用户头像 4=>'csadmin'//客服
            );
            
            $file_ext_arr = explode('.', trim($file_name));
            $file_ext = strtolower(array_pop($file_ext_arr));
            
            
            if ($_GET['dir'] == 'image') {//上传到图片服务器
                //检测强制更改格式的img
                if (!$this->checkImageType($tmp_name)) {
                    $this->retJSON(0, array(), "上传文件扩展名是不允许的扩展名。只允许" . implode(", ", $ext_arr [$dir_name]) . "格式。");
                }

                $newFileName = $tmp_name . '.' . $file_ext;
                move_uploaded_file($tmp_name, $newFileName);
               // $upArr ['uploaded'] = '@' . $newFileName;
                if (class_exists('\CURLFile')) {
                    $upArr ['uploaded'] = new \CURLFile(realpath($newFileName));
                } else {
                    $upArr ['uploaded'] =  '@' . realpath($newFileName);
                }
                $picErrMsg = array(
                    1 => '图片上传服务系统错误',
                    2 => '文件为空',
                    3 => 'Key验证失败',
                    4 => '图片业务类型不对',
                    5 => '图片大小或分辨率超出限制',
                    6 => '文件扩展名不支持'
                );
                $imgresult = $this->uploadByCURL($upArr, STATIC_HTTP_UPLOAD);
                if (!empty($imgresult)) {
                    if(array_key_exists($imgresult, $picErrMsg)){
                        $picErrMsg = array(
                            1 => '图片上传服务系统错误',
                            2 => '文件为空',
                            3 => 'Key验证失败',
                            4 => '图片业务类型不对',
                            5 => '图片大小或分辨率超出限制',
                            6 => '文件扩展名不支持'
                        );
                        $this->retJSON(0, array(), $picErrMsg[$imgresult]);
                    }else{
                         $billModel = new ExtendedWarrantyBillModel();  
                         $ret = $billModel->UploadWarrantyImg($imgresult);//图片路径转化成id
                         if ($ret ['ret'] == 0) {
                             if(!empty($ret['data'])){
                                 $this->retJSON(1, array('file_url' => $file_name . '-' . $ret['data']), "上传图片成功。");
                             }
                             else{
                                 $this->retJSON(0, array(), $ret['errMsg']);
                             }
                         }else{
                             $this->retJSON(0, array(), $ret['errMsg']);
                         }
                    }
                    
                } else {
                    $this->retJSON(0, array(), "上传图片失败。");
                }
            } else {//上传到项目文件夹

                //---------------------------老代码
                // 创建文件夹
                if ($dir_name !== '') {
                    $save_path .= $dir_name . "/";
                    $save_url .= $dir_name . "/";

                    if (!file_exists($save_path)) {
                        @mkdir($save_path, 0777, true);
                    }
                }

                $ymd = date("Ymd");
                $save_path .= $ymd . "/";
                $save_url .= $ymd . "/";

                if (!file_exists($save_path)) {
                    @mkdir($save_path, 0777, true);
                }
                // 新文件名
                $new_file_name = date("YmdHis") . '_' . rand(10000, 99999) . '.' . $file_ext;
                // 移动文件
                $file_path = $save_path . $new_file_name;

                if (move_uploaded_file($tmp_name, $file_path) === false) {
                    $this->retJSON(0, array(), "上传文件失败。");
                }

                @chmod($file_path, 0644);
                $file_url = HTTP_UPLOAD . $save_url . $new_file_name;
                $this->retJSON(1, array('file_url' => $file_name . '-' . $file_url), "上传图片成功。");
                    }
                }
            }

    /**
     * 文件管理
     */
    public function fileManager($value) {
        // 类型
        $dir = $value ['dir'];
        // 存储目录
        $save_path = $value ['updir'];
        // 路径
        $path = $value ['path'];

        $php_url = $value ['uploads'];
        // $php_path = PROJECT_PATH . $php_url;
        $php_path = UPLOAD_DIR . '/' . $php_url;

        // 根目录路径，可以指定绝对路径，比如 /var/www/attached/
        $root_path = $php_path . $save_path . '/';

        // 根目录URL，可以指定绝对路径，比如 http://www.yoursite.com/attached/
        $root_url = $save_path . '/';
        // 图片扩展名
        $ext_arr = array('gif', 'jpg', 'jpeg', 'png', 'bmp', 'apk', 'rar', 'zip', 'doc', 'docx', 'ppt', 'pptx', 'xlsx', 'xls', 'pdf');
        // 目录名
        $dir_name = empty($dir) ? '' : trim($dir);
        if (!in_array($dir_name, array(
                    '',
                    'image',
                    'flash',
                    'media',
                    'file'
                ))) {
            echo "Invalid Directory name.";
            exit();
        }
        if ($dir_name !== '') {
            $root_path .= $dir_name . "/";
            $root_url .= $dir_name . "/";

            if (!file_exists($root_path)) {
                @mkdir($root_path, 0777, true);
            }
        }

        // 根据path参数，设置各路径和URL
        if (empty($path)) {
            $current_path = $root_path;
            $current_url = $root_url;
            $current_dir_path = '';
            $moveup_dir_path = '';
        } else {
            $current_path = $root_path . $path;
            $current_url = $root_url . $path;
            $current_dir_path = $path;
            $moveup_dir_path = preg_replace('/(.*?)[^\/]+\/$/', '$1', $current_dir_path);
        }
        // echo $root_path;测试输出
        // 排序形式，name or size or type
        $order = empty($value ['order']) ? 'name' : strtolower($value ['order']);

        // 不允许使用..移动到上一级目录
        if (preg_match('/\.\./', $current_path)) {
            echo 'Access is not allowed.';
            exit();
        }
        // 最后一个字符不是/
        if (!preg_match('/\/$/', $current_path)) {
            echo 'Parameter is not valid.';
            exit();
        }
        // 目录不存在或不是目录
        if (!file_exists($current_path) || !is_dir($current_path)) {
            @mkdir($save_path, 0777, true);
            $file_list = array();
            // echo 'Directory does not exist.';
            // exit ();
        } else {

            // 遍历目录取得文件信息
            $file_list = array();
            if ($handle = opendir($current_path)) {
                $i = 0;
                while (false !== ($filename = readdir($handle))) {
                    if ($filename {0} == '.')
                        continue;
                    $file = $current_path . $filename;
                    if (is_dir($file)) {
                        $file_list [$i] ['is_dir'] = true; // 是否文件夹
                        $file_list [$i] ['has_file'] = (count(scandir($file)) > 2); // 文件夹是否包含文件
                        $file_list [$i] ['filesize'] = 0; // 文件大小
                        $file_list [$i] ['is_photo'] = false; // 是否图片
                        $file_list [$i] ['filetype'] = ''; // 文件类别，用扩展名判断
                    } else {
                        $file_list [$i] ['is_dir'] = false;
                        $file_list [$i] ['has_file'] = false;
                        $file_list [$i] ['filesize'] = filesize($file);
                        $file_list [$i] ['dir_path'] = '';
                        $file_ext = strtolower(array_pop(explode('.', trim($file))));
                        $file_list [$i] ['is_photo'] = in_array($file_ext, $ext_arr);
                        $file_list [$i] ['filetype'] = $file_ext;
                    }
                    $file_list [$i] ['filename'] = $filename; // 文件名，包含扩展名
                    $file_list [$i] ['datetime'] = date('Y-m-d H:i:s', filemtime($file)); // 文件最后修改时间
                    $i ++;
                }
                closedir($handle);
            }

            function cmp_func($a, $b) {
                global $order;
                if ($a ['is_dir'] && !$b ['is_dir']) {
                    return - 1;
                } else if (!$a ['is_dir'] && $b ['is_dir']) {
                    return 1;
                } else {
                    if ($order == 'size') {
                        if ($a ['filesize'] > $b ['filesize']) {
                            return 1;
                        } else if ($a ['filesize'] < $b ['filesize']) {
                            return - 1;
                        } else {
                            return 0;
                        }
                    } else if ($order == 'type') {
                        return strcmp($a ['filetype'], $b ['filetype']);
                    } else {
                        return strcmp($a ['filename'], $b ['filename']);
                    }
                }
            }

            usort($file_list, 'cmp_func');
        }
        // call_user_func_array(array('usort'), array($file_list,'$this->cmp_func'));

        $result = array();
        // 相对于根目录的上一级目录
        $result ['moveup_dir_path'] = $moveup_dir_path;
        // 相对于根目录的当前目录
        $result ['current_dir_path'] = $current_dir_path;
        // 当前目录的URL
        $result ['current_url'] = HTTP_UPLOAD . $current_url;
        // 文件数
        $result ['total_count'] = count($file_list);
        // 文件列表数组
        $result ['file_list'] = $file_list;

        // 输出JSON字符串
        header('Content-type: application/json; charset=UTF-8');
        echo json_encode($result);
        exit();
    }

    //ajax消息提示
    public function alert($msg) {
        header('Content-type: text/html; charset=UTF-8');
        echo json_encode(array(
            'error' => 1,
            'message' => $msg
        ));
        exit();
    }
    
    /**
     * 文件类型检测
     * @return bool
     */
    private function checkImageType($tmp_name)
    {
        set_error_handler(function (){
            echo '';
        });
        $data = file_get_contents($tmp_name);
        $im = imagecreatefromstring($data);
        if ($im != false) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 远程提交数据
     * @param unknown $post_data
     * @param unknown $post_url
     * @return unknown|string
     */
    public function uploadByCURL($post_data, $post_url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_URL, $post_url);
        curl_setopt($curl, CURLOPT_POST, 1);
        if (class_exists('\CURLFile')) {
            curl_setopt($curl, CURLOPT_SAFE_UPLOAD, true);
        } else {
            if (defined('CURLOPT_SAFE_UPLOAD')) {
                curl_setopt($curl, CURLOPT_SAFE_UPLOAD, false);
            }
        }

        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curl);
        $error = curl_error($curl);
        if (empty($error) && !empty($result)) {
            //返回的对象转换为数组
            $responseJson = json_decode($result, true); // $this->objectToArray($result);
            
            //图片路径地址，上传图片后获得
            if ($responseJson['Errno'] == 0) {
                //返回的图片Url
                $picUrl = $responseJson['ImageMap']['uploaded'];
                return $picUrl;
            } else {
                $picErrMsg = array(
                    1 => '图片上传服务系统错误',
                    2 => '文件为空',
                    3 => 'Key验证失败',
                    4 => '图片业务类型不对',
                    5 => '图片大小或分辨率超出限制',
                    6 => '文件扩展名不支持'
                );
                return $responseJson['Errno'];
            }
        } else {
            return '';
        }
    }
}
