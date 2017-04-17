<?php
namespace Home\Controller;
use Think\Controller;

class SessionController extends Controller{
    public function setSession(){
        //1.启动会话
//        $time = 1*60;
//        session_set_cookie_params($time);
//        session_start();
        session_start();
        $time = 1*60;
        setcookie(session_name(),session_id(),time()+$time,"/");
        //2.注册会话
        $_SESSION["admin"] = "mr.right";
//        if(empty($_SESSION["admin"]))
//            //3.使用会话
//            $myvalue = $_SESSION["admin"];
//        echo "myvalue: ".$myvalue."<br>";
        //4.删除会话
//        unset($_SESSION["admin"]);
        //删除多个会话：
//        $_SESSION = array();
        //彻底销毁session
//        session_destroy();
    }
}