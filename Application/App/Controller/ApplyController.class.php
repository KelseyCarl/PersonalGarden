<?php
namespace App\Controller;
use Think\Controller\RestController;
require_once  __DIR__."/../../../Public/php-sdk-7.1.3/autoload.php";
use Qiniu\Auth;//七牛云存储鉴权
use Qiniu\Storage\BucketManager;
use Qiniu\Storage\UploadManager;

class ApplyController extends RestController{
    public function sale(){//售卖申请
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'post':{
//                $json = '{"data":{"username":"18408245117","item_name":"小白菜","item_desc":"蔬菜的详细信息描述","item_amount":"100","item_type":"root"}}';
//                $data = json_decode(file_get_contents("php://input"), true);
//                $data = json_decode($json, true);
//                $username = $data["data"]["username"];
//                $item_name = $data["data"]["item_name"];
//                $item_desc = $data["data"]["item_desc"];
//                $item_amount = $data["data"]["item_amount"];
//                $item_type = $data["data"]["item_type"];
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
//                echo $item_type."  ";
                //选择图片进行上传到七牛云并入库
                $accesskey = "I4t5AP1DslqxdG4F4HG-YpGzCQXie8hGRB-oZtsw";
                $secretkey = "LoEFHILGp7Bt8bm2BHJ0V6-NWJS33dDWqNN0P9_M";
                $auth = new Auth($accesskey,$secretkey);
                $bucket = "mygardentest";
                //生成上传的token
                $uptoken = $auth->uploadToken($bucket);
                $file = $_FILES["myfile"];
//                $filePath = "./Public/img_up/".$file["name"];//获取文件原名
                $filePath = str_replace("\\","/",getcwd()."/Public/img_up/").$file["name"];//获取文件原名
                $key = $file["name"];//保存文件为原名
                // 初始化 UploadManager 对象并进行文件的上传。
                $uploadMgr = new UploadManager();
                // 调用 UploadManager 的 putFile 方法进行文件的上传。
                list($ret, $err) =$uploadMgr->putFile($uptoken, $key, $filePath);
                if ($err !== null) {
                    var_dump($err);
                } else {
                    $type = $file["type"];
                    $savepath = "http://om2m6x1n8.bkt.clouddn.com/".$key;
                    //将用户提交的售卖信息入库
                    $sql = "select max(item_id) as last_item from garden_items";
                    $result = $Model->query($sql);
//                    echo $result[0]["last_item"]."  ";
//                    echo $result[0]["last_item"]+1;
                    $max_item = $result[0]["last_item"]+1;
                    //售卖人信息查询
                    $sql2 = "select nickname from garden_userinfor where user_phone='$username'";
                    $re2 = $Model->query($sql2);
                    $from = $re2[0]["nickname"];
//                    $status_desc = ""
                    $time = date("Y-m-d H:i:s");
                    //入库操作
                    $sql_insert = "insert into garden_items(item_id,item_name,item_desc,item_from,item_amount,item_sale_out,item_img,is_verify,apply_time,item_type,type_desc)
                      values (" ."'". safe($max_item) . "','" .
                        safe($item_name) . "','" . safe($item_desc) . "','" . safe($from) . "'," . safe($item_amount) . "," .
                        safe("0") . ",'" . safe($savepath) . "','" . safe("0") .
                        "','" . safe($time) . "','" . safe($item_type) . "','".safe($type_desc)."'". ")";
                    $insert_result = $Model->execute($sql_insert);
                    $update = "update garden_userinfor set apply_sale=apply_sale+1 where user_phone='$username'";
                    $Model->execute($update);
                    if(is_bool($insert_result)){
                        $this->response(retmsg(-1,null,"申请提交失败"),'json');
                    }else{
//                        $this->response(retmsg(0,null,"申请提交成功"),'json');
                        $url = "http://localhost:8080/PersonalGarden/webapp/pages/UserCenter/my_apply.html";
                        header("refresh:1;url=$url");
                    }
                }
                break;
            }
        }
    }


    public function rent(){//租赁申请
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'post':{
                $user_phone = $_POST["apply_person"];
                $token = md5($user_phone);
                $farm = $_POST["farm"];
                $soil = $_POST["soil"];
                $period = $_POST["period"];
                $nickname = $_POST["nickname"];
                $desc = $_POST["soil_desc"];
                $money = $_POST["rent_money"];
                $sql_query = "select balance from garden_wallet where token='$token'";
                $re = $Model->query($sql_query);
                if($re[0]["balance"] < $money){
//                    $this->response(retmsg(-2,null,"余额已不足，无法提交申请"),'json');
                    $html = "<p>余额已不足，无法提交申请</p>";
                    echo $html;
                }else{
                    $time = date("Y-m-d H:i:s");
                    $up_wallet = "update garden_wallet set balance=balance-'$money',pay_time='$time' where token='$token'";
                    $Model->execute($up_wallet);
                    $sql_insert = "insert into garden_rent_soil(user_phone,soil_id,farm_belong,soil_status,rent_period)
                  values (" ."'". safe($user_phone) . "','" .
                        safe($soil) . "','" . safe($farm) . "'," . safe("0") .",'".safe($period)."'". ")";
                    $Model->execute($sql_insert);
                    //修改土地名称
                    $up_soil = "update garden_soil set soil_name='$nickname',rent_to_who='$token',soil_desc='$desc' where soil_id='$soil'";
                    $Model->execute($up_soil);
                    $area = "select soil_area from garden_soil where soil_id='$soil' ";
                    $area_re = $Model->query($area);
                    $area1 = $area_re[0]["soil_area"];
                    $in = "update garden_rent_soil set soil_area='$area1'where soil_id='$soil'";
                    $re_in = $Model->execute($in);
                    $update = "update garden_userinfor set soil_nums=soil_nums+1 where user_phone='$user_phone'";
                    $Model->execute($update);
                    if(is_bool($re_in)){
                        $this->response(retmsg(-1,null,"申请提交失败"),'json');
                    }else{
//                        $this->response(retmsg(0,null,"申请提交成功"),'json');
                        $url = "http://localhost:8080/PersonalGarden/webapp/pages/Mysoil/my_soil.html";
                        header("refresh:1;url=$url");
                    }
                }
                break;
            }
        }
    }

