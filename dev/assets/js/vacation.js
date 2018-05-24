	//DETAIL, 결재라인, 결재 POPUP LAYER SHOW/HIDE
	//var isPopup1 = false;
	//var isPopup2 = false;

	$( function(){
	/*
		$('a[href=#]').click( function( $evt ) { $evt.preventDefault() } );

		// 결재로그' 팝업
		$('.approvalList-popup2 .content-title a').bind( 'click', clickPopupApprovalLog );
		$('.decisionApprovalOk-popup3 .pop_top a').bind( 'click', clickPopupApprovalLog );

		// 결재' 팝업
		$('.approvalList-popup2 .content-wrap a').bind( 'click', clickPopupApproval );
		$('.approvalList-popup3 .pop_top a').bind( 'click', clickPopupApproval );
	*/
		$(window).on( 'resize', resizeHandler );
		$(window).trigger( 'resize' );
	});
	/*
	function clickPopupApprovalLog( $evt ) {
		var d = isPopup1 ? 'none' : 'block';
		var z = isPopup1 ? 0 : 987654;
		$('.decisionApprovalOk-popup3').css( {display:d, 'z-index':z} );

		isPopup1 = !isPopup1;
	}

	function clickPopupApproval( $evt ) {
		var d = isPopup2 ? 'none' : 'block';
		var z = isPopup2 ? 0 : 987654;
		$('.approvalList-popup3').css( {display:d, 'z-index':z} );

		isPopup2 = !isPopup2;
	}
	*/

	function resizeHandler() {
			var w = $('#approval .inner-home').width() - $('#approval .left-wrap').width();
			$('#approval .content-wrap').css( {width:w} );
	}

	//검색
	function funSearch(f)
	{
		f.page.value="1";
		f.target="_self";
		f.submit();
	}
	//검색조건 변경
	function selCase(f)
	{
        if(f.mode.value=="" || f.mode.value=="all")
        {
            $('#team_div').css("display","none");
            $('#vacation_div').css("display","none");
            f.team.style.display = "none";
            f.vacation.style.display = "none";

        }
		else if (f.mode.value == "team")
		{
            $('#team_div').css("display","");
            $('#vacation_div').css("display","none");
            f.team.style.display = "";
            f.vacation.style.display = "none";
		}
		else if (f.mode.value == "vacation")
		{
            $('#team_div').css("display","none");
            $('#vacation_div').css("display","");
            f.team.style.display = "none";
            f.vacation.style.display = "";
		}
        else
        {
            $('#team_div').css("display","none");
            $('#vacation_div').css("display","none");
            f.team.style.display = "none";
            f.vacation.style.display = "none";
        }

	}
	//휴가계 읽기
	function funView(doc_no)
	{
		hdnFrame.location.href = '/vacation/vacation_detail.php?doc_no='+doc_no;
	}
	//수정
	function funModify(doc_no)
	{
		location.href = '/approval/approval_modify.php?doc_no='+doc_no;
	}

	//댓글 textarea글자수 200자 제한 스크립트
	function textcounter(field, countfield, maxlimit) { 
		  tempstr = field.value;
		  countfield.value = maxlimit - tempstr.length;
			if (maxlimit - tempstr.length < 0) {
						 alert(maxlimit+"글자를 초과할 수 없습니다.");
						 //document.form.remlen.focus();       //포커스를 이동시키지 않을 경우 글자가 지워짐
						 tempstr = tempstr.substring(0,maxlimit); //포커스 이동 후 글자 자르기
						 field.value = tempstr;
						 countfield.value = maxlimit - tempstr.length;
						 //document.form.reply_contents.focus();  //포커스를 입력상자로 되돌리기
	   }
	}
	//댓글 달기
	function writeReply(){ 
		var frm = document.form2;
		if(frm.reply_contents.value.length < 1){
			alert("내용을 입력해주세요");
			frm.reply_contents.focus();
			return;
		}	
		frm.target = 'hdnFrame';
		frm.type.value = 'write_reply';
		frm.action = '/approval/approval_reply_act.php';
		frm.submit();
	}; 
	//댓글 수정 (댓글번호)
	function mod_Reply(replyno){	
		var frm = document.form2;
		var text = document.getElementById("c_text_"+replyno);

		frm.reply_contents.value = text.innerHTML;
		frm.reply_contents.focus();
		document.all("reply_btn").innerHTML = "<a href='javascript:modifyReply("+replyno+");'><img src=\"/img/btn_popup_modify.gif\" alt=\"\"></a>";
	}
	//댓글 수정 실행
	function modifyReply(replyno){ 
		var frm = document.form2;
		if(frm.reply_contents.value.length < 1){
			alert("내용을 입력해주세요");
			frm.reply_contents.focus();
			return;
		}	
		frm.target = 'hdnFrame';
		frm.action = '/approval/approval_reply_act.php?reply_no='+replyno+'&type=modify_reply';
		frm.submit();
	}
	//댓글 삭제
	function delReply(replyno){ 
		var frm = document.form2;
		if(!confirm("댓글을 삭제 하시겠습니까?")){
			return;
		}
		else
		{		
			frm.target = 'hdnFrame';
			frm.action = '/approval/approval_reply_act.php?reply_no='+replyno+'&type=delete_reply';
			frm.submit();
		}
	}
