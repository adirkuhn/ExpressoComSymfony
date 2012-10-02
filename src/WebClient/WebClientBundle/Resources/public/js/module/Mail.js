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
    /*
    $("#imap-folder-table-messages").jqGrid({
    	data : [],
    	datatype: "local",
    	colNames:['#',' ', 'De', 'Assunto', 'Data', 'Tamanho'],
        colModel:[
            {name:'msg_number',index:'msg_number', width:45, hidden:true, sortable:false},
            {name:'flags',index:'msg_number',edittype: 'image', width:60, formatter:flags2Class, sortable:false, title :false},
            {name:'from.name',index:'SORTFROM', width:100, sortable:true, formatter:subjectFormatter},
            {name:'subject',index:'SORTSUBJECT', width:245, sortable:true},
            {name:'timestamp',index:'SORTARRIVAL', width:65, align:"center", sortable:true, formatter: date2Time},
            {name:'size',index:'SORTSIZE', width:55, align:"center", sortable:true, formatter: bytes2Size}
        ],
        rowNum:50,
        rowList:[10,25,50],
        pager: "#imap-folder-table-pager",
        sortorder: "desc",
        multiselect: true,
        autowidth: true,
        height : '100%'
    });
    */
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
            $(Module).addClass("selected-folder");
        });

        $('[id="INBOX"] .folder:first').addClass("selected-folder");
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


oMail.prototype.destroy = function()
{
    $('#main').empty();
    return true;
}

Module = new oMail();