    public function query_rent($token){//租赁申请记录查询
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'get':{
                $sql = "select user_phone from garden_userinfor where token='$token'";
                $result = $Model->query($sql);
                $user = $result[0]["user_phone"];
                $sql2 = "select * from garden_rent_soil where user_phone='$user'";
                $re = $Model->query($sql2);
                $data = array();
                for($i = 0;$i < count($re);$i++){
                    $test1 = array("rent_period"=>$re[$i]["rent_period"]);
                    $item_id = $re[$i]["soil_id"];
                    $sql3 = "select * from garden_soil where soil_id='$item_id'";
                    $re2 = $Model->query($sql3);
//                    $data["subitem"][] = $re2;
                    $farm = $re2[0]["farm_belong"];
                    $belong = "";
                    if($farm == "001"){
                        $belong = "一点田西江月农场";
                    }elseif($farm == "002"){
                        $belong = "田园牧歌";
                    }elseif($farm == "003"){
                        $belong = "翔生有机生态农场";
                    }
                    $test = array("soil_id"=>$re2[0]["soil_id"],"soil_name"=>$re2[0]["soil_name"],"farm_belong"=>$belong,
                        "soil_area"=>$re2[0]["soil_area"],"soil_status"=>$re2[0]["soil_status"],"status_desc"=>$re2[0]["status_desc"],
                        "soil_desc"=>$re2[0]["soil_desc"],"soil_photo"=>$re2[0]["soil_photo"]);
                    $data["subitem"][] = array_merge($test,$test1);
                }
                if($data == null){
                    $data["subitem"] = array();
                    $this->response(retmsg(0,$data,"查询成功"),'json');
                }else{
                    $this->response(retmsg(0,$data,"查询成功"),'json');
                }
                break;
            }
        }
    }

    public function query_sale($token){//售卖申请记录查询
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'get':{
                $sql = "select nickname from garden_userinfor where token='$token'";
                $result = $Model->query($sql);
                $nickname = $result[0]["nickname"];
                $sql2 = "select * from garden_items where item_from='$nickname'";
                $re = $Model->query($sql2);
                $data = array();
                $data["subitem"] = $re;
                if(is_bool($re)){
                    $this->response(retmsg(-1,null,"查询失败"),'json');
                }else{
                    $this->response(retmsg(0,$data,"查询成功"),'json');
                }
                break;
            }
        }
    }

    public function transfer($token){//转让:转让土地
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'get':{
                $sql = "select * from garden_soil where soil_status=1 and rent_to_who='$token'";
                $result = $Model->query($sql);
                if(is_bool($result)){
                    $this->response(retmsg(-1,null,"查询失败"),'json');
                }else{
                    $data = array();
                    $data["subitem"] = $result;
                    $this->response(retmsg(0,$data,"查询成功"),'json');
                }
                break;
            }
        }
    }

    public function trans(){//土地转让
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'post':{
                $user_phone = $_POST["apply_person"];
                $token1 = md5($user_phone);
                $out_person = $_POST["out_person"];
                $token2 = md5($out_person);
                $soil = $_POST["soil"];
                $time = date("Y-m-d H:i:s");
                $insert = "insert into garden_transfer(trans_out,soil_id,trans_in,trans_time) values("."'".safe($token1)."','".safe($soil)."','".safe($token2)."','".safe($time)."'".")";
                $result = $Model->execute($insert);
                if(is_bool($result)){
                    $this->response(retmsg(-1,null,"申请失败"),'json');
                }else{
                    $this->response(retmsg(0,null,"申请成功"),'json');
                }
                break;
            }
        }
    }
    public function query_transfer($token){//转让申请记录查询
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'get':{
                $sql2 = "select * from garden_soil where status_desc='转让待租赁' and rent_to_who='$token'";
                $re = $Model->query($sql2);
                $data = array();
                foreach($re as $key=>$value){
                    $belong = $value["farm_belong"];
                    if($belong == "001"){
                        $farm = "一点田西江月农场";
                    }else if($belong == "002"){
                        $farm = "田园牧歌";
                    }else if($belong == "003"){
                        $farm = "翔生有机生态农场";
                    }
                    $arr = array("sid"=>$value["sid"],"soil_id"=>$value["soil_id"],"soil_photo"=>$value["soil_photo"],
                        "soil_area"=>$value["soil_area"],"farm"=>$farm,"status_desc"=>$value["status_desc"],
                        "farm_belong"=>$value["farm_belong"],);
                    $data["subitem"] = $arr;
                }
                if($data == null){
                    $data["subitem"] = array();
                    $this->response(retmsg(0,$data,"查询成功"),'json');
                }else{
                    $this->response(retmsg(0,$data,"查询成功"),'json');
                }
                break;
            }
        }
    }
}