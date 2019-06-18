function changeLang (lang) {
	window.location = "controller.php?p_action=changelanglogin&u_lang="+lang;
}

function view_relations(p_relation_id, p_parent_inst_id) {
	$("#rel"+p_relation_id).load("controller.php?p_action=ajax_view_relations&p_relation_id="+p_relation_id+"&p_parent_inst_id="+p_parent_inst_id);
}

function deleterelation(id) {
	$.post("controller.php", { p_action: "delete_relations_parent", id_rel: id } );
	$("li#parent_rel"+id).hide();
}

function changeTab(name, notab, element, tabid) {
	$('div#taulatab div.amagable').hide();
	$('div#'+name).show();
	$('input#p_tab').val(name);
	$('ul#rowtab li').attr('class',notab);
	element.attr('class','selected');

	$(".link_tabs").attr('href', function(i,a){
		return a.replace( /(p_tab=)[0-9]+/ig, '$1'+tabid);
	});
	$(".input_tabs").val(tabid);
}

function select_unselect_all() {
	if (document.getElementById("select_all").checked==false) var accio=0;
	else var accio=1;
	for (var i=0;i<document.getElementById("num_rel").value;i++) {
		if (accio==1) document.getElementById("rel_chb_"+i).checked=true;
		else document.getElementById("rel_chb_"+i).checked=false;
	}
}

function select_unselect_del_all() {
	if (document.getElementById("select_all").checked==false) var accio=0;
	else var accio=1;
	for (var i=0;i<document.getElementById("num_del").value;i++) {
		if (accio==1) document.getElementById("del_chb_"+i).checked=true;
		else document.getElementById("del_chb_"+i).checked=false;
	}
}

function changestatusimg(rel) {
	var st=$(rel).val();
	if(st=="O") {
		$("#statusimg").attr("class",'status publish');
	}
	else if(st=="P") {
		$("#statusimg").attr("class",'status pending');
	}
	else {
		$("#statusimg").attr("class",'status revised');
	}
}

function make_magic(code, info, return_to, list) {
	info = 'ajax='+code+'&'+info;

	$.ajax({
		url: window.APP_BASE+'/ajax_actions',
		type: "POST",
		data: info,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
		beforeSend: function (jqXHR, settings) {
			list.removeClass('alert_error alert_right hidden').addClass('saving');
			$('.alert.list div p').html('Saving ...');
		},
		success: function(data) {
			if(data != '') {
				$(return_to).html(data);
				list.removeClass('saving').addClass('alert_right');
			} else {
				$(return_to).html(data);
				list.removeClass('saving').addClass('alert_error');
			}
		}
	});
}

function save_list(instance_id, sortable, list) {
	var info = 'instance_id=' + instance_id + '&ordered=' + sortable;
	make_magic('ajax_order', info, '.alert.list div p', list);
}
