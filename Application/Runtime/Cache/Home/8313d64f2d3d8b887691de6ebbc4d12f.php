<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title></title>
    <style>
        body{
            background-color: #e2e5e8;
        }
        .m{
            cursor: pointer;
        }
        .btn{
            width: 200px;
            height: 50px;
            background-color: #0099FF;
            border: 1px solid #0088cc;
            border-radius: 10px;
            margin-top: 40px;
            font-size: 18px;
            color: white;
            cursor: pointer;
        }
        #msg{
            color: red;
        }
    </style>
    <script src="http://apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
    <script>
        $(function(){
            $("#verify_img").click(function() {
                var verifyURL = "http://localhost/PersonalGarden/index.php/Home/Login/verify";
                var time = new Date().getTime();
                $("#verify_img").attr({
                    "src" : verifyURL + "/" + time
                });
            });
            $("#j_verify").keyup(function() {
                $.post("http://localhost/PersonalGarden/index.php/Home/Login/check_verify", {
                    code : $("#j_verify").val()
                }, function(data) {
                    if (data.data['flag'] == true) {
                        $('#msg').text("验证码输入正确");
                    } else {
                        $('#msg').text("验证码输入错误");
                    }
                });
            });

            $("#tip").click(function() {
                var username = $("#username").val();
                var pswd = $("#pswd").val();
                $.ajax({
                    type:"get",
                    url:"http://localhost/PersonalGarden/index.php/Home/Login/login/user_phone/"+username,
                    async: false,
                    dataType: 'json',
                    success: function(data) {
                        if(data.hasOwnProperty("resultcode")){
                            if(data.resultcode==-2){
                                alert("你的账号在其他地方登录了或者已经超时，请重新登录！");
                                window.location.href = "index.html";
                            }
                            else{
                                var user = data.data.subitem[0]["user_phone"];
                                var pass = data.data.subitem[0]["login_pass"];
                                var verify = $("#msg").text();
                                var msg = "验证码输入正确";
                                console.log(user);
                                console.log(username);
                                console.log(pass);
                                console.log(pswd);
                                console.log(verify);
                                console.log(msg);
                                if(username == user && pswd == pass && verify == msg){
                                    alert("登录成功");//跳转到主页面
                                }else{
                                    if(username != user && pswd != pass){
                                        alert("用户名或密码错误，请重新输入用户名或密码！");//保留在原页面
                                    }else if(pswd != pass){
                                        alert("密码错误！");//保留在原页面
                                    }else if(verify != msg){
                                        alert("验证码不正确！");//保留在原页面
                                    }

                                }
                            }
                        }
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        alert("加载失败，刷新或者检查网络设置！");
                    }
                });
            });
        })
    </script>
</head>
<body>
<div>blue page for registering</div>
<div class="">
    <label class="part" for="username">用户名：</label>
    <input id="username"  name="username" type="text"><br>
    <label class="part" for="pswd">密  码：</label>
    <input id="pswd"  name="pswd" type="password"><br>

    <label for="j_verify" class="t">验证码：</label>
    <input id="j_verify"  name="j_verify" type="text" class="form-control x164 in">
    <img id="verify_img" alt="点击更换" title="点击更换" src="<?php echo U('YZM/verify',array());?>" class="m"><br>
    <p id="msg">提示信息</p>
    <button class="btn" id="tip">登录</button>
    <!--<img id="verify_img" alt="点击更换" title="点击更换" src="http://localhost/mygarden/mygarden.php/Api/YZM/verify" class="m">-->
</div>
</body>
</html>