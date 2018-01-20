<?php
//防注入式
function safe($s)
{ 
	//安全过滤函数
	if(get_magic_quotes_gpc())
	{
		$s=stripslashes($s);
	}
	//$s=mysql_real_escape_string($s);
	$s = addslashes($s);
	return $s;
}	

//返回函数
function retmsg($retcode,$retdata=null,$retmessage=null)
{
	$retmsg = "";
	switch($retcode)
	{
		case 0	: { $retmsg = "操作成功"; break; }
		case -1	: { $retmsg = "操作失败"; break; }
		case -4	: { $retmsg = "号码已被注册"; break; }
		case -5	: { $retmsg = "手机号格式不正确"; break; }
		case -9	: { $retmsg = "密码错误或用户不存在"; break;}
		case -14: { $retmsg = "密码不能为空"; break; }
		case -19: { $retmsg = "该号码已被注册"; break; }
		default	: { $retmsg = "未知错误";}
	}
	return array("resultcode"=>$retcode,"resultmsg"=>empty($retmessage)?$retmsg:$retmessage,"data"=>$retdata);
}
?>