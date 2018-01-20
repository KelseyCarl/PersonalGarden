<?php
namespace Admin\Controller;
use Think\Controller\RestController;

class UserController extends RestController{
    public function query_user($token="",$uid="",$uname="",$page="1",$pagesize="10"){
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'get':{
                $where = "";
                if($uid != ''){
                    $where .="user_phone='".$uid."'";
                }
                if($uname!=''){
                    $where .="nickname like '%".$uname."%'";
                }
                $sql = "select * from garden_userinfor";
                $result = $Model->query($sql);
                $n = count($result);
                $m = ceil($n/$pagesize);
                $l = ($page-1)*$pagesize;
                if($where == "") {
                    $sql1 = "select * from garden_userinfor order by login_time desc limit ".$l.",".$pagesize;
                }else{
                    $sql1 = "select * from garden_userinfor  where  ".$where."  order by login_time desc  limit ".$l.",".$pagesize;
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

    public function reset_pass($token="",$id){
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'post':{
                //重置密码为：123456
                $up = "update garden_userinfor set login_pass='123456' where user_id='$id'";
                $result = $Model->execute($up);
                if(is_bool($result)){
                    $this->response(retmsg(-1,null,"重置用户密码失败"),'json');
                }else{
                    $this->response(retmsg(0,null,"重置用户密码成功"),'json');
                }
                break;
            }
        }
    }

    public function one_user($token="",$id){
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'get':{
                $sql = "select * from garden_userinfor where user_id='$id'";
                $result = $Model->query($sql);
                if(is_bool($result)){
                    $this->response(retmsg(-1,null,"查询用户信息失败"),'json');
                }else{
                    $data = array();
                    $data["subitem"] = $result;
                    $this->response(retmsg(0,$data,"查询用户信息成功"),'json');
                }
                break;
            }
        }
    }
}