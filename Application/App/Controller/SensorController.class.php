<?php
namespace App\Controller;
use Think\Controller\RestController;

class SensorController extends RestController{
    public function data(){
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'get':{
//                $starttime = date("YmdHm",strtotime("-7 day"));
//                $endtime = date("Y-m-d H:m:s");
////                echo $starttime."   ";
//                echo $endtime;
//                $data = file_get_contents("http://www.0531yun.cn/wsjc/Device/getDeviceData.do?userID=170508xhdx&userPassword=170508xhdx");
                $data = file_get_contents("http://www.0531yun.cn/wsjc/Device/getDevHisData.do?devKey=226529&beginTime=201705101800&endTime=201705151930&userID=170508xhdx%20&userPassword=170508xhdx");
//                echo $data;
                print_r($data);
                break;
            }
        }
    }

    public function insert_data($token = "", $now_time = ""){
        header("Access-Control-Allow-Origin:*");
        $file_contents = file_get_contents("http://www.0531yun.cn/wsjc/Device/getDevHisData.do?devKey=226529&beginTime=201705151030&endTime=" . $now_time . "&userID=170508xhdx%20&userPassword=170508xhdx");
//         echo $file_contents;
        $arr = json_decode($file_contents, true);
        $data = array();
        $Model = M();
        switch ($this->_method) {
            case 'get': {
                //获取所有历史数据
//                echo "count arr:".count($arr['HisData']);
                foreach ($arr['HisData'] as $k => $v) {
                    $soil_temper = $v['TempValue'];
                    $soil_wet = $v['HumiValue'];
                    $monitor_data_time = $v['TimeValue'];
                    $sql1 = "select * from monitor where monitor_data_time ='$monitor_data_time'";
                    $list = $Model->query($sql1);
                    if ($list == null) {
                        $sql2 = "insert into monitor(sid,soil_id,soil_temper,soil_wet,monitor_data_time)
                        values(26,'17011'," . $soil_temper . "," . $soil_wet . ",'" . $monitor_data_time . "')";
                       $Model->execute($sql2);
                    }else{
                        //不执行入库
                        echo "11122222222";
                    }
                }
                return;
            }
        }
    }
}