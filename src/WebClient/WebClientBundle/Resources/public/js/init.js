var home = {
    load : function(){
        var template = $("#login").html();
        var data = {
            title : "Login",
            user : "Usuário",
            pass : "Senha",
            login: "Login"
        };
        var html = require('ejs').render(template, {data : data});
        var login = $(html).find(".login-window");
        login.dialog({
            buttons: {
                "Acessar": function () {
                    $(this).parents(".ui-dialog").find("form").submit();
                    $(this).dialog("close");
                }
            },
            draggable:false,
            resizable:false,
            position: ["right","center"]
        });
        //SOME INTERFACE DETAILS
        login.find('input[type=button]').button();
        login.find('input[type=password]').keydown(function(e){
            if(e.keyCode === 13){
               $(this).parents(".ui-dialog").find("form").submit();
            }
        });
        login.parents(".ui-dialog").css("left", "75%");
        login.parents(".ui-dialog").css("top", "30%");
        login.parents(".ui-dialog").css("border-radius", "5px");
        login.parents(".ui-dialog").find(".ui-dialog-titlebar-close").remove();
    }
}
onload = function(){
    home.load();
}
