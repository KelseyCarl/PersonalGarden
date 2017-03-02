<?php
namespace Home\Controller;
use Think\Controller\RestController;
use Think\Verify;

class LoginController extends RestController{
    public function login($user_phone){
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'get':{
                $sql = "select * from userinfor where user_phone=".$user_phone;
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
        }
    }

    public function register(){
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'post':{
//                $json = '{"data":{"nickname":"yingtao","user_phone":"18408245117","password":"123456","user_sex":1}}';//获取用户写入的个人信息
                $data = json_decode(file_get_contents("php://input"), true);
//                $data = json_decode($json, true);
                $nickname = safe($data['data']['nickname']);
                $user_phone = safe($data['data']['user_phone']);
                $password = safe($data['data']['password']);
                $user_sex = safe($data['data']['user_sex']);
                $token = md5($password);

                if($user_sex == 1){
                    $sex = "male";
                }else{
                    $sex = "female";
                }
                $sql_query = "select user_phone from userinfor where user_phone=".$user_phone;
                $sql_result = $Model->query($sql_query);
                if($sql_result == null){
                    //注册用户入库
                    $sql_insert = "insert into userinfor(user_id,user_phone,user_name,nickname,user_sex,user_addr,
                                   photo_type,photo_url,login_pass,token,soil_nums) values (".safe("null").",'".
                                   safe($user_phone)."','".safe("null")."','".safe($nickname)."','".safe($sex)."','".
                                   safe("null")."','".safe("image/jpg")."','".safe("http://placehold.it/80x80/000000/ffffff?text=head").
                                   "','".safe($password)."','".safe($token)."','".safe("0")."'".")";
                    $insert_result = $Model->execute($sql_insert);
                    if(is_bool($insert_result)){
                        $this->response(retmsg(-1,null,"注册失败"),'json');
                    }else{
                        $this->response(retmsg(0,null,"注册成功"),'json');
                    }
                }else{
                    $this->response(retmsg(-1,null,"该账号已被注册过"),'json');
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