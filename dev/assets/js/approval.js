	//DETAIL, 결재라인, 결재 POPUP LAYER SHOW/HIDE
	//var isPopup1 = false;
	//var isPopup2 = false;
	var FRAMELINE_WIDTH = 8+2;		// 좌우 세로라인 border 두께
	//var LAYERPOPUP = $('.approval-popup2');

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

		//$('#popDetail').draggable( {axis:"xy", containment:"parent", drag:dragHandler} );
		$('#popDetail').draggable({ handle: ".title" });
		$('#popDetail .title' ).disableSelection();
		
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
			var w = ( $('#approval .inner-home').width() * 0.95 ) - (FRAMELINE_WIDTH*2) - $('#approval .left-wrap').width();
			$('#approval .content-wrap').css( {width:w} );
	}

	function dragHandler( $evt, $target ) {
		$('#popDetail').css( {left:$target.position.left, top:$target.position.top} );
	}

	//검색
	function funSearch(f,action)
	{
		f.page.value = "1";
		f.type.value = "search";
		f.target = "_self";
		f.action = action;
		f.submit();
	}

	//검색조건 변경
	function selCase(f)
	{
		if (f.category.value == "휴가계")
		{
			f.vacation.style.display = "inline";
		}
		else
		{
			f.vacation.style.display = "none";
		}
	}

	//읽기(구버전)
	function funView_old(doc_no)
	{
		hdnFrame.location.href = '/approval/old/approval_detail.php?doc_no='+doc_no;
	}

	//읽기
	function funView(doc_no)
	{
		hdnFrame.location.href = '/approval/approval_detail.php?doc_no='+doc_no;
	}

	//지급
	function funPayment(doc_no)
	{
		var frm = document.form;
		if(!confirm("지급완료 확인 하시겠습니까?")){
			return;
		}
		else
		{		
			frm.type.value='payment';
			frm.target = 'hdnFrame';
			frm.action = 'approval_payment.php?doc_no='+doc_no;
			frm.submit();
		}
	}
	
	//수정(구버전)
	function funModify_old(doc_no)
	{
		location.href = '/approval/old/approval_write.php?doc_no='+doc_no+'&type=modify';
	}

	//수정
	function funModify(doc_no)
	{
		location.href = '/approval/approval_write.php?doc_no='+doc_no+'&type=modify';
	}

	//삭제확인
	function funDelete(doc_no)
	{
		var frm = document.form4;
		frm.doc_no.value = doc_no;

		$("#popDel").attr("style","display:inline;");
	}

	//삭제실행
	function funDeleteOk()
	{
		var frm = document.form4;
		var doc_no = frm.doc_no.value;
		hdnFrame.location.href = '/approval/approval_write_act.php?doc_no='+doc_no+'&type=delete';
	}

	//재상신확인
	function funReWrite(doc_no)
	{
		var frm = document.form4;
		frm.doc_no.value = doc_no;

		$("#popReWrite").attr("style","display:inline;");
	}

	//재상신페이지이동
	function funReWriteOk()
	{
		var frm = document.form4;
		var doc_no = frm.doc_no.value;
		location.href = '/approval/approval_rewrite.php?doc_no='+doc_no;
	}

	//결재 팝업
	function funSign(doc_no,order,pwd)
	{
		hdnFrame.location.href = '/approval/approval_sign.php?doc_no='+doc_no+'&order='+order+'&pwd='+pwd;
	}

	//결재 실행
	function funSignOk(){ 
		var frm = document.form3;
		var pwd = frm.pwd.value;

		if (pwd == "Y")
		{
			frm.target = 'hdnFrame';
			frm.action = '/approval/approval_sign_pwd.php';
			frm.submit();
		}
		else
		{
			frm.target = 'hdnFrame';
			frm.action = '/approval/approval_sign_act.php';
			frm.submit();
	
			$("#signpwd1",top.document).attr("checked",true);
		}
	}

	//결재 비밀번호 확인
	function funSignPwd(){ 
		var frm = document.form5;
		frm.target = 'hdnFrame';
		frm.action = '/approval/approval_sign_act.php';
		frm.submit();

		$("#popPassword",top.document).attr("style","display:none;");
		$("#signpwd1",top.document).attr("checked",true);
	}

	//결재로그
	function funLog(doc_no)
	{
		hdnFrame.location.href = '/approval/approval_log.php?doc_no='+doc_no;
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
		frm.type.value = 'modify_reply';
		frm.action = '/approval/approval_reply_act.php?reply_no='+replyno;
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
			frm.type.value = 'delete_reply';
			frm.action = '/approval/approval_reply_act.php?reply_no='+replyno;
			frm.submit();
		}
	}

	//결제금액 수정(구버전)
	function modifyExpense_old(idx){ 
		if(!confirm("결재정보를 수정하시겠습니까?")){
			return;
		}
		else
		{
			var frm = document.form2;
			frm.target = 'hdnFrame';
			frm.action = '/approval/old/approval_expense_act.php?idx='+idx+'&mode=modify';
			frm.submit();
		}
	} 

	//결제금액 수정
	function modifyExpense(idx){ 
		if(!confirm("결재정보를 수정하시겠습니까?")){
			return;
		}
		else
		{
			var frm = document.form2;
			frm.target = 'hdnFrame';
			frm.action = '/approval/approval_expense_act.php?idx='+idx+'&mode=modify';
			frm.submit();
		}
	} 

	//결제금액 삭제(구버전)
	function deleteExpense_old(idx){ 
		if(!confirm("결재정보를 수정하시겠습니까?")){
			return;
		}
		else
		{
			var frm = document.form2;
			frm.target = 'hdnFrame';
			frm.action = '/approval/old/approval_expense_act.php?idx='+idx+'&mode=delete';
			frm.submit();
		}
	} 

	//결제금액 삭제
	function deleteExpense(idx){ 
		if(!confirm("결재정보를 수정하시겠습니까?")){
			return;
		}
		else
		{
			var frm = document.form2;
			frm.target = 'hdnFrame';
			frm.action = '/approval/approval_expense_act.php?idx='+idx+'&mode=delete';
			frm.submit();
		}
	} 

	//결제금액 수정내역(구버전)
	function funExpenseList_old(docno,idx) {
		MM_openBrWindow('/approval/old/approval_expense_list.php?doc_no='+docno+'&idx='+idx,'','width=565 ,height=370,scrollbars=yes, scrolling=yes');
	}

	//결제금액 수정내역
	function funExpenseList(docno,idx) {
		MM_openBrWindow('/approval/approval_expense_list.php?doc_no='+docno+'&idx='+idx,'','width=565 ,height=370,scrollbars=yes, scrolling=yes');
	}

	//숫자 천단위 콤마찍기
	function checkThousand(fld,num){

		num = num.replace(/,/g,'');
		var commaValue = "";
		var i;

		for(i=1; i<=num.length; i++)
		{
			if(i>1 && (i%3==1))
			{
				commaValue = num.charAt(num.length-i) + "," + commaValue;
			}
			else
			{
				commaValue = num.charAt(num.length-i) + commaValue;
			}
		}
		fld.value = commaValue;
	}

	//입사정보 수정
	function modifyExpansion(idx){ 
		if(!confirm("입사정보를 수정하시겠습니까?")){
			return;
		}
		else
		{
			var frm = document.form2;
			frm.target = 'hdnFrame';
			frm.action = '/approval/approval_expansion_act.php?idx='+idx+'&mode=modify';
			frm.submit();
		}
	} 
	//입사정보 삭제
	function deleteExpansion(idx){ 
		if(!confirm("입사정보를 수정하시겠습니까?")){
			return;
		}
		else
		{
			var frm = document.form2;
			frm.target = 'hdnFrame';
			frm.action = '/approval/approval_expansion_act.php?idx='+idx+'&mode=delete';
			frm.submit();
		}
	} 
