<?php
namespace Home\Controller;
use Think\Controller;

class SessionController extends Controller{
    public function setSession(){
        //1.�����Ự
//        $time = 1*60;
//        session_set_cookie_params($time);
//        session_start();
        session_start();
        $time = 1*60;
        setcookie(session_name(),session_id(),time()+$time,"/");
        //2.ע��Ự
        $_SESSION["admin"] = "mr.right";
//        if(empty($_SESSION["admin"]))
//            //3.ʹ�ûỰ
//            $myvalue = $_SESSION["admin"];
//        echo "myvalue: ".$myvalue."<br>";
        //4.ɾ���Ự
//        unset($_SESSION["admin"]);
        //ɾ������Ự��
//        $_SESSION = array();
        //��������session
//        session_destroy();
    }
}