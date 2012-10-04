bytes2Size = function(bytes) {
	var sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
	if (bytes == 0) return 'n/a';
	var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
	var size = (i<2) ? Math.round((bytes / Math.pow(1024, i))) : Math.round((bytes / Math.pow(1024, i)) * 100)/100;
	return  size + ' ' + sizes[i];
}

numberMonths = function(months){
	switch(months){
		case 'Jan':
			return 1;
		case 'Feb':
			return 2;
		case 'Mar':
			return 3;
		case 'Apr':
			return 4;
		case 'May':
			return 5;
		case 'June':
			return 6;
		case 'July':
			return 7;
		case 'Aug':
			return 8;
		case 'Sept':
			return 9;
		case 'Oct':
			return 10;
		case 'Nov':
			return 11;
		case 'Dec':
			return 12;
	}	
}

date2Time = function (timestamp) {
    date = new Date();
    timestamp *= 1000;
    dat = new Date(timestamp);
    if ((date.getTime() - timestamp) < (24*60*60*1000)) {
        return '<span class="timable" title="'+dat.getTime()+'"></span>';
    } else {
        var b = dat.toISOString().split("T")[0].split("-");
        var c = b[2] + "/" + b[1] + "/" + b[0];
        return '<span class="datable">' + c + '</span>';
    }
}

subjectFormatter = function(subject){
    var formatted = $.trim(subject) != "" ?
        (subject.length > 40 ? subject.substring(0, 37)+"..." : subject) :
        "(Sem assunto)";
    return '<span>' + formatted + '</span>';
}

fromFormatter = function(from){
    if(from.length){
        if($.trim(from[0].name) != ""){
            return '<span>' + (from[0].name.length > 40 ?  from[0].name.substring(0, 37)+"..." : from[0].name) + '</span>';    
        }else if($.trim(from[0].mail) != ""){
            return '<span>' + (from[0].mail.length > 40 ?  from[0].mail.substring(0, 37)+"..." : from[0].mail) + '</span>';    
        }    
    }
    else{
        return '<span>(Rascunho)</span>';
    }    
}


flags2Class = function(cellvalue, options, rowObject) {
    var flags = {
        Answered : "reply",
        Attachment: "attachment",
        //Flagged : "forward",
        Draft: "draft",
        Importance:"important",
        Recent: "recent",
        Unseen: "unseen"
    };
    var classes = '<span class="flags '+ (!cellvalue.Unseen ? "seen" : "unseen") +'"></span>';
    classes += '<span class="flags '+ (cellvalue.Attachment ? "attachment" : "") +'"></span>';
    if(cellvalue.Answered && cellvalue.Draft){
        classes += '<span class="flags forward"></span>';      
    }

    for(var i in cellvalue){
        if(i != "Unseen" && i != "Attachment")
            classes += '<span class="flags '+(cellvalue[i] ? flags[i] : "")+'"></span>';
    }
    return classes;
}
