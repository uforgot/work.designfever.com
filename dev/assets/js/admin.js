var FRAMELINE_WIDTH = 8+2;		// 좌우 세로라인 border 두께

$( function(){
	$(window).on( 'resize', resizeHandler );
	$(window).trigger( 'resize' );

	//$('#popDetail').draggable( {axis:"xy", containment:"parent", drag:dragHandler} );
		$('#popDetail').draggable({ handle: ".title" });
		$('#popDetail .title' ).disableSelection();
//	$('#popDetail').draggable();
});

function resizeHandler() {
		// var w = $('#admin .inner-home').width() - $('#admin .left-wrap').width();
		var w = ( $('#admin .inner-home').width() * 0.95 ) - (FRAMELINE_WIDTH*2) - $('#admin .left-wrap').width();
		$('#admin .content-wrap').css( {width:w} );
}

function dragHandler( $evt, $target ) {
	$('#popDetail').css( {left:$target.position.left, top:$target.position.top} );
}

