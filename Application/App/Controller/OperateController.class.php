<?php
namespace App\Controller;
use Think\Controller\RestController;

class OperateController extends RestController{
    public function operate($token){//查询服务券并使用
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'post':{
//                $json = '{"data":{"which":"松土","sid":"26"}}';
                $data = json_decode(file_get_contents("php://input"), true);
//                $data = json_decode($json, true);
                $which = $data["data"]["which"];
                $sid = $data["data"]["sid"];
                $sql0 = "select user_phone from `garden_userinfor` where token='$token'";
                $result0 = $Model->query($sql0);
                $phone = $result0[0]["user_phone"];
                //查询土地信息
                $sql = "select sid,soil_name,plant from `garden_soil` where sid='$sid'";
                $result = $Model->query($sql);
                $soil_id = $result[0]["sid"];
                $soil_name = $result[0]["soil_name"];
                $plant = $result[0]["plant"];
                //查询服务券信息
                $sql2 = "select sum(buy_amount) as buy_amount from `garden_order` where buyer_phone='$phone' and item_name like '%$which%'";
                $result2 = $Model->query($sql2);
                $send_time = date("Y-m-d H:i:s");
                $desc  = "";
                if($which == "松土"){
                    $desc = "通过人力耕松之后,就使土壤颗粒之间的空隙加大,空气就容易进去,增加了根细胞的呼吸";
                }elseif($which == "栽种"){
                    $desc = "在做好种子处理工作之后将蔬菜种子按照合适密度、采用合适播撒方法，播入适宜的土层中。";
                }elseif($which == "除草"){
                    $desc = "草与植物争肥水,除草后肥水会集中供给植物,使植物长得更好。";
                }elseif($which == "浇水"){
                    $desc = "通过人工或者机器方式补充土壤水分而使蔬菜健康生长发育的田间管理手段。";
                }elseif($which == "施肥"){
                    $desc = "为了让蔬菜良好生长，对蔬菜施用有机肥以弥补土壤养分的不足。";
                }elseif($which == "收割"){
                    $desc = "生长发育到一定阶段，符合收割要求，产量与营养成分积累动态已达到最佳程度时，采取一定的技术措施，从田间将其收集运回。";
                }
                $data = array();
                $buy_amount = $result2[0]["buy_amount"];
                if($buy_amount == null){
                    $buy_amount = 0;
                    $data["my_quan"] = $buy_amount;
                    $this->response(retmsg(-1,$data,"没有服务券"),'json');
                }else{
                    $buy_amount = $result2[0]["buy_amount"];
                    $insert = "insert into garden_operate(send_order,soil_id,soil_name,which,which_desc,send_time,plant) values("."'".
                        safe($token)."','".safe($soil_id)."','".safe($soil_name)."','".safe($which)."','".safe($desc)."','".safe($send_time)."','".safe($plant)."')";
                    $result3 = $Model->execute($insert);
                    if($result3){
                        $sql3 = "select id,item_id,buy_amount from `garden_order` where buyer_phone='$phone' and item_name like '%$which%' order by id desc ";
                        $result4 = $Model->query($sql3);
                        $test = array();
                        if(count($result4) != 1){
                            foreach($result4 as $k=>$v){
                                $test[] = $v["buy_amount"];
                                $id = $v["id"];
                                if($v["buy_amount"] == 1){
                                    $del = "delete from `garden_order` where id='$id'";
                                    $Model->execute($del);
                                }elseif($v["buy_amount"] != 1){
                                    $update = "update `garden_order` set buy_amount=buy_amount-1 where id='$id'";
                                    $Model->execute($update);
                                }
                            }
                        }else{
                            $order_id = $result4[0]["id"];
                            if($result4[0]["buy_amount"] > 1){//大于1就减去一张服务券
                                $update = "update `garden_order` set buy_amount=buy_amount-1 where id='$order_id'";
                                $Model->execute($update);
                            }else{//否则直接删除服务券
                                $del = "delete from `garden_order` where id='$order_id'";
                                $Model->execute($del);
                            }
                        }
                        $data["my_quan"] = $buy_amount;
                        $this->response(retmsg(0,$data,"指令提交成功"),'json');
                    }
                }
                break;
            }
        }
    }

    public function my_seeds($token){//查询我的种子种苗
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'get':{
                $sql = "select user_phone from garden_userinfor where token='$token'";
                $result = $Model->query($sql);
                $user = $result[0]["user_phone"];
                $sql2 = "select a.item_id,a.item_name,a.buy_amount from `garden_order` as a where a.buyer_phone='$user' and a.item_type='seeds'";
                $result2 = $Model->query($sql2);
                $data = array();
                if($result2 == null){
                    $this->response(retmsg(-1,null,"没有种子"),'json');
                }else{
                    $data["subitem"] = $result2;
                    $this->response(retmsg(0,$data,"查询成功"),'json');
                }
                break;
            }
        }
    }

    public function sub_item($item_id,$sid){//从订单表减去种子购买的数量，调用一次接口减一次，并将种子加入土地表
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'get':{
                $sql0 = "select plant from garden_soil where sid='$sid'";
                $result0 = $Model->query($sql0);
                if($result0[0]["plant"] != null){
                    $this->response(retmsg(-1,null,"作物已经种下啦"),'json');
                }else{
                    $sql = "select buy_amount,item_name from `garden_order` where item_id='$item_id'";
                    $result = $Model->query($sql);
                    if($result[0]["buy_amount"] == 1){
                        $del = "delete from `garden_order` where item_id='$item_id'";
                        $Model->execute($del);
                    }else{
                        $update = "update `garden_order` set buy_amount=buy_amount-1 where item_id='$item_id'";
                        $Model->execute($update);
                    }
                    //更新土地表
                    $item_name = $result[0]["item_name"];
                    $up = "update `garden_soil` set plant='$item_name' where sid='$sid'";
                    $re = $Model->execute($up);
                    //更新操作表
                    $up2 = "update garden_operate set plant='$item_name' where soil_id='$sid' and which='栽种'";
                    $Model->execute($up2);
                    if($re){
                        $this->response(retmsg(0,null,"请求栽种成功"),'json');
                    }
                }
                break;
            }
        }
    }

    public function user_operate($sid,$rent_to_who){//追溯流查询
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'get':{
                $rent = md5($rent_to_who);
                $sql = "select * from garden_operate where soil_id='$sid' and send_order='$rent'";
                $result = $Model->query($sql);
                $data = array();
                if($result == null){
                    $data["subitem"] = $result;
                    $this->response(retmsg(0,$data,"查询账户失败"),'json');
                }else{
                    $data["subitem"] = $result;
                    $this->response(retmsg(0,$data,"查询成功"),'json');
                }
                break;
            }
        }
    }

    public function admin_operate($token,$operid,$other=""){//追溯流查询
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'get':{
                $time = date("Y-m-d H:i:s");
                $update = "update garden_operate set get_order='$token',get_time='$time',other='$other' where id='$operid'";
                $result = $Model->execute($update);
                if($result == null){
                    $this->response(retmsg(0,null,"回复指令已提交"),'json');
                }
                break;
            }
        }
    }

    public function dateback($token="",$item_name=""){//追溯流查询
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'get':{
                if($token == ""){
                    $sql = "select a.*,b.name from garden_operate as a,garden_admin as b where a.get_order=b.token and a.plant like '%$item_name%'  order by a.get_time desc";
                }else{
                    $sql = "select a.*,b.name from garden_operate as a,garden_admin as b where a.get_order=b.token and a.plant like '%$item_name%' and a.send_order='$token' order by a.get_time desc";
                }
                $result = $Model->query($sql);
                $data = array();
                if($result == null){
                    $data["subitem"] = $result;
                    $this->response(retmsg(0,$data,"查询失败"),'json');
                }else{
                    $data["subitem"] = $result;
                    $this->response(retmsg(0,$data,"查询成功"),'json');
                }
                break;
            }
        }
    }
}