<?php
namespace Home\Controller;
use Think\Controller;

class CookieController extends Controller{
    public function getCookie($value="username"){
        setcookie("TMcookie","www.baidu.com");
        setcookie("TMcookie","www.baidu.com",time()+60);
        setcookie("TMcookie",$value,time()+3600,"/tm/",".baidu.com",1);
    }
    public function readCookie(){
        if(!isset($_COOKIE["visittime"])){
            setcookie("visittime",date("Y-m-d H:i:s"));
            echo "Welcome to this website the first time".".<br>";
        }else{
            setcookie("visittime",date("Y-m-d H:i:s"),time()+60);
            echo "last time your visited: ".$_COOKIE["visittime"]."<br>";
        }
        echo "this time your visit time is :".date("Y-m-d H:i:s");
    }
    public function deleteCookie(){
        $set = setcookie("TMcookie","www.baidu.com",time()+10);
        echo $set."<br>";
//        $del = setcookie("TMcookie","",time()-1);
//        $del = setcookie("TMcookie","",0);
//        echo $del;
    }
}