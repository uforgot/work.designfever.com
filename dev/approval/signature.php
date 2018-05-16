<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>


<?
	$sign = "";
	$signpwd = "";

	$sql = "SELECT PRS_SIGN, PRS_SIGNPWD FROM DF_PERSON WITH(NOLOCK) WHERE PRS_ID = '$prs_id'";
	$rs = sqlsrv_query($dbConn,$sql);

	$record = sqlsrv_fetch_array($rs);
	$sign = $record['PRS_SIGN'];
	$signpwd = $record['PRS_SIGNPWD'];

	if ($signpwd == "") { $signpwd = "Y"; }
?>

<? include INC_PATH."/top.php"; ?>

<script type="text/javascript">
	//등록
	function funWrite()
	{
		var frm = document.form;
		frm.target ="hdnFrame";
		frm.action = 'signature_act.php'; 
		frm.submit();
	}

	$(document).ready(function(){
		//선택된 파일명 표시
		$("#sign").change(function(){
			var str = this.value;
			var arr_str = str.split("\\");
			var arr_len = arr_str.length;
			$("#attachment").val(this.value);
		});
	 });

</script>

</head>

<body>
<div id="approval" class="wrapper">
<form name="form" method="post" enctype="multipart/form-data">
	<? include INC_PATH."/top_menu.php"; ?>

		<div class="inner-home">
		<? include INC_PATH."/approval_menu.php"; ?>

			<div class="signature-wrap clearfix">
				<div class="content-wrap">
					<table class="content-table" width="100%">
						<colgroup>
							<col width="180px" />
							<col width="*" />
						</colgroup>
						<tbody>
						   <tr>
								<th class="gray" rowspan="2">결재 시 비밀번호 설정</th>
								<td>
									<input type="radio" name="signpwd" id="signpwd1" value="Y"<? if ($signpwd == "Y") { echo " checked"; } ?>>
									<label for="signpwd1">비밀번호 설정 (결재 시 마다 비밀번호를 묻습니다. 해당 비밀번호는 인트라넷 로그인 시
사용되는 비밀번호와 동일합니다.)</label><br>
								</td>
						   </tr>
							<tr>
								<td>
									<input type="radio" name="signpwd" id="signpwd2" value="N"<? if ($signpwd == "N") { echo " checked"; } ?>> 
									<label for="signpwd2">비밀번호 설정안함</label>
								</td>
							</tr>
							<tr>
								<th class="gray" rowspan="2">서명등록</th>
								<td>
									<div class="attach_section clearfix">
										<div class="right clearfix">
											<div class="info_file clearfix">
												<span>
												<? if ($sign != "") { ?>
													<img src="<?=PRS_URL . $sign?>" width="41" height="41">
												<? } else { ?>
													<img src="/img/attach_df.gif" alt="" width="41" height="41" />
												<? } ?>
												</span>
												<p>* 이미지 사이즈는 41X 41(픽셀)</p>
											</div>
												<input type="text" name="attachment" id="attachment" class="attach df_textinput">
												<div class="input"><input type="file" name="sign" id="sign" class="browse"></div>
										</div>
									</div>
								</td>
							</tr>
						</tbody>
					</table>
					<div style="float:right; padding:10px;">
						<a href="javascript:funWrite();"><img src="/img/btn_insert_154.gif" alt=""></a>
					</div>

				</div>
			</div>
		</div>
</form>
<? include INC_PATH."/bottom.php"; ?>
</div>
</body>
</html>

