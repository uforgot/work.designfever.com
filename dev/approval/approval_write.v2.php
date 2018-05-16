<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
	$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : "write";

	if ($type == "write") {

		if ($prf_id == 7) {	
			$doc_form = isset($_REQUEST['doc_form']) ? $_REQUEST['doc_form'] : "003"; 
		} else {
			$doc_form = isset($_REQUEST['doc_form']) ? $_REQUEST['doc_form'] : "004"; 
		}

		switch($doc_form) {
			case "001" : 
				$form_category = "���ǰ�Ǽ�";
				break;
			case "002" : 
				//$form_category = "������Ʈ ����ǰ�Ǽ�";
				$form_category = "���ǰ�Ǽ�(v2)";
				break;
			case "003" : 
				$form_category = "�ٰܱ�/�İ߰�";
				break;
			case "004" : 
				$form_category = "�ް���";
				break;
			case "005" : 
				$form_category = "�����";
				break;
			case "006" : 
				$form_category = "������";
				break;
			case "007" : 
				$form_category = "�ø���";
				break;
			case "008" : 
				$form_category = "�����";
				break;
			case "009" : 
				$form_category = "�Ի���ΰ�";
				break;
		}

		if ($form_category == "�ް���" ) {
			$form_title = isset($_REQUEST['form_title']) ? $_REQUEST['form_title'] : "����"; 
		} else {
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
	
	// ����ȭ���� ���
	} else if ($type == "modify") {

		$doc_no = isset($_REQUEST['doc_no']) ? $_REQUEST['doc_no'] : null;

		if ($doc_no == "") {
?>
			<script type="text/javascript">
				alert("�ش� ������ �������� �ʽ��ϴ�.");
				self.close();
			</script>
<?
			exit;
		}

		$sql = "SELECT 
					FORM_CATEGORY, FORM_TITLE, TITLE, CONTENTS, OPEN_YN, FILE_1, FILE_2, FILE_3, PROJECT_NO, TEAM_NAME,
					CONVERT(char(10),REG_DATE,120) AS REG_DATE, CONVERT(char(10),START_DATE,120) AS START_DATE, CONVERT(char(10),END_DATE,120) AS END_DATE, STATUS, 
					CONVERT(char(10),APPROVAL_DATE,120) AS APPROVAL_DATE
				FROM 
					DF_APPROVAL WITH(NOLOCK) 
				WHERE 
					DOC_NO = '$doc_no'";
		$rs = sqlsrv_query($dbConn, $sql);

		$record = sqlsrv_fetch_array($rs);

		$form_category	= $record['FORM_CATEGORY'];
		$form_title		= $record['FORM_TITLE'];
		$title			= $record['TITLE'];
		$contents		= $record['CONTENTS'];
		$open_yn		= $record['OPEN_YN'];
		$file_1			= $record['FILE_1'];
		$file_2			= $record['FILE_2'];
		$file_3			= $record['FILE_3'];
		$project_no		= $record['PROJECT_NO'];
		$team			= $record['TEAM_NAME'];
		$reg_date		= $record['REG_DATE'];
		$start_date		= $record['START_DATE'];
		$end_date		= $record['END_DATE'];
		$status			= $record['STATUS'];
		$approval_date	= $record['APPROVAL_DATE'];
		$up_year		= substr($approval_date,0,4);
		$up_month		= substr($approval_date,5,2);
		$up_day			= substr($approval_date,8,2);

		switch($form_category) {
			case "���ǰ�Ǽ�" : 
				$doc_form = "001";
				break;
			//case "������Ʈ ����ǰ�Ǽ�" : 
			case "���ǰ�Ǽ�(v2)" : 
				$doc_form = "002";
				break;
			case "�ٰܱ�/�İ߰�" : 
				$doc_form = "003";
				break;
			case "�ް���" : 
				$doc_form = "004";
				break;
			case "�����" : 
				$doc_form = "005";
				break;
			case "������" : 
				$doc_form = "006";
				break;
			case "�ø���" : 
				$doc_form = "007";
				break;
			case "�����" : 
				$doc_form = "008";
				break;
			case "�Ի���ΰ�" : 
				$doc_form = "009";
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

//	if ($form_category == "�ٰܱ�/�İ߰�" || $form_category == "�����") { 
		$sql = "SELECT TOP 1 DOC_NO FROM DF_APPROVAL WITH(NOLOCK) WHERE PRS_LOGIN = '$prs_login' AND USE_YN = 'Y' AND FORM_CATEGORY = '$form_category' ORDER BY SEQNO DESC";
//	} else {
//		$sql = "SELECT TOP 1 DOC_NO FROM DF_APPROVAL WITH(NOLOCK) WHERE PRS_LOGIN = '$prs_login' AND USE_YN = 'Y' ORDER BY SEQNO DESC";
//	}
	$rs = sqlsrv_query($dbConn, $sql);

	$record = sqlsrv_fetch_array($rs);

	$last_doc_no = $record['DOC_NO'];

	// ����ǥ�� ��ȯ
	$_n = array('0'=>'��','1'=>'��','2'=>'��','3'=>'��','4'=>'��');
?>

<!-- �Ի���ΰ��� ��� ���� ó�� ���� -->
<?
	// �Ի���ΰ�
	if ($doc_form == "009") {
		// ���� üũ
		if(!in_array($prf_id, array("2","3","4"))) { 
?>
	<meta http-equiv="Content-Type" content="text/html" charset="euc-kr">
	<script type="text/javascript">
		alert("�ش��������� ����,����,�ӿ� �� �����ڸ� Ȯ�� �����մϴ�.");
		history.back();
	</script>
<?
			exit;
		}
		// �ۼ� ȭ��
		if($type == "write") {
			$open_yn = "N"; // ����� ����Ʈ
		}
	}
?>
<!-- �Ի���ΰ��� ��� ���� ó�� ���� -->

<? include INC_PATH."/top.php"; ?>

<? if ($form_category != "���ǰ�Ǽ�(v2)") { ?>		
<script type="text/javascript">
	$(document).ready(function(){
		$("#fr_date").val($("#fr_month").val()+"/"+$("#fr_day").val()+"/"+$("#fr_year").val());
		$("#to_date").val($("#to_month").val()+"/"+$("#to_day").val()+"/"+$("#to_year").val());
		//��¥ ����
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
	window.onload=function() {
		CKEDITOR.replace('contents', {
			width:700,
			<? if($doc_form == '002' || $doc_form == '009') { ?>
			height:250,
			<? } else { ?>
			height:500,
			<? } ?>
			skin:'kama',
			enterMode:'2',
			shiftEnterMode:'3',
			filebrowserUploadUrl:'upload.php?type=files',
			filebrowserImageUploadUrl:'upload.php?type=images',
			filebrowserFlashUploadUrl:'upload.php?type=flash'
			}
		);
	};

	function funPersonDel(type,no) 
	{
		if (type == "to") {
			document.getElementsByName("to_div_"+no)[0].innerHTML = "";
			document.getElementsByName("to_btn_div_"+no)[0].innerHTML = "";
			document.getElementsByName("to_btn_div_"+no)[0].innerHTML = "<input type=button id=to_btn_"+no+" name=to_btn_"+no+" value=���� onClick=javascript:funPersonAdd('to','"+no+"');>";
			document.getElementsByName("to_id_"+no)[0].value = "";

			var to_id = "";

			for (var i=0; i<<?=$to_count?>; i++) {
				to_id = to_id + document.getElementsByName("to_id_"+i)[0].value + ",";
			}

			document.form.to_id.value = to_id;
		}
	}

	//����
	function toAdd() 
	{
		$("#search_type1").val("to").attr("selected","selected");
		$("#cc_list").addClass('hide');
		$("#to_list").removeClass('hide');
		$("#popToAdd").attr("style","display:inline;");
	}

	//��������
	function ccAdd() 
	{
		$("#search_type1").val("cc").attr("selected","selected");
		$("#to_list").addClass('hide');
		$("#cc_list").removeClass('hide');
		$("#popToAdd").attr("style","display:inline;");
	}

	$(document).ready(function() {

		<? if ($form_category == "�ٰܱ�/�İ߰�" || $form_category == "�����") { ?>

		//������
		$("#PartnerAddBtn").click(function() {
			$("#popPartnerAdd").attr("style","display:inline;");
		});

		<? } ?>

		//���� ���ð� �ҷ�����
		//������
		var total_to = $("#total_to").val();
		var check_to = $("#to_id").val();
		var check_to_arr = check_to.split(",");

		for (var c=0; c<check_to_arr.length; c++) {
			for (var i=1; i<=total_to; i++)	{
				if (Number($("#sel_to_id_"+ i).val()) == check_to_arr[c]) {
					$("#check_to_"+ i).attr("disabled",true);
					$("#check_to_"+ i).attr("checked",true);

					var DivList = "";

					DivList += "<li id='list_to_"+ i +"' name='list_to'>";
					DivList += "	<input type='hidden' name='list_to_input' id='list_to_input_"+ i +"' value=''>";
					DivList += "	<input type='hidden' name='list_to_id' id='list_to_id_"+ i +"' value='"+$("#sel_to_id_"+ i).val()+"'>";
					DivList += "	<input type='hidden' name='list_to_login' id='list_to_login_"+ i +"' value='"+$("#sel_to_login_"+ i).val()+"'>";
					DivList += "	<input type='hidden' name='list_to_position' id='list_to_position_"+ i +"' value='"+$("#sel_to_position_"+ i).val()+"'>";
					DivList += "	<input type='hidden' name='list_to_name' id='list_to_name_"+ i +"' value='"+$("#sel_to_name_"+ i).val()+"'>";
					DivList += "	<p class='attach_file'><span style='cursor:pointer' onclick=oneCheck('check_list_to','list_to_check','list_to_"+ i +"');>" + $("#sel_to_position_"+ i).val() +" "+ $("#sel_to_name_"+ i).val() + "</span></p><a href=javascript:oneDel('list_to_"+ i +"','check_to_"+ i +"','total_to'); class='delete'><img src='/img/icon_del.gif' alt='����'></a>";
					DivList += "</li>";

					$("#list_to").append(DivList);
				}
			}
		}

		//��������
		var total_cc = $("#total_cc").val();
		var check_cc = $("#cc_id").val();
		var check_cc_arr = check_cc.split(",");

		for (var c=0; c<check_cc_arr.length; c++) {
			for (var i=1; i<=total_cc; i++) {
				if (Number($("#sel_cc_id_"+ i).val()) == check_cc_arr[c]) {
					$("#check_cc_"+ i).attr("disabled",true);
					$("#check_cc_"+ i).attr("checked",true);

					var DivList = "";

					DivList += "<li id='list_cc_"+ i +"' name='list_cc'>";
					DivList += "	<input type='hidden' name='list_cc_input' id='list_cc_input_"+ i +"' value=''>";
					DivList += "	<input type='hidden' name='list_cc_id' id='list_cc_id_"+ i +"' value='"+$("#sel_cc_id_"+ i).val()+"'>";
					DivList += "	<input type='hidden' name='list_cc_login' id='list_cc_login_"+ i +"' value='"+$("#sel_cc_login_"+ i).val()+"'>";
					DivList += "	<input type='hidden' name='list_cc_position' id='list_cc_position_"+ i +"' value='"+$("#sel_cc_position_"+ i).val()+"'>";
					DivList += "	<input type='hidden' name='list_cc_name' id='list_cc_name_"+ i +"' value='"+$("#sel_cc_name_"+ i).val()+"'>";
					DivList += "	<p class='attach_file'><span style='cursor:pointer' onclick=oneCheck('check_list_cc','list_cc_check','list_cc_"+ i +"');>" + $("#sel_to_position_"+ i).val() +" "+ $("#sel_to_name_"+ i).val() + "</span></p><a href=javascript:oneDel('list_cc_"+ i +"','check_cc_"+ i +"','total_cc'); class='delete'><img src='/img/icon_del.gif' alt='����'></a>";
					DivList += "</li>";

					$("#list_cc").append(DivList);
				}
			}
		}

		<? if ($form_category == "�ٰܱ�/�İ߰�" || $form_category == "�����") { ?>

		//������
		var total_partner = $("#total_partner").val();
		var check_partner = $("#partner_id").val();
		var check_partner_arr = check_partner.split(",");

		for (var c=0; c<check_partner_arr.length; c++) {
			for (var i=1; i<=total_partner; i++) {
				if (Number($("#sel_partner_id_"+ i).val()) == check_partner_arr[c]) {
					$("#check_partner_"+ i).attr("disabled",true);
					$("#check_partner_"+ i).attr("checked",true);

					var DivList = "";

					DivList += "<li id='list_partner_"+ i +"' name='list_partner'>";
					DivList += "	<input type='hidden' name='list_partner_input' id='list_partner_input_"+ i +"' value=''>";
					DivList += "	<input type='hidden' name='list_partner_id' id='list_partner_id_"+ i +"' value='"+$("#sel_partner_id_"+ i).val()+"'>";
					DivList += "	<input type='hidden' name='list_partner_login' id='list_partner_login_"+ i +"' value='"+$("#sel_partner_login_"+ i).val()+"'>";
					DivList += "	<input type='hidden' name='list_partner_position' id='list_partner_position_"+ i +"' value='"+$("#sel_partner_position_"+ i).val()+"'>";
					DivList += "	<input type='hidden' name='list_partner_name' id='list_partner_name_"+ i +"' value='"+$("#sel_partner_name_"+ i).val()+"'>";
					DivList += "	<p class='attach_file'><span style='cursor:pointer' onclick=oneCheck('check_list_partner','list_partner_check','list_partner_"+ i +"');>" + $("#sel_partner_position_"+ i).val() +" "+ $("#sel_partner_name_"+ i).val() + "</span></p><a href=javascript:oneDel('list_partner_"+ i +"','check_partner_"+ i +"','total_partner'); class='delete'><img src='/img/icon_del.gif' alt='����'></a>";
					DivList += "</li>";

					$("#list_partner").append(DivList);
				}
			}
		}

		<? } ?>

		//������ ����.��������
		$("#sel_click_to").click(function()	
		{
			if ($("#search_type1").val() == "to") {
				var total = $(":checkbox[name=check_to]:checked:enabled").length;

				if (total == 0)	{
					alert("�����ڸ� ������ �ּ���.");
					return;
				}

				var kids = $("#list_to").children().length;

				if (kids+total > <?=$to_count?>) {
					alert("�����ڴ� <?=$to_count?>����� ���� �����մϴ�.");
					return;
				}

				var j = kids;

				for (var i=1; i<=$("#total_to").val(); i++) {
					if ($("#check_to_"+ i).is(":checked") && $("#check_to_"+ i).is(":enabled")) {
						j = j + 1;
						var DivList = "";

						DivList += "<li id='list_to_"+ j +"' name='list_to'>";
						DivList += "	<input type='hidden' name='list_to_input' id='list_to_input_"+ j +"' value=''>";
						DivList += "	<input type='hidden' name='list_to_id' id='list_to_id_"+ j +"' value='"+$("#sel_to_id_"+ i).val()+"'>";
						DivList += "	<input type='hidden' name='list_to_login' id='list_to_login_"+ j +"' value='"+$("#sel_to_login_"+ i).val()+"'>";
						DivList += "	<input type='hidden' name='list_to_position' id='list_to_position_"+ j +"' value='"+$("#sel_to_position_"+ i).val()+"'>";
						DivList += "	<input type='hidden' name='list_to_name' id='list_to_name_"+ j +"' value='"+$("#sel_to_name_"+ i).val()+"'>";
						DivList += "	<p class='attach_file'><span style='cursor:pointer' onclick=oneCheck('check_list_to','list_to_check','list_to_"+ j +"');>" + $("#sel_to_position_"+ i).val() +" "+ $("#sel_to_name_"+ i).val() + "</span></p><a href=javascript:oneDel('list_to_"+ j +"','check_to_"+ j +"','total_to'); class='delete'><img src='/img/icon_del.gif' alt='����'></a>";
						DivList += "</li>";

						$("#list_to").append(DivList);

						$("#to_list").val($("#to_list").val()+ $("#sel_to_position_"+ i).val() +" "+ $("#sel_to_name_"+ i).val() +",");
						$("#to_ids").val($("#to_id").val()+ $("#sel_to_id_"+ i).val() +",");

						$("#check_to_"+ i).attr("disabled",true);
					}
				}
			} else if ($("#search_type1").val() == "cc") {
				var total = $(":checkbox[name=check_cc]:checked:enabled").length;

				if (total == 0) {
					alert("�����ڸ� ������ �ּ���.");
					return;
				}

				var kids = $("#list_cc").children().length;

				if (kids+total > <?=$cc_count?>) {
					alert("�����ڴ� <?=$cc_count?>����� ���� �����մϴ�.");
					return;
				}

				var j = kids;

				for (var i=1; i<=$("#total_cc").val(); i++) {
					if ($("#check_cc_"+ i).is(":checked") && $("#check_cc_"+ i).is(":enabled")) {
						j = j + 1;
						var DivList = "";

						DivList += "<li id='list_cc_"+ j +"' name='list_to'>";
						DivList += "	<input type='hidden' name='list_cc_input' id='list_cc_input_"+ j +"' value=''>";
						DivList += "	<input type='hidden' name='list_cc_id' id='list_cc_id_"+ j +"' value='"+$("#sel_cc_id_"+ i).val()+"'>";
						DivList += "	<input type='hidden' name='list_cc_login' id='list_cc_login_"+ j +"' value='"+$("#sel_cc_login_"+ i).val()+"'>";
						DivList += "	<input type='hidden' name='list_cc_position' id='list_cc_position_"+ j +"' value='"+$("#sel_cc_position_"+ i).val()+"'>";
						DivList += "	<input type='hidden' name='list_cc_name' id='list_cc_name_"+ j +"' value='"+$("#sel_cc_name_"+ i).val()+"'>";
						DivList += "	<p class='attach_file'><span style='cursor:pointer' onclick=oneCheck('check_list_to','list_cc_check','list_cc_"+ j +"');>" + $("#sel_cc_position_"+ i).val() +" "+ $("#sel_cc_name_"+ i).val() + "</span></p><a href=javascript:oneDel('list_cc_"+ j +"','check_cc_"+ j +"','total_cc'); class='delete'><img src='/img/icon_del.gif' alt='����'></a>";
						DivList += "</li>";

						$("#list_cc ").append(DivList);

						$("#cc_list").val($("#cc_list").val()+ $("#sel_cc_position_"+ i).val() +" "+ $("#sel_cc_name_"+ i).val() +",");
						$("#cc_ids").val($("#cc_id").val()+ $("#sel_cc_id_"+ i).val() +",");

						$("#check_cc_"+ i).attr("disabled",true);
					}
				}
			}
		});
		//������ ����
		$("#sel_click_partner").click(function() {
			var total = $(":checkbox[name=check_partner]:checked:enabled").length;

			if (total == 0) {
				alert("�����ڸ� ������ �ּ���.");
				return;
			}

			var kids = $("#list_partner").children().length;

			if (kids+total > <?=$partner_count?>) {
				alert("�����ڴ� <?=$partner_count?>����� ���� �����մϴ�.");
				return;
			}

			var j = kids;

			for (var i=1; i<=$("#total_partner").val(); i++) {
				if ($("#check_partner_"+ i).is(":checked") && $("#check_partner_"+ i).is(":enabled")) {
					j = j + 1;
					var DivList = "";

					DivList += "<li id='list_partner_"+ j +"' name='list_partner'>";
					DivList += "	<input type='hidden' name='list_partner_input' id='list_partner_input_"+ j +"' value=''>";
					DivList += "	<input type='hidden' name='list_partner_id' id='list_partner_id_"+ j +"' value='"+$("#sel_partner_id_"+ i).val()+"'>";
					DivList += "	<input type='hidden' name='list_partner_login' id='list_partner_login_"+ j +"' value='"+$("#sel_partner_login_"+ i).val()+"'>";
					DivList += "	<input type='hidden' name='list_partner_position' id='list_partner_position_"+ j +"' value='"+$("#sel_partner_position_"+ i).val()+"'>";
					DivList += "	<input type='hidden' name='list_partner_name' id='list_partner_name_"+ j +"' value='"+$("#sel_partner_name_"+ i).val()+"'>";
					DivList += "	<p class='attach_file'><span style='cursor:pointer' onclick=oneCheck('check_list_partner','list_partner_check','list_partner_"+ j +"');>" + $("#sel_partner_position_"+ i).val() +" "+ $("#sel_partner_name_"+ i).val() + "</span></p><a href=javascript:oneDel('list_partner_"+ j +"','check_partner_"+ j +"','total_partner'); class='delete'><img src='/img/icon_del.gif' alt='����'></a>";
					DivList += "</li>";

					$("#list_partner").append(DivList);

					$("#partner_list").val($("#partner_list").val()+ $("#sel_partner_position_"+ i).val() +" "+ $("#sel_partner_name_"+ i).val() +",");
					$("#partner_ids").val($("#partner_id").val()+ $("#sel_partner_id_"+ i).val() +",");

					$("#check_partner_"+ i).attr("disabled",true);
				}
			}
		});

		//�˻�
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
			if (e.keyCode == 13) {
				$("#popup_form1").attr("target","hdnFrame");
				$("#popup_form1").attr("action","person_list.php"); 
				$("#popup_form1").submit();
			}
		});
		$("#search_name2").keypress(function(e){
			if (e.keyCode == 13) {
				$("#popup_form2").attr("target","hdnFrame");
				$("#popup_form2").attr("action","person_list.php"); 
				$("#popup_form2").submit();
			}
		});
		//���
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

		//�θ�â ����
		$("#popup_select_ok").attr("style","cursor:pointer;").click(function(){
			$("#to_ids").val("");
			
			for (var i=0; i<5; i++) {
				$("#to_position_"+i).html("&nbsp;");
				$("#to_name_"+i).html("<a href=\"javascript:toAdd();\"><img src=\"/img/btn_appoint.gif\" name=\"ToAddBtn\"></a>");
				$("#to_del_"+i).html("&nbsp;");
			}

			for (var i=0; i<$("#list_to").children().length; i++) {
				if ($("[name=list_to_input]").eq(i).val() == "") {
					$("#to_ids").val($("#to_ids").val() + $("[name=list_to_id]").eq(i).val() + ",");
					
					$("#to_position_"+i).text($("[name=list_to_position]").eq(i).val());
					$("#to_name_"+i).text($("[name=list_to_name]").eq(i).val());
					$("#to_del_"+i).html("<a href=\"javascript:toAdd();\"><img src=\"/img/btn_change.gif\"></a>");
				}
			}

			$("#to_id").val($("#to_ids").val());

			$("#cc_list").val("");
			$("#cc_ids").val("");

			for (var i=0; i<$("#list_cc").children().length; i++) {
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

			for (var i=0; i<$("#list_partner").children().length; i++) {
				if ($("[name=list_partner_input]").eq(i).val() == "") {
					$("#partner_list").val($("#partner_list").val() + $("[name=list_partner_position]").eq(i).val() + " " + $("[name=list_partner_name]").eq(i).val() + ",");
					$("#partner_ids").val($("#partner_ids").val() + $("[name=list_partner_id]").eq(i).val() + ",");
				}
			}

			$("#partner").val($("#partner_list").val());
			$("#partner_id").val($("#partner_ids").val());

			$("#popPartnerAdd").attr("style","display:none;");
		});

	<? if ($form_category == "���ǰ�Ǽ�(v2)") { ?>

		// ������� ����ó��
		$(".pay_type").change(function() {
			var type = $(this).val();
			var no = $(this).parent().data("no");

			$("#paytype_B_"+no).hide();
			$("#paytype_C_"+no).hide();
			$("#paytype_P_"+no).hide();
			$("#paytype_A_"+no).hide();
			$("#paytype_H_"+no).hide();
			$("#paytype_"+type+"_"+no).show();
		});

		// �����׸� ������ư
		$(".btn_delete").on("click", function() {
			var no = $(this).data("no");
			var flg = $(this).data("flg"); 

			if (flg == "Y") $("input[name=type_"+no+"]:input[value=1]").prop("checked", true);
			else			$("input[name=type_"+no+"]:checked").prop("checked", false);
			$("input[name=tax_"+no+"]:checked").prop("checked", false);
			$("input[name=target_"+no+"]:checked").prop("checked", false);
			$("input[name=pay_type_"+no+"]:checked").prop("checked", false);
			$("input[name=pay_info_"+no+"]:checked").prop("checked", false);
			$("input[name=pay_date_"+no+"]:checked").prop("checked", false);
			$("input[name=money_"+no+"]").val("");
			$("input[name=company_"+no+"]").val("");
			$("input[name=manager_"+no+"]").val("");
			$("input[name=contact_"+no+"]").val("");
			$("input[name=bank_name_"+no+"]").val("");
			$("input[name=bank_num_"+no+"]").val("");
			$("input[name=bank_user_"+no+"]").val("");
			$("input[name=memo_"+no+"]").val("");

			$("#payment_"+no).hide();
		});

	<? } else if ($form_category == "�Ի���ΰ�") { ?>

		// �Ի籸�� ����ó��
		$(".employ_type").change(function() {
			var type = $(this).val();
			var no = $(this).parent().data("no");

			$("#employtype_A_"+no).hide();
			$("#employtype_B_"+no).hide();
			$("#employtype_"+type+"_"+no).show();
		});

		// �Ի��׸� ������ư
		$(".btn_delete").on("click", function() {
			var no = $(this).data("no");
			var flg = $(this).data("flg"); 

			if (flg == "Y") $("input[name=type_"+no+"]:input[value=1]").prop("checked", true);
			else			$("input[name=type_"+no+"]:checked").prop("checked", false);
			$("input[name=cause1_"+no+"]").val("");
			$("input[name=career_"+no+"]:checked").prop("checked", false);
			$("input[name=name1_"+no+"]").val("");
			$("input[name=birth_"+no+"]").val("");
			$("input[name=school_"+no+"]").val("");
			$("input[name=major_"+no+"]").val("");
			$("input[name=career_y_"+no+"]").val("");
			$("input[name=career_m_"+no+"]").val("");
			$("input[name=position_"+no+"]:checked").prop("checked", false);
			$("input[name=rating_"+no+"]:checked").prop("checked", false);
			$("input[name=reader_"+no+"]:checked").prop("checked", false);
			$("input[name=join_y_"+no+"]").val("");
			$("input[name=join_m_"+no+"]").val("");
			$("input[name=join_d_"+no+"]").val("");
			$("input[name=gubun_"+no+"]:checked").prop("checked", false);
			$("input[name=cause2_"+no+"]").val("");
			$("input[name=name2_"+no+"]").val("");
			$("input[name=relay_"+no+"]").val("");
			$("input[name=salary_h_"+no+"]").val("");
			$("input[name=salary_m_"+no+"]").val("");
			$("input[name=period1_y_"+no+"]").val("");
			$("input[name=period1_m_"+no+"]").val("");
			$("input[name=period1_d_"+no+"]").val("");
			$("input[name=period2_y_"+no+"]").val("");
			$("input[name=period2_m_"+no+"]").val("");
			$("input[name=period2_d_"+no+"]").val("");
			$("input[name=memo_"+no+"]").val("");

			$("#employ_"+no).hide();
		});
	<? } ?>

	});

	// ���� �̵�
	function list_up()
	{
		var ul_id = $("#move_ul").val();
		var li_id = $("#"+ul_id).val();

		if (li_id == "") {
			alert("�̵� �� ����ڸ� ������ �ּ���");
			return;
		} else {
			// ���� �̵��� �������� Ȯ��
			var prev_item = $("#"+li_id).prev();

			if ($(prev_item).attr("id") == undefined) // id�� ���ǵǾ� ���� �ʴٸ� �ֻ��� li ����
			return;

			// ���� ���õ� li �� ���ܽ�Ų��.
			var selected_item = $("#"+li_id).detach(); 

			// ���� li ������ �����Ͽ� ��ġ�� ��ȯ��Ų��.
			$(prev_item).before(selected_item);
		}
	}

	// �Ʒ��� �̵�
	function list_down()
	{
		var ul_id = $("#move_ul").val();
		var li_id = $("#"+ul_id).val();

		if (li_id == "") {
			alert("�̵� �� ����ڸ� ������ �ּ���");
			return;
		} else {
			// �Ʒ��� �̵��� �������� Ȯ��
			var next_item = $("#"+li_id).next();

			if ($(next_item).attr("id") == undefined) // id�� ���ǵǾ� ���� �ʴٸ� ������ li ����
			return;

			// ���� ���õ� li �� ���ܽ�Ų��.
			var selected_item = $("#"+li_id).detach();

			// ���� li ������ �����Ͽ� ��ġ�� ��ȯ��Ų��.
			$(next_item).after(selected_item);
		}
	}

    function oneCheck(a,b,c)
	{
		$("#"+a).val(c);
		$("#"+c).parent().children().attr("style","background:#fff;");
		$("#"+c).attr("style","font-weight:bold;");
		$("#move_ul").val(a);
    }
	function oneDel(a,b,c)
	{
		$("#"+a).remove();
		$("#"+b).attr("disabled",false);
		$("#"+b).attr("checked",false);
	}

	function changeDiv()
	{
		if ($("#search_type1").val() == "to") {
			$("#cc_list").addClass('hide');
			$("#to_list").removeClass('hide');
		} else if ($("#search_type1").val() == "cc") {
			$("#to_list").addClass('hide');
			$("#cc_list").removeClass('hide');
		}
	}

	function addFile()
	{
		if (document.getElementById("file_D2").style.display == "none") {
			document.getElementById("file_D2").style.display = "inline";
		} else {
			if (document.getElementById("file_D3").style.display == "none") {
				document.getElementById("file_D3").style.display = "inline";
			} else {
				alert("���� ÷�δ� �ִ� 3������ �����մϴ�.");
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
		//���õ� ���ϸ� ǥ��
		$("#file_1").change(function(){
			var str = this.value;
			var arr_str = str.split("\\");
			var arr_len = arr_str.length;
			$("#attachment_1").val(this.value);
			$("#delfile_1").html("<span>"+arr_str[arr_len-1]+"</span><a href='javascript:delFile(1);'><img src='../img/btn_delete.gif' alt='����' /></a>");
		});
		$("#file_2").change(function(){
			var str = this.value;
			var arr_str = str.split("\\");
			var arr_len = arr_str.length;
			$("#attachment_2").val(this.value);
			$("#delfile_2").html("<span>"+arr_str[arr_len-1]+"</span><a href='javascript:delFile(2);'><img src='../img/btn_delete.gif' alt='����' /></a>");
		});
		$("#file_3").change(function(){
			var str = this.value;
			var arr_str = str.split("\\");
			var arr_len = arr_str.length;
			$("#attachment_3").val(this.value);
			$("#delfile_3").html("<span>"+arr_str[arr_len-1]+"</span><a href='javascript:delFile(3);'><img src='../img/btn_delete.gif' alt='����' /></a>");
		});
	 });

	<? if ($form_category == "���ǰ�Ǽ�(v2)") { ?>

	// �ѱݾ� �հ� ���
	function sumPayment()
	{
		var frm = document.form;

		var money_ = 0;
		$(".money_").each(function() {
			var won = $(this).val().replace(/,/g,"");
			if(won > 0)	money_ = money_ + parseInt(won);
		});		
		
		checkThousand(frm.money_total, String(money_));
	}

	// �������� �߰�
	function addPayment()
	{
		if (document.getElementById("payment_1").style.display == "none") {
			document.getElementById("payment_1").style.display = "";
		} else {
			if (document.getElementById("payment_2").style.display == "none") {
				document.getElementById("payment_2").style.display = "";
			} else {
				if (document.getElementById("payment_3").style.display == "none") {
					document.getElementById("payment_3").style.display = "";
				} else {
					if (document.getElementById("payment_4").style.display == "none") {
						document.getElementById("payment_4").style.display = "";
					} else {
						alert("���������� �ִ� 5������ �����մϴ�.");
					}
				}
			}
		}
	}
	
	// û�� ���ý� �ȳ���
	function alertType(obj)
	{
		var val = obj.value;

		if(val == "1") {
			alert("Ŭ���̾�Ʈ û�� ���� ���Խ� ����.");
		} else if(val == "2") {
			alert("Ŭ���̾�Ʈ ��û���� ����.");
		} else if(val == "3") {
			<? if ($prs_team != "�濵������") { ?>
			alert("�濵������ �����ڵ� �Դϴ�(��Ÿ �μ������� ���Ұ�)");
			obj.checked = false;
			<? } ?>
		}
	}

	// �������� ���ý� �ȳ���
	function alertPaydate(obj)
	{
		var val = obj.value;

		if(val == "3") {
			if(!confirm("�濵������ ���� ���� Ȯ�� ��, ��� ��.\n�濵�������� Ȯ�� �ϼ̽��ϱ�?")) {
				obj.checked = false;
			}
		}
	}	
	
	// �ڵ���ü ���ý� �ȳ���
	function alertPaytype(obj)
	{
		var val = obj.value;

		if(val == "A") {
			if(!confirm("�濵������ ���� ���� ��, ��� ��.\n�濵�������� ���� �ϼ̽��ϱ�?")) {
				obj.checked = false;
			}
		}
	}

	//�������� �� ����
	function chkPayForm(no)
	{
		var frm = document.form;

		var $type	   = $(":radio[name=type_"+no+"]:checked");
		var $money	   = $("input[name=money_"+no+"]");
		var $tax	   = $(":radio[name=tax_"+no+"]:checked");
		var $target	   = $(":radio[name=target_"+no+"]:checked");
		var $pay_type  = $(":radio[name=pay_type_"+no+"]:checked");
		var $pay_info  = $(":radio[name=pay_info_"+no+"]:checked");
		var $company   = $("input[name=company_"+no+"]");
		var $manager   = $("input[name=manager_"+no+"]");
		var $contact   = $("input[name=contact_"+no+"]");
		var $bank_name = $("input[name=bank_name_"+no+"]");
		var $bank_num  = $("input[name=bank_num_"+no+"]");
		var $bank_user = $("input[name=bank_user_"+no+"]");
		var $pay_date  = $(":radio[name=pay_date_"+no+"]:checked");
		var $memo	   = $("input[name=memo_"+no+"]");

		if($money.val()) {
			if(!$type.val() || $type.val() == "undefined")	   { alert("û��Ÿ���� ������ �ּ���.");	$(":radio[name=type_"+no+"]:input[value=1]").focus();	return false; }			
			if(!$tax.val() || $tax.val() == "undefined")	   { alert("�ΰ��� ���θ� ������ �ּ���."); $(":radio[name=tax_"+no+"]:input[value=1]").focus();	return false; }			
			if(!$target.val() || $target.val() == "undefined") { alert("��������� ������ �ּ���.");	$(":radio[name=target_"+no+"]:input[value=1]").focus(); return false; }
			// ������ü�� ���	
			if($pay_type.val() == "B") {
				if($pay_info.val() != '1' && $pay_info.val() != '2' && $pay_info.val() != '3' && $pay_info.val() != '4')	
									  { alert("��꼭�� ������ �ּ���.");   $(":radio[name=pay_info_"+no+"]:input[value=1]").focus(); return false; }	
				if(!$company.val())	  { alert("��ü���� �Է��� �ּ���.");   $("input[name=company_"+no+"]").focus();				  return false; }		
				if(!$manager.val())	  { alert("����ڸ� �Է��� �ּ���.");   $("input[name=manager_"+no+"]").focus();				  return false; }
				if(!$contact.val())	  { alert("����ó�� �Է��� �ּ���.");   $("input[name=contact_"+no+"]").focus();				  return false; }		
				if(!$bank_name.val()) { alert("������� �Է��� �ּ���.");   $("input[name=bank_name_"+no+"]").focus();				  return false; }	
				if(!$bank_num.val())  { alert("���¹�ȣ�� �Է��� �ּ���."); $("input[name=bank_num_"+no+"]").focus();				  return false; }
				if(!$bank_user.val()) { alert("�����ָ� �Է��� �ּ���.");   $("input[name=bank_user_"+no+"]").focus();				  return false; }	
				if(!$pay_date.val())  { alert("�������ڸ� ������ �ּ���."); $(":radio[name=pay_date_"+no+"]:input[value=1]").focus(); return false; }
			// ī������� ���
			} else if($pay_type.val() == "C") {
				if($pay_info.val() != '5' && $pay_info.val() != '6') { 
					alert("�������� ������ �ּ���.");	$(":radio[name=pay_info_"+no+"]:input[value=5]").focus(); return false; 
				}
			// ���ΰ���� ���
			} else if($pay_type.val() == "P") {
				if($pay_info.val() != '7' && $pay_info.val() != '8') { 
					alert("���������� ������ �ּ���."); $(":radio[name=pay_info_"+no+"]:input[value=7]").focus(); return false; 
				}
			// �ڵ���ü�� ���
			} else if($pay_type.val() == "A") {
				if($pay_info.val() != '9' && $pay_info.val() != '10' && $pay_info.val() != '11' && $pay_info.val() != '12') { 
					alert("��ü���ڸ� ������ �ּ���."); $(":radio[name=pay_info_"+no+"]:input[value=9]").focus(); return false; 
				}
			// ���ݰ����� ���
			} else if($pay_type.val() == "H") {

			}
			if(!$memo.val() || $memo.val() == "undefined") { 
				alert("Ȱ�볻���� �Է��� �ּ���."); $("input[name=memo_"+no+"]").focus(); return false; 
			}				
		} else {
			//alert("������ �ݾ��� �Է��� �ּ���."); $("input[name=money_"+no+"]").focus(); return false;
		}

		return true;
	}

	<? } else if ($form_category == "�Ի���ΰ�") { ?>

	// �Ի����� �߰�
	function addEmploy()
	{
		if (document.getElementById("employ_1").style.display == "none") {
			document.getElementById("employ_1").style.display = "";
		} else {
			if (document.getElementById("employ_2").style.display == "none") {
				document.getElementById("employ_2").style.display = "";
			} else {
				if (document.getElementById("employ_3").style.display == "none") {
					document.getElementById("employ_3").style.display = "";
				} else {
					if (document.getElementById("employ_4").style.display == "none") {
						document.getElementById("employ_4").style.display = "";
					} else {
						alert("�Ի������� �ִ� 5������ �����մϴ�.");
					}
				}
			}
		}
	}

	//�Ի����� �� ����
	function chkPayForm(no)
	{
		var frm = document.form;

		var $name = null;
		var $type = $(":radio[name=type_"+no+"]:checked");
		
		$name1 = $("input[name=name1_"+no+"]");
		$name2 = $("input[name=name2_"+no+"]");

		if(!$type.val() || $type.val() == "undefined") { alert("ä�뱸���� ������ �ּ���."); $(":radio[name=type_"+no+"]:input[value=A]").focus(); return false; }
		// �������� ���	
		if($type.val() == "A") {
			if(!$name1.val()) { alert("������ �Է��� �ּ���."); $("input[name=name1_"+no+"]").focus(); return false; }	
		// ������� ���
		} else if($type.val() == "B") {
			if(!$name2.val()) { alert("������ �Է��� �ּ���."); $("input[name=name2_"+no+"]").focus(); return false; }	
		}

		return true;
	}

	<? } ?>

	//���
	function funWrite(type)
	{
		var frm = document.form;
		var contents =  CKEDITOR.instances['contents'].getData();//ckeditor ���� ���� �� �ޱ�

		if (type == "save") {
			var type_text = "�ӽ�����";
			var goUrl = "approval_write_act.php";
		}

		if (type == "write") {
			var type_text = "���";
			var goUrl = "approval_write_act.php";
		}

		if (type == "modify_save") {
			var type_text = "����";
			var goUrl = "approval_modify_act.php";
		}

		if (type == "modify") {
			var type_text = "����";
			var goUrl = "approval_modify_act.php";
		}

		<?	if (strstr($prs_team,"2��") && ($doc_form == "002" || ($doc_form == "004" && strstr($form_title,"������Ʈ")))) { ?>
		var to_str = frm.to_id.value;
		var project_no = frm.project_no.value;

		if(!to_str.match("87") && !to_str.match("148") && project_no != ""){
			alert("������Ʈ ��� ��ǥ���� ������ �ּ���.");
			frm.to_id.focus();
			return;
		}
		<? } ?>

		if(frm.title.value == "") {
			alert("������ �Է��� �ּ���");
			frm.title.focus();
			return;
		}

		<? if ($form_category == "���ǰ�Ǽ�(v2)") { ?>

		if(frm.project_no.value == ""){
			alert("������Ʈ�� �������ּ���");
			frm.project_no.focus();
			return;
		}		

		if(frm.money_total.value == 0) {
			alert("�Էµ� �ݾ��� �����ϴ�.");
			frm.money_0.focus();
			return;
		}

		for(i=0; i<5; i++) {
			var open_chk = document.getElementById("payment_"+i).style.display;
			// �Է��׸� Ȱ��ȭ �Ǿ��ٸ�
			if(open_chk != "none") {
				if(chkPayForm(i) == false) {
					return;
				}
			}
		}
		
		<? } else if ($form_category == "�Ի���ΰ�") { ?>

		if(frm.team.value == ""){
			alert("ä��μ��� �������ּ���");
			frm.team.focus();
			return;
		}

		for(i=0; i<5; i++) {
			var open_chk = document.getElementById("employ_"+i).style.display;
			// �Է��׸� Ȱ��ȭ �Ǿ��ٸ�
			if(open_chk != "none") {
				if(chkPayForm(i) == false) {
					return;
				}
			}
		}

		<? } ?>

		if(contents=="") {
			alert("������ �Է����ּ���");
			CKEDITOR.instances['contents'].focus();		//ckeditor ��Ŀ�� �̵��ϴ� �κ�
			return;    	
		}

		if ($("#form_title").val() == "����ް�") {
			var fr = new Date($("#fr_date").val());
			var to = new Date($("#to_date").val());

			var limitY = fr.getFullYear();
			var limitM = fr.getMonth()+4;
			var limitD = fr.getDate();

			if (limitM > 12) {
				limitY = limitY + 1;
				limitM = limitM - 12;
			}

			var limit = new Date(limitM+"/"+limitD+"/"+limitY);

			if (to >= limit) {
				alert("����ް��� �ִ� 3���� �Դϴ�.");
				$("#to_month").focus();
				return;
			}
		}

		if ($("#form_title").val() == "��������") {
			var fr = new Date($("#fr_date").val());
			var to = new Date($("#to_date").val());

			var limitY = fr.getFullYear()+1;
			var limitM = fr.getMonth()+1;
			var limitD = fr.getDate();

			var limit = new Date(limitM+"/"+limitD+"/"+limitY);

			if (to >= limit) {
				alert("���������� �ִ� 1�� �Դϴ�.");
				$("#to_month").focus();
				return;
			}
		}

		//���� ��ȿ�� �˻� �� �κ�
		if(confirm("<?=$form_category?>�� "+ type_text +" �Ͻðڽ��ϱ�")) {
			frm.type.value = type;
			frm.target ="hdnFrame";
			frm.action = goUrl; 
			frm.submit();
		}
	}
	//���� ��� ����
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
							<!--<li><a href="approval_write.php?doc_form=001"><? if ($doc_form == "001") { ?><strong class="orange">+ ���ǰ�Ǽ�</strong><? } else { ?>+ ���ǰ�Ǽ�<? } ?></a></li>-->
							<li><a href="approval_write.v2.php?doc_form=009"><? if ($doc_form == "009") { ?><strong class="orange">+ �Ի���ΰ�</strong><? } else { ?>+ �Ի���ΰ�<? } ?></a></li>
							<li><a href="approval_write.v2.php?doc_form=002"><? if ($doc_form == "002") { ?><strong class="orange">+ ���ǰ�Ǽ�</strong><? } else { ?>+ ���ǰ�Ǽ�<? } ?></a></li>
							<li><a href="approval_write.php?doc_form=003"><? if ($doc_form == "003") { ?><strong class="orange">+ �ٰܱ�/�İ߰�</strong><? } else { ?>+ �ٰܱ�/�İ߰�<? } ?></a></li>
							<li><a href="approval_write.php?doc_form=004"><? if ($doc_form == "004") { ?><strong class="orange">+ �ް���</strong><? } else { ?>+ �ް���<? } ?></a></li>
							<li><a href="approval_write.php?doc_form=005"><? if ($doc_form == "005") { ?><strong class="orange">+ �����</strong><? } else { ?>+ �����<? } ?></a></li>
							<li><a href="approval_write.php?doc_form=006"><? if ($doc_form == "006") { ?><strong class="orange">+ ������</strong><? } else { ?>+ ������<? } ?></a></li>
							<li><a href="approval_write.php?doc_form=007"><? if ($doc_form == "007") { ?><strong class="orange">+ �ø���</strong><? } else { ?>+ �ø���<? } ?></a></li>
							<li><a href="approval_write.php?doc_form=008"><? if ($doc_form == "008") { ?><strong class="orange">+ �����</strong><? } else { ?>+ �����<? } ?></a></li>
						</ul>
					</div>
				</div>

				<div class="content-wrap"> 
					<div class="title clearfix">
						<table class="notable " width="100%">
							<tr>
								<th scope="row"><?=$form_category?></th>
								<td>
									(2018 df �������� ���� ����/���������� ����Ǿ����� �� Ȯ���� �ּ���.)
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
								<th class="gray">������ȣ</th>
								<td><?=$doc_no?><input type="hidden" name="doc_no" value="<?=$doc_no?>"></td>
								<th class="gray" rowspan="3">����</th>
								<td rowspan="3" class="app-td">
									<table width="20%" style="float:left; border-right:1px solid #b2b2b2">
									  <tr><td id="to_position_0"><?=$prs_position?></td></tr>
									  <tr><td id="to_name_0"><?=$prs_name?></td></tr>
									  <tr style="border:0;"><td id="to_del_0"></td></tr>
									</table>
					<?
						$to = $prs_id . ",";
						$max_to_count = $to_count - 1;

						if ($type == "write") {

							$field = "FORM_". $doc_form;
							
							if ($form_category == "�ް���") {
								if (strstr($form_title,"������Ʈ")) {
									$field = $field ."_2";
								} else {
									$field = $field ."_1";
								}
							}

							$sql = "SELECT $field FROM DF_APPROVAL_TO_LINE WHERE PRS_ID = '$prs_id'";
							$rs = sqlsrv_query($dbConn, $sql);

							$record = sqlsrv_fetch_array($rs);
							$to_ord = $record[0];

							if ($to_ord != "") {
								$to_ord_ex = explode("-",$to_ord);

								for ($i=0; $i<sizeof($to_ord_ex); $i++) {
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
							} else {
								$i = 0;
							}

							for ($m=$i; $m<$max_to_count; $m++) {
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

						// ����ȭ���� ���
						} else if ($type == "modify") {
				
							for ($i=0; $i<$max_to_count; $i++) {
								$j = $i + 1;
								$k = $i + 2;

								$sql = "SELECT A_PRS_NAME, PRS_POSITION, A_PRS_ID FROM DF_APPROVAL_TO INNER JOIN DF_PERSON ON A_PRS_ID = PRS_ID WHERE DOC_NO = '$doc_no' AND A_ORDER ='$k' AND PRF_ID IN (1,2,3,4,7)";
								$rs = sqlsrv_query($dbConn, $sql);

								$record = sqlsrv_fetch_array($rs);
								$rows = sqlsrv_has_rows($rs);

								if ($rows > 0) {
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
								} else {
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
								<th class="gray">��������</th>
								<td>
									<input type="hidden" name="form_no" value="<?=$form_no?>">
								<? if ($form_category == "�ް���") { ?>
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
								<th class="gray">��������</th>
								<td>
									<select name="open_yn">
										<option value="Y"<? if ($open_yn == "Y") { ?> selected<? } ?>>����</option>
										<option value="N"<? if ($open_yn == "N") { ?> selected<? } ?>>�����</option>
									</select>
								</td>
							</tr>
							<tr>
								<th class="gray">�����</th>
								<td>
									<input type="hidden" name="up_year" id="up_year" value="<?=$up_year?>">
									<input type="hidden" name="up_month" id="up_month" value="<?=$up_month?>">
									<input type="hidden" name="up_day" id="up_day" value="<?=$up_day?>">
									<span><?=$up_year?>�� <?=$up_month?>�� <?=$up_day?>��</span>
								</td>
								<th class="gray">��������</th>
								<td class="appoint">
						<?
							if ($type == "modify") {

								$sql = "SELECT C_PRS_NAME, PRS_POSITION, C_PRS_ID FROM DF_APPROVAL_CC INNER JOIN DF_PERSON ON C_PRS_ID = PRS_ID WHERE DOC_NO = '$doc_no' AND PRF_ID IN (1,2,3,4,7) ORDER BY C_ORDER, PRS_POSITION";
								$rs = sqlsrv_query($dbConn, $sql);

								$i = 0;
								$cc = "";
								$c_id = "";
								while ($record = sqlsrv_fetch_array($rs)) {
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
								<th class="gray">�μ�</th>
								<td colspan="3"><?=getTeamInfo($prs_team)?></td>
						    </tr>
							<tr>
								<th class="gray">�̸�</th>
								<td colspan="3"><?=$prs_position?> <?=$prs_name?></td>
							</tr>
							<tr class="last">
								<th class="gray">����</th>
								<td colspan="3"><input type="text" name="title" value="<?=$title?>"></td>
							</tr>

						<? if ($form_category == "�Ի���ΰ�") { ?>
							
							<tr style="border-bottom:2px solid;">
								<th class="gray">ä��μ�</th>
								<td colspan="3">
									<select name="team" style="width:250px;">
										<option value=""></option>
						<?
								$sql = "SELECT TEAM FROM DF_TEAM_CODE WITH(NOLOCK) WHERE VIEW_YN = 'Y' ORDER BY SORT";
								$rs = sqlsrv_query($dbConn,$sql);

								while ($record = sqlsrv_fetch_array($rs)) {
									$p_team = $record['TEAM'];
						?>
										<option value="<?=$p_team?>"<? if ($team == $p_team) { echo " selected"; } ?>><?=$p_team?></option>
						<?
								}
						?>
									</select>
								</td>
							</tr>						

						<? } ?>

						<? if ($form_category == "���ǰ�Ǽ�(v2)") { ?>

							<tr class="last">
								<th class="gray">������Ʈ</th>
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

								while ($record = sqlsrv_fetch_array($rs)) {
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
							<tr class="last" style="border-bottom:2px solid;">
								<th class="gray">�ѱݾ�</th>
								<td colspan="3"><input type="text" name="money_total" id="money_total" style="ime-mode:disabled;width:100px;" onKeyPress="if (event.keyCode < 48 || event.keyCode > 57) { event.returnValue = false; }" onKeyup="javascript:checkThousand(this,this.value);" readonly>�� <span style="color:#777777">(�ڵ��Է�)</span>
								</td>
							</tr>
							<tr>
								<td colspan="4" style="padding:0px; padding-left:0px;">
						<?
							$sql = "SELECT 
										IDX, TYPE, MONEY, TAX, TARGET, PAY_TYPE, PAY_INFO, COMPANY, MANAGER, CONTACT, 
										BANK_NAME, BANK_NUM, BANK_USER, PAY_DATE, MEMO
									FROM 
										DF_PROJECT_EXPENSE_V2
									WHERE 
										DOC_NO = '$doc_no' AND LAST = 'Y'
									ORDER BY 
										IDX";
							$rs = sqlsrv_query($dbConn,$sql);

							$expense = 0;
							while ($record = sqlsrv_fetch_array($rs)) {
								$db_type		= $record['TYPE'];		// û��
								$db_moeny		= $record['MONEY'];		// �ѱݾ�
								$db_tax			= $record['TAX'];		// ����
								$db_target		= $record['TARGET'];	// ���
								$db_pay_type	= $record['PAY_TYPE'];	// �������
								$db_pay_info	= $record['PAY_INFO'];	// �������� �߰�
								$db_company		= $record['COMPANY'];	// ��ü��
								$db_manager		= $record['MANAGER'];	// �����
								$db_contact		= $record['CONTACT'];	// ����ó
								$db_bank_name	= $record['BANK_NAME'];	// �����
								$db_bank_num	= $record['BANK_NUM'];	// ���¹�ȣ
								$db_bank_user	= $record['BANK_USER'];	// �����ָ�
								$db_pay_date	= $record['PAY_DATE'];	// ��������
								$db_memo		= $record['MEMO'];		// Ȱ�볻��
								$db_idx			= $record['IDX'];		// IDX

								// ������� ��
								$display['B'] = "style='display:none;'";
								$display['C'] = "style='display:none;'";
								$display['P'] = "style='display:none;'";
								$display['A'] = "style='display:none;'";
								$display['H'] = "style='display:none;'";
								$display[$db_pay_type] = "style='display:;'";

								// �׸� ���ð�								
								$checked1[$db_type]		= "checked";	// û�� ���ð�
								$checked2[$db_tax]		= "checked";	// ���� ���ð�
								$checked3[$db_target]	= "checked";	// ��� ���ð�								
								$checked4[$db_pay_type] = "checked";	// ������� ���ð�
								$checked5[$db_pay_info] = "checked";	// �������� ���ð�
								$checked6[$db_pay_date] = "checked";	// �������� ���ð�

								// �ѱݾ�
								$money_total += $db_moeny;
						?>
									<!-- // �������� ���� End -->
									<div id="payment_<?=$expense?>" <? if ($db_moeny == "" && $expense != 0) { ?> style="display:none;"<? } ?>>
									<table width="100%">
									<tr style="border-bottom:2px solid;">
										<th width="100" class="gray">�׸�<?=$_n[$expense]?><br><br>
																	<? if ($expense > 0) { ?>
																	<img src="/img/btn_popup_delete.gif" class="btn_delete" data-no="<?=$expense?>" data-flg="Y" alt="">
																	<? } ?>
										</th>
										<td style="padding-left:1px;">

										<table width="100%">
										<tr>
											<th class="gray2">û��</th>
											<td colspan="3">
												<input type="radio" name="type_<?=$expense?>" value="1" <? echo $checked1['1']; ?> onChange="alertType(this);">�Ǻ�û�� 
												<input type="radio" name="type_<?=$expense?>" value="2" <? echo $checked1['2']; ?> onChange="alertType(this);">��û�� 
												<input type="radio" name="type_<?=$expense?>" value="3" <? echo $checked1['3']; ?> onChange="alertType(this);">�
											</td>
										</tr>
										<tr>
											<th class="gray2">�ݾ�</th>
											<td colspan="3">
												<input type="text" name="money_<?=$expense?>" class="money_" style="ime-mode:disabled;width:100px;" onKeyPress="if (event.keyCode < 48 || event.keyCode > 57) { event.returnValue = false; }" onKeyup="javascript:checkThousand(this,this.value);sumPayment();" value="<?=number_format($db_moeny,0)?>">�� 
												&nbsp;&nbsp;&nbsp;&nbsp;
												<input type="radio" name="tax_<?=$expense?>" value="1" <? echo $checked2['1']; ?>>�鼼 
												<input type="radio" name="tax_<?=$expense?>" value="2" <? echo $checked2['2']; ?>>�ΰ������� 
												<input type="radio" name="tax_<?=$expense?>" value="3" <? echo $checked2['3']; ?>>�ΰ��� ���� 
												<input type="radio" name="tax_<?=$expense?>" value="4" <? echo $checked2['4']; ?>>���������� ���� 
												<input type="radio" name="tax_<?=$expense?>" value="5" <? echo $checked2['5']; ?>>���������� ����
											</td>
										</tr>
										<tr>
											<th class="gray2">���</th>
											<td colspan="3">
												<input type="radio" name="target_<?=$expense?>" value="1" <? echo $checked3['1']; ?>>���ֻ� 
												<input type="radio" name="target_<?=$expense?>" value="2" <? echo $checked3['2']; ?>>�뿪(��������) 
												<input type="radio" name="target_<?=$expense?>" value="3" <? echo $checked3['3']; ?>>�̹���,����,���� 
												<input type="radio" name="target_<?=$expense?>" value="4" <? echo $checked3['4']; ?>>�̺�Ʈ��ǰ  
												<input type="radio" name="target_<?=$expense?>" value="5" <? echo $checked3['5']; ?>>����� 
												<input type="radio" name="target_<?=$expense?>" value="6" <? echo $checked3['6']; ?>>��Ÿ
											</td>
										</tr>
										<tr class="last">
											<th class="gray2">��������</th>
											<td colspan="3" style="padding-left:1px;">
												<table width="100%">
													<tr>
														<td class="gray2">�������</td>
														<td>
															<span data-no="<?=$expense?>">
																<input type="radio" class="pay_type" name="pay_type_<?=$expense?>" value="B" <? echo $checked4['B']; ?>>������ü
																<input type="radio" class="pay_type" name="pay_type_<?=$expense?>" value="C" <? echo $checked4['C']; ?>>ī�����
																<input type="radio" class="pay_type" name="pay_type_<?=$expense?>" value="P" <? echo $checked4['P']; ?>>���ΰ��
																<input type="radio" class="pay_type" name="pay_type_<?=$expense?>" value="A" <? echo $checked4['A']; ?> onChange="alertPaytype(this);">�ڵ���ü
																<input type="radio" class="pay_type" name="pay_type_<?=$expense?>" value="H" <? echo $checked4['H']; ?>>���ݰ���
															</span>
														</td>
													</tr>
												</table>
												<table width="100%" id="paytype_B_<?=$expense?>" <?=$display['B']?>>
													<tr>
														<td class="gray2">��꼭</td>
														<td colspan="6">
															<input type="radio" name="pay_info_<?=$expense?>" value="1" <? echo $checked5['1']; ?>>���ݰ�꼭 
															<input type="radio" name="pay_info_<?=$expense?>" value="2" <? echo $checked5['2']; ?>>��꼭 
															<input type="radio" name="pay_info_<?=$expense?>" value="3" <? echo $checked5['3']; ?>>�������������(���ݿ�����)
															<input type="radio" name="pay_info_<?=$expense?>" value="4" <? echo $checked5['4']; ?>>������������ҵ�
														</td>
													</tr>
													<tr>
														<td class="gray2">��ü��</td>
														<td colspan="6"><input type="text" style="width:150px;" name="company_<?=$expense?>" maxlength="20" value="<?=$db_company?>"></td>
													</tr>
													<tr>
														<td class="gray2">�����</td>
														<td colspan="6"><input type="text" style="width:150px;" name="manager_<?=$expense?>" maxlength="10" value="<?=$db_manager?>"></td>
													</tr>
													<tr>
														<td class="gray2">����ó</td>
														<td colspan="6"><input type="text" style="width:150px;" name="contact_<?=$expense?>" maxlength="20" value="<?=$db_contact?>"></td>
													</tr>
													<tr>
														<td class="gray2">��ü��������</td>
														<td>�����</td>
														<td><input type="text" style="width:100px;" name="bank_name_<?=$expense?>" maxlength="10" value="<?=$db_bank_name?>"></td>
														<td>���¹�ȣ</td>
														<td><input type="text" style="width:120px;" name="bank_num_<?=$expense?>" maxlength="30" value="<?=$db_bank_num?>"></td>
														<td>������</td>
														<td><input type="text" style="width:100px;" name="bank_user_<?=$expense?>" maxlength="10" value="<?=$db_bank_user?>"></td>
													</tr>
													<tr style="border-bottom:0px;">
														<td class="gray2">��������</td>
														<td colspan="6">
															<input type="radio" name="pay_date_<?=$expense?>" value="1" <? echo $checked6['1']; ?>>���� 
															<input type="radio" name="pay_date_<?=$expense?>" value="2" <? echo $checked6['2']; ?>>���� 
															<input type="radio" name="pay_date_<?=$expense?>" value="3" <? echo $checked6['3']; ?> onChange="alertPaydate(this)">��Ȱ��� �Ϸ� ���
														</td>
													</tr>
												</table>
												<table width="100%" id="paytype_C_<?=$expense?>" <?=$display['C']?>>
													<tr style="border-bottom:0px;">
														<td class="gray2">��������</td>
														<td>
															<input type="radio" name="pay_info_<?=$expense?>" value="5" <? echo $checked5['5']; ?> >�¶��� 
															<input type="radio" name="pay_info_<?=$expense?>" value="6" <? echo $checked5['6']; ?>>�湮����
														</td>
													</tr>
												</table>
												<table width="100%" id="paytype_P_<?=$expense?>" <?=$display['P']?>>
													<tr style="border-bottom:0px;">
														<td class="gray2">��������</td>
														<td>
															<input type="radio" name="pay_info_<?=$expense?>" value="7" <? echo $checked5['7']; ?>>����ī�� 
															<input type="radio" name="pay_info_<?=$expense?>" value="8" <? echo $checked5['8']; ?>>��������
														</td>
													</tr>
												</table>
												<table width="100%" id="paytype_A_<?=$expense?>" <?=$display['A']?>>
													<tr style="border-bottom:0px;">
														<td class="gray2">��ü����</td>
														<td>
															<input type="radio" name="pay_info_<?=$expense?>" value="9" <? echo $checked5['9']; ?>>5�� 
															<input type="radio" name="pay_info_<?=$expense?>" value="10" <? echo $checked5['10']; ?>>10�� 
															<input type="radio" name="pay_info_<?=$expense?>" value="11" <? echo $checked5['11']; ?>>15�� 
															<input type="radio" name="pay_info_<?=$expense?>" value="12" <? echo $checked5['12']; ?>>20�� / �ڵ���ü �ű� ��û�� �濵������ Ȯ�� �� ���
														</td>
													</tr>
												</table>
												<table width="100%" id="paytype_H_<?=$expense?>" <?=$display['H']?>>
													<tr style="border-bottom:0px;">
														<td class="gray2">����</td>
														<td>�濵������ Ȯ�� �� ���� ����</td>
													</tr>
												</table>
											</td>
										</tr>
										<tr style="border-bottom:0px;">
											<th class="gray2">Ȱ�볻��</th>
											<td colspan="3"><input type="text" name="memo_<?=$expense?>" value="<?=$db_memo?>" style="width:100%;"></td>
										</tr>
										</table>
										<input type="hidden" name="idx_<?=$expense?>" value="<?=$db_idx?>">

										</td>
									</tr>
									</table>
									</div>
									<!-- // �������� ���� End -->
									<?
											$expense = $expense + 1;

											unset($display);

											unset($checked1);
											unset($checked2);
											unset($checked3);
											unset($checked4);
											unset($checked5);
											unset($checked6);
										}
									?>
									<!-- �ѱݾ� ǥ�� -->
									<script> sumPayment(); </script>

									<?
										for ($i=$expense; $i<5; $i++) {
									?>
									<!-- // �������� ���� Start -->
									<div id="payment_<?=$i?>" <? if ($i != 0) { ?> style="display:none;"<? } ?>>
									<table width="100%">
									<tr style="border-bottom:2px solid;">
										<th class="gray">�׸�<?=$_n[$i]?><br><br>
														<? if ($i > 0) { ?>
														<img src="/img/btn_popup_delete.gif" class="btn_delete" data-no="<?=$i?>" data-flg="N" alt="">
														<? } ?>
										</th>
										<td style="padding-left:1px;">

										<table width="100%">
										<tr>
											<th class="gray2">û��</th>
											<td colspan="3"> 
												<input type="radio" name="type_<?=$i?>" value="1" onClick="alertType(this);">�Ǻ�û�� 
												<input type="radio" name="type_<?=$i?>" value="2" onClick="alertType(this);">��û�� 
												<input type="radio" name="type_<?=$i?>" value="3" onClick="alertType(this);">�
											</td>
										</tr>
										<tr>
											<th class="gray2">�ݾ�</th>
											<td colspan="3">
												<input type="text" name="money_<?=$i?>" class="money_" style="ime-mode:disabled;width:100px;" onKeyPress="if (event.keyCode < 48 || event.keyCode > 57) { event.returnValue = false; }" onKeyup="javascript:checkThousand(this,this.value);sumPayment();" value="">�� 
												&nbsp;&nbsp;&nbsp;&nbsp;
												<input type="radio" name="tax_<?=$i?>" value="1">�鼼 
												<input type="radio" name="tax_<?=$i?>" value="2">�ΰ������� 
												<input type="radio" name="tax_<?=$i?>" value="3">�ΰ��� ���� 
												<input type="radio" name="tax_<?=$i?>" value="4">���������� ���� 
												<input type="radio" name="tax_<?=$i?>" value="5">���������� ����
											</td>
										</tr>
										<tr>
											<th class="gray2">���</th>
											<td colspan="3">
												<input type="radio" name="target_<?=$i?>" value="1">���ֻ� 
												<input type="radio" name="target_<?=$i?>" value="2">�뿪(��������) 
												<input type="radio" name="target_<?=$i?>" value="3">�̹���,����,���� 
												<input type="radio" name="target_<?=$i?>" value="4">�̺�Ʈ��ǰ  
												<input type="radio" name="target_<?=$i?>" value="5">����� 
												<input type="radio" name="target_<?=$i?>" value="6">��Ÿ
											</td>
										</tr>
										<tr class="last">
											<th class="gray2">��������</th>
											<td colspan="3" style="padding-left:1px;">
												<table width="100%">
													<tr>
														<td class="gray2">�������</td>
														<td>
															<span data-no="<?=$i?>">
																<input type="radio" class="pay_type" name="pay_type_<?=$i?>" value="B" checked>������ü
																<input type="radio" class="pay_type" name="pay_type_<?=$i?>" value="C">ī�����
																<input type="radio" class="pay_type" name="pay_type_<?=$i?>" value="P">���ΰ��
																<input type="radio" class="pay_type" name="pay_type_<?=$i?>" value="A" onChange="alertPaytype(this);">�ڵ���ü
																<input type="radio" class="pay_type" name="pay_type_<?=$i?>" value="H">���ݰ���
															</span>
														</td>
													</tr>
												</table>
												<table width="100%" id="paytype_B_<?=$i?>">
													<tr>
														<td class="gray2">��꼭</td>
														<td colspan="6">
															<input type="radio" name="pay_info_<?=$i?>" value="1">���ݰ�꼭 
															<input type="radio" name="pay_info_<?=$i?>" value="2">��꼭 
															<input type="radio" name="pay_info_<?=$i?>" value="3">�������������(���ݿ�����)
															<input type="radio" name="pay_info_<?=$i?>" value="4">������������ҵ�
														</td>
													</tr>
													<tr>
														<td class="gray2">��ü��</td>
														<td colspan="6"><input type="text" style="width:150px;" name="company_<?=$i?>" maxlength="20"></td>
													</tr>
													<tr>
														<td class="gray2">�����</td>
														<td colspan="6"><input type="text" style="width:150px;" name="manager_<?=$i?>" maxlength="10"></td>
													</tr>
													<tr>
														<td class="gray2">����ó</td>
														<td colspan="6"><input type="text" style="width:150px;" name="contact_<?=$i?>" maxlength="20"></td>
													</tr>
													<tr>
														<td style="background-color:#efefef;">��ü��������</td>
														<td>�����</td>
														<td><input type="text" style="width:100px;" name="bank_name_<?=$i?>" maxlength="10"></td>
														<td>���¹�ȣ</td>
														<td><input type="text" style="width:120px;" name="bank_num_<?=$i?>" maxlength="30"></td>
														<td>������</td>
														<td><input type="text" style="width:100px;" name="bank_user_<?=$i?>" maxlength="10"></td>
													</tr>
													<tr style="border-bottom:0px;">
														<td class="gray2">��������</td>
														<td colspan="6">
															<input type="radio" name="pay_date_<?=$i?>" value="1">���� 
															<input type="radio" name="pay_date_<?=$i?>" value="2">���� 
															<input type="radio" name="pay_date_<?=$i?>" value="3" onChange="alertPaydate(this)">��Ȱ��� �Ϸ� ���
														</td>
													</tr>
												</table>
												<table width="100%" id="paytype_C_<?=$i?>" style="display:none;">
													<tr style="border-bottom:0px;">
														<td class="gray2">��������</td>
														<td>
															<input type="radio" name="pay_info_<?=$i?>" value="5">�¶��� 
															<input type="radio" name="pay_info_<?=$i?>" value="6">�湮����
														</td>
													</tr>
												</table>
												<table width="100%" id="paytype_P_<?=$i?>" style="display:none;">
													<tr style="border-bottom:0px;">
														<td class="gray2">��������</td>
														<td>
															<input type="radio" name="pay_info_<?=$i?>" value="7">����ī�� 
															<input type="radio" name="pay_info_<?=$i?>" value="8">��������
														</td>
													</tr>
												</table>
												<table width="100%" id="paytype_A_<?=$i?>" style="display:none;">
													<tr style="border-bottom:0px;">
														<td class="gray2">��ü����</td>
														<td>
															<input type="radio" name="pay_info_<?=$i?>" value="9">5�� 
															<input type="radio" name="pay_info_<?=$i?>" value="10">10�� 
															<input type="radio" name="pay_info_<?=$i?>" value="11">15�� 
															<input type="radio" name="pay_info_<?=$i?>" value="12">20�� / �ڵ���ü �ű� ��û�� �濵������ Ȯ�� �� ���
														</td>
													</tr>
												</table>
												<table width="100%" id="paytype_H_<?=$i?>" style="display:none;">
													<tr style="border-bottom:0px;">
														<td class="gray2">����</td>
														<td>�濵������ Ȯ�� �� ���� ����</td>
													</tr>
												</table>
											</td>
										</tr>
										<tr style="border-bottom:0px;">
											<th class="gray2">Ȱ�볻��</th>
											<td colspan="3"><input type="text" name="memo_<?=$i?>" style="width:100%;"></td>
										</tr>
										</table>
										<input type="hidden" name="idx_<?=$i?>" value="<?=$i?>">

										</td>
									</tr>
									</table>
									</div>
									<!-- // �������� ���� End -->
									<?
										}
									?>
									<table width="100%">
									<tr style="background-color:#f2f2f2; border:0px;">
										<td>
											<a href="javascript:addPayment();" class="btn_plus">�����׸� �߰� &nbsp;<img src="../img/btn_plus.jpg" alt="�߰��ϱ�" /><br><br></a>
										</td>
									</tr>
									</table>
								</td>
							</tr>

						<? } else if ($form_category == "�Ի���ΰ�") { ?>

							<tr>
								<td colspan="4" style="padding:0px; padding-left:0px;">
						<?
							$sql = "SELECT 
										IDX, TYPE, DATA1, DATA2, DATA3, DATA4, DATA5, DATA6, DATA7, DATA8, 
										DATA9, DATA10, DATA11, DATA12, DATA13, DATA14, DATA15
									FROM 
										DF_APPROVAL_EXPANSION
									WHERE 
										DOC_NO = '$doc_no' AND LAST = 'Y'
									ORDER BY 
										IDX";
							$rs = sqlsrv_query($dbConn,$sql);

							$expansion = 0;
							while ($record = sqlsrv_fetch_array($rs)) {
								$db_type = trim($record['TYPE']);							// ä�뱸��

								if ($db_type == "A") {
									$db_name1	  = trim($record['DATA1']);					// ����
									$db_cause1	  = trim($record['DATA2']);					// �ѱݾ�
									$db_career	  = trim($record['DATA3']);					// ��±���
									$db_birth	  = trim($record['DATA4']);					// �������
									$db_school	  = trim($record['DATA5']);					// �����б�
									$db_major	  = trim($record['DATA6']);					// ����
									$db_career2	  = explode("-",trim($record['DATA7']));	// ��±Ⱓ
									$db_career_y  = $db_career2[0];
									$db_career_m  = $db_career2[1];
									$db_position  = trim($record['DATA8']);					// ����
									$db_rating	  = trim($record['DATA9']);					// ȣ��
									$db_reader	  = trim($record['DATA10']);				// ��å
									$db_join	  = explode("-",trim($record['DATA11']));	// �Ի翹����
									$db_join_y	  = $db_join[0];
									$db_join_m	  = $db_join[1];
									$db_join_d	  = $db_join[2];
									$db_name2	  = null;
									$db_cause2	  = null;
									$db_gubun	  = null;
									$db_relay	  = null;
									$db_salary_h  = null;
									$db_salary_m  = null;
									$db_period1_y = null;
									$db_period1_m = null;
									$db_period1_d = null;
									$db_period2_y = null;
									$db_period2_m = null;
									$db_period2_d = null;
									$db_memo	  = null;
								} else if ($db_type == "B") {
									$db_name2	  = trim($record['DATA1']);					// ����
									$db_cause2	  = trim($record['DATA2']);					// �ѱݾ�
									$db_gubun	  = trim($record['DATA3']);					// ä�뱸��
									$db_relay	  = trim($record['DATA4']);					// �߰���ü
									$db_salary_h  = number_format(trim($record['DATA5']));	// �ñ�
									$db_salary_m  = number_format(trim($record['DATA6']));	// ����
									$db_period1	  = explode("-",trim($record['DATA7']));	// �Ⱓ1
									$db_period1_y = $db_period1[0];
									$db_period1_m = $db_period1[1];
									$db_period1_d = $db_period1[2];
									$db_period2	  = explode("-",trim($record['DATA8']));	// �Ⱓ2
									$db_period2_y = $db_period2[0];
									$db_period2_m = $db_period2[1];
									$db_period2_d = $db_period2[2];
									$db_memo	  = trim($record['DATA9']);					// ��Ÿ
									$db_name1	  = null;
									$db_cause1	  = null;
									$db_career	  = null;
									$db_birth	  = null;
									$db_school	  = null;
									$db_major	  = null;
									$db_career_y  = null;
									$db_career_m  = null;
									$db_position  = null;
									$db_rating	  = null;
									$db_reader	  = null;
									$db_join_y	  = null;
									$db_join_m	  = null;
									$db_join_d	  = null;
								}

								$db_idx	= trim($record['IDX']);								// IDX

								// ������� ��
								$display['A'] = "style='display:none;'";
								$display['B'] = "style='display:none;'";
								$display[$db_type] = "style='display:;'";

								// �׸� ���ð�								
								$checked1[$db_type]		= "checked";	// ä�뱸��
								$checked2[$db_career]	= "checked";	// ��±���
								$checked3[$db_position]	= "checked";	// ����								
								$checked4[$db_rating]	= "checked";	// ȣ��
								$checked5[$db_reader]	= "checked";	// ��å
								$checked6[$db_gubun]	= "checked";	// ����
						?>
									<!-- // �Ի����� ���� Start -->
									<div id="employ_<?=$expansion?>" <? if ($db_type == "" && $expansion != 0) { ?> style="display:none;"<? } ?>>
									<table width="100%">
									<tr style="border-bottom:2px solid;">
										<th width="100" class="gray">���<?=$_n[$expansion]?><br><br>
																	<? if ($expansion > 0) { ?>
																	<img src="/img/btn_popup_delete.gif" class="btn_delete" data-no="<?=$expansion?>" data-flg="Y" alt="">
																	<? } ?>
										</th>
										<td style="padding-left:1px;">

										<table width="100%">
										<tr>
											<th class="gray2">ä�뱸��</th>
											<td colspan="3">
												<span data-no="<?=$expansion?>">
													<input type="radio" class="employ_type" name="type_<?=$expansion?>" value="A" <? echo $checked1['A']; ?>>������ 
													<input type="radio" class="employ_type" name="type_<?=$expansion?>" value="B" <? echo $checked1['B']; ?>>����� 
												</span>
											</td>
										</tr>
										</table>
										<!-- ������ �Է� �� -->
										<table width="100%" id="employtype_A_<?=$expansion?>" <?=$display['A']?>>
										<tr>
											<th class="gray2">ä�����</th>
											<td colspan="3">
												<input type="text" name="cause1_<?=$expansion?>" style="width:100%;" value="<?=$db_cause1?>">
											</td>
										</tr>
										<tr>
											<th class="gray2">��±���</th>
											<td colspan="3">
												<input type="radio" name="career_<?=$expansion?>" value="1" <? echo $checked2['1']; ?>>���� 
												<input type="radio" name="career_<?=$expansion?>" value="2" <? echo $checked2['2']; ?>>��� 
											</td>
										</tr>
										<tr>
											<th class="gray2">����</th>
											<td>
												<input type="text" name="name1_<?=$expansion?>" maxlength="30" style="width:100px;" value="<?=$db_name1?>">
											</td>
											<th class="gray2">�������</th>
											<td>
												<input type="text" name="birth_<?=$expansion?>" maxlength="30" style="width:100px;" value="<?=$db_birth?>">
											</td>
										</tr>
										<tr>
											<th class="gray2">�����б�</th>
											<td>
												<input type="text" name="school_<?=$expansion?>" maxlength="30" style="width:180px;" value="<?=$db_school?>">
											</td>
											<th class="gray2">����</th>
											<td>
												<input type="text" name="major_<?=$expansion?>" maxlength="30" style="width:200px;" value="<?=$db_major?>">
											</td>
										</tr>
										<tr>
											<th class="gray2">�� ��±Ⱓ</th>
											<td colspan="3">
												<input type="text" name="career_y_<?=$expansion?>" maxlength="3" style="width:50px;" value="<?=$db_career_y?>">��&nbsp;&nbsp;&nbsp; 
												<input type="text" name="career_m_<?=$expansion?>" maxlength="2" style="width:40px;" value="<?=$db_career_m?>">�� 
											</td>
										</tr>
										<tr>
											<th class="gray2">������ ����</th>
											<td>
												<input type="radio" name="position_<?=$expansion?>" value="1" <? echo $checked3['1']; ?>>���
												<input type="radio" name="position_<?=$expansion?>" value="2" <? echo $checked3['2']; ?>>����
												<input type="radio" name="position_<?=$expansion?>" value="3" <? echo $checked3['3']; ?>>�븮
												<input type="radio" name="position_<?=$expansion?>" value="4" <? echo $checked3['4']; ?>>����
												<input type="radio" name="position_<?=$expansion?>" value="5" <? echo $checked3['5']; ?>>����
											</td>
											<th class="gray2">������ ȣ��</th>
											<td>
												<input type="radio" name="rating_<?=$expansion?>" value="1" <? echo $checked4['1']; ?>>1ȣ��
												<input type="radio" name="rating_<?=$expansion?>" value="2" <? echo $checked4['2']; ?>>2ȣ��
												<input type="radio" name="rating_<?=$expansion?>" value="3" <? echo $checked4['3']; ?>>3ȣ��
												<input type="radio" name="rating_<?=$expansion?>" value="4" <? echo $checked4['4']; ?>>4ȣ��
											</td>
										</tr>
										<tr>
											<th class="gray2">��å</th>
											<td colspan="3">
												<input type="radio" name="reader_<?=$expansion?>" value="1" <? echo $checked5['1']; ?>>����
												<input type="radio" name="reader_<?=$expansion?>" value="2" <? echo $checked5['2']; ?>>����
											</td>
										</tr>
										<tr style="border-bottom:0px;">
											<th class="gray2">�Ի翹����</th>
											<td colspan="3">
												<input type="text" name="join_y_<?=$expansion?>" maxlength="4" style="width:50px;" value="<?=$db_join_y?>">��&nbsp;&nbsp;&nbsp;
												<input type="text" name="join_m_<?=$expansion?>" maxlength="2" style="width:40px;" value="<?=$db_join_m?>">��&nbsp;&nbsp;&nbsp; 
												<input type="text" name="join_d_<?=$expansion?>" maxlength="2" style="width:40px;" value="<?=$db_join_d?>">�� 
											</td>
										</tr>
										</table>

										<!-- ����� �Է� �� -->
										<table width="100%" id="employtype_B_<?=$expansion?>" <?=$display['B']?>>
										<tr>
											<th width="100" class="gray2">����</th>
											<td colspan="3">
												<input type="radio" name="gubun_<?=$expansion?>" value="1" <? echo $checked6['1']; ?>>��ǽ� ����
												<input type="radio" name="gubun_<?=$expansion?>" value="2" <? echo $checked6['2']; ?>>�ܱ�����(3�����̸�) 
												<input type="radio" name="gubun_<?=$expansion?>" value="3" <? echo $checked6['3']; ?>>������� 
												<input type="radio" name="gubun_<?=$expansion?>" value="4" <? echo $checked6['4']; ?>>������ٹ� �� ������ ���� 
											</td>
										</tr>
										<tr>
											<th class="gray2">ä�����</th>
											<td colspan="3">
												<input type="text" name="cause2_<?=$expansion?>" value="<?=$db_cause2?>" style="width:100%;">
											</td>
										</tr>
										<tr>
											<th class="gray2">����</th>
											<td colspan="3">
												<input type="text" name="name2_<?=$expansion?>" maxlength="30" style="width:100px;" value="<?=$db_name2?>">
											</td>
										</tr>
										<tr>
											<th class="gray2">�߰���ü</th>
											<td colspan="3">
												<input type="text" name="relay_<?=$expansion?>" maxlength="30" style="width:180px;" value="<?=$db_relay?>">
											</td>
										</tr>
										<tr>
											<th class="gray2">�޿�</th>
											<td colspan="3">
												�ñ� <input type="text" name="salary_h_<?=$expansion?>" maxlength="10" style="ime-mode:disabled;width:50px;" onKeyPress="if (event.keyCode < 48 || event.keyCode > 57) { event.returnValue = false; }" onKeyup="javascript:checkThousand(this,this.value);" value="<?=$db_salary_h?>">
												���� <input type="text" name="salary_m_<?=$expansion?>" maxlength="10" style="ime-mode:disabled;width:70px;" onKeyPress="if (event.keyCode < 48 || event.keyCode > 57) { event.returnValue = false; }" onKeyup="javascript:checkThousand(this,this.value);" value="<?=$db_salary_m?>">
											</td>
										</tr>
										<tr>
											<th class="gray2">�Ⱓ</th>
											<td colspan="3">
												<input type="text" name="period1_y_<?=$expansion?>" maxlength="4" style="width:50px;" value="<?=$db_period1_y?>">�� 
												<input type="text" name="period1_m_<?=$expansion?>" maxlength="2" style="width:40px;" value="<?=$db_period1_m?>">�� 
												<input type="text" name="period1_d_<?=$expansion?>" maxlength="2" style="width:40px;" value="<?=$db_period1_d?>">�� ����&nbsp;&nbsp;&nbsp;
												<input type="text" name="period2_y_<?=$expansion?>" maxlength="4" style="width:50px;" value="<?=$db_period2_y?>">�� 
												<input type="text" name="period2_m_<?=$expansion?>" maxlength="2" style="width:40px;" value="<?=$db_period2_m?>">�� 
												<input type="text" name="period2_d_<?=$expansion?>" maxlength="2" style="width:40px;" value="<?=$db_period2_d?>">�� ����
											</td>
										</tr>
										<tr style="border-bottom:0px;">
											<th class="gray2">��Ÿ</th>
											<td colspan="3">
												<input type="text" name="memo_<?=$expansion?>" maxlength="30" style="width:100%;" value="<?=$db_memo?>">
											</td>
										</tr>
										</table>
										<input type="hidden" name="idx_<?=$expansion?>" value="<?=$db_idx?>">

										</td>
									</tr>
									</table>
									</div>
									<!-- // �Ի����� ���� End -->
									<?
											$expansion = $expansion + 1;

											unset($display);

											unset($checked1);
											unset($checked2);
											unset($checked3);
											unset($checked4);
											unset($checked5);
											unset($checked6);
										}

										for ($i=$expansion; $i<5; $i++) {
									?>
									<!-- // �Ի����� ���� Start -->
									<div id="employ_<?=$i?>" <? if ($i != 0) { ?> style="display:none;"<? } ?>>
									<table width="100%">
									<tr style="border-bottom:2px solid;">
										<th width="100" class="gray">���<?=$_n[$i]?><br><br>
																	<? if ($i > 0) { ?>
																	<img src="/img/btn_popup_delete.gif" class="btn_delete" data-no="<?=$i?>" data-flg="N" alt="">
																	<? } ?>
										</th>
										<td style="padding-left:1px;">

										<table width="100%">
										<tr>
											<th width="100" class="gray2">ä�뱸��</th>
											<td colspan="3">
												<span data-no="<?=$i?>">
													<input type="radio" class="employ_type" name="type_<?=$i?>" value="A" checked>������ 
													<input type="radio" class="employ_type" name="type_<?=$i?>" value="B">����� 
												</span>
											</td>
										</tr>
										</table>
										<!-- ������ �Է� �� -->
										<table width="100%" id="employtype_A_<?=$i?>">
										<tr>
											<th width="100" class="gray2">ä�����</th>
											<td colspan="3">
												<input type="text" name="cause1_<?=$i?>" style="width:100%;">
											</td>
										</tr>
										<tr>
											<th class="gray2">��±���</th>
											<td colspan="3">
												<input type="radio" name="career_<?=$i?>" value="1">���� 
												<input type="radio" name="career_<?=$i?>" value="2">��� 
											</td>
										</tr>
										<tr>
											<th class="gray2">����</th>
											<td>
												<input type="text" name="name1_<?=$i?>" maxlength="30" style="width:100px;">
											</td>
											<th class="gray2">�������</th>
											<td>
												<input type="text" name="birth_<?=$i?>" maxlength="30" style="width:100px;">
											</td>
										</tr>
										<tr>
											<th class="gray2">�����б�</th>
											<td>
												<input type="text" name="school_<?=$i?>" maxlength="30" style="width:180px;">
											</td>
											<th width="100" class="gray2">����</th>
											<td>
												<input type="text" name="major_<?=$i?>" maxlength="30" style="width:200px;">
											</td>
										</tr>
										<tr>
											<th class="gray2">�� ��±Ⱓ</th>
											<td colspan="3">
												<input type="text" name="career_y_<?=$i?>" maxlength="3" style="width:50px;">��&nbsp;&nbsp;&nbsp; 
												<input type="text" name="career_m_<?=$i?>" maxlength="2" style="width:40px;">�� 
											</td>
										</tr>
										<tr>
											<th class="gray2">������ ����</th>
											<td>
												<input type="radio" name="position_<?=$i?>" value="1">���
												<input type="radio" name="position_<?=$i?>" value="2">����
												<input type="radio" name="position_<?=$i?>" value="3">�븮
												<input type="radio" name="position_<?=$i?>" value="4">����
												<input type="radio" name="position_<?=$i?>" value="5">����
											</td>
											<th class="gray2">������ ȣ��</th>
											<td>
												<input type="radio" name="rating_<?=$i?>" value="1">1ȣ��
												<input type="radio" name="rating_<?=$i?>" value="2">2ȣ��
												<input type="radio" name="rating_<?=$i?>" value="3">3ȣ��
												<input type="radio" name="rating_<?=$i?>" value="4">4ȣ��
											</td>
										</tr>
										<tr>
											<th class="gray2">��å</th>
											<td colspan="3">
												<input type="radio" name="reader_<?=$i?>" value="1">����
												<input type="radio" name="reader_<?=$i?>" value="2">����
											</td>
										</tr>
										<tr style="border-bottom:0px;">
											<th class="gray2">�Ի翹����</th>
											<td colspan="3">
												<input type="text" name="join_y_<?=$i?>" maxlength="4" style="width:50px;">��&nbsp;&nbsp;&nbsp;
												<input type="text" name="join_m_<?=$i?>" maxlength="2" style="width:40px;">��&nbsp;&nbsp;&nbsp;
												<input type="text" name="join_d_<?=$i?>" maxlength="2" style="width:40px;">�� 
											</td>
										</tr>
										</table>
										<!-- ����� �Է� �� -->
										<table width="100%" id="employtype_B_<?=$i?>" style="display:none;">
										<tr>
											<th width="100" class="gray2">����</th>
											<td colspan="3">
												<input type="radio" name="gubun_<?=$i?>" value="1">��ǽ� ����
												<input type="radio" name="gubun_<?=$i?>" value="2">�ܱ�����(3�����̸�) 
												<input type="radio" name="gubun_<?=$i?>" value="3">������� 
												<input type="radio" name="gubun_<?=$i?>" value="4">������ٹ� �� ������ ���� 
											</td>
										</tr>
										<tr>
											<th class="gray2">ä�����</th>
											<td colspan="3">
												<input type="text" name="cause2_<?=$i?>" style="width:100%;">
											</td>
										</tr>
										<tr>
											<th class="gray2">����</th>
											<td colspan="3">
												<input type="text" name="name2_<?=$i?>" maxlength="30" style="width:100px;">
											</td>
										</tr>
										<tr>
											<th class="gray2">�߰���ü</th>
											<td colspan="3">
												<input type="text" name="relay_<?=$i?>" maxlength="30" style="width:180px;">
											</td>
										</tr>
										<tr>
											<th class="gray2">�޿�</th>
											<td colspan="3">
												�ñ� <input type="text" name="salary_h_<?=$i?>" maxlength="10" style="ime-mode:disabled;width:50px;" onKeyPress="if (event.keyCode < 48 || event.keyCode > 57) { event.returnValue = false; }" onKeyup="javascript:checkThousand(this,this.value);">
												���� <input type="text" name="salary_m_<?=$i?>" maxlength="10" style="ime-mode:disabled;width:70px;" onKeyPress="if (event.keyCode < 48 || event.keyCode > 57) { event.returnValue = false; }" onKeyup="javascript:checkThousand(this,this.value);">
											</td>
										</tr>
										<tr>
											<th class="gray2">�Ⱓ</th>
											<td colspan="3">
												<input type="text" name="period1_y_<?=$i?>" maxlength="4" style="width:50px;">��&nbsp;&nbsp;&nbsp; 
												<input type="text" name="period1_m_<?=$i?>" maxlength="2" style="width:40px;">��&nbsp;&nbsp;&nbsp; 
												<input type="text" name="period1_d_<?=$i?>" maxlength="2" style="width:40px;">�� ����&nbsp;&nbsp;&nbsp;
												<input type="text" name="period2_y_<?=$i?>" maxlength="4" style="width:50px;">��&nbsp;&nbsp;&nbsp; 
												<input type="text" name="period2_m_<?=$i?>" maxlength="2" style="width:40px;">��&nbsp;&nbsp;&nbsp; 
												<input type="text" name="period2_d_<?=$i?>" maxlength="2" style="width:40px;">�� ����
											</td>
										</tr>
										<tr style="border-bottom:0px;">
											<th class="gray2">��Ÿ</th>
											<td colspan="3">
												<input type="text" name="memo_<?=$i?>" maxlength="30" style="width:100%;">
											</td>
										</tr>
										</table>
										<input type="hidden" name="idx_<?=$i?>" value="<?=$i?>">

										</td>
									</tr>
									</table>
									</div>
									<!-- // �Ի����� ���� End -->
									<?
										}
									?>
									<table width="100%">
									<tr style="background-color:#f2f2f2; border:0px;">
										<td>
											<a href="javascript:addEmploy();" class="btn_plus">�Ի����� �߰� &nbsp;<img src="../img/btn_plus.jpg" alt="�߰��ϱ�" /><br><br></a>
										</td>
									</tr>
									</table>
								</td>
							</tr>

						<? } else { ?>

							<tr class="period">
								<th class="gray">�Ⱓ</th>
								<td colspan="3" class="last">
									<select name="fr_year" id="fr_year">
									<?
										for ($i=$startYear; $i<=date("Y",strtotime("+1 year")); $i++) {
											if ($i == substr($start_date,0,4)) { 
												$selected = " selected"; 
											} else {
												$selected = "";
											}

											echo "<option value='".$i."'".$selected.">".$i."</option>";
										}
									?>
									</select>
									<span>��</span>
									<select name="fr_month" id="fr_month">
									<?
										for ($i=1; $i<=12; $i++) {
											if (strlen($i) == "1") {
												$j = "0".$i;
											} else {
												$j = $i;
											}

											if ($j == substr($start_date,5,2)) {
												$selected = " selected";
											} else {
												$selected = "";
											}

											echo "<option value='".$j."'".$selected.">".$i."</option>";
										}
									?>
									</select>
									<span>��</span>
									<select name="fr_day" id="fr_day">
									<?
										for ($i=1; $i<=31; $i++) {
											if (strlen($i) == "1") {
												$j = "0".$i;
											} else {
												$j = $i;
											}

											if ($j == substr($start_date,8,2)) {
												$selected = " selected";
											} else {
												$selected = "";
											}

											echo "<option value='".$j."'".$selected.">".$i."</option>";
										}
									?>
									</select>
									<span>��</span>
									<input type="hidden" id="fr_date" class="datepicker">
									<span>-</span>
									<select name="to_year" id="to_year">
									<?
										for ($i=$startYear; $i<=date("Y",strtotime("+1 year")); $i++) {
											if ($i == substr($end_date,0,4)) { 
												$selected = " selected"; 
											} else {
												$selected = "";
											}

											echo "<option value='".$i."'".$selected.">".$i."</option>";
										}
									?>
									</select>
									<span>��</span>
									<select name="to_month" id="to_month">
									<?
										for ($i=1; $i<=12; $i++) {
											if (strlen($i) == "1") {
												$j = "0".$i;
											} else {
												$j = $i;
											}

											if ($j == substr($end_date,5,2)) {
												$selected = " selected";
											} else {
												$selected = "";
											}

											echo "<option value='".$j."'".$selected.">".$i."</option>";
										}
									?>
									</select>
									<span>��</span>
									<select name="to_day" id="to_day">
									<?
										for ($i=1; $i<=31; $i++) {
											if (strlen($i) == "1") {
												$j = "0".$i;
											} else {
												$j = $i;
											}

											if ($j == substr($end_date,8,2)) {
												$selected = " selected";
											} else {
												$selected = "";
											}

											echo "<option value='".$j."'".$selected.">".$i."</option>";
										}
									?>
									</select>
									<span>��</span>
									<input type="hidden" id="to_date" class="datepicker">
								</td>
							</tr>
						<? } ?>
						<? if ($form_category == "�ٰܱ�/�İ߰�" || $form_category == "�����") { ?>				
							<tr class="partner">
								<th class="gray">������</th>
								<td colspan="3" class="appoint">
							<?
							if ($type == "write") {
								$sql = "SELECT P_PRS_NAME, PRS_POSITION, P_PRS_ID FROM DF_APPROVAL_PARTNER INNER JOIN DF_PERSON ON P_PRS_ID = PRS_ID WHERE DOC_NO = '$last_doc_no' ORDER BY P_ORDER";
							} else if ($type == "modify") {
								$sql = "SELECT P_PRS_NAME, PRS_POSITION, P_PRS_ID FROM DF_APPROVAL_PARTNER INNER JOIN DF_PERSON ON P_PRS_ID = PRS_ID WHERE DOC_NO = '$doc_no' ORDER BY P_ORDER";
							}
								$rs = sqlsrv_query($dbConn, $sql);

								$i = 0;
								$partner = "";
								$p_id = "";
								while ($record = sqlsrv_fetch_array($rs)) {
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
								<th class="gray">��</th>
								<td colspan="3" style="padding:10px;"><textarea name="contents" style="width:100%;height:100%;"><?=$contents?></textarea></td>
							</tr>
						</tbody>
					</table>

					<? if ($form_category == "�Ի���ΰ�") { ?>

					<div id="bbs">
						<div class="attach_section clearfix" style="border:0px;">
							<p class="left">�̷¼�</p>
							<div class="right clearfix">
								<div class="clearfix">
									<div id="file_D1" name="file_D1">
										<input id="attachment_1" name="attachment_1" class="attach df_textinput" type="text" readonly>
										<div class="input"><input id="file_1" name="file_1" class="browse" type="file"></div>
										<span class="description">�� �ѹ��� �ø� �� �ִ� ���� �뷮�� <b>�ִ� 10MB</b> �Դϴ�.</span>
										<input type="hidden" name="filedel_1" id="filedel_1">
										<div class="attached" id="delfile_1">
										<? if ($type == "modify" && $file_1 != "") { ?>	
											<span><?=$file_1?></span>
											<a href="javascript:delFile(1);"><img src="../img/btn_delete.gif" alt="����" /></a>
										<? } ?>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="attach_section clearfix">
							<p class="left">��Ʈ������</p>
							<div class="right clearfix">
								<div class="clearfix">
									<div id="file_D2" name="file_D2">
										<input id="attachment_2" name="attachment_2" class="attach df_textinput" type="text" readonly>
										<div class="input"><input id="file_2" name="file_2" class="browse" type="file"></div>
										<span class="description">�� �ѹ��� �ø� �� �ִ� ���� �뷮�� <b>�ִ� 10MB</b> �Դϴ�.</span>
										<input type="hidden" name="filedel_2" id="filedel_2">
										<div class="attached" id="delfile_2">
										<? if ($type == "modify" && $file_2 != "") { ?>	
											<span><?=$file_2?></span>
											<a href="javascript:delFile(2);"><img src="../img/btn_delete.gif" alt="����" /></a>
										<? } ?>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<? } else { ?>

					<div id="bbs">
						<div class="attach_section clearfix">
							<p class="left">÷������</p>
							<div class="right clearfix">
								<div class="clearfix">
									<div id="file_D1" name="file_D1">
										<input id="attachment_1" name="attachment_1" class="attach df_textinput" type="text" readonly>
										<div class="input"><input id="file_1" name="file_1" class="browse" type="file"></div>
										<span class="description">�� �ѹ��� �ø� �� �ִ� ���� �뷮�� <b>�ִ� 10MB</b> �Դϴ�.</span>
										<a href="javascript:addFile();" class="btn_plus"><img src="../img/btn_plus.jpg" alt="�߰��ϱ�" /></a>
										<input type="hidden" name="filedel_1" id="filedel_1">
										<div class="attached" id="delfile_1">
										<? if ($type == "modify" && $file_1 != "") { ?>	
											<span><?=$file_1?></span>
											<a href="javascript:delFile(1);"><img src="../img/btn_delete.gif" alt="����" /></a>
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
											<a href="javascript:delFile(2);"><img src="../img/btn_delete.gif" alt="����" /></a>
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
											<a href="javascript:delFile(3);"><img src="../img/btn_delete.gif" alt="����" /></a>
										<? } ?>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<? } ?>

					<div class="btn-wrap">
					<? if ($type == "write") { ?>
						<a href="javascript:funWrite('save');"><img src="/img/btn_tmpSave.gif" alt=""></a>
						<a href="javascript:funWrite('write');" class="floatr"><img src="/img/btn_insert_154.gif" alt=""></a>
					<? } else if ($type == "modify") { ?>
						<? if ($status == "�ӽ�") { ?>
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
			<p class="pop_title">���缱 ���</p>
			<a href="javascript:HidePop('ToAdd');" class="close">�ݱ�</a>
		</div>
		<div class="pop_bottom clearfix">
			<div class="left section">
				<div class="top">
					<select name="search_type" id="search_type1" class="role" onchange="javascript:changeDiv();">
						<option value="to">������</option>
						<option value="cc">������</option>
					</select>
				</div>
				<div class="mid clearfix">
					<div class="left_area floatl">
						<div class="search_area">
							<input id="search_name1" name="search_name" class="df_textinput" type="text" style="width:100px; border:none;">
							<img src="/img/project/btn_x.gif" alt="����" class="btn_x" id="resetBtn1" />
						</div>
					</div>
					<div class="right_area floatr">
						<img src="/img/btn_search_pop.gif" alt="�˻�" id="searchBtn1" style="cursor:pointer;">
					</div>
				</div>
				<div id="to_list" class="bottom">
	<?
		$sql = "SELECT SEQNO, POSITION FROM DF_POSITION_CODE WITH(NOLOCK) ORDER BY SEQNO";
		$rs = sqlsrv_query($dbConn,$sql);

		while($record=sqlsrv_fetch_array($rs)) {
			$orderby .= "WHEN PRS_POSITION='". $record['POSITION'] ."' THEN ". $record['SEQNO'] ." ";
		}

		$orderbycase = " ORDER BY CASE ". $orderby . " END, PRS_NAME";

		$sql = "SELECT PRS_ID, PRS_LOGIN, PRS_POSITION, PRS_NAME FROM DF_PERSON WITH(NOLOCK) WHERE PRF_ID IN (1,2,3,4,7) AND PRS_ID NOT IN (102)". $orderbycase;
		$rs = sqlsrv_query($dbConn,$sql);

		$i = 0;
		while($record=sqlsrv_fetch_array($rs)) {
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

		while($record=sqlsrv_fetch_array($rs)) {
			$orderby .= "WHEN PRS_POSITION='". $record['POSITION'] ."' THEN ". $record['SEQNO'] ." ";
		}

		$orderbycase = " ORDER BY CASE ". $orderby . " END, PRS_NAME";

		$sql = "SELECT PRS_ID, PRS_LOGIN, PRS_POSITION, PRS_NAME FROM DF_PERSON WITH(NOLOCK) WHERE PRF_ID IN (1,2,3,4,7) AND PRS_ID NOT IN (102)". $orderbycase;
		$rs = sqlsrv_query($dbConn,$sql);

		$i = 0;
		while($record=sqlsrv_fetch_array($rs)) {
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
			<img src="/img/btn_select.gif" alt="����" id="sel_click_to" class="sel" style="cursor:pointer;">
			<div class="right section clearfix">
				<div class="top"><span><strong>������</strong><span></div>
				<div id="to_div" name="to_div" class="middle-1" style="height:158px;">
					<input type="hidden" name="total_to" id="total_to" value="0">
					<ul id="list_to" style="padding:5px;"></ul>
					<input type="hidden" name="check_list_to" id="check_list_to">
				</div>
				<input type="hidden" name="to_list" id="to_list" value="<?=$to?>">
				<input type="hidden" name="to_ids" id="to_ids" value="<?=$p_id?>">

				<div class="top"><span><strong>������</strong><span></div>
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
			<img src="/img/btn_accept.gif" alt="Ȯ��" id="popup_select_ok" class="accept" style="cursor:pointer;">
		</div>
	</form>
	</div>
</div>

<div id="popPartnerAdd" class="approvalDocument-popup2" style="display:none;">
	<div class="select" id="popup_select2">
	<form name="popup_form2" id="popup_form2" method="post">
	<input type="hidden" name="search_type" id="search_type2" value="partner">
		<div class="pop_top">
			<p class="pop_title">������ ����</p>
			<a href="javascript:HidePop('PartnerAdd');" class="close">�ݱ�</a>
		</div>
		<div class="pop_bottom clearfix">
			<div class="left section">
				<div>
					<div class="top">
						<div style="width:262px; padding:18px 0 0 26px">
							<div class="left_area floatl">
								<div class="search_area">
									<input id="search_name2" name="search_name" class="df_textinput" type="text" style="width:100px; border:none;" />
									<img src="/img/project/btn_x.gif" alt="����" class="btn_x" id="resetBtn2" />
								</div>
							</div>
							<div class="right_area floatr">
								<img src="/img/btn_search_pop.gif" alt="�˻�" id="searchBtn2" style="cursor:pointer;">
							</div>
						</div>
					</div>
				</div>

				<div class="bottom" id="partner_list">
	<?
		$sql = "SELECT SEQNO, POSITION FROM DF_POSITION_CODE WITH(NOLOCK) ORDER BY SEQNO";
		$rs = sqlsrv_query($dbConn,$sql);

		while($record=sqlsrv_fetch_array($rs)) {
			$orderby .= "WHEN PRS_POSITION='". $record['POSITION'] ."' THEN ". $record['SEQNO'] ." ";
		}

		$orderbycase = " ORDER BY CASE ". $orderby . " END, PRS_NAME";

		$sql = "SELECT PRS_ID, PRS_LOGIN, PRS_POSITION, PRS_NAME FROM DF_PERSON WITH(NOLOCK) WHERE PRF_ID IN (1,2,3,4,7) AND PRS_ID NOT IN (102)". $orderbycase;
		$rs = sqlsrv_query($dbConn,$sql);

		$i = 0;
		while($record=sqlsrv_fetch_array($rs)) {
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
			<img src="/img/btn_select.gif" alt="����" id="sel_click_partner" class="sel" style="cursor:pointer;">
			<div class="right section clearfix">
				<div class="top"><span><strong>������</strong><span></div>
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
			<img src="/img/btn_accept.gif" alt="Ȯ��" id="popup_partner_ok" class="accept" style="cursor:pointer;">
		</div>
	</form>
	</div>
</div>

</body>
</html>
