<?php
namespace App\Controller;
use Think\Controller\RestController;

class UploadController extends RestController{

    public function upload(){
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'post':{
                $config = array(
                    "maxSize"=>3145728,// 设置附件上传大小
                    "exts"=>array('jpg','gif','png','jpeg'),// 设置附件上传类型
                    "rootPath"=>'./Public/',// 设置附件上传根目录
                    "savePath"=>'/Uploads/',// 设置附件上传（子）目录
                    "replace"=>true//存在同名文件，true表示被覆盖
//                    "saveName"=>''//保持上传的文件名不变
                );
                $upload = new \Think\Upload($config);// 实例化上传类
                // 上传多个文件 $info   =   $upload->upload();
                //上传单个文件  $info = $upload->uploadOne($_FILES['photo1']);
                $info = $upload->upload();
                if(!$info) {// 上传错误提示错误信息
                    $this->error($upload->getError());
                }else { //将图片数据保存到数据表
                    foreach ($info as $key => $value) {
                        $name = $value["name"];
                        $type = $value["type"];
                        $savename = $value["savename"];
                        $savepath = $value["savepath"];
                        $url = $savepath.$savename;
                        $time = date("Y-m-d");
                        $sql = "replace into upload_img(img_name,img_url,create_time) values(
" . "'" . $savename  . "','" . $savepath . "','" . $time . "'" . ")";
//                        $sql = "update userinfor set photo_url='$url' where token='$token' ";
                        $result = $Model->execute($sql);
                        if (is_bool($result)) {
                            $this->response(retmsg(-1,null,"保存到数据库失败"),'json');
                        } else {
                            $this->response(retmsg(0,null,"图片上传成功"),'json');
//                            $this->success('上传成功！');
                        }
                    }
                }
                break;
            }
        }
    }

    public function getdata(){
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'get': {
                $sql2 = "select * from upload_img";
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

    public function sale(){//售卖申请：测试文件上传
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'post':{
                $username = $_POST["apply_person"];
                $item_name = $_POST["shucai_name"];
                $item_desc = $_POST["detail"];
                $item_amount = $_POST["amount"];
                $item_type = $_POST["classify"];
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
                }
                //选择图片进行上传，将图片元素保存在本地，图片地址存于数据库
                $config = array(
                    "maxSize"=>3145728,// 设置附件上传大小
                    "exts"=>array('jpg','gif','png','jpeg'),// 设置附件上传类型
                    "rootPath"=>'./Public/',// 设置附件上传根目录
                    "savePath"=>'/Uploads/',// 设置附件上传（子）目录
                    "replace"=>true//存在同名文件，true表示被覆盖
//                    "saveName"=>''//保持上传的文件名不变
                );
                $upload = new \Think\Upload($config);// 实例化上传类
                //上传单个文件  $info = $upload->uploadOne($_FILES['photo1']);
                $info = $upload->upload();// 上传多个文件
                if(!$info) {// 上传错误提示错误信息
                    $this->error($upload->getError());
                }else { //将图片数据保存到数据表
                    $sql = "select max(item_id) as last_item from garden_items";
                    $result = $Model->query($sql);
                    $max_item = $result[0]["last_item"]+1;

                    //售卖人信息查询
                    $sql2 = "select nickname from garden_userinfor where user_phone='$username'";
                    $re2 = $Model->query($sql2);
                    $from = $re2[0]["nickname"];

                    //入库操作
                    foreach ($info as $key => $value) {
                        $name = $value["name"];
                        $type = $value["type"];
                        $savename = $value["savename"];
                        $savepath = $value["savepath"];
                        $url = "http://localhost/PersonalGarden/Public".$savepath.$savename;
                        $time = date("Y-m-d H:i:s");
                        $sql_insert = "insert into garden_items(item_id,item_name,item_desc,item_from,item_amount,item_sale_out,item_img,is_verify,apply_time,item_type,type_desc)
                      values (" ."'". safe($max_item) . "','" .
                            safe($item_name) . "','" . safe($item_desc) . "','" . safe($from) . "'," . safe($item_amount) . "," .
                            safe("0") . ",'" . safe($url) . "','" . safe("0") .
                            "','" . safe($time) . "','" . safe($item_type) . "','".safe($type_desc)."'". ")";
                        $insert_result = $Model->execute($sql_insert);
                        $update = "update garden_userinfor set apply_sale=apply_sale+1 where user_phone='$username'";
                        $Model->execute($update);
                        if(is_bool($insert_result)){
                            $this->response(retmsg(-1,null,"申请提交失败"),'json');
                        }else{
                            $this->response(retmsg(0,null,"申请提交成功"),'json');
//                            $url = "http://localhost:8080/PersonalGarden/webapp/pages/UserCenter/my_apply.html";
//                            header("refresh:1;url=$url");
                        }
                    }
                }
                break;
            }
        }
    }
}