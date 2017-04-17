<?php
namespace Home\Controller;
use Think\Controller\RestController;

class SettingController extends RestController{
    public function update_sign($token=""){//设置个人签名
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'post':{
                //取得输入的值
//                $data = '{"data":{"sign":"我就是不一样的烟火"}}';
                $json = json_decode(file_get_contents("php://input"),true);
//                $json = json_decode($data,true);
                $sign = $json["data"]["sign"];
                $sub_sign = "update  userinfor set sentence='$sign' where token='$token'";
                $re = $Model->execute($sub_sign);
                if(is_bool($re)){
                    $this->response(retmsg(-1,null,"修改签名失败！"),'json');
                }else{
                    $this->response(retmsg(0,null,"修改签名成功！"),'json');
                }
                break;
            }
        }
    }

    public function save($token){
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'post':{
//                $json = '{"data":{"nickname":"hello","tel":"123","address":"here"}}';//获取用户的资料修改信息
                $data = json_decode(file_get_contents("php://input"), true);
//                $data = json_decode($json, true);
                $nickname = safe($data['data']['nickname']);
                $tel = safe($data['data']['tel']);
                $address = safe($data['data']['address']);
                $up_sql = "update userinfor set nickname='$nickname',user_phone='$tel',user_addr='$address' where token='$token'";
                $result = $Model->execute($up_sql);
                if(is_bool($result)){
                    $this->response(retmsg(-1,null,"资料保存失败！"),'json');
                }else{
                    $this->response(retmsg(0,null,"资料保存成功！"),'json');
//                    $url = "http://localhost:8080/PersonalGarden/webapp/pages/UserCenter/user_center.html";
//                    header("refresh:1;url=$url");
                }
                break;
            }
        }
    }
}