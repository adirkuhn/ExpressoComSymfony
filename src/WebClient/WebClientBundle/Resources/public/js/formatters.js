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
	dat = new Date(timestamp);
	if ((date.getTime() - timestamp) < (24*60*60*1000)) {
		return '<span class="timable" title="'+dat.getTime()+'"></span>';
	} else {
		date = new Date(timestamp);
		var b = date.toString("dd/MM/yyyy");
		return '<span class="datable">' + b + '</span>';
	}
}

subjectFormatter = function(subject){
    var formatted = subject ?
        (subject.length > 40 ? subject.substring(0, 37)+"..." : subject) :
        "(Rascunho)";
    return '<span>' + formatted + '</span>';
}

flags2Class = function(cellvalue, options, rowObject) {
    var classes = '';
    cellvalue = cellvalue.split(',');
    cell = {
        Unseen: parseInt(cellvalue[0])  ? 'Unseen' : 'Seen',
        Answered: parseInt(cellvalue[1]) ? 'Answered' : (parseInt(cellvalue[2]) ? 'Forwarded' : ''),
        Flagged: parseInt(cellvalue[3]) ? 'Flagged' : '',
        Recent: parseInt(cellvalue[4])  ? 'Recent' : '',
        Draft: parseInt(cellvalue[5]) ? 'Draft' : ''
    };
    for(var flag in cell){
        classes += '<span class="flags '+ (cell[flag]).toLowerCase() + '"' + (cell[flag] != "" ? 'title="'+ cell[flag]+'"' : '')+'> </span>';
    }

    // REFAZER LABELS E ACOMPANHAMENTO
    /*if(rowObject.labels){
        var titles = [];
        var count = 0;
        for(i in rowObject.labels){
            titles[count] = " "+rowObject.labels[i].name;
            count++;
        }
        titles = titles.join();
        classes += '<span class="flags labeled" title="'+titles+'"> </span>';
    }else{
        classes += '<span class="flags"> </span>';
    }

    if(rowObject.followupflagged){
        if(rowObject.followupflagged.followupflag.id < 7){
            var nameFollowupflag = get_lang(rowObject.followupflagged.followupflag.name);
        }else{
            var nameFollowupflag = rowObject.followupflagged.followupflag.name;
        }
        if(rowObject.followupflagged.isDone == 1){
            classes += '<span class="flags followupflagged" title="'+nameFollowupflag+'" style="background:'+rowObject.followupflagged.backgroundColor+';"><img style=" margin-left:-3px;" src="../prototype/modules/mail/img/flagChecked.png"></span>';
        }else{
            classes += '<span class="flags followupflagged" title="'+nameFollowupflag+'" style="background:'+rowObject.followupflagged.backgroundColor+';"><img src="../prototype/modules/mail/img/flagEditor.png"></span>';
        }

    }*/

    return classes;
}
