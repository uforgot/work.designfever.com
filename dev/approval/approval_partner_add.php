<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
	$max = isset($_REQUEST['max']) ? $_REQUEST['max'] : 10; 
?>

<? include INC_PATH."/pop_top.php"; ?>

<script type="text/javascript">
	//���� ����
	$(document).ready(function(){
		var frm = document.form;
		var pfrm = opener.document.form;

		frm.partner_list.value = pfrm.partner.value+",";
		frm.partner_id.value = pfrm.partner_id.value+",";

		var partner_id = $("#partner_id").val();
		var arr_partner_id = partner_id.split(",");

		var partner_list = $("#partner_list").val();
		var arr_partner_list = partner_list.split(",");

		for (var i=0; i<arr_partner_id.length; i++ )
		{
			if (arr_partner_list[i] != "")
			{
				ex_partner_id = arr_partner_id[i].replace(/(^\s*)|(\s*$)/gi, "");

				$("#partner_div").append("<div id='partner_"+ex_partner_id+"' name='partner_"+ex_partner_id+"'><a href='javascript:delPerson("+ex_partner_id.replace(/(^\s*)|(\s*$)/gi, "")+","+i+");'>"+arr_partner_list[i]+"</a></div>");
			}
		}

	});
	//������ ����
	function selPerson(pos,name,id)
	{
		var frm = document.form;
		var k = 0;

		for (var i=0; i<frm.partner_id.value.length; i++)
		{
			if (frm.partner_id.value.charAt(i) == ",")	
			{
				k = k + 1;
			}
		}
		if ( k >= <?=$max?>)
		{
			alert("�ִ� ������ �ο��� <?=$max?>�� �Դϴ�.");
			return;
		}

		var partner_id = $("#partner_id").val();
		var arr_partner_id = partner_id.split(",");

		for (var i=0; i<arr_partner_id.length; i++ )
		{
			if (arr_partner_id[i] != "" && arr_partner_id[i] == id)
			{
				alert("�̹� �����ϼ̽��ϴ�.");
				return;
			}
		}

		$("#partner_div").append("<div id='partner_"+id+"' name='partner_"+id+"'><a href='javascript:delPerson("+id+","+k+");'>"+pos+" "+name+"</a></div>");
		$("#partner_list").val($("#partner_list").val()+pos+" "+name+",");
		$("#partner_id").val($("#partner_id").val()+id+",");
	}
	//������ ����
	function delPerson(id,k)
	{
		var partner_id = $("#partner_id").val();
		var arr_partner_id = partner_id.split(",");

		var partner_list = $("#partner_list").val();
		var arr_partner_list = partner_list.split(",");

		$("#partner_"+id).remove();
		$("#partner_list").val("");
		$("#partner_id").val("");

		for (var i=0; i<arr_partner_id.length; i++ )
		{
			if (arr_partner_list[i] != "" && i != k)
			{
				$("#partner_list").val($("#partner_list").val()+arr_partner_list[i]+",");
				$("#partner_id").val($("#partner_id").val()+arr_partner_id[i]+",");
			}
		}
	}
	//������ ����
	function AddPerson()
	{
		var frm = document.form;
		var pfrm = opener.document.form;

		pfrm.partner.value = "";
		pfrm.partner_id.value = "";

		pfrm.partner.value = frm.partner_list.value;
		pfrm.partner_id.value = frm.partner_id.value;

		self.close();
	}
</script>
</head>
<body>
<div align="center">
<form name="form" method="post">
<input type="hidden" name="type" value="<?=$type?>">
	<table>
		<tr>
			<td align="center" valign="top">
				��ü ���� ����Ʈ<br>
				<div style="height:300px; overflow-y:scroll; text-align:left;">
