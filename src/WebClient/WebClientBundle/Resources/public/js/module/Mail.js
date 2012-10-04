var oMail = function()
{
    this.name = 'Mail';
}

oMail.prototype.load = function()
{
    $('#main').empty();
    API.render("Mail/Mail", {}, '#main');
    $('#main').layout({
        applyDefaultStyles: true,
        center__maxSize : 0,
        livePaneResizing: true,
        west__closable:false,
        west__resizable:   false,
        west__slidable:    false
    });
    $("#tabs").wijtabs({
        scrollable: true,
        sortable: true
    });
    
    API.restGET("Mail/ListFolders", function(data){
        var tree1 = new Array();
        var tree2 = new Array();
        var tree3 = new Array();
        for (var i=0; i<data.length; i++) {
            if (/^INBOX/.test(data[i].id)) {
                if (!Module.unorphanize(tree1, data[i])) {
                    data[i].children = new Array();
                    tree1.push(data[i]);
                }
            }
            else if (/^user/.test(data[i].id)) {
                if (!Module.unorphanize(tree2, data[i])) {
                    data[i].children = new Array();
                    tree2.push(data[i]);
                }
            }
            else if (/^local_messages/.test(data[i].id)) {
                if (!Module.unorphanize(tree3, data[i])) {
                    data[i].children = new Array();
                    tree3.push(data[i]);
                }
            }
        }
        for(var i =0; i<tree1.length; i++){
            Module.countUnseenChildren(tree1[i]);
        }
        for(var i =0; i<tree2.length; i++){
            Module.countUnseenChildren(tree2[i]);
        }
        for(var i =0; i<tree3.length; i++){
            Module.countUnseenChildren(tree3[i]);
        }
        var html = API.render("Mail/Folder", {folders: [tree1, tree2, tree3]});
        $('.imap-folders').append(html).children().treeview({
            animated: "fast"
        }).find(".folder:not(.head_folder)").unbind("click").click(function(){
            $(".mainfoldertree .folder.selected-folder").removeClass("selected-folder");
            Module.changeFolder($(this).parent().attr("id"));
            $(this).addClass("selected-folder");
        });

        $('[id="INBOX"] .folder:first').addClass("selected-folder");
    });
    
    var clearMarkersOnGrid = function (){
        $("#imap-folder-table-messages").jqGrid('setGridWidth', $("#tabs-0").width()).trigger("reloadGrid");
    };

    var clearGridTimer = "";

    var resize = function() {
        if (clearGridTimer) {
            window.clearTimeout(clearGridTimer);
        }
        clearGridTimer = window.setTimeout(clearMarkersOnGrid, 200);
    };

    $(window).resize(resize);
    $("#main ui-layout-center").resize(resize);

    $("#imap-folder-table-messages").jqGrid({
        url : API.URL+"/rest/Mail/jqGridListMessages/Folder/INBOX",
        datatype: "json",
        mtype: 'GET',
        colNames:['#',' ', 'De', 'Assunto', 'Data', 'Tamanho'],
        colModel:[
            {name:'id',index:'msg_number', width:45, hidden:true, sortable:false},
            {name:'flags',index:'msg_number',edittype: 'image', width:60, formatter:flags2Class, sortable:false, title :false},
            {name:'from',index:'SORTFROM', width:100, sortable:true, formatter:fromFormatter},
            {name:'subject',index:'SORTSUBJECT', width:245, sortable:true, formatter:subjectFormatter},
            {name:'udate',index:'SORTARRIVAL', width:65, align:"center", sortable:true, formatter: date2Time},
            {name:'size',index:'SORTSIZE', width:55, align:"center", sortable:true, formatter: bytes2Size}
        ],
        rowNum:50,
        jsonReader : {
            root:"rows",
            page: "page",
            total: "total",
            records: "records",
            repeatitems: false,
            id: "0"
        },
        rowList:[10,25,50],
        pager: "#imap-folder-table-pager",
        sortorder: "desc",
        multiselect: true,
        autowidth: true,
        height : '100%',
        loadComplete: function(data) {
                // aplica o contador
                jQuery('.timable').each(function (i) {
                    jQuery(this).countdown({
                        since: new Date(parseInt(this.title)), 
                        significant: 1,
                        layout: 'h&aacute; {d<}{dn} {dl} {d>}{h<}{hn} {hl} {h>}{m<}{mn} {ml} {m>}{s<}{sn} {sl}{s>}', 
                        description: ' atr&aacute;s'
                    });                 
                });
        }
    });
    API.restGET("Mail/Folder/INBOX", function(data){
        var tab = $("#tabs li:first");
        tab.find(".folder-tab-name").html(data[0].cn);
        tab.find(".folder-tab-new-msgs-number").html(data[0].status.Unseen);
        tab.find(".folder-tab-total-msgs-number").html(data[0].status.Messages);
    });
    
}

oMail.prototype.unorphanize = function(root, element) {
    var ok = false;
    var f = 0;
    for (var i=0; i<root.length; i++) {
        if (root[i].id == element.parentFolder) {
            element.children = new Array(); 
            root[i].children.push(element);
            return true;
        } else if (ok = Module.unorphanize(root[i].children, element)) {
            break;
        }
    }
    return ok;
}

oMail.prototype.countUnseenChildren = function(folder){
    if(folder.children.length){
        for(var i=0; i< folder.children.length; i++){
            if(folder.children[i].children.length)
                folder.children[i]['children_unseen'] = (folder.children[i]['children_unseen'] ? folder.children[i]['children_unseen'] : 0) + Module.countUnseenChildren(folder.children[i]);
            
            folder['children_unseen'] = (folder['children_unseen'] ? folder['children_unseen'] : 0)+ (folder.children[i]['children_unseen'] ? folder.children[i]['children_unseen'] : 0) + parseInt(folder.children[i].status.Unseen);            
        }
        return folder['children_unseen'];
    }else{
        return parseInt(folder.status.Unseen);
    }
}

oMail.prototype.changeFolder = function(folder){
    $("#imap-folder-table-messages").jqGrid('setGridParam', {url : API.URL+"/rest/Mail/jqGridListMessages/Folder/"+folder}).trigger("reloadGrid");
    API.restGET("Mail/Folder/"+folder, function(data){
        var tab = $("#tabs li:first");
        tab.find(".folder-tab-name").html(data[0].cn);
        tab.find(".folder-tab-new-msgs-number").html(data[0].status.Unseen);
        tab.find(".folder-tab-total-msgs-number").html(data[0].status.Messages);
    });
}

oMail.prototype.destroy = function()
{
    $('#main').empty();
    $(window).unbind("resize");
    return true;
}

Module = new oMail();
