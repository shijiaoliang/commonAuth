<?php

Yii::import('service.models.extendedwarranty.ExtendedWarrantyBillModel', true);

class ImageController extends Controller {


    /**
     * 图片展示
     * @return type
     */
    function actionImageShow() {
        $imgID = $_GET['id'];
        $Url = "";
        $billModel = new ExtendedWarrantyBillModel();
        $ret = $billModel->SelectWarrantyImg($imgID);
        if ($ret ['ret'] == 0) {
            if (!empty($ret['data'])) {
                $Url = $ret['data'];
                header('content-type:image/gif;');
                header('content-type:image/jpg;');
                header('content-type:image/jpeg;');
                header('content-type:image/png;');
                header('content-type:image/bmp;');
                $content = file_get_contents($Url);
                echo $content;
            } else {
                header("Content-Type:text/html;charset=utf-8"); 
                $content = file_get_contents($ret['errMsg']);
                echo $content;
            }
        } else {
            header("Content-Type:text/html;charset=utf-8"); 
            $content = file_get_contents($ret['errMsg']);
            echo $content;
        }
    }

}
