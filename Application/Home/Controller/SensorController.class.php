<?php
namespace Home\Controller;
use Think\Controller\RestController;

class SensorController extends RestController{
    public function data(){
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'get':{
                $time = date("Ymd");
//                $data = file_get_contents("http://www.0531yun.cn/wsjc/Device/getDeviceData.do?userID=170508xhdx&userPassword=170508xhdx");
                $data = file_get_contents("http://www.0531yun.cn/wsjc/Device/getDevHisData.do?devKey=226529&beginTime=201705101800&endTime=201705151930&userID=170508xhdx%20&userPassword=170508xhdx");
                echo $data;
                break;
            }
        }
    }
}