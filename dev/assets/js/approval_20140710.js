	//DETAIL, �������, ���� POPUP LAYER SHOW/HIDE
	//var isPopup1 = false;
	//var isPopup2 = false;

	$( function(){
	/*
		$('a[href=#]').click( function( $evt ) { $evt.preventDefault() } );

		// ����α�' �˾�
		$('.approvalList-popup2 .content-title a').bind( 'click', clickPopupApprovalLog );
		$('.decisionApprovalOk-popup3 .pop_top a').bind( 'click', clickPopupApprovalLog );

		// ����' �˾�
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

	//�˻�
	function funSearch(f)
	{
		f.page.value="1";
		f.type.value="search";
		f.target="_self";
		f.submit();
	}
	//�˻����� ����
	function selCase(f)
	{
		if (f.category.value == "�ް���")
		{
			f.vacation.style.display = "inline";
		}
		else
		{
			f.vacation.style.display = "none";
		}
	}
	//�б�
	function funView(doc_no)
	{
		hdnFrame.location.href = '/approval/approval_detail.php?doc_no='+doc_no;
	}
	//����
	function funPayment(doc_no)
	{
		var frm = document.form;
		frm.type.value='payment';
		frm.target = 'hdnFrame';
		frm.action = 'approval_payment.php?doc_no='+doc_no;
		frm.submit();
	}
	//����
	function funModify(doc_no)
	{
//		location.href = '/approval/approval_modify.php?doc_no='+doc_no;
		location.href = '/approval/approval_write.php?doc_no='+doc_no+'&type=modify';
	}
	//����Ȯ��
	function funDelete(doc_no)
	{
		var frm = document.form4;
		frm.doc_no.value = doc_no;

		$("#popDel").attr("style","display:inline;");
	}
	//��������
	function funDeleteOk()
	{
		var frm = document.form4;
		var doc_no = frm.doc_no.value;
		hdnFrame.location.href = '/approval/approval_write_act.php?doc_no='+doc_no+'&type=delete';
	}
	//����Ȯ��
	function funReWrite(doc_no)
	{
		var frm = document.form4;
		frm.doc_no.value = doc_no;

		$("#popReWrite").attr("style","display:inline;");
	}
	//�����������̵�
	function funReWriteOk()
	{
		var frm = document.form4;
		var doc_no = frm.doc_no.value;
		location.href = '/approval/approval_rewrite.php?doc_no='+doc_no;
	}
	//���� �˾�
	function funSign(doc_no,order,pwd)
	{
		hdnFrame.location.href = '/approval/approval_sign.php?doc_no='+doc_no+'&order='+order+'&pwd='+pwd;
	}
	//���� ����
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
		}
	}
	//���� ��й�ȣ Ȯ��
	function funSignPwd(){ 
		var frm = document.form5;
		frm.target = 'hdnFrame';
		frm.action = '/approval/approval_sign_act.php';
		frm.submit();

		$("#popPassword",top.document).attr("style","display:none;");
	}
	//����α�
	function funLog(doc_no)
	{
		hdnFrame.location.href = '/approval/approval_log.php?doc_no='+doc_no;
	}

	//��� textarea���ڼ� 200�� ���� ��ũ��Ʈ
	function textcounter(field, countfield, maxlimit) { 
		  tempstr = field.value;
		  countfield.value = maxlimit - tempstr.length;
			if (maxlimit - tempstr.length < 0) {
						 alert(maxlimit+"���ڸ� �ʰ��� �� �����ϴ�.");
						 //document.form.remlen.focus();       //��Ŀ���� �̵���Ű�� ���� ��� ���ڰ� ������
						 tempstr = tempstr.substring(0,maxlimit); //��Ŀ�� �̵� �� ���� �ڸ���
						 field.value = tempstr;
						 countfield.value = maxlimit - tempstr.length;
						 //document.form.reply_contents.focus();  //��Ŀ���� �Է»��ڷ� �ǵ�����
	   }
	}
	//��� �ޱ�
	function writeReply(){ 
		var frm = document.form2;
		if(frm.reply_contents.value.length < 1){
			alert("������ �Է����ּ���");
			frm.reply_contents.focus();
			return;
		}	
		frm.target = 'hdnFrame';
		frm.type.value = 'write_reply';
		frm.action = '/approval/approval_reply_act.php';
		frm.submit();
	}; 
	//��� ���� (��۹�ȣ)
	function mod_Reply(replyno){	
		var frm = document.form2;
		var text = document.getElementById("c_text_"+replyno);

		frm.reply_contents.value = text.innerHTML;
		frm.reply_contents.focus();
		document.all("reply_btn").innerHTML = "<a href='javascript:modifyReply("+replyno+");'><img src=\"/img/btn_popup_modify.gif\" alt=\"\"></a>";
	}
	//��� ���� ����
	function modifyReply(replyno){ 
		var frm = document.form2;
		if(frm.reply_contents.value.length < 1){
			alert("������ �Է����ּ���");
			frm.reply_contents.focus();
			return;
		}	
		frm.target = 'hdnFrame';
		frm.type.value = 'modify_reply';
		frm.action = '/approval/approval_reply_act.php?reply_no='+replyno;
		frm.submit();
	}
	//��� ����
	function delReply(replyno){ 
		var frm = document.form2;
		if(!confirm("����� ���� �Ͻðڽ��ϱ�?")){
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
