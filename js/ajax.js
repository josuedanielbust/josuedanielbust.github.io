$(function() {
    $.getJSON('js/cvdata.json', function(info, textStatus) {
	$(jQuery.parseJSON(JSON.stringify(info.exp))).each(function() {
	    var what	  =	this.what;
	    var where	  =     this.where;
	    var place	  =	this.place;
	    var date      =     this.date;
	    var image     =     this.image;
	    
	    $('#exp').append('<div class="grid_12"><div class="cv-job"><div class="grid_9"><p class="cv-position">' + what + '<span> en ' + where + '</span></p><p class="cv-place">' + place + '</p><p class="cv-date">' + date + '</p></div><div class="grid_3 omega"><img src="/images/jobs/' + image + '" alt="' + where + '" class="cv-image" width="auto"></div></div></div>');
	});
	$(jQuery.parseJSON(JSON.stringify(info.est))).each(function() {
	    var what	  =	this.what;
	    var where	  =     this.where;
	    var place	  =	this.place;
	    var date      =     this.date;
	    var image     =     this.image;
	    
	    $('#est').append('<div class="grid_12"><div class="cv-job"><div class="grid_9"><p class="cv-position">' + what + '<span> en ' + where + '</span></p><p class="cv-place">' + place + '</p><p class="cv-date">' + date + '</p></div><div class="grid_3 omega"><img src="/images/jobs/' + image + '" alt="' + where + '" class="cv-image" width="auto"></div></div></div>');
	});
    });
});
