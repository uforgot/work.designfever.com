	function quotation(){
		if (event.keyCode == 34 || event.keyCode == 39) 
			event.returnValue = false;	
	}
	
	function com_onlyNumber(){

		if (event.keyCode < 48 || event.keyCode > 57) 
				event.returnValue = false;
	}

	/* �α��κκ�*/
	function fcSelectValue (varSelect) {
		var objSelect = eval (varSelect);
		return objSelect.options[objSelect.selectedIndex].value;
	}

	/* str�� ��,�� ���ӵ� ���鹮�ڸ� ������ ���ڿ��� ��ȯ�Ѵ�. */
	function trim(str) {
		return(lTrim(rTrim(str)));
	}
	
	/* str�� �� ���ӵ� ���鹮�ڿ��� ������ ���ڿ��� ��ȯ�Ѵ�. */
	function lTrim(str) {
		var i=0;
		while (str.charAt(i) == " " || str.charAt(i) == "\t" || str.charCodeAt(i) == 12288) i++;
		return str.substring(i);
	}
	
	/* str�� �� ���ӵ� ���鹮�ڿ��� ������ ���ڿ��� ��ȯ�Ѵ�. */
	function rTrim(str) {
		var i=str.length -1;
		while (str.charAt(i) == " " || str.charAt(i) == "\t" || str.charCodeAt(i) == 12288) i--;
		return str.substring(0, i+1);
	}
	
	//ȸ�籸�� return�Լ�


	//�� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� ȸ������ �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� 
	function Inert_MemberInfo() {

		var frm = document.form;
	
        //-----------------------------------------------------------------[�ڡڡڡ� ����ó���ڡڡڡ�]-----------------------------------------------------------
		
		if(trim(frm.login.value).length < 1){
			alert("ȸ�����̵�� 2�� �̻� 15�ڸ� ���Ϸ� �Է��� �ּ���.");
			frm.login.focus();
			return;
		}
		
		if (frm.IdCheck.value != "Y") {
			alert("�α��ξ��̵� �ߺ�Ȯ�� ��ư�� ���� �ߺ����θ� üũ�Ͻñ� �ٶ��ϴ�.");
			frm.login.focus();
			return;
		}
		
		if(trim(frm.PassWd.value).length < 4){
			alert("��й�ȣ�� 4�� �̻� 15�ڸ� ���Ϸ� �Է��� �ּ���.");
			frm.PassWd.focus();
			return;
		}
		
		if(trim(frm.PassWdCon.value).length < 4){
			alert("��й�ȣȮ���� ���� ��й�ȣ�� �ѹ� �� �Է��� �ּ���.");
			frm.PassWdCon.focus();
			return;
		}
		
		if(trim(frm.PassWd.value) != trim(frm.PassWdCon.value)) {
		    alert("��й�ȣ�� ��й�ȣȮ���� ���� �ʽ��ϴ�.\n�ٽ� �Է��Ͽ� �ֽʽÿ�.");
			frm.PassWd.focus();
			frm.PassWd.select();
			return;
		}
			
		if(trim(frm.name.value).length == 0){
			alert("����ڸ��� �Է��� �ּ���.");
			frm.name.focus();
			return;
		}
		
		if((trim(frm.join1.value).length == 0) || +
				 (trim(frm.join2.value).length == 0) || +
				 (trim(frm.join3.value).length == 0)){
				alert("�Ի����� �Է��� �ּ���.");
				frm.join1.focus();
				return;
		}

		if((trim(frm.birth1.value).length == 0) || +
				 (trim(frm.birth2.value).length == 0) || +
				 (trim(frm.birth3.value).length == 0)){
				alert("������ �Է��� �ּ���.");
				frm.birth1.focus();
				return;
		}

		if((trim(frm.mobile1.value).length < 2) || +
				 (trim(frm.mobile2.value).length < 3) || +
				 (trim(frm.mobile3.value).length < 4)){
				alert("�޴��� ��ȣ�� �Է��� �ּ���.");
				frm.mobile1.focus();
				return;
		}

		if(trim(frm.position.value) != "�İ�/�����"){
			if(trim(frm.email.value).length == 0){
				alert("DF E-mail�� �Է��� �ּ���.");
				frm.email.focus();
				return;
			}
		}
		/*�̸��� ���� �ϴ� ����
		if(trim(frm.Email.value).length == 0){
			alert("�̸����ּҸ� �Է��� �ּ���.");
			frm.Email.focus();
			
			return;
		}else if(trim(frm.EmailGubun.value).length == 0){
		    alert("�̸����ּҸ� ������ �ּ��� ");
		    frm.EmailGubun.focus();
		    
		    return;
		}else if (frm.EmailGubun.value == "input" && trim(frm.EmailInput.value).length == 0){
				alert("�����Է� �Ͻ� �����ּҸ� �Է��� �ּ���");
 		    	frm.EmailInput.focus();
 		    	
				return;
		}else{
			countValue = 0;

			countValue = trim(frm.Email.value).length + trim(frm.EmailInput.value).length;

			if(countValue > 30){
				alert("�̸��� �ּ��� ���� 30�ڸ��� ������ �����ϴ�.\n�ٸ� �����ּҸ� �Է��ϼ���");
				return;
			}
		}
		*/
		if((trim(frm.e_tel1.value).length < 2) || +
				 (trim(frm.e_tel2.value).length < 3) || +
				 (trim(frm.e_tel3.value).length < 4)){
				alert("��󿬶����� ��ȣ�� �Է��� �ּ���.");
				frm.e_tel1.focus();
				return;
		}
		
		if (frm.zipcode_new.value.length == 0 || trim(frm.address_new.value).length == 0){		
			alert("�����ȣ ã�� ��ư�� ���� ��Ȯ�� �ּҸ� �Է��� �ּ���.");
			frm.zipcode_new.focus();
			return;		
		}
		
		if(trim(frm.team.value).length < 2){
			alert("�μ��� ������ �ּ���.");
			frm.team.focus();
			return;
		}

		if(trim(frm.position.value).length < 2){
			alert("������ ������ �ּ���.");
			frm.position.focus();
			return;
		}
		
		//alert(frm.file_img.value);
	  	var ext = frm.file_img.value.slice(frm.file_img.value.lastIndexOf(".")+1).toLowerCase();	//Ȯ����
		//alert(ext);
	  	var filetxt = frm.file_img.value.slice(frm.file_img.value.lastIndexOf("\\")+1,frm.file_img.value.lastIndexOf("."));	 //���ϸ�
		//alert(filetxt);
	  	var maxSize  = 512000;    //500kb
        
		if(filetxt !="" ){
			
		  	if(filetxt.replace(/[a-zA-Z0-9_\-]+/,"") != ""){
				alert("�ѱ� ���ϸ��� ����Ͻ� �� �����ϴ�.");
				frm.file_img.value="";
				return;
			}
			if(filetxt.search(/[\",\',<,>]/g) >= 0) {
				 alert("���ϸ� Ư�����ڸ� ����Ͻ� �� �����ϴ�.");
				 frm.file_img.value="";
	        	return;
			}
			if  (filetxt.length > 15)
			{
				alert("���ϸ��� 15�ڸ��� �ʰ��� �� �����ϴ�.");
				frm.file_img.value="";
				return;
			}
			if(!(ext == 'jpg' || ext == 'jpg' || ext =='png' || ext=='bmp'))	
			{
				alert("Ȯ���ڰ� jpg,gif,png,bmp�� ���ϸ� ���ε� �����մϴ�.");
				frm.file_img.value="";
				return;
			}
		}else{
			;
		}
	
		//-----------------------------------------------------------------[�ڡڡڡ� ����ó�� ���ڡڡڡ�]-----------------------------------------------------------

		if(!confirm("���� �Ͻðڽ��ϱ�?")){
				return;
		}else{
			frm.target="hdnFrame";
			frm.action="join_act.php";
			frm.submit();
		}
	}
	//�� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� ȸ������ �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� 


	//�� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� ȸ��  �������� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� 
	function Modify_MemberInfo() {
	
		var frm = document.form;

		//-----------------------------------------------------------------[�ڡڡڡ� ����ó���ڡڡڡ�]-----------------------------------------------------------
		
		if(trim(frm.team.value).length < 2){
			alert("�μ��� ������ �ּ���.");
			frm.team.focus();
			return;
		}

		if(trim(frm.position.value).length < 2){
			alert("������ ������ �ּ���.");
			frm.position.focus();
			return;
		}
		
		//����й�ȣ�� �ѱ��ڶ� ������� ����ȴ�	
		if(trim(frm.PassWd.value).length > 0){
			if(trim(frm.PassWd.value).length < 4){
				alert("��й�ȣ�� 4�� �̻� 15�ڸ� ���Ϸ� �Է��� �ּ���.");
				frm.PassWd.focus();
				return;
			}
			
			if(trim(frm.PassWdCon.value).length < 4){
				alert("��й�ȣȮ���� ���� ��й�ȣ�� �ѹ� �� �Է��� �ּ���.");
				frm.PassWdCon.focus();
				return;
			}
		}		
		
		if(trim(frm.PassWd.value) != trim(frm.PassWdCon.value)) {
			alert("����й�ȣ�� ��й�ȣȮ���� ���� �ʽ��ϴ�.\n�ٽ� �Է��Ͽ� �ֽʽÿ�.");
			frm.PassWd.focus();
			frm.PassWd.select();
			return;
		}
		
		if((trim(frm.mobile1.value).length < 2) || + (trim(frm.mobile2.value).length < 3) || + (trim(frm.mobile3.value).length < 4))
		{
				alert("�޴���ȭ ��ȣ�� �Է��� �ּ���.");
				frm.mobile1.focus();
				return;
		}

		if(trim(frm.position.value) != "�İ�/�����"){
			if(trim(frm.email.value).length == 0){
				alert("DF E-mail�� �Է��� �ּ���.");
				frm.email.focus();
				return;
			}
		}
	
		/*�̸��ϸ���
		if(trim(frm.Email.value).length == 0){
			alert("�̸����ּҸ� �Է��� �ּ���.");
			frm.Email.focus();
			
			return;
		}else if(trim(frm.EmailGubun.value).length == 0){
		    alert("�̸����ּҸ� ������ �ּ��� ");
		    frm.EmailGubun.focus();
		    
		    return;
		}else if (frm.EmailGubun.value == "input" && trim(frm.EmailInput.value).length == 0){
				alert("�����Է� �Ͻ� �����ּҸ� �Է��� �ּ���");
 		    	frm.EmailInput.focus();
 		    	
				return;
		}else{
			countValue = 0;

			countValue = trim(frm.Email.value).length + trim(frm.EmailInput.value).length;

			if(countValue > 30){
				alert("�̸��� �ּ��� ���� 30�ڸ��� ������ �����ϴ�.\n�ٸ� �����ּҸ� �Է��ϼ���");
				return;
			}
		}
		*/
		if((trim(frm.e_tel1.value).length < 2) || + (trim(frm.e_tel2.value).length < 3) || + (trim(frm.e_tel3.value).length < 4))
		{
				alert("��󿬶�ó�� �Է��� �ּ���.");
				frm.e_tel1.focus();
				return;
		}

		if (frm.zipcode_new.value.length == 0 || trim(frm.address_new.value).length == 0){		
			alert("�����ȣ ã�� ��ư�� ���� ��Ȯ�� �ּҸ� �Է��� �ּ���.");
			frm.zipcode_new.focus();
			return;		
		}
			
	  	var ext = frm.file_img2.value.slice(frm.file_img2.value.lastIndexOf(".")+1).toLowerCase();	//Ȯ����
	  	var filetxt = frm.file_img2.value.slice(frm.file_img2.value.lastIndexOf("\\")+1,frm.file_img2.value.lastIndexOf("."));	 //���ϸ�
	  	var maxSize  = 512000;    //500kb
        
		if(filetxt !="" ){
			
		  	if(filetxt.replace(" ","").replace(/[a-zA-Z0-9_\-]+/,"") != ""){
				alert("�ѱ� ���ϸ��� ����Ͻ� �� �����ϴ�.");
				frm.file_img2.value="";
				return;
			}
			 if(filetxt.search(/[\",\',<,>]/g) >= 0) {
				 alert("���ϸ� Ư�����ڸ� ����Ͻ� �� �����ϴ�.");
				 frm.file_img2.value="";
	        	return;
			 }
	  	
			if  (filetxt.length > 15)
			{
				alert("���ϸ��� 15�ڸ��� �ʰ��� �� �����ϴ�.");
				frm.file_img2.value="";
				return;
			}
			if(!(ext == 'jpg' || ext == 'jpg' || ext =='png' || ext=='bmp'))	
			{
				alert("Ȯ���ڰ� jpg,gif,png,bmp�� ���ϸ� ���ε� �����մϴ�.");
				frm.file_img2.value="";
				return;
			}
			
		}else{
			;
		}
	
	    //-----------------------------------------------------------------[�ڡڡڡ� ����ó�� ���ڡڡڡ�]-----------------------------------------------------------
	    
		if(!confirm("���� �Ͻðڽ��ϱ�?")){
				return;
		}else{
			frm.target="hdnFrame";
			frm.action="modify_act.php";       
			frm.submit();
		}
	}
	//�� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� ȸ��  �������� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� �� ��





