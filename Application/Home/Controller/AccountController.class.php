<?php
namespace Home\Controller;
use Think\Controller\RestController;

class AccountController extends RestController{
    public function my_account($token){//查询账户余额
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'get':{
                $sql = "select * from wallet where token='$token'";
                $result = $Model->query($sql);
                if(is_bool($result)){
                    $this->response(retmsg(-1,null,"查询账户失败"),'json');
                }else{
                    $data = array();
                    $data["subitem"] = $result;
                    $this->response(retmsg(0,$data,"查询成功"),'json');
                }
                break;
            }
        }
    }
}