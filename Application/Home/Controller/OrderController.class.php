<?php
namespace Home\Controller;
use Think\Controller\RestController;

class OrderController extends RestController{
    public function my_order1($token){//我的订单查询
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'get':{
                $sql = "select user_phone from userinfor where token='$token'";
                $result = $Model->query($sql);
                $user = $result[0]["user_phone"];
                $sql2 = "select * from `order` where buyer_phone='$user'";
                // buyer_phone='$user' and
                $result2 = $Model->query($sql2);
                if(is_bool($result2)){
                    $this->response(retmsg(-1,null,"获取订单失败"),'json');
                }else{
                    $data = array();
                    for($i = 0;$i < count($result2);$i++){
                        $item_id = $result2[$i]["item_id"];
//                        echo $result2[$i]["item_id"]."   ";
                        $sql3 = "select * from items where id='$item_id'";
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
                $sql = "select user_phone,nickname,user_addr from userinfor where token='$token'";
                $result = $Model->query($sql);
                $user = $result[0]["user_phone"];
                $username = $result[0]["nickname"];
                $useraddr = $result[0]["user_addr"];
                $sql1 = "SELECT DISTINCT(order_id) FROM `order` WHERE buyer_phone='$user'";//先查询订单号
                $result1 = $Model->query($sql1);
                $data["count"] = count($result1);//总条数
                foreach($result1 as $k=>$v){
                    $orderid = $v["order_id"];
                    $sql2 = "SELECT * from `order` as a,`items` as b where a.item_id=b.id and a.order_id='$orderid' ";
                    $result2 = $Model->query($sql2);
                    $sql3 = "SELECT sum(order_money) as total_money,sum(buy_amount) as amount from `order` as a where a.order_id='$orderid' ";
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
                    $sql0 = "SELECT DISTINCT(order_id) FROM `order` ";//先查询订单号 WHERE buyer_phone='$user'
                }else{
                    $sql0 = "SELECT DISTINCT(order_id) FROM `order` where ".$where;
                }
                $result0 = $Model->query($sql0);
                if($where == ""){
                    //查询所有订单
                    $sql = "SELECT DISTINCT(order_id),buyer_phone,order_time FROM `order` order by order_id desc limit ".$l.",".$pagesize;//先查询订单号
//                    echo $sql."  ";
                }else{
                    $sql = "SELECT DISTINCT(order_id),buyer_phone,order_time FROM `order` where ".$where." order by order_id desc limit ".$l.",".$pagesize;
                }
                $result = $Model->query($sql);
                $n = count($result0);
                $m = ceil($n/$pagesize);
                $data["page"] = $page;//页数
                $data["pagecount"] = $m;//总页数
//                echo $n;
//                echo $m;
                foreach($result as $k=>$v){
                    $or_id = $v["order_id"];
                    $buyer = $v["buyer_phone"];
                    $ordertime = $v["order_time"];
                    $sql2 = "SELECT * from `order` as a,`items` as b where a.item_id=b.id and a.order_id='$or_id' ";
//                    echo $sql2."<br>  ";
                    $result2 = $Model->query($sql2);
                    $sql3 = "SELECT sum(order_money) as total_money,sum(buy_amount) as amount from `order` as a where a.order_id='$or_id' ";
                    $result3 = $Model->query($sql3);
                    $sql4 = "select * from userinfor where user_phone='$buyer'";
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

    public function orderlist1($token="",$uphone="",$orderid="",$page=1,$pagesize=10){
        header("Access-Control-Allow-Origin:*");
        //参数都为空，默认显示所有订单
        $Model = M();
        switch ($this->_method)
        {
            case 'get':{
                    $str= '';
                    if($uphone!="")
                    {
                        $str .= " buyer_phone = '".$uphone."' and";
                    }
                    if($orderid!="")
                    {
                        $str .= " order_id = '".$orderid."' and";
                    }
                    $sql="select count(distinct(a.order_id)) as order_num from order  a right JOIN items b ON a.item_id = b.item_id  where b.order_status='yifukuan'".$str;
                    echo $sql;
//                    $list=M()->query($sql);
//                    $count=$list[0]['itemid'];
//                    $page =ceil($count/$page);
//                    $sql="select distinct a.order_id,a.order_status_desc,a.buyer_name,a.buyer_phone from order a right JOIN items b ON a.item_id = b.item_id where b.order_status='yifukuan'".$str.' order by id desc'." limit 1,10";
//                    //$sql="select ordercode,ordertype,orderdate,uid,uname,udetail,sid,sname,sdetail,shopaddr,price,pay_type,orderstat,delflag,glyremark from order_orderlist where ordercode a in (select ordercode from order_product where ptype='$ptype')";
//                    //echo $sql;
//                    echo $sql;//return;
//                    $list=M()->query($sql);
//                    for($i=0;$i<count($list);$i++)
//                    {
//                        //$code=$list[$i]['orderstat'];
//                        $os=$list[$i]['orderstat'];//订单状态
//                        $ot=$list[$i]['ordertype'];//订单类型
//                        $oid=$list[$i]['ordercode'];//订单编号
//                        $sql="select pid,pname,ptype,pimg,staderd,amount,price,end_price,sremark,producturl from order_product where ordercode='".$oid."' and ptype ='".$ptype."'";
//                        $vlist=M()->query($sql);
//                        for($j=0;$j<count($vlist);$j++)
//                        {
//                            $vlist[$j]['staderd']=unserialize($vlist[$j]['staderd']);
//                        }
//                        $sql ="select statname from order_stat where ordertypeid='$ot' and orderstatid='$os'";
//                        $zlist=M()->query($sql);
//                        //dump($zlist);
//                        $stat =$zlist[0]["statname"];
//                        //dump($stat);
//                        $list[$i]['orderstat']=$stat;
//                        $sql="select operaname as name,operalabel as label,CONCAT('$yum',CONCAT(CONCAT(operalabel,'$url'),'$oid'),'/ordertype/$ordertype/ptype/$ptype') as url from order_operation where operaid in (select operaid from order_oper_htstat where ordertypeid='$ot' and orderstatid='$os')";
//                        $slist=M()->query($sql);
//
//                        $list[$i]['operalist']=$slist;
//                        $list[$i]['productinfo']=$vlist;
//                    }
//                    //echo json_encode($list);return;
//
//                    $retarr = array("resultcode"=>"0","resultmsg"=>"订单查询成功！","data"=>array("count"=>$count,"pagecount"=>$page,"page"=>$p,"orderinfo"=>$list));
//                    //dump($retarr);
//                    $elist= json_encode($retarr);
//                    echo $elist;
//
//                    return;
//                }
            break;
            }
        }
    }
}