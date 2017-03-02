<?php
namespace Home\Controller;
use Qiniu\Storage\UploadManager;
use Think\Controller\RestController;
require 'Public/php-sdk-7.1.3/autoload.php';
use Qiniu\Auth;//七牛云存储鉴权
use Qiniu\Storage\BucketManager;

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

    public function callback(){
        $accesskey = "I4t5AP1DslqxdG4F4HG-YpGzCQXie8hGRB-oZtsw";
        $secretkey = "LoEFHILGp7Bt8bm2BHJ0V6-NWJS33dDWqNN0P9_M";
        $auth = new Auth($accesskey,$secretkey);
        //获取回调的body信息
        $callbackBody = file_get_contents('php://input');

        //回调的contentType
        $contentType = 'application/x-www-form-urlencoded';

        //回调的签名信息，可以验证该回调是否来自七牛
        $authorization = $_SERVER['HTTP_AUTHORIZATION'];

        //七牛回调的url，具体可以参考：http://developer.qiniu.com/docs/v6/api/reference/security/put-policy.html
        $url = 'http://172.30.251.210/callback.php';

        $isQiniuCallback = $auth->verifyCallback($contentType, $authorization, $url, $callbackBody);

        if ($isQiniuCallback) {
            $resp = array('ret' => 'success');
        } else {
            $resp = array('ret' => 'failed');
        }

        echo json_encode($resp);
    }

    public function upload(){
        $accesskey = "I4t5AP1DslqxdG4F4HG-YpGzCQXie8hGRB-oZtsw";
        $secretkey = "LoEFHILGp7Bt8bm2BHJ0V6-NWJS33dDWqNN0P9_M";
        $auth = new Auth($accesskey,$secretkey);
        //存储空间名
        $bucket = "mygardentest";
        //生成上传的token
        $token = $auth->uploadToken($bucket);
        $filePath = "./Public/img_up/my_love.png";//要上传文件的本地路径
        // 上传到七牛后保存的文件名
        $key = 'love.png';
        // 初始化 UploadManager 对象并进行文件的上传。
        $uploadMgr = new UploadManager();
        // 调用 UploadManager 的 putFile 方法进行文件的上传。
        list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
        echo "\n====> putFile result: \n";
        if ($err !== null) {
            var_dump($err);
        } else {
//            var_dump($ret);
            echo json_encode($ret);
        }
    }



    public function add(){
        $this->theme('blue')->display('form');//加载blue主题下面的blue.html
    }

}