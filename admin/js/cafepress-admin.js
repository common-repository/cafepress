(function( $ ) {
	'use strict';
	
	$(function() {
		$("#print-qr-code").click(function() {
			var printContent = $("#qr-code-image");
			if(printContent) {
				var win = window.open('', '');
				win.document.write('<html><head>');
				win.document.write('</head><body >');
				win.document.write('<img id="print-image-element" src="'+printContent.attr('src')+'"/>');
				win.document.write('<script>var img = document.getElementById("print-image-element"); img.addEventListener("load",function(){ window.focus(); window.print(); window.document.close(); window.close(); }); </script>');
				win.document.write('</body></html>');
				win.window.print(); 
				win.close();
			}
		});

		$("#generate-qr-code").click(function(e) {
			var data = {
			  action: "generate_qr_code",
			  security: $('#cafepress_qr_code_metabox_nonce').val(),
			  post_id: $('#post_ID').val()
			};

			$.post(ajaxurl, data, function() {
			  $("#cafepress_qr_code_metabox #generate-qr-code").remove();
			});
		});
	});
	
	// $( window ).load(function() {
	 
	// });
	 

})( jQuery );
