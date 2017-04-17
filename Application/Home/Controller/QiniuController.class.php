<?php
namespace Home\Controller;
use Think\Controller\RestController;
//require "../../../Public/php-sdk-7.1.3/autoload.php";
require_once  __DIR__."/../../../Public/php-sdk-7.1.3/autoload.php";
use Qiniu\Auth;//七牛云存储鉴权
use Qiniu\Storage\BucketManager;
use Qiniu\Storage\UploadManager;

class QiniuController extends RestController{

    public function store(){
        //鉴权的公钥和私钥
        $accesskey = "I4t5AP1DslqxdG4F4HG-YpGzCQXie8hGRB-oZtsw";
        $secretkey = "LoEFHILGp7Bt8bm2BHJ0V6-NWJS33dDWqNN0P9_M";
        $auth = new Auth($accesskey,$secretkey);
        //存储空间名
        $bucket = "mygardentest";
        //生成上传的token
        $token = $auth->uploadToken($bucket);
        echo "token=".$token;
//        echo __DIR__."/../../../Public/php-sdk-7.1.3/autoload.php";
    }

    public function bucket(){
        $accesskey = "I4t5AP1DslqxdG4F4HG-YpGzCQXie8hGRB-oZtsw";
        $secretkey = "LoEFHILGp7Bt8bm2BHJ0V6-NWJS33dDWqNN0P9_M";
        $auth = new Auth($accesskey,$secretkey);
        $bucketManager = new BucketManager($auth);
        //测试空间
        $bucket = "mygardentest";
        $key = 'cc.jpg';//空间中存在的文件

        //获取文件的状态信息
        list($ret, $err) = $bucketManager->stat($bucket, $key);
        if ($err !== null) {
            var_dump($err);
        } else {
            $data = array();
            $data["subitem"] = array();
            $data["subitem"] = $ret;
            echo $data."<br>";
//            $this->ajaxReturn($data,'JSON');
        }
        //将文件从文件$key 复制到文件$key2。 可以在不同bucket复制
        $key2 = 'cc2.png';
        $err = $bucketManager->copy($bucket, $key, $bucket, $key2);
        echo "\n====> copy $key to $key2 : \n";
        if ($err !== null) {
            var_dump($err);
        } else {
            echo "Success!";
        }

        //将文件从文件$key2 移动到文件$key3。 可以在不同bucket移动
//        $key3 = 'cc3.png';
//        $err = $bucketManager->move($bucket, $key2, $bucket, $key3);
//        echo "\n====> move $key2 to $key3 : \n";
//        if ($err !== null) {
//            var_dump($err);
//        } else {
//            echo "Success!";
//        }

        //删除$bucket 中的文件 $key
        $err = $bucketManager->delete($bucket, $key2);
        echo "\n====> delete $key2 : \n";
        if ($err !== null) {
            var_dump($err);
        } else {
            echo "Success!";
        }
    }

    public function upload(){
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'post':{
                $token2 = $_POST["token2"];
//                echo $token2;
                //鉴权的公钥和私钥
                $accesskey = "I4t5AP1DslqxdG4F4HG-YpGzCQXie8hGRB-oZtsw";
                $secretkey = "LoEFHILGp7Bt8bm2BHJ0V6-NWJS33dDWqNN0P9_M";
                $auth = new Auth($accesskey,$secretkey);
                $bucket = "mygardentest";
                //生成上传的token
                $uptoken = $auth->uploadToken($bucket);
//                echo "token=".$uptoken;
                $file = $_FILES["myfile"];
//                $filePath = "./Public/img_up/".$file["name"];//获取文件原名
                $filePath = str_replace("\\","/",getcwd()."/Public/img_up/").$file["name"];//获取文件原名
                // 上传到七牛后保存的文件名
                $key = $file["name"];//保存文件为原名
                // 初始化 UploadManager 对象并进行文件的上传。
                $uploadMgr = new UploadManager();
                // 调用 UploadManager 的 putFile 方法进行文件的上传。
                list($ret, $err) = $uploadMgr->putFile($uptoken, $key, $filePath);
                if ($err !== null) {
                    var_dump($err);
                } else {
                    $type = $file["type"];
                    $savepath = "http://om2m6x1n8.bkt.clouddn.com/".$key;
                    $insert_sql = "update userinfor set photo_type='$type', photo_url='$savepath' where token='$token2'";
//                    echo $insert_sql;
                    $insert_result = $Model->execute($insert_sql);
                    if(is_bool($insert_result)){
                        $this->response(retmsg(-1,null,"上传更新图片失败！"),'json');
                    }else{
//                        $this->response(retmsg(0,null,"上传更新图片成功！"),'json');
                        $url = "http://localhost:8080/PersonalGarden/webapp/pages/UserCenter/userinfor_setting.html";
                        header("refresh:1;url=$url");
                    }
                }
            }
            break;
        }
    }

    public function getdata(){
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'get': {
                $sql2 = "select * from photo";
                $result2 = $Model->query($sql2);
                if(is_bool($result2)){
                    echo "查询数据失败<br>";
                }else{
                    $data = $result2;
                    $this->response(retmsg(0,$data,"查询成功！"),'json');
                }
                break;
            }
        }
    }

    public function add(){
        $this->theme('blue')->display('form');//加载blue主题下面的blue.html
    }

    public function upload2(){
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'post':{
                $token2 = $_POST["token2"];
//                echo $token2;
                //鉴权的公钥和私钥
                $accesskey = "I4t5AP1DslqxdG4F4HG-YpGzCQXie8hGRB-oZtsw";
                $secretkey = "LoEFHILGp7Bt8bm2BHJ0V6-NWJS33dDWqNN0P9_M";
                $auth = new Auth($accesskey,$secretkey);
                $bucket = "mygardentest";
                //生成上传的token
                $uptoken = $auth->uploadToken($bucket);
//                echo "token=".$uptoken;
                $file = $_FILES["myfile"];
//                $filePath = "./Public/img_up/".$file["name"];//获取文件原名
                $filePath = str_replace("\\","/",getcwd()."/Public/img_up/").$file["name"];//获取文件原名
                // 上传到七牛后保存的文件名
                $key = $file["name"];//保存文件为原名
                // 初始化 UploadManager 对象并进行文件的上传。
                $uploadMgr = new UploadManager();
                // 调用 UploadManager 的 putFile 方法进行文件的上传。
                list($ret, $err) = $uploadMgr->putFile($uptoken, $key, $filePath);
                if ($err !== null) {
                    var_dump($err);
                } else {
                    $type = $file["type"];
                    $savepath = "http://om2m6x1n8.bkt.clouddn.com/".$key;
//                    $insert_sql = "update userinfor set photo_type='$type', photo_url='$savepath' where token='$token2'";
////                    echo $insert_sql;
//                    $insert_result = $Model->execute($insert_sql);
//                    if(is_bool($insert_result)){
//                        $this->response(retmsg(-1,null,"上传更新图片失败！"),'json');
//                    }else{
////                        $this->response(retmsg(0,null,"上传更新图片成功！"),'json');
//                        $url = "http://localhost:8080/PersonalGarden/webapp/pages/UserCenter/userinfor_setting.html";
//                        header("refresh:1;url=$url");
//                    }
                }
            }
                break;
        }
    }

}