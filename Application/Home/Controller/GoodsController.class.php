<?php
namespace Home\Controller;
use Think\Controller\RestController;

class GoodsController extends RestController{
    public function all_goods($token="",$type="",$page="1",$pagesize="10"){
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'get':{
                $where = "";
                if($type != ''){
                    $where .="item_type='".$type."'";
                }
                $sql = "select * from items";
                $result = $Model->query($sql);
                $n = count($result);
                $m = ceil($n/$pagesize);
                $l = ($page-1)*$pagesize;
                if($where == "") {
                    $sql1 = "select * from items order by apply_time desc limit ".$l.",".$pagesize;
                }else{
                    $sql1 = "select * from items  where  ".$where."  order by apply_time desc  limit ".$l.",".$pagesize;
                    $m='1';
                }
                $list = $Model->query($sql1);
                if(is_bool($list)){
                    $this->response(retmsg(-1),'json');
                    return;
                }
                $data = array('resultcode'=>'0','resultmsg'=>'查询成功','page'=>$page,'pagecount'=>$m,'data'=>$list);
                echo json_encode($data);
                return;
            }
        }
    }

    public function my_sale($token="",$id){//用户的id
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'get':{
                $sql1 = "select nickname from userinfor where user_id='$id'";
                $re1 = $Model->query($sql1);
                $nickname = $re1[0]["nickname"];
                $sql2 = "select * from items where item_from='$nickname'";
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

    public function verify_sale($token=""){
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'post':{
//              $data = '{"data":{"item_id":16,"is_verify":1,"unpass_cause":""}}';
                $json = json_decode(file_get_contents("php://input"),true);
//              $json = json_decode($data,true);
                $item_id = $json["data"]["item_id"];
                $is_verify = $json["data"]["is_verify"];
                $unpass_cause = $json["data"]["unpass_cause"];
                if($is_verify == 1){
                    $verify = 1;
                    $desc = "审核通过";
                    $unpass = "暂无数据";
                }else if($is_verify == -1){
                    $verify = -1;
                    $desc = "审核未过";
                    $unpass = $unpass_cause;
                }
                $time = date("Y-m-d H:i:s");
                $up = "update items set is_verify='$verify',status_desc='$desc',unpass_cause='$unpass',verify_time='$time' where id='$item_id'";
                $result = $Model->execute($up);
                if(is_bool($result)){
                    $this->response(retmsg(-1,null,"售卖申请审核失败"),'json');
                }else{
                    $this->response(retmsg(0,null,"售卖申请审核成功"),'json');
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
//                $data = '{"data":{"id":17,"itemid":"10010","itemname":"","itemdesc":"","itemamount":"","itemprice":""}}';
                $json = json_decode(file_get_contents("php://input"),true);
//                $json = json_decode($data,true);
                $id = $json["data"]["id"];
                $itemid = $json["data"]["itemid"];
                $itemname = $json["data"]["itemname"];
                $itemdesc = $json["data"]["itemdesc"];
                $amount = $json["data"]["itemamount"];
                $price = $json["data"]["itemprice"];
                $up = "update items set item_name='$itemname',item_desc='$itemdesc',item_amount='$amount',item_price='$price' where id='$id'";
                $result = $Model->execute($up);
                if(is_bool($result)){
                    $this->response(retmsg(-1,null,"商品信息修改失败"),'json');
                }else{
                    $this->response(retmsg(0,null,"商品信息修改成功"),'json');
                }
                break;
            }
        }
    }

    //删除土地信息
    public function del_goods($token="",$id){
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'post':{
                $del = "delete from items where id='$id'";
                $result = $Model->execute($del);
                if(is_bool($result)){
                    $this->response(retmsg(-1,null,"商品信息删除失败"),'json');
                }else{
                    $this->response(retmsg(0,null,"商品信息删除成功"),'json');
                }
                break;
            }
        }
    }
}