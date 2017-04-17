<?php
namespace Home\Controller;
use Think\Controller\RestController;

class FarmController extends RestController{
    public function get_farm(){
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'get':{
                $sql = "select * from farm";
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
                $sql = "select * from soil where rent_to_who='$token'";
                $result = $Model->query($sql);
                $sql1 = "select nickname,soil_nums from userinfor where token='$token'";//属于哪个用户的土地
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
                        $sql2 = "select farm_name from farm where farm_id='$belong'";//土地属于哪个农场
                        $result2 = $Model->query($sql2);
                        $data["subitem"][$i]["farm_name"] = $result2[0]["farm_name"];
                    }
                    $this->response(retmsg(0,$data,"查询成功"),'json');
                }
                break;
            }
        }
    }
}