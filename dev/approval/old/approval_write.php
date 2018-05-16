<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
	$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : "write";

	if ($type == "write")
	{
		if ($prf_id == 7) {	
			$doc_form = isset($_REQUEST['doc_form']) ? $_REQUEST['doc_form'] : "003"; 
		} else {
			$doc_form = isset($_REQUEST['doc_form']) ? $_REQUEST['doc_form'] : "004"; 
		}

		switch($doc_form)
		{
			case "001" : 
				$form_category = "비용품의서";
				break;
			case "002" : 
				$form_category = "프로젝트 관련품의서";
				break;
			case "003" : 
				$form_category = "외근계/파견계";
				break;
			case "004" : 
				$form_category = "휴가계";
				break;
			case "005" : 
				$form_category = "출장계";
				break;
			case "006" : 
				$form_category = "사유서";
				break;
			case "007" : 
				$form_category = "시말서";
				break;
			case "008" : 
				$form_category = "조퇴계";
				break;
		}
		if ($form_category == "휴가계" )
		{
			$form_title = isset($_REQUEST['form_title']) ? $_REQUEST['form_title'] : "연차"; 
		}
		else
		{
			$form_title = $form_category; 
		}

		$doc_no = date("ym") ."-XXXX";
	
		$sql = "SELECT FORM_NO, CONTENTS FROM DF_APPROVAL_FORM WITH(NOLOCK) WHERE TITLE = '$form_title'";
		$rs = sqlsrv_query($dbConn, $sql);

		$record = sqlsrv_fetch_array($rs);

		$form_no = $record['FORM_NO'];
		$contents = $record['CONTENTS'];

		$to_count = 5;
		$cc_count = 5;
		$partner_count = 10;

		$up_year = date("Y");
		$up_month = date("m");
		$up_day = date("d");

		$start_date = date("Y-m-d");
		$end_date = date("Y-m-d");
	}
	else if ($type == "modify")
	{
		$doc_no = isset($_REQUEST['doc_no']) ? $_REQUEST['doc_no'] : null;

		if ($doc_no == "")
		{
?>
	<script type="text/javascript">
		alert("해당 문서가 존재하지 않습니다.");
		self.close();
	</script>
<?
			exit;
		}

		$sql = "SELECT 
					FORM_CATEGORY, FORM_TITLE, TITLE, CONTENTS, OPEN_YN, FILE_1, FILE_2, FILE_3, PROJECT_NO,
					CONVERT(char(10),REG_DATE,120) AS REG_DATE, CONVERT(char(10),START_DATE,120) AS START_DATE, CONVERT(char(10),END_DATE,120) AS END_DATE, STATUS, 
					CONVERT(char(10),APPROVAL_DATE,120) AS APPROVAL_DATE
				FROM 
					DF_APPROVAL WITH(NOLOCK) 
				WHERE 
					DOC_NO = '$doc_no'";
		$rs = sqlsrv_query($dbConn, $sql);

		$record = sqlsrv_fetch_array($rs);

		$form_category = $record['FORM_CATEGORY'];
		$form_title = $record['FORM_TITLE'];
		$title = $record['TITLE'];
		$contents = $record['CONTENTS'];
		$open_yn = $record['OPEN_YN'];
		$file_1 = $record['FILE_1'];
		$file_2 = $record['FILE_2'];
		$file_3 = $record['FILE_3'];
		$project_no = $record['PROJECT_NO'];
		$reg_date = $record['REG_DATE'];
		$start_date = $record['START_DATE'];
		$end_date = $record['END_DATE'];
		$status = $record['STATUS'];
		$approval_date = $record['APPROVAL_DATE'];
		$up_year = substr($approval_date,0,4);
		$up_month = substr($approval_date,5,2);
		$up_day = substr($approval_date,8,2);

		switch($form_category)
		{
			case "비용품의서" : 
				$doc_form = "001";
				break;
			case "프로젝트 관련품의서" : 
				$doc_form = "002";
				break;
			case "외근계/파견계" : 
				$doc_form = "003";
				break;
			case "휴가계" : 
				$doc_form = "004";
				break;
			case "출장계" : 
				$doc_form = "005";
				break;
			case "사유서" : 
				$doc_form = "006";
				break;
			case "시말서" : 
				$doc_form = "007";
				break;
			case "조퇴계" : 
				$doc_form = "008";
				break;
		}

		$sql = "SELECT FORM_NO FROM DF_APPROVAL_FORM WITH(NOLOCK) WHERE TITLE = '$form_title'";
		$rs = sqlsrv_query($dbConn, $sql);

		$record = sqlsrv_fetch_array($rs);

		$form_no = $record['FORM_NO'];

		$to_count = 5;
		$cc_count = 5;
		$partner_count = 10;
	}
//	if ($form_category == "외근계/파견계" || $form_category == "출장계") { 
		$sql = "SELECT TOP 1 DOC_NO FROM DF_APPROVAL WITH(NOLOCK) WHERE PRS_LOGIN = '$prs_login' AND USE_YN = 'Y' AND FORM_CATEGORY = '$form_category' ORDER BY SEQNO DESC";
//	}
//	else
//	{
//		$sql = "SELECT TOP 1 DOC_NO FROM DF_APPROVAL WITH(NOLOCK) WHERE PRS_LOGIN = '$prs_login' AND USE_YN = 'Y' ORDER BY SEQNO DESC";
//	}
	$rs = sqlsrv_query($dbConn, $sql);

	$record = sqlsrv_fetch_array($rs);

	$last_doc_no = $record['DOC_NO'];
?>

<? include INC_PATH."/top.php"; ?>

