<?php
namespace Home\Controller;
use Think\Controller\RestController;

//支付接口
class PayController extends RestController{
    public function pay($token){//单个商品支付
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'post':{
//                $data = '{"data":{"pay_money":"5","item_id":7,"token":"8cbfacea76e34f6bf956afb40fc8e384"}}';
                $json = json_decode(file_get_contents("php://input"),true);
//                $json = json_decode($data,true);
                $money = $json["data"]["pay_money"];
                $item_id = $json["data"]["item_id"];
                $token = $json["data"]["token"];
                //查询购买者的信息
                $buyer = "select user_phone,nickname from userinfor where token='$token'";
                $buy = $Model->query($buyer);
                //支付过后减去钱包中的余额现金
                $sql = "select balance from wallet where token='$token'";
                $reslut = $Model->query($sql);
                if($money > $reslut[0]["balance"]){
                    $this->response(retmsg(-1,null,"您的余额已不足"),'json');
                }else{
                    //余额足够就进行支付，更新钱包里面的收支数据：balance
                    $update = "update wallet set balance=balance-'$money' where token='$token'";
                    $re = $Model->execute($update);
                    //更新商品表中对应商品的库存量和售卖总数量
                    $update2 = "update items set item_amount=item_amount-1,item_sale_out=item_sale_out+1 where id='$item_id'";
                    $Model->execute($update2);
                    //购买将产生订单数据，将数据插入订单表，并将状态置为“已付款”
                    $test=rand(1,100);
                    $order = date("YmdHis");
                    $order_id = $order.$test;
                    $buyer_phone = $buy[0]["user_phone"];
                    $buyer_name = $buy[0]["nickname"];
                    $buy_account = 1;
                    $order_status = "yifukuan";
                    $order_status_desc = "已付款";
                    $order_money = $money;
                    $order_time = date("Y-m-d H:i:s");
                    $insert1 = "insert into `order`(order_id,buyer_phone,buyer_name,item_id,buy_amount,order_status,order_status_desc,order_money,order_time) values ("."'".safe($order_id)."','"
                        .safe($buyer_phone)."','".safe($buyer_name)."','".safe($item_id)."','".safe($buy_account)."','"
                        .safe($order_status)."','".safe($order_status_desc)."','".safe($order_money)."','".safe($order_time)."'".")";
                    $Model->execute($insert1);
                    if(is_bool($re)){
                        $this->response(retmsg(-1,null,"支付失败"),'json');
                    }else{
                        $this->response(retmsg(0,null,"支付成功"),'json');
                    }
                }
                break;
            }
        }
    }

    public function pay_more($token){//购物车实现多个商品的购买
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'post':{
//                $data = '{"data":{"token":"444","item_id":7,"token":"8cbfacea76e34f6bf956afb40fc8e384"}}';
                $json = json_decode(file_get_contents("php://input"),true);
//                $json = json_decode($data,true);
                $part_pay = $json["data"]["part_pay"];//分别需要支付的费用
                $item_id = $json["data"]["item_id"];
                $item_amount = $json["data"]["item_amount"];
                //更新商品表中对应商品的库存量和售卖总数量
                $update2 = "update items set item_amount=item_amount-'$item_amount',item_sale_out=item_sale_out+'$item_amount' where id='$item_id'";
                $Model->execute($update2);
                //查询购买者的信息
                $buyer = "select user_phone,nickname from userinfor where token='$token'";
                $buy = $Model->query($buyer);
                //购买将产生订单数据，将数据插入订单表，并将状态置为“已付款”
//                $test = rand(1, 100);
                $buyer_phone = $buy[0]["user_phone"];
                $str = substr($buyer_phone,-4);
                $order = date("YmdHi");
                $order_id = $order.$str;
                $order_time = date("Y-m-d H:i:s");

                $buyer_name = $buy[0]["nickname"];
                $order_status = "yifukuan";
                $order_status_desc = "已付款";
                $order_money = $part_pay;
                $insert1 = "insert into `order`(order_id,buyer_phone,buyer_name,item_id,buy_amount,order_status,order_status_desc,order_money,order_time) values (" . "'" . safe($order_id) . "','"
                    . safe($buyer_phone) . "','" . safe($buyer_name) . "','" . safe($item_id) . "','" . safe($item_amount) . "','"
                    . safe($order_status) . "','" . safe($order_status_desc) . "','" . safe($order_money) . "','".safe($order_time)."'" . ")";
                $in = $Model->execute($insert1);
                if ($in == 0) {
                    $this->response(retmsg(-1, null, "订单生成失败"), 'json');
                } else {
                    $this->response(retmsg(0, null, "订单生成"), 'json');
                }
                break;
            }
        }
    }

    public function pay_total($token){//总结算
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'post':{
                $json = json_decode(file_get_contents("php://input"),true);
//                $json = json_decode($data,true);
                $total_pay = $json["data2"]["total"];
                //查询账户余额
                $sql = "select balance from wallet where token='$token'";
                $reslut = $Model->query($sql);
                if($total_pay > $reslut[0]["balance"]){
                    $this->response(retmsg(-1,null,"您的余额已不足"),'json');
                }else{
                    //余额足够就进行支付，更新钱包里面的收支数据：balance
                    $update = "update wallet set balance=balance-'$total_pay' where token='$token'";
                    $re = $Model->execute($update);
                    if (is_bool($re)) {
                        $this->response(retmsg(-1, null, "支付失败"), 'json');
                    } else {
                        $this->response(retmsg(0, null, "支付成功"), 'json');
                    }
                }
                break;
            }
        }
    }
}
