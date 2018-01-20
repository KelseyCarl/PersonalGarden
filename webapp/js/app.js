// URL = "http://localhost/PersonalGarden/index.php/Home/";
URL = "http://192.111.111.221/PersonalGarden/index.php/App/";
$(document).foundation();
function getQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return unescape(decodeURI(r[2]));
    return null;
}
//验证电话号码
function checkForm(o){
    var re=/^(13[0-9]{9})|(15[89][0-9]{8})$/;
    if(!re.test(o)){
        layer.msg('请输入正确格式的手机号码!');
        return false;
    }
}

//获取cookie数据
function getCookie(name)
{
    var arr,reg=new RegExp("(^| )"+name+"=([^;]*)(;|$)");
    if(arr=document.cookie.match(reg))
        return unescape(arr[2]);
    else
        return null;
}

//cookie删除方式，将有效期设置为当前时期之前，及让cookie失效
function delCookie(name) {
    var exp = new Date();
    exp.setTime(exp.getTime() - 1);
    var cval=getCookie(name);
    if(cval!=null)
        document.cookie= name + "="+cval+";expires="+exp.toGMTString();
}

$(function(){
    var token = getCookie("token");
    console.log(token);
    //退出登录
    $("#logout").click(function(){
//				localStorage.clear();//清除全部保存的对象
//         localStorage.removeItem("log_status");
        window.location.href = "../UserCenter/login.html";
    });

    //未登陆则跳转登陆页面
    if(token == null) {
        $(".head img").removeClass("hide");
        $(".no-data").removeClass("hide");
				layer.msg("请先登录！");
				window.location.href = "../UserCenter/login.html";
    }
    else {
        //获取用户左侧的头像、昵称信息
        $.get(URL+"Login/login/token/"+token, function(json) {
            if(json.resultcode != 0) {
                layer.msg("登录失败,请重新登录！");
                window.location.href = "../UserCenter/login.html";
            }
            else {
                $(".head img").removeClass("hide");
                $(".no-data").addClass("hide");
                var nickname = json.data.subitem[0].nickname;//获取用户昵称
                var user_head = json.data.subitem[0].photo_url;
                var user_phone = json.data.subitem[0].user_phone;
                console.log(user_head);
                console.log(user_phone);
                if(nickname){
                    $(".head img").attr("src",user_head);
                    $(".user_infor").append("<h5>"+nickname+"</h5>");
                    $(".user_infor").append("<a href='../UserCenter/userinfor_setting.html'>点此编辑</a>");
                }
            }
        });
    }
});

function get_user(){
    $.get(URL+"Login/login/token/"+token, function(json) {
        if(json.resultcode != 0) {
            alert("登录失败,请重新登录！");
            window.location.href = "../UserCenter/login.html";
        }
        else {
            //alert(json.data.subitem[0].nickname);
            $(".head img").removeClass("hide");
            $(".no-data").addClass("hide");
            var nickname = json.data.subitem[0].nickname;//获取用户昵称
            var user_head = json.data.subitem[0].photo_url;
            var user_phone = json.data.subitem[0].user_phone;
            console.log(user_head);
            console.log(user_phone);
            if(nickname){
                $(".head img").attr("src",user_head);
                $(".user_infor").append("<h5>"+nickname+"</h5>");
                $(".user_infor").append("<a href='../UserCenter/userinfor_setting.html'>点此编辑</a>");
            }
        }
    });
}