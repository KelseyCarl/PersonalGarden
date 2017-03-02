<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title></title>
</head>
<body>
    <form method="post" action="http://upload.qiniu.com/" enctype="multipart/form-data">
        <input name="token" type="hidden" value="<upload_token>">
        <input name="file" type="file" />
        <input type="submit" value="上传"/>
    </form>
</body>
</html>