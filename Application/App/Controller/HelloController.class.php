<?php
namespace App\Controller;
use Think\Controller\RestController;

class HelloController extends RestController{
    public function test(){
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'get': {
                $sql = "select * from garden_admin";
                $result = $Model->query($sql);
                if (is_bool($result)) {
                    $this->response(retmsg(-1, "qqq"), 'json');
                } else {
                    $data = array();
                    $data["subitem"] = array();
                    $data["subitem"] = $result;
                    $this->response(retmsg(0,"222","111"),'json');
                }
                break;
            }
        }
    }
}