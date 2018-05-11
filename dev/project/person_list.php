<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
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
		//$nameSQL = " AND PRS_NAME = '$name'";
        $nameSQL = " AND PRS_NAME LIKE '%$name%'";

	}

	//$sql = "SELECT PRS_ID, PRS_LOGIN, PRS_POSITION, PRS_NAME FROM DF_PERSON WITH(NOLOCK) WHERE PRF_ID IN (1,2,3,4) AND PRS_ID NOT IN (102)". $nameSQL . $orderbycase;
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

		$DivHtml .= "<input type='hidden' id='sel_id_". $i ."' value='". $id ."'>";
		$DivHtml .= "<input type='hidden' id='sel_login_". $i ."' value='". $login ."'>";
		$DivHtml .= "<input type='hidden' id='sel_position_". $i ."' value='". $position ."'>";
		$DivHtml .= "<input type='hidden' id='sel_name_". $i ."' value='". $name ."'>";
		$DivHtml .= "<div class='search-member'><input type='checkbox' id='check_". $i ."' name='check' title='". $id ."'><span class='is-size-7'><label for='check_". $i ."' style='cursor:pointer;'>". $position ." ". $name ."</label></span></div>";
	}
	$DivHtml .= "<input type='hidden' name='total' id='total' value='". $i ."'>";
?>

<? include INC_PATH."/top.php"; ?>

<script type="text/JavaScript">
	$(document).ready(function(){
		$("#person_list",parent.document).empty();
		$("#person_list",parent.document).append("<?=$DivHtml?>");

		var check = $("#total",parent.document).val();

		for (var c=1; c<=check; c++)
		{
			for (var t=1; t<=7; t++)
			{
				var kids = $("#list_"+ t,parent.document).children().length;
				
				for (var j=1; j<=kids; j++)
				{
					if ($("#list_"+ t +"_id_"+ j,parent.document).val() == $("#check_"+c,parent.document).attr("title"))
					{
						var id = $("#check_"+c,parent.document).attr("id");
						$("#"+id,parent.document).attr("disabled",true);
						$("#"+id,parent.document).attr("checked",true);
					}
				}
			}
		}
	});
</script>
			