$(function() {
	$.getJSON('js/portfolio.json', function(info, textStatus) {
		var items = info.items;
		//console.log(items);

		$(jQuery.parseJSON(JSON.stringify(info))).each(function() {  
			var sour	=	this.sour;
			var name	=	this.name;
			var addr	=	this.addr;

			//console.log('Source: ' + sour); console.log('Name: ' + name); console.log('Address: ' + addr); console.log('-----');
			
			$('#contentPortfolio').append('<div class="pure-u-1 pure-u-sm-1-3 pure-u-md-1-4 port"><div class="imgPort" style="background-image:url(' + sour + ');"></div><p>' + name + '<br/><span>' + addr + '</span></p></div>');
		});
	});
	$.get('contact.php', function(form) {
		$('#formContact').append(form);
	});
});