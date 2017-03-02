<?php
namespace Home\Controller;
use Think\Controller\RestController;
use Think\Verify;

class YZMController extends RestController{
    public function test(){
//        echo "number verify"."<br>";
        $data = array();
        $data["data"] = array();
        $temp = array(array("id"=>"123","name"=>"dhsjjdoas","age"=>21),
            array("id"=>"233","name"=>"eyiwopx","age"=>19)
        );
        $data["data"] = $temp;
        $this->ajaxReturn($data,"JSON");
    }
    public function add(){
        $this->theme('blue')->display('blue');//加载blue主题下面的blue.html
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


    public function verify_user($phone){
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'get':{
                $sql = "select * from userinfor where user_phone = ".$phone;
                $result = $Model->query($sql);
//                var_dump($result);
                if(is_bool($result)){
                    echo "find error<br>";
                }else{
                    $data = array();
                    $data["resultcode"] = 0;
                    $data["resultmsg"] = "操作成功";
                    $data["data"] = array();
                    $data["data"]["subitem"] = $result;
                    $this->ajaxReturn($data,'JSON');
                }
                break;
            }
        }
    }


}