<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title></title>
    <style>
        body{
            background-color: dodgerblue;
        }
        .m{
            cousor:pointer;
        }
    </style>
    <script src="http://apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
    <script>
        $(function(){
            $("#verify_img").click(function() {
                var verifyURL = "http://localhost/PersonalGarden/index.php/Home/YZM/verify";
                var time = new Date().getTime();
                $("#verify_img").attr({
                    "src" : verifyURL + "/" + time
//                    "src" : "http://localhost/mygarden/mygarden.php/Api/YZM/verify"
                });
            });
            $("#j_verify").keyup(function() {
                $.post("http://localhost/PersonalGarden/index.php/Home/YZM/check_verify", {
                    code : $("#j_verify").val()
                }, function(data) {
                    if (data == true) {
//                        $('#tip').val("验证码输入正确");
//                        console.log("验证码输入正确");//验证码输入正确
                    } else {
//                        $('#tip').val("验证码输入错误");
//                        console.log("验证码输入错误");//验证码输入错误
                    }
                });
            });
        })
    </script>
</head>
<body>
<div>blue page for registering</div>
<div class="">
    <label for="j_verify" class="t">验证码：</label>
    <input id="j_verify"  name="j_verify" type="text" class="form-control x164 in">
    <img id="verify_img" alt="点击更换" title="点击更换" src="<?php echo U('YZM/verify',array());?>" class="m">
    <p id="tip">qqq</p>
    <!--<img id="verify_img" alt="点击更换" title="点击更换" src="http://localhost/mygarden/mygarden.php/Api/YZM/verify" class="m">-->
</div>
</body>
</html>