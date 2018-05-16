<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
	//권한 체크
	if ($prf_id != "4") { 
?>
	<script type="text/javascript">
		alert("해당페이지는 관리자만 확인 가능합니다.");
		self.close();
	</script>
<?
		exit;
	}

	$doc_no = isset($_REQUEST['doc_no']) ? $_REQUEST['doc_no'] : null; 
	$idx = isset($_REQUEST['idx']) ? $_REQUEST['idx'] : null; 
?>

<? include INC_PATH."/pop_top.php"; ?>

</head>
<body>
<!-- pop -->		 
			<div class="intra_pop work_team_pop" style="border:0px; margin-top:-170px;">
				<div class="pop_top">
					<p class="pop_title">결제금액 수정 내역</p>
					<a href="javascript:self.close();" class="close">닫기</a>
				</div>
				<div class="pop_body">
					<table class="notable edit_table"  width="100%" style="margin-top:-30px;">
						<summary></summary>
						<colgroup>
							<col width="*" />
							<col width="15%" />
							<col width="15%" />
							<col width="15%" />
						</colgroup>
						<tr style="border-bottom:1px solid #000;">
							<th style="padding-bottom:10px;">비용항목</th>
							<th style="padding-bottom:10px;">금액</th>
							<th></th>
							<th></th>
						</tr>
			<?
				$sql = "SELECT 
							MEMO, MONEY, PRS_NAME, PRS_POSITION, CONVERT(char(20),REG_DATE,120) AS REG_DATE
						FROM 
							DF_PROJECT_EXPENSE_V2 WITH(NOLOCK)
						WHERE 
							DOC_NO = '$doc_no' AND IDX = '$idx'
						ORDER BY 
							SEQNO DESC";
				$rs = sqlsrv_query($dbConn, $sql);

				while ($record=sqlsrv_fetch_array($rs))
				{
					$memo = $record['MEMO'];
					$money = $record['MONEY'];
					$name = $record['PRS_NAME'];
					$position = $record['PRS_POSITION'];
					$regdate = $record['REG_DATE'];
			?>
						<tr style="border-bottom:1px solid #e0e0e0;">
							<td style="padding:5px 0;" align="center"><?=$memo?></td>
							<td style="padding:5px 0;" align="center"><?=number_format($money,0)?></td>
							<td style="padding:5px 0;" align="center"><?=$position?> <?=$name?></td>
							<td style="padding:5px 0;" align="center"><?=$regdate?></td>
						</tr>
			<?
				}
			?>
					</table>
				</div>
			<!-- //pop -->
</form>
<? include INC_PATH."/pop_bottom.php"; ?>
</body>
</html>
