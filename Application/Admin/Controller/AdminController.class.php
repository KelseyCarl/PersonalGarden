<?php
namespace Admin\Controller;
use Think\Controller\RestController;

class AdminController extends RestController{
    public function login($token=""){
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'get':{
                $sql = "select * from garden_admin where token='$token'";
                $result = $Model->query($sql);
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
                $query = "select * from garden_admin where admin_account='".$user."'";
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
                        $time = date("Y-m-d H:i:s");
                        $up = "update garden_admin set login_time='$time' where admin_account='$user'";
                        $Model->execute($up);
                        if($pswd != $pass){
                            $this->response(retmsg(-1,null,"用户名或密码错误！"),'json');
                        }else{
                            $data["subitem"] = $re;
                            $this->setLocalCookie("username",$user);
                            $this->setLocalCookie("password",md5($pass));
                            $this->setLocalCookie("token",md5(md5($user)));
                            $data['cookies'] = $_COOKIE;
                            $this->response(retmsg(0,$data,"登录成功！"),'json');
                        }
                    }
                }
                break;
            }
        }
    }

    public function admin($token="",$uid="",$uname="",$page="1",$pagesize="10"){
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'get':{
                $where = "";
                if($uid != ''){
                    $where .="admin_account='".$uid."'";
                }
                if($uname!=''){
                    $where .="name like '%".$uname."%'";
                }
                $sql = "select * from garden_admin";
                $result = $Model->query($sql);
                $n = count($result);
                $m = ceil($n/$pagesize);
                $l = ($page-1)*$pagesize;
                if($where == "") {
                    $sql1 = "select * from garden_admin order by login_time desc limit ".$l.",".$pagesize;
                }else{
                    $sql1 = "select * from garden_admin  where  ".$where."  order by login_time desc  limit ".$l.",".$pagesize;
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

    public function add_admin($token=""){
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'post':{
                //取得输入的值
//                $data = '{"data":{"phone":"2017001","uname":"sgh","pwd":"123456"}}';
                $json = json_decode(file_get_contents("php://input"),true);
//                $json = json_decode($data,true);
                $account = $json["data"]["phone"];
                $pass = $json["data"]["pwd"];
                $name = $json["data"]["uname"];
                $token1 = md5($account);
                $privilege = "超级管理员";
                $sql = "select * from garden_admin where admin_account='$account'";
                $re = $Model->query($sql);
                if($re){
                    $this->response(retmsg(-1,null,"管理员已存在，不可重复添加"),'json');
                }else{
                    $insert = "insert into garden_admin(admin_account,admin_pass,token,name,privilege) values
("."'".safe($account)."','".safe($pass)."','".safe($token1)."','".safe($name)."','".safe($privilege)."'".")";
                    $result = $Model->execute($insert);
                    if(is_bool($result)){
                        $this->response(retmsg(-1,null,"新增管理员失败"),'json');
                    }else{
                        $this->response(retmsg(0,null,"新增管理员成功"),'json');
                    }
                }
                break;
            }
        }
    }

    public function setLocalCookie($name,$value,$expireDay = 1){
        $seconds = $expireDay*24*60*60;//设置过期秒数，不设置则浏览器关闭cookie失效
        cookie($name,$value,time()+$seconds);//thinkPHP封装
//        setcookie($name,$value,time()+$seconds);//调用原生PHP函数库
    }

    public function del_admin($token="",$id){
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'post':{
                $del = "delete from garden_admin where id='$id'";
                $result = $Model->execute($del);
                if(is_bool($result)){
                    $this->response(retmsg(-1,null,"删除失败"),'json');
                }else{
                    $this->response(retmsg(0,null,"删除管理员成功"),'json');
                }
                break;
            }
        }
    }

    public function reset_pass($token="",$id){
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'post':{
                //重置密码为：123456
                $up = "update garden_admin set admin_pass='123456' where id='$id'";
                $result = $Model->execute($up);
                if(is_bool($result)){
                    $this->response(retmsg(-1,null,"重置密码失败"),'json');
                }else{
                    $this->response(retmsg(0,null,"重置密码成功"),'json');
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
//                $data = '{"data":{"userid":"2017001","mobile":"2017001","uname":"sgh"}}';
                $json = json_decode(file_get_contents("php://input"),true);
//                $json = json_decode($data,true);
                $userid = $json["data"]["userid"];
                $mobile = $json["data"]["mobile"];
                $uname = $json["data"]["uname"];
                $up = "update garden_admin set admin_account='$mobile',name='$uname' where admin_account='$userid'";
                $result = $Model->execute($up);
                if(is_bool($result)){
                    $this->response(retmsg(-1,null,"管理员信息修改失败"),'json');
                }else{
                    $this->response(retmsg(0,null,"管理员信息修改成功"),'json');
                }
                break;
            }
        }
    }
}