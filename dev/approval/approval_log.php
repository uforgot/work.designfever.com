<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
	$doc_no = isset($_REQUEST['doc_no']) ? $_REQUEST['doc_no'] : null; 

	if ($doc_no == "")
	{
?>
<script type="text/javascript">
	alert("해당 문서가 존재하지 않습니다.");
</script>
<?
		exit;
	}

	$sql = "SELECT
				A_PRS_TEAM, A_PRS_POSITION, A_PRS_NAME, A_STATUS, CONVERT(varchar(20),A_REG_DATE,120) AS A_REG_DATE
			FROM 
				DF_APPROVAL_TO WITH(NOLOCK)
			WHERE
				DOC_NO = '$doc_no'
			ORDER BY 
				A_ORDER";
	$rs = sqlsrv_query($dbConn,$sql);
?>

<? include INC_PATH."/top.php"; ?>
<script src="/js/approval.js"></script>

<script type="text/javascript">
	$(document).ready(function(){

		content_html = "";

		content_html = content_html + "		<table>";
		<?
			$i = 0;
			while ($record = sqlsrv_fetch_array($rs))
			{
				$to_team = $record['A_PRS_TEAM'];
				$to_position = $record['A_PRS_POSITION'];
				$to_name = $record['A_PRS_NAME'];
				$to_status = $record['A_STATUS'];
				$to_date = $record['A_REG_DATE'];
		?>
		content_html = content_html + "			<tr<? if ($i == 0) { echo " class='first'"; } ?>>";
		content_html = content_html + "				<td class=\"s1\"><?=$to_team?></td>";
		content_html = content_html + "				<td class=\"s2\"><?=$to_position?> <?=$to_name?></td>";
		content_html = content_html + "				<td class=\"s3\"><strong><?=$to_status?></strong></td>";
		content_html = content_html + "				<td class=\"s4\"><?=substr($to_date,0,10)?></td>";
		content_html = content_html + "				<td class=\"s5\"><?=substr($to_date,11,8)?></td>";
		content_html = content_html + "			</tr>";
		<?
				$i++;
			}
		?>
		content_html = content_html + "		<table>";

		$("#pop_log_body",top.document).html(content_html);

		var isPopup1 = false;
		var d = isPopup1 ? 'none' : 'block';
		var z = isPopup1 ? 0 : 987654;

		$("#popLog",top.document).attr("style","display:inline; z-index:"+z);

		isPopup1 = !isPopup1;
	});
</script>
</head>
<body>
</body>
</html>