<?php
namespace Home\Controller;
use Think\Controller\RestController;

class HelloController extends RestController{
    public function test(){
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'get': {
                $sql = "select * from admin";
                $result = $Model->query($sql);
//                var_dump($result);
                if (is_bool($result)) {
                    $this->response(retmsg(-1, "查询失败！"), 'json');
                } else {
                    $data = array();
                    $data["subitem"] = array();
                    //取得数据表所有字段的值
                    $data["subitem"] = $result;
//                    var_dump($data);
                    $this->response(retmsg(0,"222","查询成功！"),'json');
//                    $this->response(retmsg(0, $data, "查询成功！"), 'json');
                }
                break;
            }
        }
    }
}