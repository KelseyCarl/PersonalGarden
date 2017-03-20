<?php
namespace Home\Controller;
use Think\Controller\RestController;
use Think\Verify;

class LoginController extends RestController{
    public function login($token=""){
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'get':{
                $sql = "select * from userinfor where token='$token'";
                $result = $Model->query($sql);
                if(is_bool($result)){
                    $this->response(retmsg(-1,"查询失败！"),'json');
                }else{
                    $data = array();
                    $data["subitem"] = array();
                    //用过foreach循环取数据
//                    foreach($result as $key=>$value){
//                        $temp = array(
//                            "token"=>$value['token'],
//                            "user_phone"=>$value['user_phone'],
//                            "user_name"=>$value['user_name'],
//                             "photo_url"=>$value['photo_url']
//                        );
//                        $data["subitem"][] = $temp;
//                    }
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
                $query = "select * from userinfor where user_phone='".$user."'";
                $re = $Model->query($query);
                if(is_bool($re)){
                    $this->response(retmsg(-1,null,"查询失败"),'json');
                }else{
                    if($re == null){
                        $this->response(retmsg(-1,null,"该用户不存在！"),'json');
                    }else{
                        $data = array();
                        $data["subitem"] = array();
                        $pass = $re[0]["login_pass"];
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

    public function register(){
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'post':{
//                $json = '{"data":{"username":"18408245117","password1":"admin","password2":"admin"}}';//获取用户写入的个人信息
                $data = json_decode(file_get_contents("php://input"), true);
//                $data = json_decode($json, true);
                $phone = safe($data['data']['username']);
                $pass1 = safe($data['data']['password1']);
                $pass2 = safe($data['data']['password2']);
                $sex = "保密";
                $head = "http://placehold.it/80x80/000000/ffffff?text=head";
                $token = md5($pass1);
                $sql_query = "select user_phone from userinfor where user_phone=".$phone;
                $sql_result = $Model->query($sql_query);
                if($sql_result != null){
                    $this->response(retmsg(-1,null,"该账号已被注册过"),'json');
                }elseif($pass1 != $pass2){
                    $this->response(retmsg(-1,null,"两次密码输入不一致，请重新输入"),'json');
                }else {
                    //注册用户入库
                    $sql_insert = "insert into userinfor(user_id,user_phone,user_name,nickname,user_sex,user_addr,
                                   photo_type,photo_url,login_pass,token,soil_nums) values (" . safe("null") . ",'" .
                        safe($phone) . "','" . safe("") . "','" . safe("") . "','" . safe($sex) . "','" .
                        safe("") . "','" . safe("image/jpg") . "','" . safe($head) .
                        "','" . safe($pass1) . "','" . safe($token) . "','" . safe("0") . "'" . ")";
                    $insert_result = $Model->execute($sql_insert);
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

    public function login_page(){
        $this->theme('blue')->display('login');
    }

    public function verify(){
        $config = array(
            'fontSize'=> 19,
            'length'=>4,
            'imageH'=>35
        );
        $verify = new Verify($config);
        $verify->entry();
    }

    public function check_verify($code,$id=''){
        $verify = new \Think\Verify();
        $temp = $verify->check($code,$id);
        $res['resultcode'] = 0;
        $res["data"]["flag"] = $temp;
        $this->ajaxReturn($res,"JSON");
    }
}