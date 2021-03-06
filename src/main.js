// Copyright 2018 Menne Kamminga <kamminga DOT m AT gmail DOT com>. All rights reserved.
// Use of this source code is governed by a BSD-style
// license that can be found in the LICENSE file.

function checkPassword(e) {
	if(e.target.type=='password') {
		//console.log(e.target);
		if(e.target.value != e.target.previousElementSibling.value) {
			var TD = $(e.target).closest('td')[0];
			$(TD).addClass("error").removeClass("changing");
			$("#"+TD.id+" .cellerrortext").text(e.target.previousElementSibling.attributes.placeholder.value+" ≠ "+e.target.attributes.placeholder.value);
			return false;
		} else {
			var TD = $(e.target).closest('td')[0];
			$(TD).removeClass("error");
			$("#"+TD.id+" .cellerrortext").text('');
		}
	}
	return true;
}

var changesMade = 0;

function getLog(mydate) {
	$.ajax({
	  method: "POST",
	  url: "index.php",
	  data: { 
			log: mydate
	  }
	}).done(function( msg ) {

		//var theadrow = 
		var tbody = $('#auditlog>tbody');
		$(tbody).empty();
		$.each(msg.entries, function(i, entry) {
			var _div = $('<tr>');
			$('#auditlog>thead>tr>th').filter(function(){
				var _span = $('<td>').addClass('_prop_'+this.attributes.name.value);
				var idx = this.attributes.name.value;
				var prop = entry[idx];
				if(prop==null){
					if(typeof entry[0] === 'object' && entry[0]!=null && idx=='value') {
						prop = entry[0];
					} else {
						if(idx=='value' && typeof entry['group'] !=='undefined' ) {
							prop = entry['group'];
						}
						if(idx=='dn'&& typeof entry['member'] !=='undefined') {
							prop = entry['member'];
						}
					}
				}
				
				if(typeof prop === 'object' && prop!=null) {
					//console.log(entry);
					$.each(prop, function(ii, propprop) {
						var D = $('<div>').addClass('_prop_'+ii).addClass('clear');
						$('<span>').addClass('left').text(ii).appendTo(D);
						$('<span>').addClass('right').text(propprop).appendTo(D);
						//text(ii+': '+propprop).appendTo(_span);
						$(D).appendTo(_span);
					});
				} else {
					$(_span).text(prop);
				}		
				 
				if( $(_span).text().trim().length==0) {
					$(_span).text('...');
				}
				$(_span).appendTo(_div);
			});
			tbody.append(_div);
		});
	}).fail(function( jqXHR, textStatus ) {
		alert( "Server error: " + textStatus );
	});

}

function changeValue(e) {
	if(!checkPassword(e)) {
		return;
	}
	var mytd = e.target.parentNode;
	var myRow = $(e.target).closest('tr')[0];
	var myTable = $(e.target).closest('table')[0];
	
	$("#"+mytd.id+" .cellerrortext").text('');
	$(mytd).addClass("changing").removeClass('changed');
	
	var _dn = myRow.attributes.__dn.value;
	var _name = e.target.name;
	var _value = e.target.value;
	if(name.trim().length==0) {
		var colidx = $(mytd).index()+1;
		_name = $(myTable).find('th:nth-child('+colidx+')')[0].attributes.name.value;
	}
	
	$.ajax({
	  method: "POST",
	  url: "index.php",
	  data: { 
		dn: _dn,
		attribute: _name,
		value: _value,
		id: mytd.id
	  }
	}).done(function( msg ) {
		if(msg.status=="ok") {
			$("#"+msg.id).addClass('changed').removeClass('changing').removeClass('error');
			$("#"+msg.id+" .cellerrortext").text('');
			changesMade++;
		} else {
			$("#"+msg.id).addClass('error').removeClass('changing');
			$("#"+msg.id+" .cellerrortext").text(msg.error);
		}
	}).fail(function( jqXHR, textStatus ) {
		alert( "Server error: " + textStatus );
	});
}

function createNewRow(e) {
	var myid = $(this).closest('tr')[0].id;
	
	var valid = true;
	var etext = '';
	$('#'+myid+'.error > td').filter(function() {
		valid = false;
		etext += $(this).text().trim();
	});
	$('#'+myid+' > td.error').filter(function() {
		valid = false;
		etext += $(this).text().trim();
	});
	if(!valid) {
		alert(etext);
		return;
	}
	//console.log(myid);
	//console.log($(this).closest('tr')[0].id);
	var AA = {
		dn: $('#'+myid)[0].attributes.__dn.value,
		id: myid,
		attributes: {},
	};
	$('#'+myid+' > td > div > input').filter(function() {			
		if(this.value.trim().length) {
			AA.attributes[this.name] = this.value.trim(); 
		}
	});
	$('#'+myid).addClass('changing').removeClass('error');
	$.ajax({
	  method: "POST",
	  url: "index.php",
	  data: AA
	}).done(function( msg ) {
		var myid = msg.id;
		if(msg.status=="ok") {
			$('#'+myid).addClass('changed').removeClass('changing');
			location.reload();
		} else {
			$('#'+myid).addClass('error').removeClass('changing');
			$('#'+myid+' > td:first .cellerrortext').text(msg.error);
			alert(msg.error);
		}
		
	}).fail(function( jqXHR, textStatus ) {
		alert( "Server error: " + textStatus );
	});
	
}

