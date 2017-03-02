<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <script src="http://apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
    <title>获取数据</title>
    <style>
        img{
            margin: 30px;
            width: 350px;
            height: 250px;
        }
        .btn{
            border-radius: 10px;
            border: 1px solid #0099FF;
            width: 200px;
            height: 50px;
            background-color: #0088cc;
            cursor: pointer;
            font-size: 20px;
            color: white;
            margin: 50px;
        }
    </style>
</head>
<body>
    <form enctype="multipart/form-data" method="post" action="upload">
        <input type="text" name="name" />
        <!--<input type="file" name="photo1" value="上传"/>-->
        <input type="file" name="photo[]" />
        <input type="file" name="photo[]" />
        <input type="file" name="photo[]" />
        <input type="submit" value="提交" >
    </form>

    <!--图片展示-->
    <div style="margin-top: 50px;">
        <img id="img1" src="http://placehold.it/350x250" alt="图片1"/>
        <img id="img2" src="http://placehold.it/350x250" alt="图片2"/>
        <img id="img3" src="http://placehold.it/350x250" alt="图片3"/>
        <img id="img4" src="http://placehold.it/350x250" alt="图片4"/>
        <img id="img5" src="http://placehold.it/350x250" alt="图片5"/>
        <img id="img6" src="http://placehold.it/350x250" alt="图片6"/>
        <img id="img7" src="http://placehold.it/350x250" alt="图片7"/>
    </div>
    <button class="btn" id="show">show pics</button>
    <button class="btn" id="change">change one pic</button>

    <script>
        $(function() {
            $("#show").click(function() {
                $.ajax({
                    type:"get",
                    url:"http://localhost/PersonalGarden/index.php/Home/Upload/getdata",
                    async: false,
                    dataType: 'json',
                    success: function(data) {
                        if(data.hasOwnProperty("resultcode")){
                            if(data.resultcode==-2){
                                alert("你的账号在其他地方登录了或者已经超时，请重新登录！");
                                window.location.href = "blue.html";
                            }
                            else{
//                                console.log("data.data"+JSON.stringify(data.data));
                                $.each(data.data,function(index,value){
                                    URL = "http://localhost/PersonalGarden/Public";
                                    var path = URL+value['savepath']+value['savename'];
//                                    console.log("path"+(index+1),path);
                                    $("#img"+(index+1)).attr('src',path);
                                });
                            }
                        }
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        alert("加载失败，刷新或者检查网络设置！");
                    }
                });
            });

            $("#change").click(function() {
                $.ajax({
                    type:"get",
                    url:"http://localhost/PersonalGarden/index.php/Home/Upload/change",
                    async: false,
                    dataType: 'json',
                    success: function(data) {
                        if(data.hasOwnProperty("resultcode")){
                            if(data.resultcode==-2){
                                alert("你的账号在其他地方登录了或者已经超时，请重新登录！");
                                window.location.href = "blue.html";
                            }
                            else{
//                                console.log("data.data"+JSON.stringify(data.data));
                                $.each(data.data,function(index,value){
                                    URL = "http://localhost/PersonalGarden/Public";
                                    var path = URL+value['savepath']+value['savename'];
//                                    console.log("path"+(index+1),path);
                                    $("#img"+(index+1)).attr('src',path);
                                });
                            }
                        }
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        alert("加载失败，刷新或者检查网络设置！");
                    }
                });
            });
        });

    </script>
</body>
</html>