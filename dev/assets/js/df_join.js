	function quotation(){
		if (event.keyCode == 34 || event.keyCode == 39) 
			event.returnValue = false;	
	}
	
	function com_onlyNumber(){

		if (event.keyCode < 48 || event.keyCode > 57) 
				event.returnValue = false;
	}

	/* 로그인부분*/
	function fcSelectValue (varSelect) {
		var objSelect = eval (varSelect);
		return objSelect.options[objSelect.selectedIndex].value;
	}

	/* str의 앞,뒤 연속된 공백문자를 제거한 문자열을 반환한다. */
	function trim(str) {
		return(lTrim(rTrim(str)));
	}
	
	/* str의 앞 연속된 공백문자열을 제거한 문자열을 반환한다. */
	function lTrim(str) {
		var i=0;
		while (str.charAt(i) == " " || str.charAt(i) == "\t" || str.charCodeAt(i) == 12288) i++;
		return str.substring(i);
	}
	
	/* str의 뒤 연속된 공백문자열을 제거한 문자열을 반환한다. */
	function rTrim(str) {
		var i=str.length -1;
		while (str.charAt(i) == " " || str.charAt(i) == "\t" || str.charCodeAt(i) == 12288) i--;
		return str.substring(0, i+1);
	}
	
	//회사구분 return함수


	//■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ 회원가입 ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ 
	function Inert_MemberInfo() {

		var frm = document.form;
	
        //-----------------------------------------------------------------[★★★★ 예외처리★★★★]-----------------------------------------------------------
		
		if(trim(frm.login.value).length < 1){
			alert("회원아이디는 2자 이상 15자리 이하로 입력해 주세요.");
			frm.login.focus();
			return;
		}
		
		if (frm.IdCheck.value != "Y") {
			alert("로그인아이디 중복확인 버튼을 눌러 중복여부를 체크하시기 바랍니다.");
			frm.login.focus();
			return;
		}
		
		if(trim(frm.PassWd.value).length < 4){
			alert("비밀번호는 4자 이상 15자리 이하로 입력해 주세요.");
			frm.PassWd.focus();
			return;
		}
		
		if(trim(frm.PassWdCon.value).length < 4){
			alert("비밀번호확인을 위해 비밀번호를 한번 더 입력해 주세요.");
			frm.PassWdCon.focus();
			return;
		}
		
		if(trim(frm.PassWd.value) != trim(frm.PassWdCon.value)) {
		    alert("비밀번호와 비밀번호확인이 같지 않습니다.\n다시 입력하여 주십시오.");
			frm.PassWd.focus();
			frm.PassWd.select();
			return;
		}
			
		if(trim(frm.name.value).length == 0){
			alert("사용자명을 입력해 주세요.");
			frm.name.focus();
			return;
		}
		
		if((trim(frm.join1.value).length == 0) || +
				 (trim(frm.join2.value).length == 0) || +
				 (trim(frm.join3.value).length == 0)){
				alert("입사일을 입력해 주세요.");
				frm.join1.focus();
				return;
		}

		if((trim(frm.birth1.value).length == 0) || +
				 (trim(frm.birth2.value).length == 0) || +
				 (trim(frm.birth3.value).length == 0)){
				alert("생일을 입력해 주세요.");
				frm.birth1.focus();
				return;
		}

		if((trim(frm.mobile1.value).length < 2) || +
				 (trim(frm.mobile2.value).length < 3) || +
				 (trim(frm.mobile3.value).length < 4)){
				alert("휴대폰 번호를 입력해 주세요.");
				frm.mobile1.focus();
				return;
		}

		if(trim(frm.position.value) != "파견/계약직"){
			if(trim(frm.email.value).length == 0){
				alert("DF E-mail을 입력해 주세요.");
				frm.email.focus();
				return;
			}
		}
		/*이메일 빼서 일단 막음
		if(trim(frm.Email.value).length == 0){
			alert("이메일주소를 입력해 주세요.");
			frm.Email.focus();
			
			return;
		}else if(trim(frm.EmailGubun.value).length == 0){
		    alert("이메일주소를 선택해 주세요 ");
		    frm.EmailGubun.focus();
		    
		    return;
		}else if (frm.EmailGubun.value == "input" && trim(frm.EmailInput.value).length == 0){
				alert("직접입력 하실 메일주소를 입력해 주세요");
 		    	frm.EmailInput.focus();
 		    	
				return;
		}else{
			countValue = 0;

			countValue = trim(frm.Email.value).length + trim(frm.EmailInput.value).length;

			if(countValue > 30){
				alert("이메일 주소의 값이 30자리를 넘을수 없습니다.\n다른 메일주소를 입력하세요");
				return;
			}
		}
		*/
		if((trim(frm.e_tel1.value).length < 2) || +
				 (trim(frm.e_tel2.value).length < 3) || +
				 (trim(frm.e_tel3.value).length < 4)){
				alert("비상연락망을 번호를 입력해 주세요.");
				frm.e_tel1.focus();
				return;
		}
		
		if (frm.zipcode_new.value.length == 0 || trim(frm.address_new.value).length == 0){		
			alert("우편번호 찾기 버튼을 통해 정확한 주소를 입력해 주세요.");
			frm.zipcode_new.focus();
			return;		
		}
		
		if(trim(frm.team.value).length < 2){
			alert("부서를 선택해 주세요.");
			frm.team.focus();
			return;
		}

		if(trim(frm.position.value).length < 2){
			alert("직급을 선택해 주세요.");
			frm.position.focus();
			return;
		}
		
		//alert(frm.file_img.value);
	  	var ext = frm.file_img.value.slice(frm.file_img.value.lastIndexOf(".")+1).toLowerCase();	//확장자
		//alert(ext);
	  	var filetxt = frm.file_img.value.slice(frm.file_img.value.lastIndexOf("\\")+1,frm.file_img.value.lastIndexOf("."));	 //파일명
		//alert(filetxt);
	  	var maxSize  = 512000;    //500kb
        
		if(filetxt !="" ){
			
		  	if(filetxt.replace(/[a-zA-Z0-9_\-]+/,"") != ""){
				alert("한글 파일명은 사용하실 수 없습니다.");
				frm.file_img.value="";
				return;
			}
			if(filetxt.search(/[\",\',<,>]/g) >= 0) {
				 alert("파일명에 특수문자를 사용하실 수 없습니다.");
				 frm.file_img.value="";
	        	return;
			}
			if  (filetxt.length > 15)
			{
				alert("파일명은 15자리를 초과할 수 없습니다.");
				frm.file_img.value="";
				return;
			}
			if(!(ext == 'jpg' || ext == 'jpg' || ext =='png' || ext=='bmp'))	
			{
				alert("확장자가 jpg,gif,png,bmp인 파일만 업로드 가능합니다.");
				frm.file_img.value="";
				return;
			}
		}else{
			;
		}
	
		//-----------------------------------------------------------------[★★★★ 예외처리 끝★★★★]-----------------------------------------------------------

		if(!confirm("가입 하시겠습니까?")){
				return;
		}else{
			frm.target="hdnFrame";
			frm.action="join_act.php";
			frm.submit();
		}
	}
	//■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ 회원가입 끝 ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ 


	//■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ 회원  정보수정 ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ 
	function Modify_MemberInfo() {
	
		var frm = document.form;

		//-----------------------------------------------------------------[★★★★ 예외처리★★★★]-----------------------------------------------------------
		
		if(trim(frm.team.value).length < 2){
			alert("부서를 선택해 주세요.");
			frm.team.focus();
			return;
		}

		if(trim(frm.position.value).length < 2){
			alert("직급을 선택해 주세요.");
			frm.position.focus();
			return;
		}
		
		//새비밀번호에 한글자라도 들어갔을경우 적용된다	
		if(trim(frm.PassWd.value).length > 0){
			if(trim(frm.PassWd.value).length < 4){
				alert("비밀번호는 4자 이상 15자리 이하로 입력해 주세요.");
				frm.PassWd.focus();
				return;
			}
			
			if(trim(frm.PassWdCon.value).length < 4){
				alert("비밀번호확인을 위해 비밀번호를 한번 더 입력해 주세요.");
				frm.PassWdCon.focus();
				return;
			}
		}		
		
		if(trim(frm.PassWd.value) != trim(frm.PassWdCon.value)) {
			alert("새비밀번호와 비밀번호확인이 같지 않습니다.\n다시 입력하여 주십시오.");
			frm.PassWd.focus();
			frm.PassWd.select();
			return;
		}
		
		if((trim(frm.mobile1.value).length < 2) || + (trim(frm.mobile2.value).length < 3) || + (trim(frm.mobile3.value).length < 4))
		{
				alert("휴대전화 번호를 입력해 주세요.");
				frm.mobile1.focus();
				return;
		}

		if(trim(frm.position.value) != "파견/계약직"){
			if(trim(frm.email.value).length == 0){
				alert("DF E-mail을 입력해 주세요.");
				frm.email.focus();
				return;
			}
		}
	
		/*이메일막음
		if(trim(frm.Email.value).length == 0){
			alert("이메일주소를 입력해 주세요.");
			frm.Email.focus();
			
			return;
		}else if(trim(frm.EmailGubun.value).length == 0){
		    alert("이메일주소를 선택해 주세요 ");
		    frm.EmailGubun.focus();
		    
		    return;
		}else if (frm.EmailGubun.value == "input" && trim(frm.EmailInput.value).length == 0){
				alert("직접입력 하실 메일주소를 입력해 주세요");
 		    	frm.EmailInput.focus();
 		    	
				return;
		}else{
			countValue = 0;

			countValue = trim(frm.Email.value).length + trim(frm.EmailInput.value).length;

			if(countValue > 30){
				alert("이메일 주소의 값이 30자리를 넘을수 없습니다.\n다른 메일주소를 입력하세요");
				return;
			}
		}
		*/
		if((trim(frm.e_tel1.value).length < 2) || + (trim(frm.e_tel2.value).length < 3) || + (trim(frm.e_tel3.value).length < 4))
		{
				alert("비상연락처를 입력해 주세요.");
				frm.e_tel1.focus();
				return;
		}

		if (frm.zipcode_new.value.length == 0 || trim(frm.address_new.value).length == 0){		
			alert("우편번호 찾기 버튼을 통해 정확한 주소를 입력해 주세요.");
			frm.zipcode_new.focus();
			return;		
		}
			
	  	var ext = frm.file_img2.value.slice(frm.file_img2.value.lastIndexOf(".")+1).toLowerCase();	//확장자
	  	var filetxt = frm.file_img2.value.slice(frm.file_img2.value.lastIndexOf("\\")+1,frm.file_img2.value.lastIndexOf("."));	 //파일명
	  	var maxSize  = 512000;    //500kb
        
		if(filetxt !="" ){
			
		  	if(filetxt.replace(" ","").replace(/[a-zA-Z0-9_\-]+/,"") != ""){
				alert("한글 파일명은 사용하실 수 없습니다.");
				frm.file_img2.value="";
				return;
			}
			 if(filetxt.search(/[\",\',<,>]/g) >= 0) {
				 alert("파일명에 특수문자를 사용하실 수 없습니다.");
				 frm.file_img2.value="";
	        	return;
			 }
	  	
			if  (filetxt.length > 15)
			{
				alert("파일명은 15자리를 초과할 수 없습니다.");
				frm.file_img2.value="";
				return;
			}
			if(!(ext == 'jpg' || ext == 'jpg' || ext =='png' || ext=='bmp'))	
			{
				alert("확장자가 jpg,gif,png,bmp인 파일만 업로드 가능합니다.");
				frm.file_img2.value="";
				return;
			}
			
		}else{
			;
		}
	
	    //-----------------------------------------------------------------[★★★★ 예외처리 끝★★★★]-----------------------------------------------------------
	    
		if(!confirm("수정 하시겠습니까?")){
				return;
		}else{
			frm.target="hdnFrame";
			frm.action="modify_act.php";       
			frm.submit();
		}
	}
	//■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ 회원  정보수정 끝 ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■ ■





