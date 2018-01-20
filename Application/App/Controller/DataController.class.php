<?php
namespace App\Controller;
use Think\Controller\RestController;

class DataController extends RestController
{
    //获取传感器历史数据
    public function all_data($token = "", $time = "")
    {
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch ($this->_method) {
            case 'get': {
                $where = "";
                if ($time != '') {
                    $where .= "DATE_FORMAT(monitor_data_time,'%Y-%m-%d')='" . $time . "'";
                }
                $sql = "select * from garden_monitor";
                $result = $Model->query($sql);
                if ($where == "") {
                    $sql1 = "select * from garden_monitor order by monitor_data_time desc limit 48 ";
                } else {
                    $sql1 = "select * from garden_monitor  where  " . $where . "  order by monitor_data_time desc  limit 48 ";
                }
                $list = $Model->query($sql1);
                if (is_bool($list)) {
                    $this->response(retmsg(-1), 'json');
                    return;
                }
                $data = array('resultcode' => '0', 'resultmsg' => '查询成功', 'data' => $list);
                echo json_encode($data);
                return;
            }
        }
    }
//获取传感器实时数据
    public function now_data($token = "")
    {
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch ($this->_method) {
            case 'get': {
                $sql1 = "select * from garden_monitor order by monitor_data_time desc limit 1 ";
                $list = $Model->query($sql1);
                if (is_bool($list)) {
                    $this->response(retmsg(-1), 'json');
                    return;
                }
                $data = array('resultcode' => '0', 'resultmsg' => '查询成功', 'data' => $list);
                echo json_encode($data);
                return;
            }
        }
    }
//获取传感器最近7天数据
    public function recent_data($token = "")
    {
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch ($this->_method) {
            case 'get': {
                $sql1 = "select max(soil_temper) maxt,max(soil_wet) maxw,min(soil_temper) mint,min(soil_wet) minw from garden_monitor 
              group by date_format(monitor_data_time,'%Y-%m-%d') 
                order by monitor_data_time desc limit 7 ";
                $list = $Model->query($sql1);
                if (is_bool($list)) {
                    $this->response(retmsg(-1), 'json');
                    return;
                }
                $data = array('resultcode' => '0', 'resultmsg' => '查询成功', 'data' => $list);
                echo json_encode($data);
                return;
            }
        }
    }
//云端数据传入本地数据库
    public function insert_data($token = "", $now_time = "")
    {
        header("Access-Control-Allow-Origin:*");
        $file_contents = file_get_contents("http://www.0531yun.cn/wsjc/Device/getDevHisData.do?devKey=226529&beginTime=201705151030&endTime=" . $now_time . "&userID=170508xhdx%20&userPassword=170508xhdx");
        // echo $file_contents;
        $arr = json_decode($file_contents, true);
        $data = array();
        $Model = M();
        switch ($this->_method) {
            case 'get': {
                //获取所有历史数据
                foreach ($arr['HisData'] as $k => $v) {
                    $data['soil_temper'] = $v['TempValue'];
                    $data['soil_wet'] = $v['HumiValue'];
                    $data['monitor_data_time'] = $v['TimeValue'];
                    $sql1 = "select * from garden_monitor where monitor_data_time ='" . $data['monitor_data_time'] . "'";
                    $list = $Model->query($sql1);
                    if ($list==null) {
                        $sql2 = "insert into garden_monitor(sid,soil_id,soil_temper,soil_wet,monitor_data_time)
                        values(226529,'17001'," . $data['soil_temper'] . "," . $data['soil_wet'] . ",'" . $data['monitor_data_time'] . "')";
                        $insert_result = $Model->execute($sql2);
                    }else{
                        echo "134";
                    }
                }
                return;
            }
        }
    }
    //获取还没有录制视频，没有回应用户操作的土地名称
    public function get_soil_name($token = ""){
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch ($this->_method) {
            case 'get': {
//                $sql1 = "select * from soil";
                $sql1 = "select distinct(soil_name) as soil_name from garden_operate where soil_name!=''";
                $list = $Model->query($sql1);
                if (is_bool($list)) {
                    $this->response(retmsg(-1), 'json');
                    return;
                }
                $data = array('resultcode' => '0', 'resultmsg' => '查询成功', 'data' => $list);
                echo json_encode($data);
                return;
            }
        }
    }
    //查询相关土地的具体操作项
    public function soil_operate($soil_name){
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case "get":{
                $sql2 = "select * from garden_operate where soil_name='$soil_name' and get_order=''";
                $reslut = $Model->query($sql2);
                if(is_bool($reslut)){
                    $this->response(retmsg(0,null,"查询失败"),'json');
                }else{
                    $data = array();
                    foreach($reslut as $k=>$v){
                        $which = $v["which"];
                        $id = $v["id"];
                        $test = array("id"=>$id,"which"=>$which);
                        $data["subitem"][]  = $test;
                    }
                    $this->response(retmsg(0,$data,"查询成功"),'json');
                }
                break;
            }
        }
    }
    //保存管理员操作信息
    public function insert_operate($token = "",$operate_id,$sn_descr){
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch ($this->_method) {
            case 'get': {
                $sql1 = "update garden_operate set get_order = '$token',which_desc='$sn_descr' where id='$operate_id'";
                $list = $Model->execute($sql1);
                if (is_bool($list)) {
                    $this->response(retmsg(-1), 'json');
                    return;
                }
                $data = array('resultcode' => '0', 'resultmsg' => '操作成功', 'data' => null);
                echo json_encode($data);
                return;
            }
        }
    }
    //录制视频信息存入数据库
    public function insert_soil_sn($token = "",$soil_name="",$sn_title="",$sn_descr="")
    {
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch ($this->_method) {
            case 'get': {
                $sql1 = "insert into garden_soil_sn(soil_name,sn_title,sn_desc) values('" . $soil_name . "','" . $sn_title . "','"
                    . $sn_descr . "')";
                $list = $Model->execute($sql1);
                if (is_bool($list)) {
                    $this->response(retmsg(-1), 'json');
                    return;
                }
                $data = array('resultcode' => '0', 'resultmsg' => '查询成功', 'data' => $list);
                echo json_encode($data);
                return;
            }
        }
    }
    //添加录制视频播放路径
    public function insert_sn_addr($token = "",$sn_video_addr="",$oper_id="")
    {
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch ($this->_method) {
            case 'get': {
                $time = date("Y-m-d H:i:s");
                $sql1 = "update garden_operate set play_url='$sn_video_addr',get_time='$time' where id='$oper_id'";
                $list1 = $Model->execute($sql1);
                if (is_bool($list1)) {
                    $this->response(retmsg(-1), 'json');
                    return;
                }
                $data = array('resultcode' => '0', 'resultmsg' => '操作成功', 'data' => $list1);
                echo json_encode($data);
                return;
            }
        }
    }

//    public function insert_sn_addr($token = "",$sn_video_addr="")
//    {
//        header("Access-Control-Allow-Origin:*");
//        $Model = M();
//        switch ($this->_method) {
//            case 'get': {
//                $sql="select max(sn_time)  max_time from soil_sn";
//                $list = $Model->query($sql);
//                $max_time=$list[0]['max_time'];
//                $sql1 = "update garden_soil_sn set sn_video_addr='" .$sn_video_addr.
//                    "' where sn_time='".$max_time."'";
//                $list1 = $Model->execute($sql1);
//                if (is_bool($list1)) {
//                    $this->response(retmsg(-1), 'json');
//                    return;
//                }
//                $data = array('resultcode' => '0', 'resultmsg' => '查询成功', 'data' => $list1);
//                echo json_encode($data);
//                return;
//            }
//        }
//    }
}