<? if ($form_category != "비용품의서" && $form_category != "프로젝트 관련품의서") { ?>				
<script type="text/javascript">
	$(document).ready(function(){
		$("#fr_date").val($("#fr_month").val()+"/"+$("#fr_day").val()+"/"+$("#fr_year").val());
		$("#to_date").val($("#to_month").val()+"/"+$("#to_day").val()+"/"+$("#to_year").val());
		//날짜 지정
		$.datepicker.setDefaults({
		  yearRange: "<?=$startYear?>:<?=date("Y",strtotime("+1 year"))?>" 
		});
		$("#fr_year, #fr_month, #fr_day").change(function() {
			$("#fr_date").val($("#fr_month").val()+"/"+$("#fr_day").val()+"/"+$("#fr_year").val());
			$("#to_year").val($("#fr_year").val());
			$("#to_month").val($("#fr_month").val());
			$("#to_day").val($("#fr_day").val());
			$("#to_date").val($("#to_month").val()+"/"+$("#to_day").val()+"/"+$("#to_year").val());
		});
		$("#to_year, #to_month, #to_day").change(function() {
			$("#to_date").val($("#to_month").val()+"/"+$("#to_day").val()+"/"+$("#to_year").val());
		});
		$("#fr_date").datepicker({
			onSelect: function (selectedDate) {
				$("#fr_year").val( selectedDate.substring(6,10) );
				$("#fr_month").val( selectedDate.substring(0,2) );
				$("#fr_day").val( selectedDate.substring(3,5) );
				$("#to_year").val( selectedDate.substring(6,10) );
				$("#to_month").val( selectedDate.substring(0,2) );
				$("#to_day").val( selectedDate.substring(3,5) );
				$("#to_date").val($("#to_month").val()+"/"+$("#to_day").val()+"/"+$("#to_year").val());
			}
		});
		$("#to_date").datepicker({
			onSelect: function (selectedDate) {
				$("#to_year").val( selectedDate.substring(6,10) );
				$("#to_month").val( selectedDate.substring(0,2) );
				$("#to_day").val( selectedDate.substring(3,5) );
			}
		});
	});
</script>
<? } ?>	
<script type="text/javascript" src="/ckeditor/ckeditor.js" /></script>
<script type="text/JavaScript">
	window.onload=function(){
		CKEDITOR.replace('contents', {
			width:700,
			height:500,
			skin:'kama',
			enterMode:'2',
			shiftEnterMode:'3',
			filebrowserUploadUrl:'../upload.php?type=files',
			filebrowserImageUploadUrl:'../upload.php?type=images',
			filebrowserFlashUploadUrl:'../upload.php?type=flash'
			}
		);
	};

	function funPersonDel(type,no)
	{
		if (type == "to")
		{
			document.getElementsByName("to_div_"+no)[0].innerHTML = "";
			document.getElementsByName("to_btn_div_"+no)[0].innerHTML = "";
			document.getElementsByName("to_btn_div_"+no)[0].innerHTML = "<input type=button id=to_btn_"+no+" name=to_btn_"+no+" value=지정 onClick=javascript:funPersonAdd('to','"+no+"');>";
			document.getElementsByName("to_id_"+no)[0].value = "";

			var to_id = "";

			for (var i=0; i<<?=$to_count?>; i++)
			{
				to_id = to_id + document.getElementsByName("to_id_"+i)[0].value + ",";
			}
			document.form.to_id.value = to_id;
		}
	}

	//결재
	function toAdd(){
		$("#search_type1").val("to").attr("selected","selected");
		$("#cc_list").addClass('hide');
		$("#to_list").removeClass('hide');
		$("#popToAdd").attr("style","display:inline;");
	}

	//수신참조
	function ccAdd(){
		$("#search_type1").val("cc").attr("selected","selected");
		$("#to_list").addClass('hide');
		$("#cc_list").removeClass('hide');
		$("#popToAdd").attr("style","display:inline;");
	}
	$(document).ready(function(){
	<? if ($form_category == "외근계/파견계" || $form_category == "출장계") { ?>
		//동반자
		$("#PartnerAddBtn").click(function(){
			$("#popPartnerAdd").attr("style","display:inline;");
		});
	<? } ?>
		//이전 선택값 불러오기
		//결재자
		var total_to = $("#total_to").val();
		var check_to = $("#to_id").val();
		var check_to_arr = check_to.split(",");
		for (var c=0; c<check_to_arr.length; c++)
		{
			for (var i=1; i<=total_to; i++)
			{
				if (Number($("#sel_to_id_"+ i).val()) == check_to_arr[c])
				{
					$("#check_to_"+ i).attr("disabled",true);
					$("#check_to_"+ i).attr("checked",true);

					var DivList = "";

					DivList += "<li id='list_to_"+ i +"' name='list_to'>";
					DivList += "	<input type='hidden' name='list_to_input' id='list_to_input_"+ i +"' value=''>";
					DivList += "	<input type='hidden' name='list_to_id' id='list_to_id_"+ i +"' value='"+$("#sel_to_id_"+ i).val()+"'>";
					DivList += "	<input type='hidden' name='list_to_login' id='list_to_login_"+ i +"' value='"+$("#sel_to_login_"+ i).val()+"'>";
					DivList += "	<input type='hidden' name='list_to_position' id='list_to_position_"+ i +"' value='"+$("#sel_to_position_"+ i).val()+"'>";
					DivList += "	<input type='hidden' name='list_to_name' id='list_to_name_"+ i +"' value='"+$("#sel_to_name_"+ i).val()+"'>";
					DivList += "	<p class='attach_file'><span style='cursor:pointer' onclick=oneCheck('check_list_to','list_to_check','list_to_"+ i +"');>" + $("#sel_to_position_"+ i).val() +" "+ $("#sel_to_name_"+ i).val() + "</span></p><a href=javascript:oneDel('list_to_"+ i +"','check_to_"+ i +"','total_to'); class='delete'><img src='/img/icon_del.gif' alt='삭제'></a>";
					DivList += "</li>";

					$("#list_to").append(DivList);
				}
			}
		}
		//수신참조
		var total_cc = $("#total_cc").val();
		var check_cc = $("#cc_id").val();
		var check_cc_arr = check_cc.split(",");
		for (var c=0; c<check_cc_arr.length; c++)
		{
			for (var i=1; i<=total_cc; i++)
			{
				if (Number($("#sel_cc_id_"+ i).val()) == check_cc_arr[c])
				{
					$("#check_cc_"+ i).attr("disabled",true);
					$("#check_cc_"+ i).attr("checked",true);

					var DivList = "";

					DivList += "<li id='list_cc_"+ i +"' name='list_cc'>";
					DivList += "	<input type='hidden' name='list_cc_input' id='list_cc_input_"+ i +"' value=''>";
					DivList += "	<input type='hidden' name='list_cc_id' id='list_cc_id_"+ i +"' value='"+$("#sel_cc_id_"+ i).val()+"'>";
					DivList += "	<input type='hidden' name='list_cc_login' id='list_cc_login_"+ i +"' value='"+$("#sel_cc_login_"+ i).val()+"'>";
					DivList += "	<input type='hidden' name='list_cc_position' id='list_cc_position_"+ i +"' value='"+$("#sel_cc_position_"+ i).val()+"'>";
					DivList += "	<input type='hidden' name='list_cc_name' id='list_cc_name_"+ i +"' value='"+$("#sel_cc_name_"+ i).val()+"'>";
					DivList += "	<p class='attach_file'><span style='cursor:pointer' onclick=oneCheck('check_list_cc','list_cc_check','list_cc_"+ i +"');>" + $("#sel_to_position_"+ i).val() +" "+ $("#sel_to_name_"+ i).val() + "</span></p><a href=javascript:oneDel('list_cc_"+ i +"','check_cc_"+ i +"','total_cc'); class='delete'><img src='/img/icon_del.gif' alt='삭제'></a>";
					DivList += "</li>";

					$("#list_cc").append(DivList);
				}
			}
		}
	<? if ($form_category == "외근계/파견계" || $form_category == "출장계") { ?>
		//동반자
		var total_partner = $("#total_partner").val();
		var check_partner = $("#partner_id").val();
		var check_partner_arr = check_partner.split(",");
		for (var c=0; c<check_partner_arr.length; c++)
		{
			for (var i=1; i<=total_partner; i++)
			{
				if (Number($("#sel_partner_id_"+ i).val()) == check_partner_arr[c])
				{
					$("#check_partner_"+ i).attr("disabled",true);
					$("#check_partner_"+ i).attr("checked",true);

					var DivList = "";

					DivList += "<li id='list_partner_"+ i +"' name='list_partner'>";
					DivList += "	<input type='hidden' name='list_partner_input' id='list_partner_input_"+ i +"' value=''>";
					DivList += "	<input type='hidden' name='list_partner_id' id='list_partner_id_"+ i +"' value='"+$("#sel_partner_id_"+ i).val()+"'>";
					DivList += "	<input type='hidden' name='list_partner_login' id='list_partner_login_"+ i +"' value='"+$("#sel_partner_login_"+ i).val()+"'>";
					DivList += "	<input type='hidden' name='list_partner_position' id='list_partner_position_"+ i +"' value='"+$("#sel_partner_position_"+ i).val()+"'>";
					DivList += "	<input type='hidden' name='list_partner_name' id='list_partner_name_"+ i +"' value='"+$("#sel_partner_name_"+ i).val()+"'>";
					DivList += "	<p class='attach_file'><span style='cursor:pointer' onclick=oneCheck('check_list_partner','list_partner_check','list_partner_"+ i +"');>" + $("#sel_partner_position_"+ i).val() +" "+ $("#sel_partner_name_"+ i).val() + "</span></p><a href=javascript:oneDel('list_partner_"+ i +"','check_partner_"+ i +"','total_partner'); class='delete'><img src='/img/icon_del.gif' alt='삭제'></a>";
					DivList += "</li>";

					$("#list_partner").append(DivList);
				}
			}
		}
	<? } ?>
		//결재자 선택.수신참조
		$("#sel_click_to").click(function(){
			if ($("#search_type1").val() == "to")
			{
				var total = $(":checkbox[name=check_to]:checked:enabled").length;
				if (total == 0)
				{
					alert("결재자를 선택해 주세요.");
					return;
				}

				var kids = $("#list_to").children().length;
				if (kids+total > <?=$to_count?>)
				{
					alert("결재자는 <?=$to_count?>명까지 선택 가능합니다.");
					return;
				}

				var j = kids;

				for (var i=1; i<=$("#total_to").val(); i++)
				{
					if ($("#check_to_"+ i).is(":checked") && $("#check_to_"+ i).is(":enabled"))
					{
						j = j + 1;
						var DivList = "";

						DivList += "<li id='list_to_"+ j +"' name='list_to'>";
						DivList += "	<input type='hidden' name='list_to_input' id='list_to_input_"+ j +"' value=''>";
						DivList += "	<input type='hidden' name='list_to_id' id='list_to_id_"+ j +"' value='"+$("#sel_to_id_"+ i).val()+"'>";
						DivList += "	<input type='hidden' name='list_to_login' id='list_to_login_"+ j +"' value='"+$("#sel_to_login_"+ i).val()+"'>";
						DivList += "	<input type='hidden' name='list_to_position' id='list_to_position_"+ j +"' value='"+$("#sel_to_position_"+ i).val()+"'>";
						DivList += "	<input type='hidden' name='list_to_name' id='list_to_name_"+ j +"' value='"+$("#sel_to_name_"+ i).val()+"'>";
						DivList += "	<p class='attach_file'><span style='cursor:pointer' onclick=oneCheck('check_list_to','list_to_check','list_to_"+ j +"');>" + $("#sel_to_position_"+ i).val() +" "+ $("#sel_to_name_"+ i).val() + "</span></p><a href=javascript:oneDel('list_to_"+ j +"','check_to_"+ j +"','total_to'); class='delete'><img src='/img/icon_del.gif' alt='삭제'></a>";
						DivList += "</li>";

						$("#list_to").append(DivList);

						$("#to_list").val($("#to_list").val()+ $("#sel_to_position_"+ i).val() +" "+ $("#sel_to_name_"+ i).val() +",");
						$("#to_ids").val($("#to_id").val()+ $("#sel_to_id_"+ i).val() +",");

						$("#check_to_"+ i).attr("disabled",true);
					}
				}
			}
			else if ($("#search_type1").val() == "cc")
			{
				var total = $(":checkbox[name=check_cc]:checked:enabled").length;
				if (total == 0)
				{
					alert("참조자를 선택해 주세요.");
					return;
				}

				var kids = $("#list_cc").children().length;
				if (kids+total > <?=$cc_count?>)
				{
					alert("참조자는 <?=$cc_count?>명까지 선택 가능합니다.");
					return;
				}

				var j = kids;

				for (var i=1; i<=$("#total_cc").val(); i++)
				{
					if ($("#check_cc_"+ i).is(":checked") && $("#check_cc_"+ i).is(":enabled"))
					{
						j = j + 1;
						var DivList = "";

						DivList += "<li id='list_cc_"+ j +"' name='list_to'>";
						DivList += "	<input type='hidden' name='list_cc_input' id='list_cc_input_"+ j +"' value=''>";
						DivList += "	<input type='hidden' name='list_cc_id' id='list_cc_id_"+ j +"' value='"+$("#sel_cc_id_"+ i).val()+"'>";
						DivList += "	<input type='hidden' name='list_cc_login' id='list_cc_login_"+ j +"' value='"+$("#sel_cc_login_"+ i).val()+"'>";
						DivList += "	<input type='hidden' name='list_cc_position' id='list_cc_position_"+ j +"' value='"+$("#sel_cc_position_"+ i).val()+"'>";
						DivList += "	<input type='hidden' name='list_cc_name' id='list_cc_name_"+ j +"' value='"+$("#sel_cc_name_"+ i).val()+"'>";
						DivList += "	<p class='attach_file'><span style='cursor:pointer' onclick=oneCheck('check_list_to','list_cc_check','list_cc_"+ j +"');>" + $("#sel_cc_position_"+ i).val() +" "+ $("#sel_cc_name_"+ i).val() + "</span></p><a href=javascript:oneDel('list_cc_"+ j +"','check_cc_"+ j +"','total_cc'); class='delete'><img src='/img/icon_del.gif' alt='삭제'></a>";
						DivList += "</li>";

						$("#list_cc ").append(DivList);

						$("#cc_list").val($("#cc_list").val()+ $("#sel_cc_position_"+ i).val() +" "+ $("#sel_cc_name_"+ i).val() +",");
						$("#cc_ids").val($("#cc_id").val()+ $("#sel_cc_id_"+ i).val() +",");

						$("#check_cc_"+ i).attr("disabled",true);
					}
				}
			}
		});
		//동반자 선택
		$("#sel_click_partner").click(function(){
			var total = $(":checkbox[name=check_partner]:checked:enabled").length;
			if (total == 0)
			{
				alert("동반자를 선택해 주세요.");
				return;
			}

			var kids = $("#list_partner").children().length;
			if (kids+total > <?=$partner_count?>)
			{
				alert("동반자는 <?=$partner_count?>명까지 선택 가능합니다.");
				return;
			}

			var j = kids;

			for (var i=1; i<=$("#total_partner").val(); i++)
			{
				if ($("#check_partner_"+ i).is(":checked") && $("#check_partner_"+ i).is(":enabled"))
				{
					j = j + 1;
					var DivList = "";

					DivList += "<li id='list_partner_"+ j +"' name='list_partner'>";
					DivList += "	<input type='hidden' name='list_partner_input' id='list_partner_input_"+ j +"' value=''>";
					DivList += "	<input type='hidden' name='list_partner_id' id='list_partner_id_"+ j +"' value='"+$("#sel_partner_id_"+ i).val()+"'>";
					DivList += "	<input type='hidden' name='list_partner_login' id='list_partner_login_"+ j +"' value='"+$("#sel_partner_login_"+ i).val()+"'>";
					DivList += "	<input type='hidden' name='list_partner_position' id='list_partner_position_"+ j +"' value='"+$("#sel_partner_position_"+ i).val()+"'>";
					DivList += "	<input type='hidden' name='list_partner_name' id='list_partner_name_"+ j +"' value='"+$("#sel_partner_name_"+ i).val()+"'>";
					DivList += "	<p class='attach_file'><span style='cursor:pointer' onclick=oneCheck('check_list_partner','list_partner_check','list_partner_"+ j +"');>" + $("#sel_partner_position_"+ i).val() +" "+ $("#sel_partner_name_"+ i).val() + "</span></p><a href=javascript:oneDel('list_partner_"+ j +"','check_partner_"+ j +"','total_partner'); class='delete'><img src='/img/icon_del.gif' alt='삭제'></a>";
					DivList += "</li>";

					$("#list_partner").append(DivList);

					$("#partner_list").val($("#partner_list").val()+ $("#sel_partner_position_"+ i).val() +" "+ $("#sel_partner_name_"+ i).val() +",");
					$("#partner_ids").val($("#partner_id").val()+ $("#sel_partner_id_"+ i).val() +",");

					$("#check_partner_"+ i).attr("disabled",true);
				}
			}
		});

		//검색
		$("#searchBtn1").attr("style","cursor:pointer;").click(function(){
			$("#popup_form1").attr("target","hdnFrame");
			$("#popup_form1").attr("action","person_list.php"); 
			$("#popup_form1").submit();
		});
		$("#searchBtn2").attr("style","cursor:pointer;").click(function(){
			$("#popup_form2").attr("target","hdnFrame");
			$("#popup_form2").attr("action","person_list.php"); 
			$("#popup_form2").submit();
		});
		$("#search_name1").keypress(function(e){
			if (e.keyCode == 13)
			{
				$("#popup_form1").attr("target","hdnFrame");
				$("#popup_form1").attr("action","person_list.php"); 
				$("#popup_form1").submit();
			}
		});
		$("#search_name2").keypress(function(e){
			if (e.keyCode == 13)
			{
				$("#popup_form2").attr("target","hdnFrame");
				$("#popup_form2").attr("action","person_list.php"); 
				$("#popup_form2").submit();
			}
		});
		//취소
		$("#resetBtn1").attr("style","cursor:pointer;").click(function(){
			$("#search_name1").val("");
			$("#popup_form1").attr("target","hdnFrame");
			$("#popup_form1").attr("action","person_list.php"); 
			$("#popup_form1").submit();
		});
		$("#resetBtn2").attr("style","cursor:pointer;").click(function(){
			$("#search_name2").val("");
			$("#popup_form2").attr("target","hdnFrame");
			$("#popup_form2").attr("action","person_list.php"); 
			$("#popup_form2").submit();
		});

		//부모창 적용
		$("#popup_select_ok").attr("style","cursor:pointer;").click(function(){
			$("#to_ids").val("");
			
			for (var i=0; i<5; i++)
			{
				$("#to_position_"+i).html("&nbsp;");
				$("#to_name_"+i).html("<a href=\"javascript:toAdd();\"><img src=\"/img/btn_appoint.gif\" name=\"ToAddBtn\"></a>");
				$("#to_del_"+i).html("&nbsp;");
			}

			for (var i=0; i<$("#list_to").children().length; i++)
			{
				if ($("[name=list_to_input]").eq(i).val() == "")
				{
					$("#to_ids").val($("#to_ids").val() + $("[name=list_to_id]").eq(i).val() + ",");
					
					$("#to_position_"+i).text($("[name=list_to_position]").eq(i).val());
					$("#to_name_"+i).text($("[name=list_to_name]").eq(i).val());
					$("#to_del_"+i).html("<a href=\"javascript:toAdd();\"><img src=\"/img/btn_change.gif\"></a>");
				}
			}
			$("#to_id").val($("#to_ids").val());

			$("#cc_list").val("");
			$("#cc_ids").val("");
			for (var i=0; i<$("#list_cc").children().length; i++)
			{
				if ($("[name=list_cc_input]").eq(i).val() == "")
				{
					$("#cc_list").val($("#cc_list").val() + $("[name=list_cc_position]").eq(i).val() + " " + $("[name=list_cc_name]").eq(i).val() + ",");
					$("#cc_ids").val($("#cc_ids").val() + $("[name=list_cc_id]").eq(i).val() + ",");
				}
			}
			$("#cc").val($("#cc_list").val());
			$("#cc_id").val($("#cc_ids").val());

			$("#popToAdd").attr("style","display:none;");
		});
		$("#popup_partner_ok").attr("style","cursor:pointer;").click(function(){
			$("#partner_list").val("");
			$("#partner_ids").val("");
			for (var i=0; i<$("#list_partner").children().length; i++)
			{
				if ($("[name=list_partner_input]").eq(i).val() == "")
				{
					$("#partner_list").val($("#partner_list").val() + $("[name=list_partner_position]").eq(i).val() + " " + $("[name=list_partner_name]").eq(i).val() + ",");
					$("#partner_ids").val($("#partner_ids").val() + $("[name=list_partner_id]").eq(i).val() + ",");
				}
			}

			$("#partner").val($("#partner_list").val());
			$("#partner_id").val($("#partner_ids").val());

			$("#popPartnerAdd").attr("style","display:none;");
		});
	});

	// 위로 이동
	function list_up()
	{
		var ul_id = $("#move_ul").val();
		var li_id = $("#"+ul_id).val();

		if (li_id == "")
		{
			alert("이동 할 담당자를 선택해 주세요");
			return;
		}
		else
		{
			// 위로 이동이 가능한지 확인
			var prev_item = $("#"+li_id).prev();

			if ($(prev_item).attr("id") == undefined) // id가 정의되어 있지 않다면 최상위 li 영역
			return;

			// 현재 선택된 li 를 제외시킨다.
			var selected_item = $("#"+li_id).detach(); 

			// 상위 li 다음에 삽입하여 위치를 교환시킨다.
			$(prev_item).before(selected_item);
		}
	}

	// 아래로 이동
	function list_down()
	{
		var ul_id = $("#move_ul").val();
		var li_id = $("#"+ul_id).val();

		if (li_id == "")
		{
			alert("이동 할 담당자를 선택해 주세요");
			return;
		}
		else
		{
			// 아래로 이동이 가능한지 확인
			var next_item = $("#"+li_id).next();

			if ($(next_item).attr("id") == undefined) // id가 정의되어 있지 않다면 최하위 li 영역
			return;

			// 현재 선택된 li 를 제외시킨다.
			var selected_item = $("#"+li_id).detach();

			// 하위 li 다음에 삽입하여 위치를 교환시킨다.
			$(next_item).after(selected_item);
		}
	}

    function oneCheck(a,b,c){
		$("#"+a).val(c);
		$("#"+c).parent().children().attr("style","background:#fff;");
		$("#"+c).attr("style","font-weight:bold;");
		$("#move_ul").val(a);
    }
	function oneDel(a,b,c){
		$("#"+a).remove();
		$("#"+b).attr("disabled",false);
		$("#"+b).attr("checked",false);
	}

	function changeDiv(){
		if ($("#search_type1").val() == "to")
		{
			$("#cc_list").addClass('hide');
			$("#to_list").removeClass('hide');
		}
		else if ($("#search_type1").val() == "cc")
		{
			$("#to_list").addClass('hide');
			$("#cc_list").removeClass('hide');
		}
	}

	function addFile()
	{
		if (document.getElementById("file_D2").style.display == "none")
		{
			document.getElementById("file_D2").style.display = "inline";
		}
		else
		{
			if (document.getElementById("file_D3").style.display == "none")
			{
				document.getElementById("file_D3").style.display = "inline";
			}
			else
			{
				alert("파일 첨부는 최대 3개까지 가능합니다.");
			}
		}
	}

	function delFile(file)
	{
		document.getElementById("file_"+file).value = "";
		document.getElementById("attachment_"+file).value = "";
		document.getElementById("delfile_"+file).innerHTML = "";
		document.getElementById("filedel_"+file).value = "Y";
	}

	$(document).ready(function(){
		//선택된 파일명 표시
		$("#file_1").change(function(){
			var str = this.value;
			var arr_str = str.split("\\");
			var arr_len = arr_str.length;
			$("#attachment_1").val(this.value);
			$("#delfile_1").html("<span>"+arr_str[arr_len-1]+"</span><a href='javascript:delFile(1);'><img src='/img/btn_delete.gif' alt='삭제' /></a>");
		});
		$("#file_2").change(function(){
			var str = this.value;
			var arr_str = str.split("\\");
			var arr_len = arr_str.length;
			$("#attachment_2").val(this.value);
			$("#delfile_2").html("<span>"+arr_str[arr_len-1]+"</span><a href='javascript:delFile(2);'><img src='/img/btn_delete.gif' alt='삭제' /></a>");
		});
		$("#file_3").change(function(){
			var str = this.value;
			var arr_str = str.split("\\");
			var arr_len = arr_str.length;
			$("#attachment_3").val(this.value);
			$("#delfile_3").html("<span>"+arr_str[arr_len-1]+"</span><a href='javascript:delFile(3);'><img src='/img/btn_delete.gif' alt='삭제' /></a>");
		});
	 });

	//등록
	function funWrite(type)
	{
		var frm = document.form;
		var contents =  CKEDITOR.instances['contents'].getData();//ckeditor 붙인 본문 값 받기

		if (type == "save") {
			var type_text = "임시저장";
			var goUrl = "approval_write_act.php";
		}
		if (type == "write") {
			var type_text = "등록";
			var goUrl = "approval_write_act.php";
		}
		if (type == "modify_save") {
			var type_text = "수정";
			var goUrl = "approval_modify_act.php";
		}
		if (type == "modify") {
			var type_text = "수정";
			var goUrl = "approval_modify_act.php";
		}
<?	if (strstr($prs_team,"2실") && ($doc_form == "002" || ($doc_form == "004" && strstr($form_title,"프로젝트")))) {	?>
		var to_str = frm.to_id.value;

		if(!to_str.match("87") && !to_str.match("148")){
			alert("프로젝트 담당 대표님을 선택해 주세요.");
			frm.to_id.focus();
			return;
		}
<?	}	?>

	<?	if ($form_category == "프로젝트 관련품의서") {	?>
		if(frm.project_no.value == ""){
			alert("프로젝트를 선택해주세요");
			frm.project_no.focus();
			return;
		}
	<?	}	?>
		if(frm.title.value == ""){
			alert("제목을 입력해주세요");
			frm.title.focus();
			return;
		}
		if(contents==""){
			alert("내용을 입력해주세요");
			CKEDITOR.instances['contents'].focus();		//ckeditor 포커스 이동하는 부분
			return;    	
		}

		if ($("#form_title").val() == "출산휴가")
		{
			var fr = new Date($("#fr_date").val());
			var to = new Date($("#to_date").val());

			var limitY = fr.getFullYear();
			var limitM = fr.getMonth()+4;
			var limitD = fr.getDate();

			if (limitM > 12)
			{
				limitY = limitY + 1;
				limitM = limitM - 12;
			}

			var limit = new Date(limitM+"/"+limitD+"/"+limitY);

			if (to >= limit)
			{
				alert("출산휴가는 최대 3개월 입니다.");
				$("#to_month").focus();
				return;
			}
		}
		if ($("#form_title").val() == "육아휴직")
		{
			var fr = new Date($("#fr_date").val());
			var to = new Date($("#to_date").val());

			var limitY = fr.getFullYear()+1;
			var limitM = fr.getMonth()+1;
			var limitD = fr.getDate();

			var limit = new Date(limitM+"/"+limitD+"/"+limitY);

			if (to >= limit)
			{
				alert("육아휴직은 최대 1년 입니다.");
				$("#to_month").focus();
				return;
			}
		}

		//내용 유효성 검사 할 부분
		if(confirm("<?=$form_category?>를 "+ type_text +" 하시겠습니까")){
			frm.type.value = type;
			frm.target ="hdnFrame";
			frm.action = goUrl; 
			frm.submit();
		}
	}
	//문서 양식 변경
	function selDocument(f)
	{
		f.target="_self";
		f.action="<?=CURRENT_URL?>";
		f.submit();
	}