function myTextExtraction(node, table, cellIndex) {
	var t = $(node).text().replace(/\s+/g, ' ').trim();
	if(t.length==0) {
		var nodes = $(node).find('input');
		if(nodes.length) {
			t = nodes[0].value;
		}
	}
	return t;

}

function openDetails() {
	var value = $(this).text();
	var _TR = $(this).closest('tr')[0];
	var _dn = _TR.attributes.__dn.value;
	console.log(_dn);
	console.log(value);
	var _ul  = $('.nav-tabs');
	var _li = $('<li>').appendTo(_ul);
	var _a = $('<a>').text(value).appendTo(_li);
}

$(document).ready(function () {

	//Start a keepalive on an interval:
	setInterval(function(){ 
		$.ajax({
		  method: "POST",
		  url: "index.php",
		  data: { 
			keepalive: true
		  }
		}).done(function( msg ) {
		}).fail(function( jqXHR, textStatus ) {
			alert( "Server error: " + textStatus );
			location.reload();
		});	
	}, 30000);
	
	

	//Install the table sorter:
	$(".sorted").tablesorter({
		cancelSelection:false,
		textExtraction:myTextExtraction,
		tabIndex:false
	});
	
	
	//Show the proper requested page:
	/*$('.sorted').hide();*/
	var hash = $(location).attr('hash');
	if(hash) {
		$("#"+hash.substr(1)).show();
		$(".nav-tabs > li").removeClass("active");
		$(".nav-tabs > li").filter(function(){
			var closesta = $(this).find('a')[0];
			if("#"+closesta.name==hash) {
				$(this).addClass("active");
			}
		});	
	} else {
		$('#users').show();
	}
	

	//Make sure rights table has rotate class assigned:
	for(a=3;a<100;a++) {
		$('#rights > thead > tr > th:nth-child('+a+')').addClass("rotate");
		$('#groups > thead > tr > th:nth-child('+a+')').addClass("rotate");
		//$('#rights > tbody > tr > td:nth-child('+a+')').addClass("group");
	}
	$('.rotate > div').wrapInner('<span></span>');
	
	

	//Highligt the column we are currently hovering:
	$('td').hover(function(){
		var col = $(this).index()+1;
		//console.log(col);
		$('th:nth-child('+col+')').addClass('columnselected');
	},function(){
		var col = $(this).index()+1;
		//console.log(col);
		$('th:nth-child('+col+')').removeClass('columnselected');
	});

	//Make sure viewport is properly sized:
	var resizeFunc = function() {
		$('#jp-container').height($(window).height()-$('#jp-container').offset().top*1.4);
	};
	$(window).resize(resizeFunc);
	resizeFunc();


	//console.log($('#audit_date'));
	getLog($('#audit_date').val());

	//Add interaction to buttons and input fields:

	$('#search').change(function(){
		// Search Text
		var search = $(this).val().toUpperCase();
		// Hide all table tbody rows
		$('table tbody tr:not(.newrow)').hide();
		//Search the table:
		$('table tbody td').each(function(){
			var searchT = myTextExtraction(this,null,null).toUpperCase();
			if(searchT.indexOf(search) >-1) {
				$(this).closest('tr').show();
			}
		});
	});

	$('.sorted_bt').click(function(){
		location.hash = "#"+this.name;
		$(".nav-tabs > li").removeClass("active");
		var parentli = $(this).closest('li')[0];
		$(parentli).addClass("active");
		$('.sorted').hide();
		$("#"+this.name).show();
		$("#tab-title").text(this.innerText);
	});

	
	$('#refresh_bt').click(function(){ location.reload(); });
	$('#audit_date').change(function(){ getLog(this.value); });
	
	$(".checkpw").change(function(e){ 	if(!checkPassword(e)) {	return;	}	});

	$('.newrow > td > div > input').change(function() {
		var myid = $(this).closest('tr')[0].id;
		$('#'+myid).removeClass('error');
	});
	
	
	$( ".createnew" ).click(createNewRow);
	$( ".detail_bt" ).click(openDetails);

	$( "input.editable" ).change(changeValue);

	$( "div.editable" ).click(function (e){
		changeValue(e);
		var t = $(e.target).text();
		if(t.trim().length==0) {
			$(e.target).text('✔');
		} else {
			$(e.target).text('');
		}
	});
	
});


function dumpUnusedStyles() {
	var style = document.styleSheets[0];
	for(a=0;a<style.cssRules.length;a++) {
		var rule = style.cssRules[a];
		//console.log(rule);
		if(document.querySelector(rule.selectorText)===null) {
			console.log(rule.selectorText);
		}
	}
}
