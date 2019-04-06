
function checkPassword(e) {
	if(e.target.type=='password') {
		//console.log(e.target);
		if(e.target.value != e.target.previousElementSibling.value) {
			var TD = $(e.target).closest('td')[0];
			$(TD).addClass("error").removeClass("changing");
			$("#"+TD.id+"_e").text(e.target.previousElementSibling.attributes.placeholder.value+" ≠ "+e.target.attributes.placeholder.value);
			return false;
		} else {
			var TD = $(e.target).closest('td')[0];
			$(TD).removeClass("error");
			$("#"+TD.id+"_e").text('');
		}
	}
	return true;
}

var changesMade = 0;

function changeValue(e) {
	if(!checkPassword(e)) {
		return;
	}
	var mytd = e.target.parentNode;
	var myRow = $(e.target).closest('tr')[0];
	var myTable = $(e.target).closest('table')[0];
	
	$("#"+mytd.id+"_e").text('');
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
			$("#"+msg.id+"_e").text('');
			changesMade++;
		} else {
			$("#"+msg.id).addClass('error').removeClass('changing');
			$("#"+msg.id+"_e").text(msg.error);
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
	var myTextExtraction = function(node, table, cellIndex) {
	  var t = $(node).text().replace(/\s+/g, ' ').trim();
	  if(t.length==0) {
			var nodes = $(node).find('input');
			if(nodes.length) {
				t = nodes[0].value;
			}
		}
	  return t;
	}
	
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


	//Add interaction to buttons and input fields:

	$('#search').keyup(function(){
		// Search Text
		var search = $(this).val().toUpperCase();
		// Hide all table tbody rows
		$('table tbody tr:not(.newrow)').hide();
		//Search the table:
		$('table tbody td').each(function(){
			var searchT = $(this).text();
			//console.log(this);
			searchT += ':'+$(this.firstElementChild).val();
			
			searchT = searchT.toUpperCase();
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
	
	$(".checkpw").change(function(e){ 	if(!checkPassword(e)) {	return;	}	});

	$('.newrow > td > div > input').change(function() {
		var myid = $(this).closest('tr')[0].id;
		$('#'+myid).removeClass('error');
	});
	
	
	$( ".createnew" ).click(createNewRow);

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
