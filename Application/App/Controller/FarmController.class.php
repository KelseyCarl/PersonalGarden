<?php
namespace App\Controller;
use Think\Controller\RestController;

class FarmController extends RestController{
    public function get_farm(){
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'get':{
                $sql = "select * from garden_farm";
                $result = $Model->query($sql);
//                var_dump($result);
                if(is_bool($result)){
                    $this->response(retmsg(-1,"查询失败！"),'json');
                }else{
                    $data = array();
                    $data["subitem"] = array();
                    //取得数据表所有字段的值
                    $data["subitem"] = $result;
                    $this->response(retmsg(0,$data,"查询成功"),'json');
                }
                break;
            }
        }
    }

    public function my_soil($token){
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'get':{
                $sql = "select * from garden_soil where rent_to_who='$token'";
                $result = $Model->query($sql);
                $sql1 = "select nickname,soil_nums from garden_userinfor where token='$token'";//属于哪个用户的土地
                $result1 = $Model->query($sql1);

//                var_dump($result);
                if(is_bool($result)){
                    $this->response(retmsg(-1,"查询失败！"),'json');
                }else{
                    $data = array();
                    $data["subitem"] = array();
                    //取得数据表所有字段的值
                    $data["subitem"] = $result;
                    $data["nickname"] = $result1[0]["nickname"];
                    $data["soil_nums"] = $result1[0]["soil_nums"];
                    for($i = 0;$i < count($result);$i++){
                        $belong = $result[$i]["farm_belong"];
                        $sql2 = "select farm_name from garden_farm where farm_id='$belong'";//土地属于哪个农场
                        $result2 = $Model->query($sql2);
                        $data["subitem"][$i]["farm_name"] = $result2[0]["farm_name"];
                    }
                    $this->response(retmsg(0,$data,"查询成功"),'json');
                }
                break;
            }
        }
    }

    public function shucai($type){//分类查询商品
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case "get":{
                $sql = "select * from garden_items where item_type='$type' and is_verify=1";
                $re = $Model->query($sql);
                $data = array();
                $data["subitem"] = $re;
                foreach($re as $k=>$v){
                    $from = $v["item_from"];
                    $sql2 = "select token from garden_userinfor where nickname='$from'";
                    $re2 = $Model->query($sql2);
                    if($re2[0]["token"] == null){
                        $data["subitem"][$k]["from"] = "";
                    }else{
                        $data["subitem"][$k]["from"] = $re2[0]["token"];
                    }
                }
                if(is_bool($re)){
                    $this->response(retmsg(0,null,"查询失败"),'json');
                }else{
                    $this->response(retmsg(0,$data,"查询成功"),'json');
                }
                break;
            }
        }
    }

    public function item_detail($id){//根据id查询商品
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case "get":{
                $sql = "select * from garden_items where id=$id";
                $re = $Model->query($sql);
                if(is_bool($re)){
                    $this->response(retmsg(0,null,"查询失败"),'json');
                }else{
                    $data = array();
                    $data["subitem"] = $re;
                    $this->response(retmsg(0,$data,"查询成功"),'json');
                }
                break;
            }
        }
    }

    //土地查询
    public function soils($farm_belong=""){
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case "get":{
                if($farm_belong == null){
                    $sql = "select * from garden_soil where soil_status=0 and rent_to_who='' order by farm_belong asc";//查询所有没有租出去的土地的信息
                }else{
                    $sql = "select * from garden_soil where soil_status=0 and rent_to_who='' and  farm_belong='$farm_belong'";//分农场查询
                }
                $re = $Model->query($sql);
                if(is_bool($re)){
                    $this->response(retmsg(0,null,"查询失败"),'json');
                }else{
                    $data = array();
                    $data["subitem"] = $re;
                    $this->response(retmsg(0,$data,"查询成功"),'json');
                }
                break;
            }
        }
    }
}