</script>
<script src="/js/approval.js"></script>
</head>

<body>
<div id="approval" class="wrapper">
<form name="form" method="post" enctype="multipart/form-data">
<input type="hidden" name="type" value="<?=$type?>">
<input type="hidden" name="doc_mode" value="<?=$doc_mode?>">
<input type="hidden" name="form_category" value="<?=$form_category?>">
	<? include INC_PATH."/top_menu.php"; ?>

		<div class="inner-home">
		<? include INC_PATH."/approval_menu.php"; ?>

			<div class="approvalDocument-wrap clearfix">
				<div class="left-wrap">
					<div class="menu">
						<ul>
							<li><a href="approval_write.php?doc_form=001"><? if ($doc_form == "001") { ?><strong class="orange">+ 비용품의서</strong><? } else { ?>+ 비용품의서<? } ?></a></li>
							<li><a href="approval_write.php?doc_form=002"><? if ($doc_form == "002") { ?><strong class="orange">+ 프로젝트 관련품의</strong><? } else { ?>+ 프로젝트 관련품의<? } ?></a></li>
							<li><a href="approval_write.php?doc_form=003"><? if ($doc_form == "003") { ?><strong class="orange">+ 외근계/파견계</strong><? } else { ?>+ 외근계/파견계<? } ?></a></li>
							<li><a href="approval_write.php?doc_form=004"><? if ($doc_form == "004") { ?><strong class="orange">+ 휴가계</strong><? } else { ?>+ 휴가계<? } ?></a></li>
							<li><a href="approval_write.php?doc_form=005"><? if ($doc_form == "005") { ?><strong class="orange">+ 출장계</strong><? } else { ?>+ 출장계<? } ?></a></li>
							<li><a href="approval_write.php?doc_form=006"><? if ($doc_form == "006") { ?><strong class="orange">+ 사유서</strong><? } else { ?>+ 사유서<? } ?></a></li>
							<li><a href="approval_write.php?doc_form=007"><? if ($doc_form == "007") { ?><strong class="orange">+ 시말서</strong><? } else { ?>+ 시말서<? } ?></a></li>
							<li><a href="approval_write.php?doc_form=008"><? if ($doc_form == "008") { ?><strong class="orange">+ 조퇴계</strong><? } else { ?>+ 조퇴계<? } ?></a></li>
						</ul>
					</div>
				</div>

				<div class="content-wrap"> 
					<div class="title clearfix">
						<table class="notable " width="100%">
							<tr>
								<th scope="row"><?=$form_category?></th>
								<td>
									(2017 df 조직개편에 따라 결재/수신참조가 변경되었으니 꼭 확인해 주세요.)
								</td>
							</tr>
						</table>




					</div>

					<table class="content-table" width="100%">
						<colgroup>
							<col width="5%" />
							<col width="45%" />
							<col width="5%" />
							<col width="45%" />
						</colgroup>
						<tbody>
						   <tr>
								<th class="gray">문서번호</th>
								<td><?=$doc_no?><input type="hidden" name="doc_no" value="<?=$doc_no?>"></td>
								<th class="gray" rowspan="3">결재</th>
								<td rowspan="3" class="app-td">
									<table width="20%" style="float:left; border-right:1px solid #b2b2b2">
									  <tr><td id="to_position_0"><?=$prs_position?></td></tr>
									  <tr><td id="to_name_0"><?=$prs_name?></td></tr>
									  <tr style="border:0;"><td id="to_del_0"></td></tr>
									</table>
					<?
						$to = $prs_id . ",";
						$max_to_count = $to_count - 1;

						if ($type == "write")
						{
							$field = "FORM_". $doc_form;
							if ($form_category == "휴가계")
							{
								if (strstr($form_title,"프로젝트"))
								{
									$field = $field ."_2";
								}
								else
								{
									$field = $field ."_1";
								}
							}

							$sql = "SELECT $field FROM DF_APPROVAL_TO_LINE WHERE PRS_ID = '$prs_id'";
							$rs = sqlsrv_query($dbConn, $sql);

							$record = sqlsrv_fetch_array($rs);
							$to_ord = $record[0];

							if ($to_ord != "")
							{
								$to_ord_ex = explode("-",$to_ord);
								for ($i=0; $i<sizeof($to_ord_ex); $i++)
								{
									$j = $i + 1;
									$k = $i + 2;

									$toSql = "SELECT PRS_NAME, PRS_POSITION, PRS_ID FROM DF_PERSON WHERE PRS_ID = '$to_ord_ex[$i]'";
									$toRs = sqlsrv_query($dbConn, $toSql);

									$toRecord = sqlsrv_fetch_array($toRs);

									$to_name = $toRecord['PRS_NAME'];
									$to_position = $toRecord['PRS_POSITION'];
									$to_id = $toRecord['PRS_ID'];
				?>
								<table width="20%" style="float:left; border-right:1px solid #b2b2b2">
								  <tr><td id="to_position_<?=$j?>"><?=$to_position?></td></tr>
								  <tr><td id="to_name_<?=$j?>"><?=$to_name?></td></tr>
								  <tr style="border:0;"><td id="to_del_<?=$j?>"><? if ($j > 0) { ?><a href="javascript:toAdd();"><img src="/img/btn_change.gif"></a><? } ?></td></tr>
								</table>
				<?
									$to = $to . $to_id .",";
								}
							}
							else
							{
								$i = 0;
							}

							for ($m=$i; $m<$max_to_count; $m++)
							{
								$j = $m + 1;
								$k = $m + 2;

								$toSql = "SELECT PRS_NAME, PRS_POSITION, PRS_ID FROM DF_PERSON WHERE PRS_ID = '$to_ord_ex[$i]'";
								$toRs = sqlsrv_query($dbConn, $toSql);

								$toRecord = sqlsrv_fetch_array($toRs);

								$to_name = $toRecord['PRS_NAME'];
								$to_position = $toRecord['PRS_POSITION'];
								$to_id = $toRecord['PRS_ID'];
				?>
								<table width="20%" style="float:left; border-right:1px solid #b2b2b2">
								  <tr><td id="to_position_<?=$j?>">&nbsp;</td></tr>
								  <tr><td id="to_name_<?=$j?>"><a href="javascript:toAdd();"><img src="/img/btn_appoint.gif" alt="" name="ToAddBtn"></a></td></tr>
								  <tr style="border:0;"><td id="to_del_<?=$j?>">&nbsp;</td></tr>
								</table>
				<?
							}
						}
						else if ($type == "modify")
						{
							for ($i=0; $i<$max_to_count; $i++)
							{
								$j = $i + 1;
								$k = $i + 2;

								$sql = "SELECT A_PRS_NAME, PRS_POSITION, A_PRS_ID FROM DF_APPROVAL_TO INNER JOIN DF_PERSON ON A_PRS_ID = PRS_ID WHERE DOC_NO = '$doc_no' AND A_ORDER ='$k' AND PRF_ID IN (1,2,3,4,7)";
								$rs = sqlsrv_query($dbConn, $sql);

								$record = sqlsrv_fetch_array($rs);
								$rows = sqlsrv_has_rows($rs);

								if ($rows > 0)
								{
									$to_name = $record['A_PRS_NAME'];
									$to_position = $record['PRS_POSITION'];
									$to_id = $record['A_PRS_ID'];
				?>
								<table width="20%" style="float:left; border-right:1px solid #b2b2b2">
								  <tr><td id="to_position_<?=$j?>"><?=$to_position?></td></tr>
								  <tr><td id="to_name_<?=$j?>"><?=$to_name?></td></tr>
								  <tr style="border:0;"><td id="to_del_<?=$j?>"><? if ($j > 0) { ?><a href="javascript:toAdd();"><img src="/img/btn_change.gif"></a><? } ?></td></tr>
								</table>
				<?
								}
								else
								{
									$to_name = "";
									$to_position = "";
									$to_id = "";
				?>
								<table width="20%" style="float:left; border-right:1px solid #b2b2b2">
								  <tr><td id="to_position_<?=$j?>">&nbsp;</td></tr>
								  <tr><td id="to_name_<?=$j?>"><a href="javascript:toAdd();"><img src="/img/btn_appoint.gif" alt="" name="ToAddBtn"></a></td></tr>
								  <tr style="border:0;"><td id="to_del_<?=$j?>">&nbsp;</td></tr>
								</table>
				<?
								}
								$to = $to . $to_id .",";
							}
						}
				?>
									<input type="hidden" name="to_id" id="to_id" value="<?=$to?>">
								</td>
						   </tr>
						   <tr>
								<th class="gray">문서종류</th>
								<td>
									<input type="hidden" name="form_no" value="<?=$form_no?>">
								<? if ($form_category == "휴가계") { ?>
									<select name="form_title" onChange="javascript:selDocument(this.form);" style="width:120px;">
								<?
									$sql = "SELECT TITLE FROM DF_APPROVAL_FORM WITH(NOLOCK) WHERE CATEGORY = '$form_category' AND USE_YN = 'Y' ORDER BY ORD";
									$rs = sqlsrv_query($dbConn, $sql);

									while ($record = sqlsrv_fetch_array($rs)) {
								?>
										<option value="<?=$record['TITLE']?>"<? if ($form_title == $record['TITLE']) { echo " selected"; } ?>><?=$record['TITLE']?></option>
								<?	} ?>
									</select>
								<? } else { ?>
									<input type="hidden" name="form_title" value="<?=$form_title?>">
									<?=$form_title?>
								<?	} ?>
								</td>
						   </tr>
							<tr>
								<th class="gray">공개여부</th>
								<td>
									<select name="open_yn">
										<option value="Y"<? if ($open_yn == "Y") { ?> selected<? } ?>>공개</option>
										<option value="N"<? if ($open_yn == "N") { ?> selected<? } ?>>비공개</option>
									</select>
								</td>
							</tr>
							<tr>
								<th class="gray">기안일</th>
								<td>
									<input type="hidden" name="up_year" id="up_year" value="<?=$up_year?>">
									<input type="hidden" name="up_month" id="up_month" value="<?=$up_month?>">
									<input type="hidden" name="up_day" id="up_day" value="<?=$up_day?>">
									<span><?=$up_year?>년 <?=$up_month?>월 <?=$up_day?>일</span>
								</td>
								<th class="gray">수신참조</th>
								<td class="appoint">
						<?
							if ($type == "modify")
							{
								$sql = "SELECT C_PRS_NAME, PRS_POSITION, C_PRS_ID FROM DF_APPROVAL_CC INNER JOIN DF_PERSON ON C_PRS_ID = PRS_ID WHERE DOC_NO = '$doc_no' AND PRF_ID IN (1,2,3,4,7) ORDER BY C_ORDER, PRS_POSITION";
								$rs = sqlsrv_query($dbConn, $sql);

								$i = 0;
								$cc = "";
								$c_id = "";
								while ($record = sqlsrv_fetch_array($rs))
								{
									$cc_name = $record['C_PRS_NAME'];
									$cc_position = $record['PRS_POSITION'];
									$cc_id = $record['C_PRS_ID'];

									if ($i == 0) {
										$cc = $cc_position ." ". $cc_name;
										$c_id = $cc_id .",";
									} else {
										$cc = $cc .", ". $cc_position ." ". $cc_name;
										$c_id = $c_id. $cc_id .",";
									}
									
									$i++;
								}
							}
						?>
									<input type="hidden" name="cc_id" id="cc_id" value="<?=$c_id?>">
									<span><input name="cc" id="cc" readonly value="<?=$cc?>"></span>
									<a href="javascript:ccAdd();"><img src="/img/btn_referenceAppoint.gif" id="CcAddBtn"></a>
								</td>
							</tr>
						   <tr>
								<th class="gray">부서</th>
								<td colspan="3"><?=getTeamInfo($prs_team)?></td>
						   </tr>
							<tr>
								<th class="gray">이름</th>
								<td colspan="3"><?=$prs_position?> <?=$prs_name?></td>
							</tr>
							<tr class="last">
								<th class="gray">제목</th>
								<td colspan="3"><input type="text" name="title" value="<?=$title?>"></td>
							</tr>
						<? if ($form_category == "비용품의서" || $form_category == "프로젝트 관련품의서") { ?>	
							<tr class="last">
								<th class="gray">프로젝트</th>
								<td colspan="3">
									<select name="project_no" style="width:500px;">
										<option value=""></option>
						<?
							/*
								$sql = "SELECT 
											A.PROJECT_NO, A.TITLE
										FROM 
											DF_PROJECT A INNER JOIN DF_PROJECT_DETAIL B
										ON
											A.PROJECT_NO = B.PROJECT_NO
										WHERE
											A.USE_YN = 'Y' AND A.STATUS = 'ING' AND A.COMPLETE = 'N' AND B.PRS_ID IN ($prs_id)
										ORDER BY 
											A.PROJECT_NO DESC";
							*/
								$sql = "SELECT 
											PROJECT_NO, TITLE
										FROM 
											DF_PROJECT 
										WHERE 
											USE_YN = 'Y' AND STATUS = 'ING' AND COMPLETE = 'N'
										ORDER BY 
											PROJECT_NO DESC";
								$rs = sqlsrv_query($dbConn,$sql);
								while ($record = sqlsrv_fetch_array($rs))
								{
									$p_no = $record['PROJECT_NO'];
									$p_title= $record['TITLE'];
						?>
										<option value="<?=$p_no?>"<? if ($project_no == $p_no) { echo " selected"; } ?>>[<?=$p_no?>] <?=$p_title?></option>
						<?
								}
						?>
									</select>
								</td>
							</tr>
						<? } else { ?>
							<tr class="period">
								<th class="gray">기간</th>
								<td colspan="3" class="last">
									<select name="fr_year" id="fr_year">
									<?
										for ($i=$startYear; $i<=date("Y",strtotime("+1 year")); $i++) 
										{
											if ($i == substr($start_date,0,4)) 
											{ 
												$selected = " selected"; 
											}
											else
											{
												$selected = "";
											}

											echo "<option value='".$i."'".$selected.">".$i."</option>";
										}
									?>
									</select>
									<span>년</span>
									<select name="fr_month" id="fr_month">
									<?
										for ($i=1; $i<=12; $i++) 
										{
											if (strlen($i) == "1") 
											{
												$j = "0".$i;
											}
											else
											{
												$j = $i;
											}

											if ($j == substr($start_date,5,2))
											{
												$selected = " selected";
											}
											else
											{
												$selected = "";
											}

											echo "<option value='".$j."'".$selected.">".$i."</option>";
										}
									?>
									</select>
									<span>월</span>
									<select name="fr_day" id="fr_day">
									<?
										for ($i=1; $i<=31; $i++) 
										{
											if (strlen($i) == "1") 
											{
												$j = "0".$i;
											}
											else
											{
												$j = $i;
											}

											if ($j == substr($start_date,8,2))
											{
												$selected = " selected";
											}
											else
											{
												$selected = "";
											}

											echo "<option value='".$j."'".$selected.">".$i."</option>";
										}
									?>
									</select>
									<span>일</span>
									<input type="hidden" id="fr_date" class="datepicker">
									<span>-</span>
									<select name="to_year" id="to_year">
									<?
										for ($i=$startYear; $i<=date("Y",strtotime("+1 year")); $i++) 
										{
											if ($i == substr($end_date,0,4)) 
											{ 
												$selected = " selected"; 
											}
											else
											{
												$selected = "";
											}

											echo "<option value='".$i."'".$selected.">".$i."</option>";
										}
									?>
									</select>
									<span>년</span>
									<select name="to_month" id="to_month">
									<?
										for ($i=1; $i<=12; $i++) 
										{
											if (strlen($i) == "1") 
											{
												$j = "0".$i;
											}
											else
											{
												$j = $i;
											}

											if ($j == substr($end_date,5,2))
											{
												$selected = " selected";
											}
											else
											{
												$selected = "";
											}

											echo "<option value='".$j."'".$selected.">".$i."</option>";
										}
									?>
									</select>
									<span>월</span>
									<select name="to_day" id="to_day">
									<?
										for ($i=1; $i<=31; $i++) 
										{
											if (strlen($i) == "1") 
											{
												$j = "0".$i;
											}
											else
											{
												$j = $i;
											}

											if ($j == substr($end_date,8,2))
											{
												$selected = " selected";
											}
											else
											{
												$selected = "";
											}

											echo "<option value='".$j."'".$selected.">".$i."</option>";
										}
									?>
									</select>
									<span>일</span>
									<input type="hidden" id="to_date" class="datepicker">
								</td>
							</tr>
						<? } ?>
						<? if ($form_category == "외근계/파견계" || $form_category == "출장계") { ?>				
							<tr class="partner">
								<th class="gray">동반자</th>
								<td colspan="3" class="appoint">
							<?
							if ($type == "write") 
							{
								$sql = "SELECT P_PRS_NAME, PRS_POSITION, P_PRS_ID FROM DF_APPROVAL_PARTNER INNER JOIN DF_PERSON ON P_PRS_ID = PRS_ID WHERE DOC_NO = '$last_doc_no' ORDER BY P_ORDER";
							}
							else if ($type == "modify")
							{
								$sql = "SELECT P_PRS_NAME, PRS_POSITION, P_PRS_ID FROM DF_APPROVAL_PARTNER INNER JOIN DF_PERSON ON P_PRS_ID = PRS_ID WHERE DOC_NO = '$doc_no' ORDER BY P_ORDER";
							}
								$rs = sqlsrv_query($dbConn, $sql);

								$i = 0;
								$partner = "";
								$p_id = "";
								while ($record = sqlsrv_fetch_array($rs))
								{
									$partner_name = $record['P_PRS_NAME'];
									$partner_position = $record['PRS_POSITION'];
									$partner_id = $record['P_PRS_ID'];

									if ($i == 0) {
										$partner = $partner_position ." ". $partner_name;
										$p_id = $partner_id;
									} else {
										$partner = $partner .", ". $partner_position ." ". $partner_name;
										$p_id = $p_id .", ". $partner_id;
									}
									
									$i++;
								}
							?>
									<input type="hidden" name="partner_id" id="partner_id" value="<?=$p_id?>">
									<span><input name="partner" id="partner" readonly value="<?=$partner?>"></span>
									<a href="#"><img src="/img/btn_partnerAppoint.gif" alt="" id="PartnerAddBtn"></a>
								</td>
							</tr>
						<? } ?>
							<tr>
								<th class="gray">양식</th>
								<td colspan="3" style="padding:10px;"><textarea name="contents" style="width:100%;height:100%;"><?=$contents?></textarea></td>
							</tr>
						<? if ($form_category == "비용품의서" || $form_category == "프로젝트 관련품의서") { ?>	
							<tr class="last">
								<th class="gray">결제금액</th>
								<td colspan="3">
						<?
							$sql = "SELECT 
										MEMO, MONEY, ACTUAL
									FROM 
										DF_PROJECT_EXPENSE
									WHERE 
										DOC_NO = '$doc_no' AND LAST = 'Y'
									ORDER BY 
										IDX";
							$rs = sqlsrv_query($dbConn,$sql);

							$expense = 0;
							while ($record = sqlsrv_fetch_array($rs))
							{
								$expense_memo = $record['MEMO'];
								$expense_moeny = $record['MONEY'];
								$expense_actual = $record['ACTUAL'];
						?>
									<table width="100%">
									<tbody>
										<tr>
											<th>항목<?=$expense+1?></th>
											<td align="left">
												<input id="memo_<?=$expense?>" name="memo_<?=$expense?>" class="df_textinput" type="text" style="width:400px;" value="<?=$expense_memo?>">
											</td>
											<th>금액</th>
											<td>
												<input id="money_<?=$expense?>" name="money_<?=$expense?>" class="df_textinput" type="text" style="ime-mode:disabled;width:100px;" onKeyPress="if (event.keyCode < 48 || event.keyCode > 57) { event.returnValue = false; }" onKeyup="javascript:checkThousand(this,this.value);" value="<?=number_format($expense_moeny,0)?>">
											</td>
											<th>실비여부</th>
											<td>
												<input type="checkbox" name="actual_<?=$expense?>" value="Y"<? if ($expense_actual == "Y") { echo " checked"; } ?>>
												<input type="hidden" name="idx_<?=$expense?>" value="<?=$expense?>">
											</td>
										</tr>
									</tbody>
									</table>
							<?
									$expense = $expense + 1;
								}

								for ($i=$expense; $i<5; $i++)
								{
							?>
									<table width="100%">
									<tbody>
										<tr>
											<th>항목<?=$i+1?></th>
											<td align="left">
												<input id="memo_<?=$i?>" name="memo_<?=$i?>" class="df_textinput" type="text" style="width:400px;" value="">
											</td>
											<th>금액</th>
											<td>
												<input id="money_<?=$i?>" name="money_<?=$i?>" class="df_textinput" type="text" style="ime-mode:disabled;width:100px;" onKeyPress="if (event.keyCode < 48 || event.keyCode > 57) { event.returnValue = false; }" onKeyup="javascript:checkThousand(this,this.value);" value="">
											</td>
											<th>실비여부</th>
											<td>
												<input type="checkbox" name="actual_<?=$i?>" value="Y">
												<input type="hidden" name="idx_<?=$i?>" value="<?=$i?>">
											</td>
										</tr>
									</tbody>
									</table>
							<?
								}
							?>
								</td>
							</tr>
						<? } ?>
						</tbody>
					</table>
					<div id="bbs">
						<div class="attach_section clearfix">
							<p class="left">첨부파일</p>
							<div class="right clearfix">
								<div class="clearfix">
									<div id="file_D1" name="file_D1">
										<input id="attachment_1" name="attachment_1" class="attach df_textinput" type="text" readonly>
										<div class="input"><input id="file_1" name="file_1" class="browse" type="file"></div>
										<span class="description">※ 한번에 올릴 수 있는 파일 용량은 <b>최대 10MB</b> 입니다.</span>
										<a href="javascript:addFile();" class="btn_plus"><img src="/img/btn_plus.jpg" alt="추가하기" /></a>
										<input type="hidden" name="filedel_1" id="filedel_1">
										<div class="attached" id="delfile_1">
										<? if ($type == "modify" && $file_1 != "") { ?>	
											<span><?=$file_1?></span>
											<a href="javascript:delFile(1);"><img src="/img/btn_delete.gif" alt="삭제" /></a>
										<? } ?>
										</div>
									</div>
									<div id="file_D2" name="file_D2"<? if ($file_2 == "") { ?> style="display:none;"<? } ?>>
										<input id="attachment_2" name="attachment_2" class="attach df_textinput" type="text" readonly>
										<div class="input"><input id="file_2" name="file_2" class="browse" type="file"></div>
										<input type="hidden" name="filedel_2" id="filedel_2">
										<div class="attached" id="delfile_2">
										<? if ($type == "modify" && $file_2 != "") { ?>	
											<span><?=$file_2?></span>
											<a href="javascript:delFile(2);"><img src="/img/btn_delete.gif" alt="삭제" /></a>
										<? } ?>
										</div>
									</div>
									<div id="file_D3" name="file_D3"<? if ($file_3 == "") { ?> style="display:none;"<? } ?>>
										<input id="attachment_3" name="attachment_3" class="attach df_textinput" type="text" readonly>
										<div class="input"><input id="file_3" name="file_3" class="browse" type="file"></div>
										<input type="hidden" name="filedel_3" id="filedel_3">
										<div class="attached" id="delfile_3">
										<? if ($type == "modify" && $file_3 != "") { ?>	
											<span><?=$file_3?></span>
											<a href="javascript:delFile(3);"><img src="/img/btn_delete.gif" alt="삭제" /></a>
										<? } ?>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="btn-wrap">
					<? if ($type == "write") { ?>
						<a href="javascript:funWrite('save');"><img src="/img/btn_tmpSave.gif" alt=""></a>
						<a href="javascript:funWrite('write');" class="floatr"><img src="/img/btn_insert_154.gif" alt=""></a>
					<? } else if ($type == "modify") { ?>
						<? if ($status == "임시") { ?>
							<a href="javascript:funWrite('modify_save');"><img src="/img/btn_tmpSave.gif" alt=""></a>
						<? } ?>
						<a href="javascript:funWrite('modify');" class="floatr"><img src="/img/btn_insert_154.gif" alt=""></a>
					<? } ?>
					</div>

				</div>
			</div>
		</div>
