	$( function(){
		$(window).on( 'resize', resizeHandler );
		$(window).trigger( 'resize' );
	});

	function resizeHandler() {
			var w = $('#admin .inner-home').width() - $('#admin .left-wrap').width();
			$('#admin .content-wrap').css( {width:w} );
	}