<?
	$sql = "SELECT 
				PRS_TEAM, PRS_POSITION, PRS_NAME, PRS_ID 
			FROM 
				DF_PERSON WITH(NOLOCK) 
			WHERE 
				PRF_ID IN (1,2,3,4) AND PRS_ID NOT IN (102,$prs_id) 
			ORDER BY 
				CASE 
					WHEN PRS_TEAM = 'Agency' THEN 1 
					WHEN PRS_TEAM = '�濵�����׷�' THEN 2 
					WHEN PRS_TEAM = '�濵������' THEN 3 
					WHEN PRS_TEAM = 'ȫ����' THEN 4 
					WHEN PRS_TEAM = '������ȹ�׷�' THEN 5 
					WHEN PRS_TEAM = 'Digital Marketing Division' THEN 6 
					WHEN PRS_TEAM = 'Digital Marketing 1' THEN 7 
					WHEN PRS_TEAM = 'Digital Marketing 2' THEN 8 
					WHEN PRS_TEAM = 'Digital eXperience Division' THEN 9 
					WHEN PRS_TEAM = 'Digital eXperience 1' THEN 10 
					WHEN PRS_TEAM = 'Digital eXperience 2' THEN 11 
					WHEN PRS_TEAM = 'Design1 Division' THEN 12 
					WHEN PRS_TEAM = 'Design 1' THEN 13 
					WHEN PRS_TEAM = 'Design 2' THEN 14 
					WHEN PRS_TEAM = 'Design2 Division' THEN 15 
					WHEN PRS_TEAM = 'Design 3' THEN 16 
					WHEN PRS_TEAM = 'Design 4' THEN 17 
					WHEN PRS_TEAM = 'Motion Graphic Division' THEN 18 
					WHEN PRS_TEAM = 'Motion Graphic 1' THEN 19 
					WHEN PRS_TEAM = 'Motion Graphic 2' THEN 20 
					WHEN PRS_TEAM = 'Interactive Lab' THEN 21 
					WHEN PRS_TEAM = 'Interactive eXperience' THEN 22 
					WHEN PRS_TEAM = 'Digital Publishing' THEN 23 
					WHEN PRS_TEAM = 'Development Lab' THEN 24 
					WHEN PRS_TEAM = 'Digital Development' THEN 25  
					WHEN PRS_TEAM = 'Creative da' THEN 26 
					WHEN PRS_TEAM = 'Designfever Holdings' THEN 27 
				END, 
				CASE 
					WHEN PRS_POSITION='��ǥ' THEN 1
					WHEN PRS_POSITION='�̻�' THEN 2
					WHEN PRS_POSITION='����' THEN 3
					WHEN PRS_POSITION='����' THEN 4
					WHEN PRS_POSITION='å��' THEN 5
					WHEN PRS_POSITION='�븮' THEN 6
					WHEN PRS_POSITION='����' THEN 7
					WHEN PRS_POSITION='����' THEN 8
					WHEN PRS_POSITION='���' THEN 9
					WHEN PRS_POSITION='����' THEN 10 
				END, 
				PRS_NAME
			";
	$rs = sqlsrv_query($dbConn, $sql);

	while($record = sqlsrv_fetch_array($rs))
	{
		$team = $record['PRS_TEAM'];
		$position = $record['PRS_POSITION'];
		$name = $record['PRS_NAME'];
		$id = $record['PRS_ID'];

		if ($team != $team2)
		{
			echo $team ."<br>";
		}
		echo "&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"javascript:selPerson('". $position ."','". $name ."','". $id ."');\">". $position ." ". $name ."</a><br>";

		$team2 = $team;
	}
?>
				</div>
			</td>
			<td width="10%">
			</td>
			<td align="center" valign="top">
				������ ����Ʈ<br>
				<div id="partner_div" name="partner_div" style="height:300px; width:150px; overflow-y:scroll; text-align:left;">
				</div>
				<input type="hidden" name="partner_list" id="partner_list" value="">
				<input type="hidden" name="partner_id" id="partner_id" value="">
			</td>
		</tr>
	</table>
	<input type="button" value="����" onClick="javascript:AddPerson();">
	<input type="button" value="�ݱ�" onClick="javascript:self.close();">
</form>
<? include INC_PATH."/pop_bottom.php"; ?>
</div>
</body>
</html>
