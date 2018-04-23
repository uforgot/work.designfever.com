<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
	//권한 체크
	if ($prf_id != "4") 
	{ 
?>
	<script type="text/javascript">
		alert("해당페이지는 임원,관리자만 확인 가능합니다.");
	</script>
<?
		exit;
	}

	$sql = "SELECT 
				PRS_ID, PRS_NAME, PRS_TEAM, PRS_POSITION, PRS_EMAIL, PRS_EXTENSION, PRS_TEL, PRS_BIRTH, PRS_BIRTH_TYPE
			FROM 
				DF_PERSON WITH(NOLOCK)
			WHERE 
				PRF_ID IN (1,2,3,4) AND PRS_ID NOT IN (102)
			ORDER BY 
				CASE 
					WHEN PRS_TEAM = 'CEO' THEN 1 
					WHEN PRS_TEAM = 'COO' THEN 2 
					WHEN PRS_TEAM = 'CCO' THEN 3 
					WHEN PRS_TEAM = '경영지원팀' THEN 4 
					WHEN PRS_TEAM = 'digital marketing division' THEN 5 
					WHEN PRS_TEAM = 'dm1' THEN 6 
					WHEN PRS_TEAM = 'dm2' THEN 7 
					WHEN PRS_TEAM = 'digital experience division' THEN 8 
					WHEN PRS_TEAM = 'dx1' THEN 9 
					WHEN PRS_TEAM = 'dx2' THEN 10 
					WHEN PRS_TEAM = 'brand experience team' THEN 11 
					WHEN PRS_TEAM = 'design1 division' THEN 12 
					WHEN PRS_TEAM = 'design1' THEN 13 
					WHEN PRS_TEAM = 'design2' THEN 14 
					WHEN PRS_TEAM = 'design2 division' THEN 15 
					WHEN PRS_TEAM = 'design3' THEN 16 
					WHEN PRS_TEAM = 'design4' THEN 17 
					WHEN PRS_TEAM = 'design5' THEN 18 
					WHEN PRS_TEAM = 'motion graphic division' THEN 19 
					WHEN PRS_TEAM = 'mg1' THEN 20 
					WHEN PRS_TEAM = 'mg2' THEN 21 
					WHEN PRS_TEAM = 'film & content division' THEN 22 
					WHEN PRS_TEAM = 'fc' THEN 23 
					WHEN PRS_TEAM = 'df lab' THEN 24 
					WHEN PRS_TEAM = 'ix1' THEN 25
					WHEN PRS_TEAM = 'ix2' THEN 26
					WHEN PRS_TEAM = 'ixd' THEN 27
				END,
				CASE 
					WHEN PRS_POSITION='대표' THEN 1
					WHEN PRS_POSITION='이사' THEN 2
					WHEN PRS_POSITION='부장' THEN 3
					WHEN PRS_POSITION='수석' THEN 4
					WHEN PRS_POSITION='실장' THEN 5
					WHEN PRS_POSITION='차장' THEN 6
					WHEN PRS_POSITION='팀장' THEN 7
					WHEN PRS_POSITION='과장' THEN 8
					WHEN PRS_POSITION='책임' THEN 9
					WHEN PRS_POSITION='대리' THEN 10 
					WHEN PRS_POSITION='선임' THEN 11 
					WHEN PRS_POSITION='주임' THEN 12 
					WHEN PRS_POSITION='사원' THEN 13 
					WHEN PRS_POSITION='인턴' THEN 14
				END";
	$rs = sqlsrv_query($dbConn, $sql);

	header( "Content-type: application/vnd.ms-excel;charset=EUC-KR");
	header( "Expires: 0" );
	header( "Cache-Control: must-revalidate, post-check=0,pre-check=0" );
	header( "Pragma: public" );
	header( "Content-Disposition: attachment; filename=조직도_".date("Y").date("m").".xls" );
?>

	<META HTTP-EQUIV='Content-Type' CONTENT='text/html; charset=EUC-KR'>
	<style>
	<!--
	br{mso-data-placement:same-cell;}
	-->
	</style>
	<table border=1>
		<thead>
			<tr>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">이름</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">팀명</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">직급</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">내선번호</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">DF E-mail</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">핸드폰</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">생년월일</td>
			</tr>
		</thead>
		<tbody>
<?
	while ($record = sqlsrv_fetch_array($rs))
	{
		$col_id = $record['PRS_ID'];
		$col_name = $record['PRS_NAME'];
		$col_team = $record['PRS_TEAM'];
		$col_position = $record['PRS_POSITION'];
		$col_email = $record['PRS_EMAIL'];
		$col_extension = $record['PRS_EXTENSION'];
		$col_tel = $record['PRS_TEL'];
		$col_birth = $record['PRS_BIRTH'];
		$col_birth_type = $record['PRS_BIRTH_TYPE'];
?>
			<tr>
				<td style='font-size:12px;font-weight:bold;text-align:center;'><?=$col_name?></td>
				<td style='font-size:12px;font-weight:bold;text-align:center;'><?=$col_team?></td>
				<td style='font-size:12px;font-weight:bold;text-align:center;'><?=$col_position?></td>
				<td style='font-size:12px;font-weight:bold;text-align:center;'><?=$col_extension?></td>
				<td style='font-size:12px;font-weight:bold;text-align:center;'><?=$col_email?></td>
				<td style='font-size:12px;font-weight:bold;text-align:center;'><?=$col_tel?></td>
				<td style='font-size:12px;font-weight:bold;text-align:center;'>
					<?=$col_birth?>
					<? if ($col_birth_type == "음력") { echo "(음)"; } ?>
				</td>
			</tr>
<?
	}
?>
	</tbody>
</table>
