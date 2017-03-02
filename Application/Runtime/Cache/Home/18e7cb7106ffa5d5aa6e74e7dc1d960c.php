<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <script src="http://apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
    <title>图片的修改</title>
    <style>
        .container{
            width: 400px;
            height: 760px;
            border: 1px solid #183152;
            border-radius: 5px;
            margin: 0 auto;
        }
        .title{
            width: 100%;
            padding-top: 15px;
            background-color: #0088cc;
            color: white;
        }
        .head{
            float: left;
            margin:20px;
        }
        .pic{
            float: right;
            margin: 20px;
            cursor: pointer;
            border-radius: 40px;
        }
        .font{
            text-align: center;
            margin-top: 10px;
            padding-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="title">
            <h4 class="font">个人信息修改</h4>
        </div>
        <form enctype="multipart/form-data" method="post" action="upload">
            <label class="head">头像</label>
            <img class="pic" src="http://placehold.it/80x80/000000/ffffff?text=head" alt="头像图片">
            <input type="submit" value="提交" >
        </form>
    </div>
</body>
</html>