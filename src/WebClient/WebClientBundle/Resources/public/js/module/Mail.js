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
        //emptyrecords: 'A pasta nao contem nenhuma mensagem.',
        //viewrecords: true
    });
}




oMail.prototype.destroy = function()
{
    $('#main').empty();
    return true;
}

Module = new oMail();
