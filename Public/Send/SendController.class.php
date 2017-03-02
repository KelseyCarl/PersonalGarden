<?php 
namespace Home\Controller;
use Home\Controller\HomeController;
include('Send.php');
header('Content-Type:text/html; charset=utf-8');
class SendController extends  CommonController{
	//发送短信的控制类为index
	public function index()
	{
		//以下三条读取配置文件中的内容即可
		$http = C('message.http');
		$uid = C('message.uid');
		$pwd = C('message.pwd');
		//要接受信息的手机号码，多个以英文逗号隔开，这里只是一个用于测试的手机号，按找个项目需求操作即可
		$mobile	 = '182156*****';
		//消息编号，该参数用于发送短信收取状态报告用，格式为消息编号+逗号；与接收号码一一对应，可以重复出现多次。
		//这里只用一个编号即可，手机号加上微秒，应该是唯一的了吧。
		$mobileids	 = intval('182156*****').microtime();
		//要发送的内容
		$content = urlencode('我是樱桃小丸子，感觉自己萌萌哒');
		//即时发送，即：操作后就会进行发送，以下有定时发送
		//调用封装好的短信接口类。
		$send = new \Send;
		$res = $send->sendSMS($http,$uid,$pwd,$mobile,$content,$mobileids);
		//以下为测试是否发送成功！
		if (substr($res,9,11) == 100) {
			//如果成功就，这里只是测试样式，可根据自己的需求进行调节
			echo "<script>alert('获取购物券密码成功，请注意查收短信');</script>";
		}else{
			//如果不成功
			echo "<script>alert('未知错误，请联系客服');</script>";
		}
		// echo $res;
	}

	/*--------------------------------定时发送在我的项目暂时用不到，所以先注释掉--------------------------------*/
		//定时发送
		//$time = '2010-05-27 12:11';
		/*
			$uid='';//用户名
			$pwd='';//用户明文密码
			$res = sendSMS($uid,$pwd,$mobile,$content);
			echo $res;
		*/
	/*------------------------------------------------------------------------------------------------------------*/
}
 ?>
