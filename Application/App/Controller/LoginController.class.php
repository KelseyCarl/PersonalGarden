<?php
namespace App\Controller;
use Think\Controller\RestController;

class LoginController extends RestController{
    public function login($token=""){
//        session_start();
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'get':{
                $sql = "select * from garden_userinfor where token='$token'";
                $result = $Model->query($sql);
                if(is_bool($result)){
                    $this->response(retmsg(-1,"查询失败！"),'json');
                }else{
                    $data = array();
                    //取得数据表所有字段的值
                    $data["subitem"] = $result;
                    $this->response(retmsg(0,$data,"查询成功！"),'json');
                }
                break;
            }
            case 'post':{
                //取得输入的值
//                $data = '{"data":{"username":"18408245117","password":"123456"}}';
                $json = json_decode(file_get_contents("php://input"),true);
                $user = $json["data"]["username"];
                $pswd = $json["data"]["password"];
                $query = "select * from garden_userinfor where user_phone='".$user."'";
                $re = $Model->query($query);
                if(is_bool($re)){
                    $this->response(retmsg(-1,null,"查询失败"),'json');
                }else{
                    if($re == null){
                        $this->response(retmsg(-2,null,"该用户不存在！"),'json');
                    }else{
                        $data = array();
                        $data["subitem"] = array();
                        $pass = $re[0]["login_pass"];
                        $token2 = md5($user);
                        if($pswd != $pass){
                            $this->response(retmsg(-1,null,"用户名或密码错误！"),'json');
                        }else{
                            $data["subitem"] = $re;
                            $time = date("Y-m-d H:i:s");
                            $log_time = "update garden_userinfor set login_time='$time',token='$token2' where user_phone='$user'";
                            $log_result = $Model->execute($log_time);
                            if(is_bool($log_result)){
                                $this->response(retmsg(-1,null,"登录失败！"),'json');
                            }else{
                                //用户登录之后产生令牌,将令牌保存在cookie中
                                $this->setLocalCookie("username",$user);
                                $this->setLocalCookie("password",md5($pass));
                                $this->setLocalCookie("token",$token2);
                                $data['cookies'] = $_COOKIE;
                                $this->response(retmsg(0,$data,"登录成功！"),'json');
                            }
                        }
                    }
                }
                break;
            }
        }
    }

    public function register(){
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'post':{
//                $json = '{"data":{"username":"18408245117","password1":"admin","password2":"admin"}}';//获取用户写入的个人信息
                $data = json_decode(file_get_contents("php://input"), true);
                $phone = safe($data['data']['username']);
                $pass1 = safe($data['data']['password1']);
                $pass2 = safe($data['data']['password2']);
                $sex = "保密";
                $head = "http://placehold.it/80x80?text=head";
                $token = md5(md5($phone));
                $sql_query = "select user_phone from garden_userinfor where user_phone=".$phone;
                $sql_result = $Model->query($sql_query);
                if($sql_result != null){
                    $this->response(retmsg(-1,null,"该账号已被注册过"),'json');
                }elseif($pass1 != $pass2){
                    $this->response(retmsg(-1,null,"两次密码输入不一致，请重新输入"),'json');
                }else {
                    //注册用户入库
                    $sql_insert = "insert into garden_userinfor(user_id,user_phone,nickname,user_addr,
                                   photo_type,photo_url,login_pass,token,soil_nums,apply_sale) values (" . safe("null") . ",'" .
                        safe($phone) . "','" . safe("") . "','" . safe("") . "','" . safe("image/jpeg") . "','" .
                        safe($head) ."','" . safe($pass1) . "','" . safe($token) . "','" . safe("0") ."',".safe("0"). ")";
                    $insert_result = $Model->execute($sql_insert);
                    //为注册用户赠送初始钱包：200元
                    $insert = "insert into garden_wallet(token,balance) values ("."'".safe($token)."','".safe("200")."')";
                    $Model->execute($insert);
                    if (is_bool($insert_result)) {
                        $this->response(retmsg(-1, null, "注册失败"), 'json');
                    } else {
                        $this->response(retmsg(0, null, "注册成功"), 'json');
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

    /**
     * @param string $token 防止输入别人的电话号码进行了修改
     */
    public function reset_pass($token=''){
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'post':{
//                $json = '{"data":{"username":"18408245117","password1":"admin","password2":"admin"}}';//获取用户写入的个人信息
                $data = json_decode(file_get_contents("php://input"), true);
                $phone = safe($data['data']['username']);
                $sql = "select user_phone from garden_userinfor where token='$token'";
                $re = $Model->query($sql);
                $local_phone = empty($re[0]['user_phone'])?'':$re[0]['user_phone'];
                if(!empty($local_phone)){
                    if($phone != $local_phone){
                        $this->response(retmsg(-1,null,"非系统登录用户，操作无效！"),'json');
                    }
                }
                $pass1 = safe($data['data']['password1']);
                $pass2 = safe($data['data']['password2']);
                if($pass1 != $pass2){
                    $this->response(retmsg(-1,null,"两次密码输入不一致，请重新输入"),'json');
                }else {
                    //修改用户密码
                    $sql = "select user_phone from garden_userinfor where user_phone='$phone'";
                    $result = $Model->query($sql);
                    if($result[0]["user_phone"] != null){
                        $sql_update = "update garden_userinfor set login_pass='$pass1' where  user_phone=".$phone;
                        $sql_result = $Model->execute($sql_update);
                        if (is_bool($sql_result)) {
                            $this->response(retmsg(-1, null, "密码修改失败！"), 'json');
                        } else {
                            $this->response(retmsg(0, null, "密码修改成功！"), 'json');
                        }
                    }else{
                        $this->response(retmsg(-2, null, "该用户不存在！"), 'json');
                    }
                }
                break;
            }
        }
    }
}