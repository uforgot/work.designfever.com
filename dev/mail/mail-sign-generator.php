<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
	if ($prs_id == "") {
?>
<meta http-equiv="Content-Type" content="text/html" charset="euc-kr">
<script type="text/javascript">
	alert("로그인 정보가 정확하지 않습니다.");
	location.href="/";
</script>
<?
		exit;
	}
?>

<?
	$col_prs_id = "";
	$col_prs_login = "";
	$col_prs_name = "";
	$col_prs_email = "";
	$col_prs_team = "";
	$col_prs_position = "";
	$col_prs_mobile = "";
	$col_prs_tel = "";
	$col_prs_extension  = "";
	$col_prs_e_tel = "";
	$col_prs_zipcode = "";
	$col_prs_addr1 = "";
	$col_prs_addr2 = "";
	$col_file_img = "";
	$col_prs_birth = "";
	$col_prs_join = "";
	$col_prs_beacon = "";

	$sql = "SELECT * FROM DF_PERSON WITH(NOLOCK) WHERE PRS_ID = '$prs_id'";
	$rs = sqlsrv_query($dbConn,$sql);

	$record = sqlsrv_fetch_array($rs);
	if (sizeof($record) > 0)
{
$col_prs_id = $record['PRS_ID'];
$col_prs_login = $record['PRS_LOGIN'];
$col_prs_name = $record['PRS_NAME'];
$col_prs_email = $record['PRS_EMAIL'];
$col_prs_team = $record['PRS_TEAM'];
$col_prs_position = $record['PRS_POSITION'];
$col_prs_mobile = $record['PRS_MOBILE'];
$col_prs_tel = $record['PRS_TEL'];
$col_prs_extension = $record['PRS_EXTENSION'];
$col_prs_e_tel = $record['PRS_E_TEL'];
$col_prs_zipcode = $record['PRS_ZIPCODE'];
$col_prs_addr1 = $record['PRS_ADDR1'];
$col_prs_addr2 = $record['PRS_ADDR2'];
$col_prs_zipcode_new = $record['PRS_ZIPCODE_NEW'];
$col_prs_address_new = $record['PRS_ADDRESS_NEW'];
$col_file_img = $record['FILE_IMG'];
$col_prs_birth = $record['PRS_BIRTH'];
$col_prs_birth_type = $record['PRS_BIRTH_TYPE'];
$col_prs_join = $record['PRS_JOIN'];
$col_prs_beacon = $record['PRS_BEACON'];
}

if ($col_prs_mobile == "")
{
$col_prs_mobile_ex[0] = "";
$col_prs_mobile_ex[1] = "";
$col_prs_mobile_ex[2] = "";
}
else
{
if (strpos($col_prs_mobile,"-") !== false)
{
$col_prs_mobile_ex = explode("-",$col_prs_mobile);
}
else
{
$col_prs_mobile_ex[0] = "";
$col_prs_mobile_ex[1] = "";
$col_prs_mobile_ex[2] = "";
}
}

if ($col_prs_tel == "")
{
$col_prs_tel_ex[0] = "";
$col_prs_tel_ex[1] = "";
$col_prs_tel_ex[2] = "";
}
else
{
if (strpos($col_prs_tel,"-") !== false)
{
$col_prs_tel_ex = explode("-",$col_prs_tel);
}
else
{
$col_prs_tel_ex[0] = "";
$col_prs_tel_ex[1] = "";
$col_prs_tel_ex[2] = "";
}
}

if ($col_prs_e_tel == "")
{
$col_prs_e_tel_ex[0] = "";
$col_prs_e_tel_ex[1] = "";
$col_prs_e_tel_ex[2] = "";
}
else
{
if (strpos($col_prs_e_tel,"-") !== false)
{
$col_prs_e_tel_ex = explode("-",$col_prs_e_tel);
}
else
{
$col_prs_e_tel_ex[0] = "";
$col_prs_e_tel_ex[1] = "";
$col_prs_e_tel_ex[2] = "";
}
}

if ($col_prs_zipcode == "")
{
$col_prs_zipcode_ex[0] = "";
$col_prs_zipcode_ex[1] = "";
}
else
{
if (strpos($col_prs_zipcode,"-") !== false)
{
$col_prs_zipcode_ex = explode("-",$col_prs_zipcode);
}
else
{
$col_prs_zipcode_ex[0] = "";
$col_prs_zipcode_ex[1] = "";
}
}

if ($col_prs_join == "")
{
$col_prs_join_ex[0] = "";
$col_prs_join_ex[1] = "";
$col_prs_join_ex[2] = "";
}
else
{
if (strpos($col_prs_join,"-") !== false)
{
$col_prs_join_ex = explode("-",$col_prs_join);
}
else
{
$col_prs_join_ex[0] = "";
$col_prs_join_ex[1] = "";
$col_prs_join_ex[2] = "";
}
}

if ($col_prs_birth == "")
{
$col_prs_birth_ex[0] = "";
$col_prs_birth_ex[1] = "";
$col_prs_birth_ex[2] = "";
}
else
{
if (strpos($col_prs_birth,"-") !== false)
{
$col_prs_birth_ex = explode("-",$col_prs_birth);
}
else
{
$col_prs_birth_ex[0] = "";
$col_prs_birth_ex[1] = "";
$col_prs_birth_ex[2] = "";
}
}
?>

<? include INC_PATH."/top.php"; ?>

<script type="text/javascript" src="/js/df_join.js"></script>
<script type="text/javascript" src="/js/df_auth.js"></script>


<script src="assets/js/jquery-1.11.3.min.js"></script>
<script src="assets/js/html2canvas.min.js"></script>
<script src="assets/js/mail_generator_php.js?v=0.08"></script>

