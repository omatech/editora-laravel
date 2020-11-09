$(document).ready(function() {
	/*CALENDARI*/
	$(".datepicker").datepicker({
		constrainInput: false,
		showOn: "button",
		buttonImage: window.location.pathname.replace(/\/?$/, '/') + "../images/calendari.png",
		buttonImageOnly: true,
		dateFormat: 'dd/mm/yy',
		dayNamesMin: ['D', 'L', 'M', 'M', 'J', 'V', 'S'],
		firstDay: 1,
		closeText: 'Cerrar',
		prevText: '&#x3c;',
		nextText: '&#x3e;',
		currentText: 'Hoy',
		monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
		'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
		monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun',
		'Jul','Ago','Sep','Oct','Nov','Dic'],
		dayNames: ['Domingo','Lunes','Martes','Mi&eacute;rcoles','Jueves','Viernes','S&aacute;bado'],
		dayNamesShort: ['Dom','Lun','Mar','Mi&eacute;','Juv','Vie','S&aacute;b'],
		weekHeader: 'Sm',
		isRTL: false,
		showMonthAfterYear: false,
		yearSuffix: ''
	});


	$('selector').datepicker({firstDayOfWeek:1}); //empezamos la semana por lunes
	// the input with an id of "date" will have a date picker that lets you pick any day in the future...
	$('input#date1').datepicker({startDate:'01/01/2007'});
	//$('input#date_s1').datepicker({startDate:'01/01/2007'});
	// ...and the input with an id of "date2" will have a date picker that lets you pick any day between the 02/11/2006 and 13/11/2006
	$('input#date2').datepicker({startDate:'01/01/2007'});
	//$('input#date_s2').datepicker({startDate:'01/01/2007'});
	$('input.date3').datepicker({startDate:'01/01/2007'});



    $(".arrow a").bind('click', function () {
        $(this).parents('.box').toggleClass('box_hide');
    });


	/* Clone images */
	$(".clone_drag_img")
	.draggable({
		revert: true,
		start: function() { $(this).css('z-index',5); },
		stop: function() { $(this).css('z-index',1); }
	})
	.droppable({
		hoverClass: "draggablehover",
		drop: function(event, ui) {
			var id_destination = $(this).attr('id').substr(3);
			var id_source = ui.draggable[0]['id'].substr(3);
			$.ajax({
				dataType: "json",
				type: "POST",
				url: "../draganddrop_image",
				data: {
					input_name: id_destination,
					filename: $("#"+id_source).val(),
					p_width: $("#wt"+id_destination).html(),
					p_height: $("#ht"+id_destination).html(),
					o_width: $("#w"+id_source).html(),
					o_height: $("#h"+id_source).html()
				},
				cache: false,
				success: function(json) {
					crop_actions(id_destination, json, 'image');
				},
				fail: function(json) {
					parent.document.location="/";
				}
			});
		}
	});
});

function getSelection(ta) {
	var bits = [ta.value,'','',''];
    if(document.selection) { //explorer
		var vs = '#$%^%$#';
        var tr=document.selection.createRange()

        if(tr.parentElement()!=ta) return null;
        bits[2] = tr.text;
        tr.text = vs;
        fb = ta.value.split(vs);
        tr.moveStart('character',-vs.length);
        tr.text = bits[2];
        bits[1] = fb[0];
        bits[3] = fb[1];
    }
	else { //FF
		if(ta.selectionStart == ta.selectionEnd) return null;

		bits[1]=(ta.value).substring(0,ta.selectionStart);
		bits[2]=(ta.value).substring(ta.selectionStart,ta.selectionEnd);
		bits[3]=(ta.value).substring(ta.selectionEnd,(ta.value).length);
	}

	return bits;
}

function matchPTags(str) {
	str = ' ' + str + ' ';
    ot = str.split(/\<[B|U|I].*?\>/i);
    ct = str.split(/\<\/[B|U|I].*?\>/i);
    return ot.length==ct.length;
}

function addPTag(ta,pTag) {
	bits = getSelection(ta);
    if(bits) {
		if(!matchPTags(bits[2])) {
			alert('\t\tInvalid Selection\nSelection contains unmatched opening or closing tags.');
            return;
        }
        ta.value = bits[1] + '<' + pTag + '>' + bits[2] + '</' + pTag + '>' + bits[3];
    }
}

function view_object(action, obj_id,w,h) {
    w2 = screen.availWidth-20;
    h2 = screen.availHeight;
    var leftPos = (w2-w)/8, topPos = (h2-h)/8;

    eval('window.open("/'+action+'/?inst_id='+obj_id+'","window_loc","top='+topPos+', left='+leftPos+', width='+w+', height='+h+',resizable=NO,scrollbars=YES")')
}

function setNewImgValue(id, value) {
	d = new Date();
	$("#"+id).val(value);
	$("#img"+id).attr("src", value+"?"+d.getTime());
	$("#img"+id).load(function () {
		$("#w"+id).html(document.getElementById("img"+id).naturalWidth);
		$("#h"+id).html(document.getElementById("img"+id).naturalHeight);
	});

	hideImgPopup();
}

function setJustInput(id, value) {
	$("#"+id).val(value);
	hideImgPopup();
}

function hideImgPopup() {
	$("#upload-popup").hide();
	$("#browse-popup1").hide();
	$("#fosc_upload").hide();
}
