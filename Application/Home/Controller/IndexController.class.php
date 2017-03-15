<?php
namespace Home\Controller;
use Think\Controller;

class IndexController extends Controller{
    public function index(){
        $url=$_SERVER['SERVER_NAME'].':'.$_SERVER["SERVER_PORT"];
        header("Location:http://".$url."/PersonalGarden/garden/index.html");
        exit();
    }
}