</head>
<body>
<div class="wrapper_login">
	<p class="login_txt1"><img src="/img/txt_left.gif" alt=""></p>
</div>
<div class="intra_pop work_join_pop individual_pop mem_pop" style="display:block;">
	<div class="pop_top">
		<p class="pop_title">이메일 서명 만들기</p>
		<a href="../main.php" class="close">닫기</a>
	</div>
	<div class="pop_body">
		<div class="individual clearfix" style="margin:0;">

			<table class="df_join_table df_individual_table" style="width:100%;">
				<caption>이메일 서명 만들기 테이블</caption>
				<colgroup>
					<col width="105px" />
					<col width="180px" />
					<col width="100px" />
					<col width="*" />
				</colgroup>
				<tr>
					<th scope="row">이메일</th>
					<td colspan="3"><span class="txt_user_id"><?=$col_prs_email?></span>@designfever.com</td>
				</tr>
				<tr class="noneborder">
					<th scope="row"><label for="#df_join_npw">영문 성</label></th>
					<td>
						<input id="df_join_npw" class="df_textinput input_lastname" type="text" style="width:110px;" placeholder="Hong"/>
					</td>
					<th scope="row"><label for="#df_join_npwc">영문 이름</label></th>
					<td>
						<input id="df_join_npwc" class="df_textinput input_firstname" type="text" style="width:110px;" placeholder="Gildong"/>
					</td>
				</tr>
			</table>
		</div>


		<div class="edit_btn">
			<a href="#" class="btn-ok"><img src="../img/btn_ok.gif" alt="ok" /></a>
			<a href="../main.php"><img src="../img/btn_cancel.gif" alt="cancel" /></a>
		</div>

		<div style="display:table;width:100%;height:30px; margin:30px 0;">
			<p style="display:table-cell; line-height:1.5;">
				사용방법<br><br>
				1. 영문 성 및 영문 이름을 넣고 확인 버튼을 눌러서 서명 생성<br>2. 아래 상자 안의 내용을 드래그 및 복사<br>3. 사용하는 메일 프로그램(웹메일, 아웃룩 등)의 서명란에 붙여넣기 하고 저장<br><a href="http://work.designfever.com/board/board_detail.php?page=1&seqno=3319&board=default" target="_blank">참조 : http://work.designfever.com/board/board_detail.php?page=1&seqno=3319&board=default</a>
			</p>
		</div>
		<div class="border-box" style="border:1px solid #000; padding-top:50px;">
			<div id="clipboard-con" style="padding:30px 30px;">
				<div id="mail-con" style="font-family:'Malgun Gothic', 'AppleGothicNeoSD', 'Apple SD 산돌고딕 Neo', 'Microsoft NeoGothic', 'Droid sans', '맑은 고딕', 'Dotum', '돋움', '굴림', 'arial', 'sans-serif'; font-size:0; font-weight:bold; color:#000;">

					<p></p>
					<div class="capture-area" style="padding-bottom:30px;font-size:0;">
						<table style="font-size:0;">
							<caption></caption>
							<tbody>
							<tr style="font-size:0;">
								<td style="font-size:0;"><img src="assets/img/mail_logo.jpg" width="85" class="img-logo" alt="df"/></td>
								<td style="font-size:0;"></td>
								<td style="font-size:0;"></td>
							</tr>
							<tr style="height:45px;font-size:0;">
								<td style="font-size:0;"></td>
								<td style="font-size:0;"></td>
								<td style="font-size:0;"></td>
							</tr>
							<tr style="font-size:0; line-height:1;">
								<td style="padding:0 10px 0 0;font-size:16px;line-height:1;color:#000;"class="df-name">홍길동 사원</td>
								<td style="width:119px;padding:0;line-height:1;color:#000;"><img src="assets/img/mail_hyphen.jpg?v20170210" alt="-" class="img-hyphen"/></td>
								<td style="padding:0 0 0 10px;font-size:16px;line-height:1;color:#000;" class="df-name-eng">Hong Gildong</td>
							</tr>

							<tr style="font-size:0;">
								<td colspan="2" class="df-division" valign="top" style="padding:0; height:18px;font-size:12px;color:#000;">DIRECTOR / IX division</td>
								<td width="300" style="height:18px;padding:0 0 0 10px;font-size:12px; line-height:1.8;color:#000;" ><a href="mailto:honggildong@designfever.com" class="df-email" style="text-decoration:none; color:#000;">honggildong@designfever.com</a><br><a href="tel:010-1234-5678" class="df-mobile-number" style="text-decoration:none;color:#000;">010 1234 5678</a><br><a href="tel:02-325-2767" style="text-decoration:none;color:#000;">02 325 2767</a> / <span class="df-ext-number">000</span></td>
							</tr>

							</tbody>
						</table>
						<table style="font-size:0;">
							<caption></caption>
							<tbody>
							<tr style="height:35px;">
								<td></td>
							</tr>
							<tr>
								<td style="font-size:11px;height:15px;padding:0;line-height:1.8;color:#000;">(주)디자인피버 04047 서울시 마포구 양화로10길 40, DF빌딩<br>DESIGNFEVER INC. 04047, 40, YANGHWA-RO 10GIL, MAPO-GU, SEOUL, KOREA<br><a href="http://www.designfever.com" target="_blank" style="text-decoration: none;color:#000;">WWW.DESIGNFEVER.COM</a></td>
							</tr>
							<tr>
								<td style="font-size:10px; text-align:justify; padding-top:28px; color:#999999;">상기 메일은 지정된 수신인만을 위한것이므로, 공개, 배포, 복사 또는 사용하는 것은 엄격히 금지됩니다.<br>
									본 메일이 잘못 전송된 경우, 발신인에게 알려 주시고 즉시 삭제하여 주시기 바랍니다.</td>
							</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</body>
</html>
