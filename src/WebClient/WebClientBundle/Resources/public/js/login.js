function oLogin(){
}

oLogin.prototype.load = function(){
    API.render("Expresso/Login", loginObj, 'body');
    var login = $("body .login-window");
    login.wijdialog({
        buttons: {
            "Acessar": function () {
                $(this).parents(".ui-dialog").find("form").submit();
                $(this).dialog("close");
            }
        },
        draggable:false,
        resizable:false,
        captionButtons:{
            pin: { visible: false },
            refresh: { visible: false },
            toggle: { visible: false },
            minimize: { visible: false },
            maximize: { visible: false },
            close: { visible: false }
        }
    });
    //SOME INTERFACE DETAILS
    login.find('input[type=button]').button();
    login.find('input[type=password]').keydown(function(e){
        if(e.keyCode === 13){
           $(this).parents(".ui-dialog").find("form").submit();
        }
    });
    login.parents(".ui-dialog").css({"left": "75%", "top": "30%", "border-radius":"5px"});
}

var Login = new oLogin();

$(document).ready(function(){
    Login.load();
});
