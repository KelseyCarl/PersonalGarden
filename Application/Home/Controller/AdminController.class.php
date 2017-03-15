<?php
namespace Home\Controller;
use Think\Controller\RestController;

class AdminController extends RestController{
    public function login($token=""){
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'get':{
                $sql = "select * from admin where token='$token'";
                $result = $Model->query($sql);
//                var_dump($result);
                if(is_bool($result)){
                    $this->response(retmsg(-1,"查询失败！"),'json');
                }else{
                    $data = array();
                    $data["subitem"] = array();
                    //取得数据表所有字段的值
                    $data["subitem"] = $result;
                    $this->response(retmsg(0,$data,"查询成功！"),'json');
                }
                break;
            }

            case 'post':{
                //取得输入的值
//                $data = '{"data":{"username":"2017001","password":"123456"}}';
                $json = json_decode(file_get_contents("php://input"),true);
//                $json = json_decode($data,true);
                $user = $json["data"]["username"];
                $pswd = $json["data"]["password"];
                $query = "select * from admin where admin_account='".$user."'";
                $re = $Model->query($query);
                if(is_bool($re)){
                    $this->response(retmsg(-1,null,"查询失败"),'json');
                }else{
                    if($re == null){
                        $this->response(retmsg(-1,null,"该管理员不存在，无法登录系统！"),'json');
                    }else{
                        $data = array();
                        $data["subitem"] = array();
                        $pass = $re[0]["admin_pass"];
                        if($pswd != $pass){
                            $this->response(retmsg(-1,null,"用户名或密码错误！"),'json');
                        }else{
                            $data["subitem"] = $re;
                            $this->response(retmsg(0,$data,"登录成功！"),'json');
                        }
                    }
                }
                break;
            }
        }
    }
}