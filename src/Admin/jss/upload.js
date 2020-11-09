$(function() {
	$("#upload-popup").on("click", "#action_crop", function(event) {
		event.preventDefault();
		$.ajax({
			dataType: "json",
			type: "POST",
			url: "../crop",
			data: {
				filename: $("#crop_p_file").val(),
				p_width: $("#p_width").val(),
				p_height: $("#p_height").val(),
				x: $("#x").val(),
				y: $("#y").val(),
				width: $("#width").val(),
				height: $("#height").val()
			},
			cache: false,
			success: function(json) {
				setNewImgValue($("#input_name").val(), json.file);
			},
			fail: function(json) {
				parent.document.location="/";
			}
		});
	});
	$("#upload-popup").on("click", "#action_resize", function(event) {
		$.ajax({
			dataType: "json",
			type: "POST",
			url: "../crop",
			data: {
				filename: $("#crop_p_file").val(),
				p_width: $("#p_width").val(),
				p_height: $("#p_height").val()
			},
			cache: false,
			success: function(json) {
				setNewImgValue($("#input_name").val(), json.file);
			},
			fail: function(json) {
				parent.document.location="/";
			}
		});
	});
	$("#upload-popup").on("click", "#action_leave_as", function(event) {
		setNewImgValue($("#input_name").val(), $("#crop_p_file").val());
	});
});

function getMultiplier(width, height) {
	if (width > 540) {
		return (540/width);
	}
	else if (height<width && height > 700) {
		return (700/height);
	}

	return 1;
}

function crop_actions(id, data, type) {
	if (data.status=="crop") {
		$("#upload-popup").html(data.html);

		if (data.width == data.p_width && data.height == data.p_height) {
			setNewImgValue(id, data.file);
		}
		else {
			/*Calcular multiplicador*/
			multiplier_original = getMultiplier(data.width, data.height);
			multiplier_crop = getMultiplier(data.p_width, data.p_height);
			$(".preview-container").css("width", (data.p_width * multiplier_crop));
			$(".preview-container").css("height", (data.p_height * multiplier_crop));

			$("#image_to_crop").Jcrop({
				bgFade: true,
				bgOpacity: .4,
				bgColor: "#C8C33A",
				onChange: showPreview,
				onSelect: showPreview,
				aspectRatio: data.p_width / data.p_height,
				multiplier_original: multiplier_original,
				multiplier_crop: multiplier_crop
			}, function () {
				// Use the API to get the real image size
				var bounds = this.getBounds();
				boundx = bounds[0];
				boundy = bounds[1];
				// Store the API in the jcrop_api variable
				jcrop_api = this;
			});

			$("#fosc_upload").show();
			$("#upload-popup").show();
		}
	}
	else if (data.status=="ok") {
		if (type=='image') setNewImgValue(id, data.file);
		else setJustInput(id, data.file);
	}
	else if (data.status=="ko") {
		parent.document.location="/";
	}
}

function getPageSize() {
	var xScroll, yScroll;

	if (window.innerHeight && window.scrollMaxY) {
		xScroll = document.body.scrollWidth;
		yScroll = window.innerHeight + window.scrollMaxY;
	} else if (document.body.scrollHeight > document.body.offsetHeight){ // all but Explorer Mac
		xScroll = document.body.scrollWidth;
		yScroll = document.body.scrollHeight;
	} else { // Explorer Mac...would also work in Explorer 6 Strict, Mozilla and Safari
		xScroll = document.body.offsetWidth;
		yScroll = document.body.offsetHeight;
	}

	var windowWidth, windowHeight;
	if (self.innerHeight) {	// all except Explorer
		windowWidth = self.innerWidth;
		windowHeight = self.innerHeight;
	} else if (document.documentElement && document.documentElement.clientHeight) { // Explorer 6 Strict Mode
		windowWidth = document.documentElement.clientWidth;
		windowHeight = document.documentElement.clientHeight;
	} else if (document.body) { // other Explorers
		windowWidth = document.body.clientWidth;
		windowHeight = document.body.clientHeight;
	}

	// for small pages with total height less then height of the viewport
	if(yScroll < windowHeight){
		pageHeight = windowHeight;
	} else {
		pageHeight = yScroll;
	}

	// for small pages with total width less then width of the viewport
	if(xScroll < windowWidth){
		pageWidth = windowWidth;
	} else {
		pageWidth = xScroll;
	}


	arrayPageSize = new Array(pageWidth,pageHeight,windowWidth,windowHeight)
	return arrayPageSize;
}

function getPageScroll() {
	var yScroll;

	if (self.pageYOffset) {
		yScroll = self.pageYOffset;
	} else if (document.documentElement && document.documentElement.scrollTop){ // Explorer 6 Strict
		yScroll = document.documentElement.scrollTop;
	} else if (document.body) {// all other Explorers
		yScroll = document.body.scrollTop;
	}

	arrayPageScroll = new Array('',yScroll);
	return arrayPageScroll;
}

function showPreview(c) {
	$preview = $('#preview'),
	$pcnt = $('#preview .preview-container'),
	$pimg = $('#preview .preview-container img'),

	xsize = $pcnt.width(),
	ysize = $pcnt.height();

	if (parseInt(c.w) > 0) {
		var rx = xsize / c.w;
		var ry = ysize / c.h;

		$("#cropped").css({
			width: Math.round(rx * boundx) + 'px',
			height: Math.round(ry * boundy) + 'px',
			marginLeft: '-' + Math.round(rx * c.x) + 'px',
			marginTop: '-' + Math.round(ry * c.y) + 'px'
		});
	}
	/*console.log('X:'+c.x/multiplier_original+' Y:'+c.y/multiplier_original+' W:'+c.w/multiplier_original+' H:'+c.h/multiplier_original+' TW:'+(c.x/multiplier_original+c.w/multiplier_original)+' TH:'+(c.y/multiplier_original+c.h/multiplier_original));*/
	$('#x').val(c.x/multiplier_original);
    $('#y').val(c.y/multiplier_original);
    $('#width').val(c.w/multiplier_original);
    $('#height').val(c.h/multiplier_original);
}
