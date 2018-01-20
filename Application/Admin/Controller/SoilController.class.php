<?php
namespace Admin\Controller;
use Think\Controller\RestController;

class SoilController extends RestController{
    public function test(){
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'get': {
                echo "enter here";
                $sql = "select * from garden_userinfor";
                $re = $Model->query($sql);
                var_dump($re);
            }
        }

    }
    public function all_soil($token="",$belong="",$is_rent="",$page="1",$pagesize="10"){
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'get':{
                $where = "";
                if($belong != ''){
                    $where .="farm_belong='".$belong."'";
                }
                if($is_rent!='' && $is_rent == 1){
                    $where .="soil_status=1";
                }
                if($is_rent!='' && $is_rent == 0){
                    $where .="soil_status != 1";
                }
                $sql = "select * from garden_soil";
                $result = $Model->query($sql);
                $n = count($result);
                $m = ceil($n/$pagesize);
                $l = ($page-1)*$pagesize;
                if($where == "") {
                    $sql1 = "select * from garden_soil order by soil_id desc limit ".$l.",".$pagesize;
                }else{
                    $sql1 = "select * from garden_soil  where  ".$where."  order by soil_id desc  limit ".$l.",".$pagesize;
//                    $m='1';
                }
                $list = $Model->query($sql1);
                $data = array();
                for($i = 0;$i < count($list);$i++){
                    $rent_to_who = $list[$i]["rent_to_who"];
                    $sql2 = "select user_phone,nickname from garden_userinfor where token='$rent_to_who'";
                    $result2 = $Model->query($sql2);
                    $rent_to = $result2[0]["user_phone"];
                    $belong = $list[$i]["farm_belong"];
                    $farm = "";
                    if($belong == "001"){
                        $farm = "一点田西江月农场";
                    }else if($belong == "002"){
                        $farm = "田园牧歌";
                    }else if($belong == "003"){
                        $farm = "翔生有机生态农场";
                    }
                    $return = array("sid"=>$list[$i]["sid"],"soil_id"=>$list[$i]["soil_id"],"soil_photo"=>$list[$i]["soil_photo"],
                        "soil_area"=>$list[$i]["soil_area"],"farm"=>$farm,"status_desc"=>$list[$i]["status_desc"],
                        "farm_belong"=>$list[$i]["farm_belong"],"rent_to_who"=>$rent_to);
                    $data[] = $return;
                }
                if(is_bool($list)){
                    $this->response(retmsg(-1),'json');
                    return;
                }
                $data = array('resultcode'=>'0','resultmsg'=>'查询成功','page'=>$page,'pagecount'=>$m,'data'=>$data);
                echo json_encode($data);
                return;
            }
        }
    }

    public function my_soil($token="",$id){//用户的id
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'get':{
                $sql1 = "select user_phone from garden_userinfor where user_id='$id'";
                $re1 = $Model->query($sql1);
                $phone = $re1[0]["user_phone"];
                $sql2 = "select * from garden_rent_soil where user_phone='$phone'";
                $re2 = $Model->query($sql2);
                $data = array();
                $data["subitem"] = $re2;
                if(is_bool($re2)){
                    $this->response(retmsg(-1,null,"查询失败"),'json');
                }else{
                    $this->response(retmsg(0,$data,"查询成功"),'json');
                }
                break;
            }
        }
    }

    public function one_soil($sid){//土地的id
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'get':{
                $sql = "select * from garden_soil where sid='$sid'";
                $result = $Model->query($sql);
                $data = array();
                $data["subitem"] = $result;
                if(is_bool($result)){
                    $this->response(retmsg(-1,null,"查询失败"),'json');
                }else{
                    $this->response(retmsg(0,$data,"查询成功"),'json');
                }
                break;
            }
        }
    }


    public function verify($token=""){
    header("Access-Control-Allow-Origin:*");
    $Model = M();
        switch($this->_method) {
            case 'post':{
    //            $data = '{"data":{"rent_id":5,"verify_status":1,"unpass_cause":""}}';
                $json = json_decode(file_get_contents("php://input"),true);
    //            $json = json_decode($data,true);
                $rent_id = $json["data"]["rent_id"];
                $verify_status = $json["data"]["verify_status"];
                $unpass_cause = $json["data"]["unpass_cause"];
                if($verify_status == 1){
                    $rent_status = 1;
                    $desc = "审核通过";
                    $unpass = "暂无数据";
                }else if($verify_status == -1){
                    $rent_status = -1;
                    $desc = "审核未过";
                    $unpass = $unpass_cause;
                }
                $time = date("Y-m-d H:i:s");
                $up = "update garden_rent_soil set soil_status='$rent_status',verify_status='$verify_status',verify_desc='$desc',unpass_cause='$unpass',verify_time='$time' where rent_id='$rent_id'";
                $result = $Model->execute($up);
                //获取土地编号
                $sql = "select soil_id from garden_rent_soil where rent_id='$rent_id'";
                $reslut2 = $Model->query($sql);
                $soil_id = $reslut2[0]["soil_id"];
                //更新土地信息表
                $up2 = "update garden_soil set soil_status='$rent_status',status_desc='$desc' where soil_id='$soil_id'";
                $Model->execute($up2);
                if(is_bool($result)){
                    $this->response(retmsg(-1,null,"土地申请审核失败"),'json');
                }else{
                    $this->response(retmsg(0,null,"土地申请审核成功"),'json');
                }
                break;
            }
        }
    }

    public function change($token=""){
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'post':{
//                $data = '{"data":{"soilid":8,"area":"3.5*1.0"}}';
                $json = json_decode(file_get_contents("php://input"),true);
//                $json = json_decode($data,true);
                $sid = $json["data"]["soilid"];
                $area = $json["data"]["area"];
                $up = "update garden_soil set soil_area='$area' where sid='$sid'";
                $result = $Model->execute($up);
                if(is_bool($result)){
                    $this->response(retmsg(-1,null,"土地信息修改失败"),'json');
                }else{
                    $this->response(retmsg(0,null,"土地信息修改成功"),'json');
                }
                break;
            }
        }
    }

    //删除土地信息
    public function del_soil($token="",$sid){
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'post':{
                $del = "delete from garden_soil where sid='$sid'";
                $result = $Model->execute($del);
                if(is_bool($result)){
                    $this->response(retmsg(-1,null,"删除失败"),'json');
                }else{
                    $sql = "select soil_id from garden_soil where sid='$sid'";
                    $result2 = $Model->query($sql);
                    $sid2 = $result2[0]["soil_id"];
                    $del2 = "delete from garden_rent_soil where soil_id='$sid2'";
                    $Model->execute($del2);
                    $this->response(retmsg(0,null,"土地信息删除成功"),'json');
                }
                break;
            }
        }
    }

    //土地转让申请提交
    public function transfer_soil($token,$sid){
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'get':{
                $sql = "select * from garden_userinfor where token='$token'";
                $reslut = $Model->query($sql);
                $user_phone = $reslut[0]["user_phone"];//转出方电话
                $nickname = $reslut[0]["nickname"];//转出方姓名
                $sql2 = "select * from `garden_soil` where sid='$sid'";
                $reslut2 = $Model->query($sql2);
                $soil_id = $reslut2[0]["soil_id"];
                $soil_area = $reslut2[0]["soil_area"];
                $soil_photo = $reslut2[0]["soil_photo"];
                $time = date("Y-m-d H:i:s");
                $sql3 = "select sid,trans_status from garden_transfer where sid='$sid'";
                $re3 = $Model->query($sql3);
                if($re3){
                    $this->response(retmsg(-1,null,"该土地已被转让"),'json');
                }else{
                    $insert = "insert into garden_transfer(trans_out,nickname_out,sid,soil_id,soil_area,soil_photo,apply_trans_time) values ("."'".
                        safe($user_phone)."','".safe($nickname)."',".safe($sid).",'".safe($soil_id)."','".safe($soil_area)."','".
                        safe($soil_photo)."','".safe($time)."'".")";
                    $re = $Model->execute($insert);
                    $up_first = "update garden_soil set status_desc='转让待租赁' where sid='$sid'";
                    $Model->execute($up_first);
                    $up_second = "update garden_rent_soil set soil_status='0',verify_desc='转让待租赁' where soil_id='$soil_id'";
                    $Model->execute($up_second);
                    $update = "update garden_userinfor set soil_nums=soil_nums-1 where user_phone='$user_phone'";
                    $Model->execute($update);
                    $update2 = "update garden_wallet set balance=balance+100 where token='$soil_photo'";
                    $Model->execute($update2);

                    if(is_bool($re)){
                        $this->response(retmsg(-1,null,"转租失败"),'json');
                    }else{
                        $this->response(retmsg(0,null,"转让申请提交成功"),'json');
                    }
                }
                break;
            }
        }
    }

    //系统转租的土地查询
    public function all_transfer_soil(){
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'get':{
                $sql = "select * from garden_transfer where trans_status='待租赁'";
                $reslut = $Model->query($sql);
                $data = array();
                $data["subitem"] = $reslut;
                if(is_bool($reslut)){
                    $this->response(retmsg(-1,null,"查询转租土地信息失败"),'json');
                }else{
                    $this->response(retmsg(0,$data,"查询转租土地信息成功"),'json');
                }
                break;
            }
        }
    }

    public function make_a_deal($token,$sid){//其他用户提交转租申请，进行土地权限转让
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'post':{
                $sql = "select user_phone,nickname from garden_userinfor where token='$token'";
                $re = $Model->query($sql);
                $sql2 = "select soil_id from garden_soil where sid='$sid'";
                $re2 = $Model->query($sql2);
                $soil_id = $re2[0]["soil_id"];
                $user = $re[0]["user_phone"];
                $time = date("Y-m-d H:i:s");
                $name = $re[0]["nickname"];
                $update = "update garden_soil set rent_to_who='$token',soil_status=0,status_desc='待审核' where sid='$sid'";
                $reslut = $Model->execute($update);
                $update2 = "update garden_transfer set trans_in='$user',nickname_in='$name',trans_out_time='$time',trans_status='已租赁'  where sid='$sid'";
                $reslut2 = $Model->execute($update2);
                $update3 = "update garden_userinfor set soil_nums=soil_nums+1 where token='$token'";
                $Model->execute($update3);
                $update4 = "update garden_rent_soil set user_phone='$user',verify_status=0,verify_desc='待审核',unpass_cause='',verify_time='' where soil_id='$soil_id'";
                $Model->execute($update4);
                $update5 = "update garden_wallet set balance=balance-100 where token='$token'";
                $Model->execute($update5);
                if($reslut2){
                    $this->response(retmsg(0,null,"转租申请成功"),'json');
                }else{
                    $this->response(retmsg(-1,null,"转租申请失败"),'json');
                }
                break;
            }
        }
    }
}