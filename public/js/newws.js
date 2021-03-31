jQuery(document).ready(function ($) {
	inputNumber($('.input-number'));
	/** ******************************
		* Simple WYSIWYG
		****************************** **/
	$('[id^=editControls] a').click(function (e) {
		ID = $(this).attr("q")
		e.preventDefault();
		switch ($(this).data('role')) {
			case 'h1':
			case 'h2':
			case 'h3':
			case 'p':
				document.execCommand('formatBlock', false, $(this).data('role'));
				break;
			default:
				document.execCommand($(this).data('role'), false, null);
				break;
		}

		var textval = $("#editor-" + ID).html();
		$("#editorCopy-" + ID).val(textval);
	});

	$("[id^=editor]").keyup(function () {
		ID = $(this).attr("q")
		var value = $(this).html();
		$("#editorCopy-" + ID).val(value);
	}).keyup();

	$('#checkIt-' + ID).click(function (e) {
		e.preventDefault();
		alert($("#editorCopy-" + ID).val());
	});

	$("#sub_create").click(function(e) {
		e.preventDefault();
		nos = $("#nos_Q").val();
		//$("#cws_form").attr("action", "{{ route('composeworksheet', [0]) }}" + nos);
		//$("#cws_form").submit();

		console.log("ola")
	})
});

function readURL(input, ID) {
	if (input.files && input.files[0]) {
		var reader = new FileReader();
		reader.onload = function (e) {

			$('#imagePreview-' + ID).attr('src', e.target.result);
			$('#imagePreview-' + ID).hide();
			$('#imagePreview-' + ID).fadeIn(650);
		}
		reader.readAsDataURL(input.files[0]);
	}
}

$(function () {
	$(document).on('change', '[id^=img-upld]', function () {
		ID = $(this).attr("id").split("img-upld-")[1]
		console.log("X::" + ID)
		readURL(this, ID);
	});
});

$(function () {
	$(document).on('click', '#sub', function () {
		$("#f").submit();
	});
});

function inputNumber(el) {
	var min = el.attr('min') || false;
	var max = el.attr('max') || false;

	var els = {};

	els.dec = el.prev();
	els.inc = el.next();

	el.each(function () {
		init($(this));
	});

	function init(el) {

		els.dec.on('click', decrement);
		els.inc.on('click', increment);

		function decrement() {
			var value = el[0].value;
			value--;
			if (!min || value >= min) {
				el[0].value = value;
			}
		}

		function increment() {
			var value = el[0].value;
			value++;
			if (!max || value <= max) {
				el[0].value = value++;
			}
		}
	}
}