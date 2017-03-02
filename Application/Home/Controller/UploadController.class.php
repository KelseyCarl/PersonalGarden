<?php
namespace Home\Controller;
use Think\Controller\RestController;

class UploadController extends RestController{

    public function upload(){
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'post':{
                $config = array(
                    "maxSize"=>3145728,// 设置附件上传大小
                    "exts"=>array('jpg','gif','png','jpeg'),// 设置附件上传类型
                    "rootPath"=>'./Public/',// 设置附件上传根目录
                    "savePath"=>'/Uploads/',// 设置附件上传（子）目录
                    "replace"=>true//存在同名文件，true表示被覆盖
//                    "saveName"=>''//保持上传的文件名不变
                );
                $upload = new \Think\Upload($config);// 实例化上传类
                // 上传多个文件 $info   =   $upload->upload();
                //上传单个文件  $info = $upload->uploadOne($_FILES['photo1']);
                $info = $upload->upload();
                if(!$info) {// 上传错误提示错误信息
                    $this->error($upload->getError());
                }else { //将图片数据保存到数据表
                    foreach ($info as $key => $value) {
                        $name = $value["name"];
                        $type = $value["type"];
                        $savename = $value["savename"];
                        $savepath = $value["savepath"];
                        $sql = "replace into photo(id,name,type,savename,savepath) values(" . "null" . ",'" . $name . "','" . $type . "','" . $savename . "','" . $savepath . "'" . ")";
                        $result = $Model->execute($sql);
                        if (is_bool($result)) {
                            $this->response(retmsg(-1,null,"保存到数据库失败"),'json');
                        } else {
                            $this->response(retmsg(0,null,"上传成功"),'json');
//                            $this->success('上传成功！');
                        }
                    }
                }
                break;
            }
        }
    }

    public function getdata(){
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'get': {
                $sql2 = "select * from photo";
                $result2 = $Model->query($sql2);
                if(is_bool($result2)){
                    echo "查询数据失败<br>";
                }else{
                    $data = $result2;
                    $this->response(retmsg(0,$data,"查询成功！"),'json');
                }
                break;
            }
        }
    }

    public function add(){
        $this->theme('blue')->display('form');//加载blue主题下面的blue.html
    }


    public function test($is_deal=""){
        header("Access-Control-Allow-Origin:*");
        $Model = M();
        switch($this->_method) {
            case 'get':{
                $sql1 = "select * from user_question_infor where question_from=1";
                $result = $Model->query($sql1);
                if(is_bool($result)){
                    $this->ajaxReturn("查询失败",'JSON');
                }else{
                    $data = array();
                    $data["subitem"] = array();
                    for($i = 0;$i < count($result); $i++){
                        $querynums = "select count(question_id) as total from user_comments where question_id=".$result[$i]['question_id'];
                        $qre = $Model->query($querynums);
                        //有“待审核”的评论数据
                        $qrecomts = "select * from user_comments where question_id=".$result[$i]['question_id'];
                        $result1 = $Model->query($qrecomts);
                        echo "first array:".$result1[0]." its null"."<br>";
                        echo "nums:".count($result1)."<br>";
                        $comment_is_verify = "";
//                        if(count($result1) == 0){
//                            $comment_is_verify = "没有评论";
//                        }else{
                            for($j = 0;$j < count($result1);$j++){
                                $comment_is_verify = 1;
                                $comment_is_verify = $comment_is_verify & $result1[$j]['is_verify'];
                                echo "review data:".$comment_is_verify."<br>";
                                echo "database data:".$result1[$j]['is_verify']."<br>";
                            }
//                        }


                        $data_temp1 = array("question_id"=>$result[$i]['question_id'],
                            "user_id"=>$result[$i]['user_id'],
                            "user_name"=>$result[$i]['user_name'],
                            "question_type"=>$result[$i]['question_type'],
                            "type_flag"=>$result[$i]['type_flag'],
                            "question_title"=>$result[$i]['question_title'],
                            "verify_status"=>$result[$i]['verify_status'],
                            "verify_desc"=>$result[$i]['verify_desc'],
                            "unpass_cause"=>$result[$i]['unpass_cause'],
                            "comment_nums_pass"=>$result[$i]['comment_nums'],//审核通过的评论
                            "comment_nums"=>$qre[0]['total'],//总的评论数
                            "answer_nums"=>$result[$i]["answer_nums"],
                            "comment_is_verify"=>$comment_is_verify,
                            "is_answer"=>$result[$i]['answer_status']
                        );
                        $data["subitem"][]=$data_temp1;
                    }
                    echo "<br><br>";
                    $this->ajaxReturn($data,"JSON");
                }
                break;
            }
        }
    }
}