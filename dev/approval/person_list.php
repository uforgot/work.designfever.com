<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
	$type = isset($_REQUEST['search_type']) ? $_REQUEST['search_type'] : "partner";
	$name = isset($_REQUEST['search_name']) ? $_REQUEST['search_name'] : null;

	$sql = "SELECT SEQNO, POSITION FROM DF_POSITION_CODE WITH(NOLOCK) ORDER BY SEQNO";
	$rs = sqlsrv_query($dbConn,$sql);

	while($record=sqlsrv_fetch_array($rs))
	{
		$orderby .= "WHEN PRS_POSITION='". $record['POSITION'] ."' THEN ". $record['SEQNO'] ." ";
	}

	$orderbycase = " ORDER BY CASE ". $orderby . " END, PRS_NAME";

	if ($name != "")
	{
		$nameSQL = " AND PRS_NAME = '$name'";
	}

	$sql = "SELECT PRS_ID, PRS_LOGIN, PRS_POSITION, PRS_NAME FROM DF_PERSON WITH(NOLOCK) WHERE PRF_ID IN (1,2,3,4) AND PRS_ID NOT IN (102)". $nameSQL . $orderbycase;
	$rs = sqlsrv_query($dbConn,$sql);

	$i = 0;
	$DivHtml = "";
	while($record=sqlsrv_fetch_array($rs))
	{
		$i++;

		$id = $record['PRS_ID'];
		$login = $record['PRS_LOGIN'];
		$position = $record['PRS_POSITION'];
		$name = $record['PRS_NAME'];

		$DivHtml .= "<input type='hidden' id='sel_".$type."_id_". $i ."' value='". $id ."'>";
		$DivHtml .= "<input type='hidden' id='sel_".$type."_login_". $i ."' value='". $login ."'>";
		$DivHtml .= "<input type='hidden' id='sel_".$type."_position_". $i ."' value='". $position ."'>";
		$DivHtml .= "<input type='hidden' id='sel_".$type."_name_". $i ."' value='". $name ."'>";
		$DivHtml .= "<p><input type='checkbox' id='check_".$type."_". $i ."' name='check_".$type."' title='". $id ."'><label for='check_".$type."_". $i ."' style='cursor:pointer;'>". $position ." ". $name ."</label></p>";
	}
	$DivHtml .= "<input type='hidden' name='total_".$type."' id='total_partner' value='". $i ."'>";
?>

<? include INC_PATH."/top.php"; ?>

<script type="text/JavaScript">
	$(document).ready(function(){
		$("#<?=$type?>_list",parent.document).empty();
		$("#<?=$type?>_list",parent.document).append("<?=$DivHtml?>");

		var check = $("#total_<?=$type?>",parent.document).val();

		for (var c=1; c<=check; c++)
		{
			var kids = $("#list_<?=$type?>",parent.document).children().length;

			for (var j=1; j<=kids; j++)
			{
				if ($("#list_<?=$type?>_id_"+ j,parent.document).val() == $("#check_<?=$type?>_"+c,parent.document).attr("title"))
				{
					var id = $("#check_<?=$type?>_"+c,parent.document).attr("id");
					$("#"+id,parent.document).attr("disabled",true);
					$("#"+id,parent.document).attr("checked",true);
				}
			}
		}
	});
</script>
			