</form>
<? include INC_PATH."/bottom.php"; ?>
</div>

<div id="popToAdd" class="approvalDocument-popup1" style="display:none;">
	<div class="select" id="popup_select1">
	<form name="popup_form1" id="popup_form1" method="post">
		<div class="pop_top">
			<p class="pop_title">결재선 등록</p>
			<a href="javascript:HidePop('ToAdd');" class="close">닫기</a>
		</div>
		<div class="pop_bottom clearfix">
			<div class="left section">
				<div class="top">
					<select name="search_type" id="search_type1" class="role" onchange="javascript:changeDiv();">
						<option value="to">결재자</option>
						<option value="cc">참조자</option>
					</select>
				</div>
				<div class="mid clearfix">
					<div class="left_area floatl">
						<div class="search_area">
							<input id="search_name1" name="search_name" class="df_textinput" type="text" style="width:100px; border:none;">
							<img src="/img/project/btn_x.gif" alt="삭제" class="btn_x" id="resetBtn1" />
						</div>
					</div>
					<div class="right_area floatr">
						<img src="/img/btn_search_pop.gif" alt="검색" id="searchBtn1" style="cursor:pointer;">
					</div>
				</div>
				<div id="to_list" class="bottom">
	<?
		$sql = "SELECT SEQNO, POSITION FROM DF_POSITION_CODE WITH(NOLOCK) ORDER BY SEQNO";
		$rs = sqlsrv_query($dbConn,$sql);

		while($record=sqlsrv_fetch_array($rs))
		{
			$orderby .= "WHEN PRS_POSITION='". $record['POSITION'] ."' THEN ". $record['SEQNO'] ." ";
		}

		$orderbycase = " ORDER BY CASE ". $orderby . " END, PRS_NAME";

		$sql = "SELECT PRS_ID, PRS_LOGIN, PRS_POSITION, PRS_NAME FROM DF_PERSON WITH(NOLOCK) WHERE PRF_ID IN (1,2,3,4,7) AND PRS_ID NOT IN (102)". $orderbycase;
		$rs = sqlsrv_query($dbConn,$sql);

		$i = 0;
		while($record=sqlsrv_fetch_array($rs))
		{
			$i++;

			$id = $record['PRS_ID'];
			$login = $record['PRS_LOGIN'];
			$position = $record['PRS_POSITION'];
			$name = $record['PRS_NAME'];
	?>
					<input type="hidden" id="sel_to_id_<?=$i?>" value="<?=$id?>">
					<input type="hidden" id="sel_to_login_<?=$i?>" value="<?=$login?>">
					<input type="hidden" id="sel_to_position_<?=$i?>" value="<?=$position?>">
					<input type="hidden" id="sel_to_name_<?=$i?>" value="<?=$name?>">
					<p><input type="checkbox" id="check_to_<?=$i?>" name="check_to" title="to_<?=$id?>"><label for="check_to_<?=$i?>" style="cursor:pointer;"><?=$position?> <?=$name?></label></p>
	<?
		}
	?>
				</div>
				<input type="hidden" name="total_to" id="total_to" value="<?=$i?>">
				<div id="cc_list" class="bottom">
	<?
		$sql = "SELECT SEQNO, POSITION FROM DF_POSITION_CODE WITH(NOLOCK) ORDER BY SEQNO";
		$rs = sqlsrv_query($dbConn,$sql);

		while($record=sqlsrv_fetch_array($rs))
		{
			$orderby .= "WHEN PRS_POSITION='". $record['POSITION'] ."' THEN ". $record['SEQNO'] ." ";
		}

		$orderbycase = " ORDER BY CASE ". $orderby . " END, PRS_NAME";

		$sql = "SELECT PRS_ID, PRS_LOGIN, PRS_POSITION, PRS_NAME FROM DF_PERSON WITH(NOLOCK) WHERE PRF_ID IN (1,2,3,4,7) AND PRS_ID NOT IN (102)". $orderbycase;
		$rs = sqlsrv_query($dbConn,$sql);

		$i = 0;
		while($record=sqlsrv_fetch_array($rs))
		{
			$i++;

			$id = $record['PRS_ID'];
			$login = $record['PRS_LOGIN'];
			$position = $record['PRS_POSITION'];
			$name = $record['PRS_NAME'];
	?>
					<input type="hidden" id="sel_cc_id_<?=$i?>" value="<?=$id?>">
					<input type="hidden" id="sel_cc_login_<?=$i?>" value="<?=$login?>">
					<input type="hidden" id="sel_cc_position_<?=$i?>" value="<?=$position?>">
					<input type="hidden" id="sel_cc_name_<?=$i?>" value="<?=$name?>">
					<p><input type="checkbox" id="check_cc_<?=$i?>" name="check_cc" title="cc_<?=$id?>"><label for="check_cc_<?=$i?>" style="cursor:pointer;"><?=$position?> <?=$name?></label></p>
	<?
		}
	?>
				</div>
				<input type="hidden" name="total_cc" id="total_cc" value="<?=$i?>">
			</div>
			<img src="/img/btn_select.gif" alt="선택" id="sel_click_to" class="sel" style="cursor:pointer;">
			<div class="right section clearfix">
				<div class="top"><span><strong>결재자</strong><span></div>
				<div id="to_div" name="to_div" class="middle-1" style="height:158px;">
					<input type="hidden" name="total_to" id="total_to" value="0">
					<ul id="list_to" style="padding:5px;"></ul>
					<input type="hidden" name="check_list_to" id="check_list_to">
				</div>
				<input type="hidden" name="to_list" id="to_list" value="<?=$to?>">
				<input type="hidden" name="to_ids" id="to_ids" value="<?=$p_id?>">

				<div class="top"><span><strong>참조자</strong><span></div>
				<div id="cc_div" name="cc_div" class="middle-2" style="height:158px;">
					<input type="hidden" name="total_cc" id="total_cc" value="0">
					<ul id="list_cc" style="padding:5px;"></ul>
					<input type="hidden" name="check_list_cc" id="check_list_cc">
				</div>
				<input type="hidden" name="cc_list" id="cc_list" value="<?=$cc?>">
				<input type="hidden" name="cc_ids" id="cc_ids" value="<?=$p_id?>">

				<a href="javascript:list_up();" style="float:left;"><img src="/img/btn_move_up.gif" alt=""></a>
				<a href="javascript:list_down();"><img src="/img/btn_move_dn.gif" alt=""></a>
				<input type="hidden" name="move_ul" id="move_ul">
				
			</div>
			<img src="/img/btn_accept.gif" alt="확인" id="popup_select_ok" class="accept" style="cursor:pointer;">
		</div>
	</form>
	</div>
