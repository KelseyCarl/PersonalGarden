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
                    $this->response(retmsg(-1, "��ѯʧ�ܣ�"), 'json');
                } else {
                    $data = array();
                    $data["subitem"] = array();
                    //ȡ�����ݱ������ֶε�ֵ
                    $data["subitem"] = $result;
//                    var_dump($data);
                    $this->response(retmsg(0,"222","��ѯ�ɹ���"),'json');
//                    $this->response(retmsg(0, $data, "��ѯ�ɹ���"), 'json');
                }
                break;
            }
        }
    }
}