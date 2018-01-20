<?php
namespace App\Controller;
use Think\Controller\RestController;

class OrderController extends RestController{
    public function my_order1($token){//我的订单查询
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'get':{
                $sql = "select user_phone from garden_userinfor where token='$token'";
                $result = $Model->query($sql);
                $user = $result[0]["user_phone"];
                $sql2 = "select * from `garden_order` where buyer_phone='$user'";
                // buyer_phone='$user' and
                $result2 = $Model->query($sql2);
                if(is_bool($result2)){
                    $this->response(retmsg(-1,null,"获取订单失败"),'json');
                }else{
                    $data = array();
                    for($i = 0;$i < count($result2);$i++){
                        $item_id = $result2[$i]["item_id"];
//                        echo $result2[$i]["item_id"]."   ";
                        $sql3 = "select * from garden_items where id='$item_id'";
                        $result3 = $Model->query($sql3);
                        $test = array("id"=>$result2[$i]["id"],"order_id"=>$result2[$i]["order_id"],"buyer_phone"=>$result2[$i]["buyer_phone"],
                            "buy_amount"=>$result2[$i]["buy_amount"],"order_status_desc"=>$result2[$i]["order_status_desc"],
                            "order_money"=>$result2[$i]["order_money"],"item_img"=>$result3[0]["item_img"],
                            "item_name"=>$result3[0]["item_name"],"item_from"=>$result3[0]["item_from"],
                            "item_id"=>$result3[0]["id"]);
                        $data["subitem"][] = $test;
                    }
                    $this->response(retmsg(0,$data,"查询订单成功"),'json');
                }
                break;
            }
        }
    }

    public function fahuo($token){//我的待发货
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'get':{
                $data = array();
                $sql = "select user_phone,nickname,user_addr from garden_userinfor where token='$token'";
                $result = $Model->query($sql);
                $user = $result[0]["user_phone"];
                $username = $result[0]["nickname"];
                $useraddr = $result[0]["user_addr"];
                $sql1 = "SELECT DISTINCT(order_id) FROM `garden_order` WHERE buyer_phone='$user'";//先查询订单号
                $result1 = $Model->query($sql1);
                $data["count"] = count($result1);//总条数
                foreach($result1 as $k=>$v){
                    $orderid = $v["order_id"];
                    $sql2 = "SELECT * from `garden_order` as a,`garden_items` as b where a.item_id=b.id and a.order_id='$orderid' ";
                    $result2 = $Model->query($sql2);
                    $sql3 = "SELECT sum(order_money) as total_money,sum(buy_amount) as amount from `garden_order` as a where a.order_id='$orderid' ";
                    $result3 = $Model->query($sql3);
                    $data["orderinfor"][] =array("order_id"=>$v["order_id"],"buyer_phone"=>$user,"buyer_name"=>$username,"addr"=>$useraddr);
                    $data["orderinfor"][$k]["iteminfor"]["total_money"] = $result3[0]["total_money"];
                    $data["orderinfor"][$k]["iteminfor"]["amount"] = $result3[0]["amount"];
                    $data["orderinfor"][$k]["iteminfor"]["goods"] = $result2;
                }
                $this->response(retmsg(0,$data,"查询成功"),'json');
            }
        }
    }

    public function orderlist($token="",$orderid="",$uid="",$page="1",$pagesize="6"){
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'get':{
                $where = "";
                if($orderid != ''){//根据订单号查询订单
                    $where .="order_id='$orderid'";
                }
                if($uid!=''){//查询某一个用户的订单
                    $where .="buyer_phone='$uid'";
                }
                $data = array();
                $l = ($page-1)*$pagesize;
                if($where == ""){
                    //查询所有订单
                    $sql0 = "SELECT DISTINCT(order_id) FROM `garden_order` ";//先查询订单号 WHERE buyer_phone='$user'
                }else{
                    $sql0 = "SELECT DISTINCT(order_id) FROM `garden_order` where ".$where;
                }
                $result0 = $Model->query($sql0);
                if($where == ""){
                    //查询所有订单
                    $sql = "SELECT DISTINCT(order_id),buyer_phone,order_time FROM `garden_order` order by order_id desc limit ".$l.",".$pagesize;//先查询订单号
//                    echo $sql."  ";
                }else{
                    $sql = "SELECT DISTINCT(order_id),buyer_phone,order_time FROM `garden_order` where ".$where." order by order_id desc limit ".$l.",".$pagesize;
                }
                $result = $Model->query($sql);
                $n = count($result0);
                $m = ceil($n/$pagesize);
                $data["page"] = $page;//页数
                $data["pagecount"] = $m;//总页数
                foreach($result as $k=>$v){
                    $or_id = $v["order_id"];
                    $buyer = $v["buyer_phone"];
                    $ordertime = $v["order_time"];
                    $sql2 = "SELECT * from `garden_order` as a,`garden_items` as b where a.item_id=b.id and a.order_id='$or_id' ";
                    $result2 = $Model->query($sql2);
                    $sql3 = "SELECT sum(order_money) as total_money,sum(buy_amount) as amount from `garden_order` as a where a.order_id='$or_id' ";
                    $result3 = $Model->query($sql3);
                    $sql4 = "select * from garden_userinfor where user_phone='$buyer'";
                    $result4 = $Model->query($sql4);
                    $data["orderinfor"][] =array("order_id"=>$or_id,"order_time"=>$ordertime,"buyer_phone"=>$result4[0]["user_phone"],"buyer_name"=>$result4[0]["nickname"],"addr"=>$result4[0]["user_addr"]);
                    $data["orderinfor"][$k]["iteminfor"]["total_money"] = $result3[0]["total_money"];
                    $data["orderinfor"][$k]["iteminfor"]["amount"] = $result3[0]["amount"];
                    $data["orderinfor"][$k]["iteminfor"]["goods"] = $result2;
                }
                $this->response(retmsg(0,$data,"查询成功"),'json');
                break;
            }
        }
    }

    public function show_quan($token){
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'get':{
                $sql = "select user_phone from garden_userinfor where token='$token'";
                $result = $Model->query($sql);
                $user_phone = $result[0]["user_phone"];
//                $sql1 = "select distinct(item_id) from `garden_order` where buyer_phone='$user_phone' and item_type='service'";
                $result1 = M('order a','garden_')->join("left join garden_items b on a.`item_id` =b.`id`")
                    ->field("distinct(a.item_id)")->where("a.`buyer_phone` ='$user_phone' and b.`item_type` ='service'")->select();
//                $result1 = $Model->query($sql1);
                $data = array();
                foreach($result1 as $k=>$v){
                    $item_id = $v["item_id"];
                    $sql2 = "select sum(buy_amount) as buy_amount,sum(order_money) as order_money from `garden_order` where buyer_phone='$user_phone' and item_id='$item_id'";
                    $result2 = $Model->query($sql2);
                    $result2 = $result2[0];
                    $buy_amount = $result2["buy_amount"];
                    $order_money = $result2["order_money"];
                    $sql3 = "select item_name,item_desc,item_from,item_img,item_type,type_desc from garden_items where id='$item_id'";
                    $result3 = $Model->query($sql3);
                    $result3 = $result3[0];
                    $item_name = $result3["item_name"];
                    $item_desc = $result3["item_desc"];
                    $item_from = $result3["item_from"];
                    $item_img = $result3["item_img"];
                    $item_type = $result3["item_type"];
                    $type_desc = $result3["type_desc"];
                    $test = array("id"=>$item_id,"buy_amount"=>$buy_amount,"item_name"=>$item_name,"item_desc"=>$item_desc,
                        "item_from"=>$item_from,"item_img"=>$item_img,"item_type"=>$item_type,"type_desc"=>$item_desc);
                    $data["subitem"][] = $test;
                }
                if($result1 == null){
                    $data["subitem"] = array();
                    $this->response(retmsg(0,$data,"暂无数据"),'json');
                }else{
                    $this->response(retmsg(0,$data,"查询成功"),'json');
                }
                break;
            }
        }
    }
}