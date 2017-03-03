<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title></title>
</head>
<body>
<!--http://upload.qiniu.com/-->
    <form method="post" action="http://localhost/PersonalGarden/index.php/Home/Qiniu/upload" enctype="multipart/form-data">
        <input name="token" type="hidden" value="<upload_token>">
        <input name="myfile" type="file" />
        <input type="submit" value="上传"/>
    </form>
</body>
</html>