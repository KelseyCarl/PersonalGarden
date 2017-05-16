<?php
namespace Home\Controller;
use Think\Controller\RestController;

class ItemController extends RestController{
    public function add_to_cart($token,$id){//添加到购物车
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'get':{
                $sql = "select * from cart where token='$token' and item_id='$id'";
                $reslut = $Model->query($sql);
                if($reslut == null){//没有添加过，则进行添加
                    $insert = "insert into cart(token,item_id) values("."'".safe($token)."',".safe($id).")";
                    $re = $Model->execute($insert);
                    if(is_bool($re)){
                        $this->response(retmsg(-1,null,"添加购物车失败！"),'json');
                    }else{
                        $this->response(retmsg(0,null,"添加购物车成功！"),'json');
                    }
                }else{
                    $data = array();
                    $data["subitem"] = $reslut;
                    $this->response(retmsg(-2,$data,"已经添加过了！"),'json');
                }
                break;
            }
        }
    }

    public function cart_num($token){//添加到购物车
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'get':{
                $sql1  = "select count(token) as total from cart where token='$token'";
                $sqlre = $Model->query($sql1);
                if(is_bool($sqlre)){
                    $this->response(retmsg(0,null,"数量查询失败"),'json');
                }else{
                    $data = array();
                    $data["subitem"] = $sqlre;
                    $this->response(retmsg(0,$data,"添加的数量"),'json');
                }
                break;
            }
        }
    }

    public function show_cart($token){//我的购物车
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'get':{
                $sql = "select * from cart where token='$token' order by item_id desc";
                $result = $Model->query($sql);
                $data = null;
                for($i = 0;$i < count($result);$i++){
                    $item_id = $result[$i]["item_id"];
                    $sql2 = "select * from items where is_verify=1 and id='$item_id'";
                    $re2 = $Model->query($sql2);
//                    $data["subitem"][] = $re2;
                    $test = array("cart_id"=>$result[$i]["id"],"id"=>$re2[0]["id"],"item_id"=>$re2[0]["item_id"],"item_name"=>$re2[0]["item_name"],
                        "item_desc"=>$re2[0]["item_desc"],"item_from"=>$re2[0]["item_from"],"item_amount"=>$re2[0]["item_amount"],
                        "item_price"=>$re2[0]["item_price"],"item_sale_out"=>$re2[0]["item_sale_out"],"item_img"=>$re2[0]["item_img"],
                        "is_verify"=>$re2[0]["is_verify"],"apply_time"=>$re2[0]["apply_time"],"verify_time"=>$re2[0]["verify_time"],
                        "item_type"=>$re2[0]["item_type"]);
                    $data["subitem"][] = $test;
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

    public function del_cart_item($token="",$id){
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'post':{
                $del = "delete from cart where id='$id'";
                $re = $Model->execute($del);
                if(is_bool($re)){
                    $this->response(retmsg(-1,null,"从购物车删除商品失败"),'json');
                }else{
                    $this->response(retmsg(0,null,"删除商品成功"),'json');
                }
                break;
            }
        }
    }

    public function del_collect_item($token="",$id){
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'post':{
                $del = "delete from collect where id='$id'";
                $re = $Model->execute($del);
                if(is_bool($re)){
                    $this->response(retmsg(-1,null,"从收藏夹删除商品失败"),'json');
                }else{
                    $this->response(retmsg(0,null,"删除收藏商品成功"),'json');
                }
                break;
            }
        }
    }



    public function collect($token,$id){//添加到我的收藏
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'get':{
                $sql = "select * from collect where token='$token' and item_id='$id'";
                $reslut = $Model->query($sql);
                if($reslut == null){//没有收藏过，则进行收藏
                    $insert = "insert into collect(token,item_id) values("."'".safe($token)."',".safe($id).")";
                    $re = $Model->execute($insert);
                    if(is_bool($re)){
                        $this->response(retmsg(-1,null,"添加收藏失败！"),'json');
                    }else{
                        $this->response(retmsg(0,null,"添加收藏成功！"),'json');
                    }
                }else{
                    $this->response(retmsg(-2,null,"已经收藏过了！"),'json');
                }
                break;
            }
        }
    }

    public function is_collect($token,$id){//是否收藏过了
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'get':{
                $sql = "select * from collect where token='$token' and item_id='$id'";
                $reslut = $Model->query($sql);
                $data = array();
                if($reslut == null){
                    $data["status"] = 0;
                    $this->response(retmsg(-1,$data,"商品未收藏"),'json');
                }else{
                    $data["status"] = 1;
                    $this->response(retmsg(0,$data,"商品已收藏"),'json');
                }
                break;
            }
        }
    }

    public function show_collect($token){//我的收藏
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'get':{
                $sql = "select * from collect where token='$token' order by item_id desc";
                $result = $Model->query($sql);
                $data = null;
                for($i = 0;$i < count($result);$i++){
                    $item_id = $result[$i]["item_id"];
                    $sql2 = "select * from items where is_verify=1 and id='$item_id'";
                    $re2 = $Model->query($sql2);
//                    $data["subitem"][] = $re2;
                    $test = array("collect_id"=>$result[$i]["id"],"id"=>$re2[0]["id"],"item_id"=>$re2[0]["item_id"],"item_name"=>$re2[0]["item_name"],
                        "item_desc"=>$re2[0]["item_desc"],"item_from"=>$re2[0]["item_from"],"item_amount"=>$re2[0]["item_amount"],
                        "item_price"=>$re2[0]["item_price"],"item_sale_out"=>$re2[0]["item_sale_out"],"item_img"=>$re2[0]["item_img"],
                        "is_verify"=>$re2[0]["is_verify"],"apply_time"=>$re2[0]["apply_time"],"verify_time"=>$re2[0]["verify_time"],
                        "item_type"=>$re2[0]["item_type"]);
                    $data["subitem"][] = $test;
                }
                if($data == null){
                    $data["subitem"] = array();
                    $this->response(retmsg(0,$data,"查询成功"),'json');
                }
                else{
                    $this->response(retmsg(0,$data,"查询成功"),'json');
                }
                break;
            }
        }
    }
}