</div>

<div id="popPartnerAdd" class="approvalDocument-popup2" style="display:none;">
	<div class="select" id="popup_select2">
	<form name="popup_form2" id="popup_form2" method="post">
	<input type="hidden" name="search_type" id="search_type2" value="partner">
		<div class="pop_top">
			<p class="pop_title">동반자 지정</p>
			<a href="javascript:HidePop('PartnerAdd');" class="close">닫기</a>
		</div>
		<div class="pop_bottom clearfix">
			<div class="left section">
				<div>
					<div class="top">
						<div style="width:262px; padding:18px 0 0 26px">
							<div class="left_area floatl">
								<div class="search_area">
									<input id="search_name2" name="search_name" class="df_textinput" type="text" style="width:100px; border:none;" />
									<img src="/img/project/btn_x.gif" alt="삭제" class="btn_x" id="resetBtn2" />
								</div>
							</div>
							<div class="right_area floatr">
								<img src="/img/btn_search_pop.gif" alt="검색" id="searchBtn2" style="cursor:pointer;">
							</div>
						</div>
					</div>
				</div>

				<div class="bottom" id="partner_list">
	<?
		$sql = "SELECT SEQNO, POSITION FROM DF_POSITION_CODE WITH(NOLOCK) ORDER BY SEQNO";
		$rs = sqlsrv_query($dbConn,$sql);

		while($record=sqlsrv_fetch_array($rs))
		{
			$orderby .= "WHEN PRS_POSITION='". $record['POSITION'] ."' THEN ". $record['SEQNO'] ." ";
		}

		$orderbycase = " ORDER BY CASE ". $orderby . " END, PRS_NAME";

		$sql = "SELECT PRS_ID, PRS_LOGIN, PRS_POSITION, PRS_NAME FROM DF_PERSON WITH(NOLOCK) WHERE PRF_ID IN (1,2,3,4,7) AND PRS_ID NOT IN (102)". $orderbycase;
		$rs = sqlsrv_query($dbConn,$sql);

		$i = 0;
		while($record=sqlsrv_fetch_array($rs))
		{
			$i++;

			$id = $record['PRS_ID'];
			$login = $record['PRS_LOGIN'];
			$position = $record['PRS_POSITION'];
			$name = $record['PRS_NAME'];
	?>
					<input type="hidden" id="sel_partner_id_<?=$i?>" value="<?=$id?>">
					<input type="hidden" id="sel_partner_login_<?=$i?>" value="<?=$login?>">
					<input type="hidden" id="sel_partner_position_<?=$i?>" value="<?=$position?>">
					<input type="hidden" id="sel_partner_name_<?=$i?>" value="<?=$name?>">
					<p><input type="checkbox" id="check_partner_<?=$i?>" name="check_partner" title="partner_<?=$id?>"><label for="check_partner_<?=$i?>" style="cursor:pointer;"><?=$position?> <?=$name?></label></p>
	<?
		}
	?>
					<input type="hidden" name="total_partner" id="total_partner" value="<?=$i?>">
				</div>
			</div>
			<img src="/img/btn_select.gif" alt="선택" id="sel_click_partner" class="sel" style="cursor:pointer;">
			<div class="right section clearfix">
				<div class="top"><span><strong>동반자</strong><span></div>
				<div id="partner_div" name="partner_div" class="middle-1" style="height:352px;">
					<ul id="list_partner" style="padding:5px;"></ul>
					<input type="hidden" name="check_list_partner" id="check_list_partner">
				</div>
				<input type="hidden" name="partner_list" id="partner_list" value="<?=$partner?>">
				<input type="hidden" name="partner_ids" id="partner_ids" value="<?=$p_id?>">

				<a href="javascript:list_up();" style="float:left;"><img src="/img/btn_move_up.gif" alt=""></a>
				<a href="javascript:list_down();"><img src="/img/btn_move_dn.gif" alt=""></a>
				<input type="hidden" name="move_ul" id="move_ul">
			</div>
			<img src="/img/btn_accept.gif" alt="확인" id="popup_partner_ok" class="accept" style="cursor:pointer;">
		</div>
	</form>
	</div>
</div>

</body>
</html>
