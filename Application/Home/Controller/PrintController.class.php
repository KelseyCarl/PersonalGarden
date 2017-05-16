<?php
namespace Home\Controller;
use Think\Controller\RestController;
//http://localhost:8080/PersonalGarden/index.php/Home/Print/to_csv
class PrintController extends RestController{
    public function to_csv(){
        $str = ",,,,学生课程信息表,,,,, \n";
        $str .= ",,学号,姓名,课程名,课程编号,开始日期,结束日期,教师 \n";
        $filename = date("Ymd")."学生数据.csv";
        header("Content-Type: application/vnd.ms-excel; charset=GB2312");
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header("Content-Disposition: attachment;filename=$filename");
        header("Content-Transfer-Encoding: binary ");
        $csvContent = iconv("utf-8","gb2312",$str);
        echo $csvContent;
        exit;
    }
}