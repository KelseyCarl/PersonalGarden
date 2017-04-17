<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title></title>
    <script src="http://libs.baidu.com/jquery/1.11.1/jquery.min.js"></script>
</head>
<body>
<!--http://upload.qiniu.com/ http://localhost:8080/PersonalGarden/index.php/Home/Qiniu/upload/token/61c483c5b43448e30b71b210b0ec7be6-->
    <form method="post" action="http://localhost:8080/PersonalGarden/index.php/Home/Qiniu/upload2" enctype="multipart/form-data">
        <input name="token" type="hidden" value="<upload_token>">
        <input id="token2" name="token2" type="hidden" value="">
        <input name="myfile" type="file" />
        <input id="up"  type="submit" value="上传"/>
    </form>
</body>
<!--<script src="jquery.form.js"></script>-->
<script>
    var token = localStorage.getItem("log_status");
//    alert(token);
//    var url = "http://localhost:8080/PersonalGarden/index.php/Home/Qiniu/upload/token/"+token;
    $(function(){
        $("#token2").val(token);
//        alert($("#token2").val());
    });
</script>
</html>