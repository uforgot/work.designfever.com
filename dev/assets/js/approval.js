	//DETAIL, �������, ���� POPUP LAYER SHOW/HIDE
	//var isPopup1 = false;
	//var isPopup2 = false;
	var FRAMELINE_WIDTH = 8+2;		// �¿� ���ζ��� border �β�
	//var LAYERPOPUP = $('.approval-popup2');

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

	//�˻�
	function funSearch(f,action)
	{
		f.page.value = "1";
		f.type.value = "search";
		f.target = "_self";
		f.action = action;
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

	//�б�(������)
	function funView_old(doc_no)
	{
		hdnFrame.location.href = '/approval/old/approval_detail.php?doc_no='+doc_no;
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
		if(!confirm("���޿Ϸ� Ȯ�� �Ͻðڽ��ϱ�?")){
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
	
	//����(������)
	function funModify_old(doc_no)
	{
		location.href = '/approval/old/approval_write.php?doc_no='+doc_no+'&type=modify';
	}

	//����
	function funModify(doc_no)
	{
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
	
			$("#signpwd1",top.document).attr("checked",true);
		}
	}

	//���� ��й�ȣ Ȯ��
	function funSignPwd(){ 
		var frm = document.form5;
		frm.target = 'hdnFrame';
		frm.action = '/approval/approval_sign_act.php';
		frm.submit();

		$("#popPassword",top.document).attr("style","display:none;");
		$("#signpwd1",top.document).attr("checked",true);
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

	//�����ݾ� ����(������)
	function modifyExpense_old(idx){ 
		if(!confirm("���������� �����Ͻðڽ��ϱ�?")){
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

	//�����ݾ� ����
	function modifyExpense(idx){ 
		if(!confirm("���������� �����Ͻðڽ��ϱ�?")){
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

	//�����ݾ� ����(������)
	function deleteExpense_old(idx){ 
		if(!confirm("���������� �����Ͻðڽ��ϱ�?")){
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

	//�����ݾ� ����
	function deleteExpense(idx){ 
		if(!confirm("���������� �����Ͻðڽ��ϱ�?")){
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

	//�����ݾ� ��������(������)
	function funExpenseList_old(docno,idx) {
		MM_openBrWindow('/approval/old/approval_expense_list.php?doc_no='+docno+'&idx='+idx,'','width=565 ,height=370,scrollbars=yes, scrolling=yes');
	}

	//�����ݾ� ��������
	function funExpenseList(docno,idx) {
		MM_openBrWindow('/approval/approval_expense_list.php?doc_no='+docno+'&idx='+idx,'','width=565 ,height=370,scrollbars=yes, scrolling=yes');
	}

	//���� õ���� �޸����
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

	//�Ի����� ����
	function modifyExpansion(idx){ 
		if(!confirm("�Ի������� �����Ͻðڽ��ϱ�?")){
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
	//�Ի����� ����
	function deleteExpansion(idx){ 
		if(!confirm("�Ի������� �����Ͻðڽ��ϱ�?")){
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
