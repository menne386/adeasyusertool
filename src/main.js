
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


$(document).ready(function () {

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
	
	setInterval(function(){ 
		$('#jp-container').height($(window).height()-$('#jp-container').offset().top*1.4);
	}, 3000);

	$( ".editable" ).change(function (e) {
		if(!checkPassword(e)) {
			return;
		}
		$("#"+e.target.parentNode.id+"_e").text('');
		$(e.target.parentNode).addClass("changing");
		var _dn = $(e.target).closest('tr')[0].attributes.__dn.value;	
		$.ajax({
		  method: "POST",
		  url: "index.php",
		  data: { 
			dn: _dn,
			attribute: e.target.name,
			value: e.target.value.trim(),
			id: e.target.parentNode.id
		  }
		}).done(function( msg ) {
			//console.log(msg);
			if(msg.status=="ok") {
				$("#"+msg.id).addClass('changed').removeClass('changing').removeClass('error');
				$("#"+msg.id+"_e").text('');
				changesMade++;
			} else {
				$("#"+msg.id).addClass('error').removeClass('changing');
				$("#"+msg.id+"_e").text(msg.error);
			}
			
		}).fail(function( jqXHR, textStatus ) {
			alert( "Server fout: " + textStatus );
		});
	});

	var myTextExtraction = function(node, table, cellIndex) {
	  // extract data from markup and return it
	  // originally: return node.childNodes[0].childNodes[0].innerHTML;
	  //console.log(node);
	  var t = $(node).text().replace(/\s+/g, ' ').trim();
	  if(t.length==0) {
		var nodes = $(node).find('input');
		//console.log(nodes);
		if(nodes.length) {
			t = nodes[0].value;
		}
		//console.log("'"+t+"'");  
		//t="aaa";
	  }
	  //console.log(t);  
	  
	  return t;
	}
	
	
	$(".sorted").tablesorter({
		cancelSelection:false,
		textExtraction:myTextExtraction,
		tabIndex:false
	});
	
	
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
	
	$('.sorted').hide();
	
	var hash = $(location).attr('hash');
	if(hash) {
		$(hash).show();
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
	
	$('.sorted_bt').click(function(){
		location.hash = "#"+this.name;
		$(".nav-tabs > li").removeClass("active");
		var parentli = $(this).closest('li')[0];
		$(parentli).addClass("active");
		//console.log(this);
		$('.sorted').hide();
		
		
		$("#"+this.name).show();
		
		$("#tab-title").text(this.innerText);
		if(changesMade) { 
			location.reload();
		}
	});

	$('#refresh_bt').click(function(){
	
		location.reload();
	});
	
	for(a=3;a<100;a++) {
		$('#rights > thead > tr > th:nth-child('+a+')').addClass("rotate");
		//$('#rights > tbody > tr > td:nth-child('+a+')').addClass("group");
	}
	$('.rotate > div').wrapInner('<span></span>');
	
	
	$(".checkpw").change(function(e){
		if(!checkPassword(e)) {
			return;
		}		
	});
	$('.newrow > td > div > input').change(function() {
		if(this.type!='password') {
			var myid = $(this).closest('tr')[0].id;
			$('#'+myid).removeClass('error');
		}
	});
	$( ".createnew" ).click(function (e) {
		//console.log(e);
		var myid = $(this).closest('tr')[0].id;
		
		var valid = true;
		var etext = '';
		$('#'+myid+'.error > td').filter(function() {
			//if()
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
			//console.log(this);
		});
		//console.log(AA);
		$('#'+myid).addClass('changing').removeClass('error');
		$.ajax({
		  method: "POST",
		  url: "index.php",
		  data: AA
		}).done(function( msg ) {
			var myid = msg.id;
			//console.log(msg);
			if(msg.status=="ok") {
				$('#'+myid).addClass('changed').removeClass('changing');
				//alert("Aangemaakt");
				location.reload();
				
			} else {
				$('#'+myid).addClass('error').removeClass('changing');
				$('#'+myid+' > td:first .cellerrortext').text(msg.error);
				alert(msg.error);
			}
			
		}).fail(function( jqXHR, textStatus ) {
			alert( "Server fout: " + textStatus );
		});
		
	});
	
	$('td').hover(function(){
		var col = $(this).index()+1;
		//console.log(col);
		$('th:nth-child('+col+')').addClass('columnselected');
	},function(){
		var col = $(this).index()+1;
		//console.log(col);
		$('th:nth-child('+col+')').removeClass('columnselected');
	});
	
	$('#jp-container').height($(window).height()-$('#jp-container').offset().top*1.4);
});
