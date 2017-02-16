<?php
namespace Home\Controller;
use Think\Controller;
use Think\Crypt\Driver\Think;
use Think\Verify;

class YZMController extends Controller{
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
            'length'=>5,
            'imageH'=>35
        );
        $verify = new Verify($config);
        $verify->entry();
    }
    public function check_verify($code,$id=''){
        $verify = new \Think\Verify();
        $res = $verify->check($code,$id);
        $this->ajaxReturn($res,"JSON");
    }
}