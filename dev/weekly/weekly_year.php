<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
	$year = isset($_REQUEST['year']) ? $_REQUEST['year'] : 2016; 
	$project = isset($_REQUEST['project']) ? $_REQUEST['project'] : null;

	if ($project == "")
	{
?>
		<script>
			alert("프로젝트 번호 확인!!");
		</script>
<?
		exit;
	}

	$sql = "SELECT 
				TITLE
			FROM 
				DF_PROJECT
			WHERE 
				PROJECT_NO = '". $project ."'";
	$rs = sqlsrv_query($dbConn,$sql);
	$record = sqlsrv_fetch_array($rs);

	$project_title = $record['TITLE'];

	$id_arr = "";
	$name_arr = "";
	$sql = "SELECT 
				DISTINCT PRS_NAME, PRS_ID
			FROM 
				DF_PROJECT_DETAIL 
			WHERE 
				PROJECT_NO = '". $project ."'";
	$rs = sqlsrv_query($dbConn,$sql);

	while ($record = sqlsrv_fetch_array($rs))
	{
		$name_arr = $name_arr . $record['PRS_NAME'] ."##";
		$id_arr = $id_arr . $record['PRS_ID'] . "##";
	}

	$weekly_arr = "";
	$sql = "SELECT DISTINCT WEEK_ORD FROM DF_WEEKLY WHERE WEEK_ORD LIKE '". $year ."%'";
	$rs = sqlsrv_query($dbConn,$sql);

	while ($record = sqlsrv_fetch_array($rs))
	{
		$weekly_arr = $weekly_arr . $record['WEEK_ORD'] . "##";
	}

	header( "Content-type: application/vnd.ms-excel;charset=EUC-KR");
	header( "Expires: 0" );
	header( "Cache-Control: must-revalidate, post-check=0,pre-check=0" );
	header( "Pragma: public" );
	header( "Content-Disposition: attachment; filename=[". $project ."] ". $project_title .".xls" );
?>

	<META HTTP-EQUIV='Content-Type' CONTENT='text/html; charset=EUC-KR'>
	
	<table border="1">
		<thead>
			<tr>
				<td></td>
		<?
			$name_ex = explode("##",$name_arr);
			$id_ex = explode("##",$id_arr);

			for ($i=0; $i<sizeof($id_ex); $i++)
			{
				if ($name_ex[$i] != "")
				{
		?>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;"><?=$name_ex[$i]?></td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">참여비율</td>
		<?
				}
			}
		?>
			</tr>
		</thead>
		<tbody>
<?
	$weekly_ex = explode("##",$weekly_arr);

	for ($i=0; $i<sizeof($weekly_ex); $i++)
	{
		if ($weekly_ex[$i] != "")
		{
?>
			<tr>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;"><?=number_format(substr($weekly_ex[$i],4,2),0)?>월<?=substr($weekly_ex[$i],6,1)?>주</td>
<?
			$sql = "SELECT
						T.PRS_NAME, T.THIS_WEEK_CONTENT, T.THIS_WEEK_RATIO
					FROM
						(
							SELECT 
								A.PRS_NAME, B.THIS_WEEK_CONTENT, ISNULL(B.THIS_WEEK_RATIO,0) AS THIS_WEEK_RATIO
							FROM
								(
									SELECT PRS_NAME FROM DF_PROJECT_DETAIL WHERE PROJECT_NO = '". $project ."'

								) A
								LEFT OUTER JOIN
								(
									SELECT 
										M.PRS_ID, M.PRS_NAME, N.WEEKLY_NO, N.THIS_WEEK_CONTENT, ISNULL(N.THIS_WEEK_RATIO ,0) AS THIS_WEEK_RATIO, M.WEEK_ORD
									FROM 
										DF_WEEKLY M INNER JOIN DF_WEEKLY_DETAIL N
									ON
										M.SEQNO = N.WEEKLY_NO
									WHERE
										N.PROJECT_NO = '". $project ."' AND M.WEEK_ORD = '". $weekly_ex[$i] ."'
								) B
							ON 
								A.PRS_NAME = B.PRS_NAME
						) T
					ORDER BY 
						T.PRS_NAME";
			$rs = sqlsrv_query($dbConn,$sql);

			while ($record = sqlsrv_fetch_array($rs))
			{
				$my_content = $record['THIS_WEEK_CONTENT'];
				$my_ratio = $record['THIS_WEEK_RATIO'];
				if ($my_ratio > 0) { $my_ratio = $my_ratio ."%"; } else { $my_ratio = ""; }
?>
				<td style="font-size:12px;text-align:left;"><?=str_replace(chr(13), "<br>",$my_content)?></td>
				<td style="font-size:12px;text-align:center;"><?=$my_ratio?></td>
<?
			}
?>

			</tr>
<?
		}
	}
?>
		</tbody>
	</table>
</body>
</html>