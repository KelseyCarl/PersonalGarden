<?php
namespace App\Controller;
use Think\Controller\RestController;
//require "../../../Public/php-sdk-7.1.3/autoload.php";
require_once  __DIR__."/../../../Public/php-sdk-7.1.3/autoload.php";
use Qiniu\Auth;//七牛云存储鉴权
use Qiniu\Storage\BucketManager;//七牛云资源管理
use Qiniu\Storage\UploadManager;//七牛云上传类

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
                $accesskey = "I4t5AP1DslqxdG4F4HG-YpGzCQXie8hGRB-oZtsw";//鉴权的公钥
                $secretkey = "LoEFHILGp7Bt8bm2BHJ0V6-NWJS33dDWqNN0P9_M";//鉴权的私钥
                $auth = new Auth($accesskey,$secretkey);
                $bucket = "mygardentest";
                $uptoken = $auth->uploadToken($bucket);//生成上传的token
                $file = $_FILES["myfile"];
//                $filePath = "./Public/img_up/".$file["name"];//获取文件原名
                $filePath = str_replace("\\","/",getcwd()."/Public/img_up/").$file["name"];//获取文件原名
                $key = $file["name"];// 上传到七牛后保存的文件名
                $uploadMgr = new UploadManager();// 初始化 UploadManager 对象并进行文件的上传。
                // 调用 UploadManager 的 putFile 方法进行文件的上传。
                list($ret, $err) = $uploadMgr->putFile($uptoken, $key, $filePath);
                if ($err !== null) {
                    var_dump($err);
                } else {
                    $type = $file["type"];
                    $savepath = "http://om2m6x1n8.bkt.clouddn.com/".$key;
                    $insert_sql = "update garden_userinfor set photo_type='$type', photo_url='$savepath' where token='$token2'";
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

    public function upload3(){
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
                    //上传土地的图片
                    $sql = "select max(soil_id) as max_num from garden_soil";
                    $result = $Model->query($sql);
                    $soil_id = $result[0]["max_num"]+1;
                    $farm = $_POST["farm"];
                    $area = $_POST["area"];
                    $insert = "insert into garden_soil(soil_id,farm_belong,soil_area,soil_photo) values("."'".safe($soil_id)."','".
                        safe($farm)."','".safe($area)."','".safe($savepath)."'".")";
                    $insert_re = $Model->execute($insert);
                    if(is_bool($insert_re)){
                        $this->response(retmsg(-1,null,"土地添加失败"),'json');
                    }else{
                        $url = "http://localhost:8080/PersonalGarden/garden/main.html?token=ddb1c8af7b5ae5cb852f580d0c04dac3#w_table3";
                        header("refresh:1;url=$url");
                    }
                }
            }
                break;
        }
    }

    public function upload4(){
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'post':{
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
                    //上传商品的图片
                    $sql = "select max(item_id) as max_num from garden_items";
                    $result = $Model->query($sql);
                    $item_id = $result[0]["max_num"]+1;
                    $item_name = $_POST["goods_name"];
                    $item_acount = $_POST["goods_amount"];
                    $item_price = $_POST["goods_price"];
                    $item_type = $_POST["chose_goods"];
                    $item_from = $_POST["goods_from"];
                    $item_desc = $_POST["goods_desc"];
                    $type_desc = "";
                    if($item_type == "root"){
                        $type_desc = "根菜类";
                    }elseif($item_type == "jing"){
                        $type_desc = "茎菜类";
                    }elseif($item_type == "leaf"){
                        $type_desc = "叶菜类";
                    }elseif($item_type == "flower"){
                        $type_desc = "花菜类";
                    }elseif($item_type == "fruit"){
                        $type_desc = "果菜类";
                    }elseif($item_type == "tools"){
                        $type_desc = "农资工具";
                    }elseif($item_type == "seeds"){
                        $type_desc = "种子种苗";
                    }elseif($item_type == "taocan"){
                        $type_desc = "套餐推荐";
                    }elseif($item_type == "dangji"){
                        $type_desc = "时令蔬菜";
                    }
                    $from = "";
                    if($item_from == "001"){
                        $from = "一点田西江月农场";
                    }elseif($item_from == "002"){
                        $from = "田园牧歌";
                    }elseif($item_from == "003"){
                        $from = "翔生有机生态农场";
                    }
                    $time = date("Y-m-d H:i:s");
                    $insert = "insert into garden_items(item_id,item_name,item_desc,item_from,item_amount,item_price,
item_sale_out,item_img,is_verify,status_desc,apply_time,verify_time,item_type,type_desc) values("."'".safe($item_id)."','".
                        safe($item_name)."','".safe($item_desc)."','".safe($from)."',".safe($item_acount).
                        ",'".safe($item_price)."',".safe("0").",'".safe($savepath)."',".safe("1").",'".safe("审核通过")."','"
                        .safe($time)."','".safe($time)."','".safe($item_type)."','".safe($type_desc)."'".")";
//                    echo $insert;
                    $insert_re = $Model->execute($insert);
                    if(is_bool($insert_re)){
                        $this->response(retmsg(-1,null,"商品添加失败"),'json');
                    }else{
                        $url = "http://localhost:8080/PersonalGarden/garden/main.html?token=ddb1c8af7b5ae5cb852f580d0c04dac3#w_table5";
                        header("refresh:1;url=$url");
                    }
                }
            }
                break;
        }
    }

}