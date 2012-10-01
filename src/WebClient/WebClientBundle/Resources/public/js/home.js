$(document).ready(function(){
    var Layout = $("#wrap").layout({
        applyDefaultStyles: true,      
        north__size:        75,
        north__closable:    false,
        north__resizable:   false,
        north__slidable:    false,       
        center__maxSize : 0,
        livePaneResizing: true
    });

    API.renderRestAppend("Expresso/Menu", "Expresso/MenuTop", ".north-menu-images");
    Module.load();
});