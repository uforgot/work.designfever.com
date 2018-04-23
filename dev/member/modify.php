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
</head>
<body>
<form name="form" method="post" action="modify_act.php" enctype="multipart/form-data">
<input type="hidden" name="prs_id" value="<?=$col_prs_id?>">
<input type="hidden" name="add_img"/> <!-- 이미지 -->
<input type="hidden" name="file_img" value="<?=$col_file_img?>">
<div class="intra_pop work_join_pop individual_pop mem_pop" style="display:">
	<div class="pop_top">
		<p class="pop_title">개인정보</p>
		<a href="javascript: history.go(-1);" class="close">닫기</a>
	</div>
	<div class="pop_body">
		<div class="individual clearfix">
			<div class="in_photo">
				<span class="photowrap"><?=getProfileImg($prs_img,138);?></span>
				<div class="input_file">
					<span class="file"><img src="../img/btn_file_long.gif" alt="파일찾기" /></span>
					<input type="file" name="file_img2">				
				</div>
				<p class="inner_info">* 용량 200 kb 이내 업로드</p>
				<input type="checkbox" name ="img_delete" value="1"> 이미지 삭제 하기
			</div>
			<table class="df_join_table df_individual_table">
				<caption>회원정보 확인 및 수정 테이블1</caption>
				<colgroup>
					<col width="105px" />
					<col width="180px" />
					<col width="100px" />
					<col width="*" />
				</colgroup>
				<tr>
					<th scope="row">이름</th>
					<td colspan="3"><strong><?=$col_prs_name?></strong> ( 아이디 : <span><?=$col_prs_login?></span> )</td>
				</tr>
				<tr class="noneborder">
					<th scope="row"><label for="#df_join_npw">새 비밀번호</label></th>
					<td>
						<input id="df_join_npw" class="df_textinput" type="password" style="width:110px;" id="dfwp" name="PassWd" value="" maxlength="16"/>
						<p class="inner_info">* 변경을 원할 시에만 입력</p>
					</td>
					<th scope="row"><label for="#df_join_npwc">비밀번호 확인</label></th>
					<td>
						<input id="df_join_npwc" class="df_textinput" type="password" style="width:110px;" id="dfwpp" name="PassWdCon"  value="" maxlength="16"/>
					</td>
				</tr>
			</table>
		</div>
		
		<div class="df_join">
			<table class="df_join_table">
				<caption>회원정보 확인 및 수정 테이블2</caption>
				<colgroup>
					<col width="105px" />
					<col width="242px" />
					<col width="95px" />
					<col width="*" />
				</colgroup>
				<tr>
					<th scope="row"><strong class="color_o"></strong><label for="#df_join_name">DF E-mail</label></th>
					<td colspan="3">
						<input id="df_join_email" class="df_textinput" type="text" style="width:100px;" name="email" maxlength="20" value="<?=$col_prs_email?>" /> @designfever.com
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="#df_join_joinl">입사일</label></th>
					<td colspan="3"><?=$col_prs_join_ex[0]?> 년 <?=$col_prs_join_ex[1]?> 월 <?=$col_prs_join_ex[2]?> 일</td>
				</tr>
				<input type="hidden" name="join1" value="<?=$col_prs_join_ex[0]?>">
				<input type="hidden" name="join2" value="<?=$col_prs_join_ex[1]?>">
				<input type="hidden" name="join3" value="<?=$col_prs_join_ex[2]?>">
				<tr>
					<th scope="row"><label for="#df_birth_birthl">생일</label></th>
					<td colspan="3">
						<select name="birth1" style="width:100px; height:31px; margin-right:5px;">
							<option value="">--</option>
						<? for ($i=date("Y")-20; $i>=1970; $i--) { ?>
							<option value="<?=$i?>"<? if ($col_prs_birth_ex[0] == $i) { echo " selected"; } ?>><?=$i?></option>
						<? } ?>
						</select>년 
						<select name="birth2" style="width:100px; height:31px; margin-right:5px;">
							<option value="">--</option>
						<? for ($i=1; $i<=12; $i++) { ?>
							<option value="<?=$i?>"<? if ($col_prs_birth_ex[1] == $i) { echo " selected"; } ?>><?=$i?></option>
						<? } ?>
						</select>월 
						<select name="birth3" style="width:100px; height:31px; margin-right:5px;">
							<option value="">--</option>
						<? for ($i=1; $i<=31; $i++) { ?>
							<option value="<?=$i?>"<? if ($col_prs_birth_ex[2] == $i) { echo " selected"; } ?>><?=$i?></option>
						<? } ?>
						</select>일 
						<input type="radio" name="birth_type" value="양력"<? if ($col_prs_birth_type == "양력") { echo " checked"; } ?>>양력
						<input type="radio" name="birth_type" value="음력"<? if ($col_prs_birth_type == "음력") { echo " checked"; } ?>>음력
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="#df_join_cell">핸드폰</label></th>
					<td colspan="3">
						<select name="mobile1" style="width:100px; height:31px; margin-right:5px;" id="df_join_cell">
                             <option value = "">선 택 </option>
							 <option value = "010"<? if ($col_prs_mobile_ex[0] == "010") { echo " selected"; } ?>>010</option>
							 <option value = "011"<? if ($col_prs_mobile_ex[0] == "011") { echo " selected"; } ?>>011</option>
							 <option value = "016"<? if ($col_prs_mobile_ex[0] == "016") { echo " selected"; } ?>>016</option>
							 <option value = "017"<? if ($col_prs_mobile_ex[0] == "017") { echo " selected"; } ?>>017</option>
							 <option value = "018"<? if ($col_prs_mobile_ex[0] == "018") { echo " selected"; } ?>>018</option>
							 <option value = "019"<? if ($col_prs_mobile_ex[0] == "019") { echo " selected"; } ?>>019</option>
						</select>- 
						<input class="df_textinput" type="text" style="width:75px;" onKeyPress="javascript:com_onlyNumber();"name="mobile2" value="<?=$col_prs_mobile_ex[1]?>"maxlength="4"/> - 
						<input class="df_textinput" type="text" style="width:75px;" onKeyPress="javascript:com_onlyNumber();"name="mobile3" value="<?=$col_prs_mobile_ex[2]?>"maxlength="4"/>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="#df_join_e">비상연락망</label></th>
					<td colspan="3">
						<input class="df_textinput" style="width:75px;" name="e_tel1" value="<?=$col_prs_e_tel_ex[0]?>" onKeyPress="javascript:com_onlyNumber();" type="text" maxlength="4" id="df_join_e"/> - 
						<input class="df_textinput" style="width:75px;" name="e_tel2" value="<?=$col_prs_e_tel_ex[1]?>" onKeyPress="javascript:com_onlyNumber();" type="text" maxlength="4"/> - 
						<input class="df_textinput" style="width:75px;" name="e_tel3" value="<?=$col_prs_e_tel_ex[2]?>" onKeyPress="javascript:com_onlyNumber();" type="text" maxlength="4"/></td>
				</tr>
				<tr>
					<th scope="row"><label for="#df_join_zipcode">우편번호</label></th>
					<td colspan="3">
						<!--input id="df_join_add" class="df_textinput" type="text" style="width:75px;" name="zipcode1" readonly value="<?=$col_prs_zipcode_ex[0]?>" maxlength="3"/ /> - 
						<input class="df_textinput" type="text" style="width:75px;" name="zipcode2" readonly value="<?=$col_prs_zipcode_ex[1]?>" maxlength="3"/>
						<a href="javascript:fcOpenNewWindow('zipcode')" onFocus="this.blur()" class="ml_6"><img src="../img/btn_post.gif" alt="우편번호 찾기" /></a-->
						<input id="df_join_zipcode" class="df_textinput" type="text" style="width:75px;" name="zipcode_new" readonly value="<?=$col_prs_zipcode_new?>" maxlength="5"/ />
						<a href="javascript:goPopup();" onFocus="this.blur()" class="ml_6"><img src="../img/btn_post.gif" alt="우편번호 찾기" /></a>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="#df_join_address">자택주소</label></th>
					<td colspan="3">
						<!--input id="df_join_add1" class="df_textinput" name="addr1" type="text" style="width:400px; margin-bottom:5px;" value="<?=$col_prs_addr1?>" readonly />
						<input id="df_join_add2" class="df_textinput" name="addr2" type="text" style="width:400px;" name="adr2" value="<?=$col_prs_addr2?>" maxlength="60" /-->
						<input type="hidden" id="df_join_add1" class="df_textinput" name="addr1" value="<?=$col_prs_addr1?>" />
						<input type="hidden" id="df_join_add2" class="df_textinput" name="addr2" value="<?=$col_prs_addr2?>" />
						<input id="df_join_address" class="df_textinput" name="address_new" type="text" style="width:580px; margin-bottom:5px;" value="<?=$col_prs_address_new?>" readonly />
						<input type="hidden" id ="roadFullAddr">
						<input type="hidden" id ="roadAddrPart1">
						<input type="hidden" id ="addrDetail">
						<input type="hidden" id ="roadAddrPart2">
						<input type="hidden" id ="engAddr">
						<input type="hidden" id ="jibunAddr">
						<input type="hidden" id ="zipNo">
						<input type="hidden" id ="admCd">
						<input type="hidden" id ="rnMgtSn">
						<input type="hidden" id ="bdMgtSn">
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="#df_join_depart">부서</label></th>
					<td><?=$col_prs_team?><input type="hidden" name="team" value="<?=$col_prs_team?>">
						<!--select name="team" style="width:200px;height:31px; ">			
                            <option value="">선택</option>    
					<?
						$selSQL = "SELECT STEP, TEAM FROM DF_TEAM_CODE WITH(NOLOCK) ORDER BY SORT";
						$selRs = sqlsrv_query($dbConn,$selSQL);

						while ($selRecord = sqlsrv_fetch_array($selRs))
						{
							$selStep = $selRecord['STEP'];
							$selTeam = $selRecord['TEAM'];

							$blank = "";
							for ($i=1;$i<=$selStep;$i++)
							{
								$blank .= "&nbsp;&nbsp;&nbsp;";
							}
					?>
							<option value="<?=$selTeam?>"<? if ($col_prs_team == $selTeam){ echo " selected"; } ?>><?=$blank?><?=$selTeam?></option>
					<?
						}
					?>
						</select-->
					</td>
					<th scope="row"><label for="#df_join_position"><span style="margin-left:45px;">직급</span></label></th>
					<td><?=$col_prs_position?><input type="hidden" name="position" value="<?=$col_prs_position?>">
						<!--select name="position" style="width:100px; height:31px; ">
                            <option value="">선택</option>    
					<?
						$selSQL = "SELECT SEQNO, POSITION FROM DF_POSITION_CODE WITH(NOLOCK) ORDER BY SEQNO";
						$selRs = sqlsrv_query($dbConn,$selSQL);

						while ($selRecord = sqlsrv_fetch_array($selRs))
						{
							$selNo = $selRecord['SEQNO'];
							$selPosition= $selRecord['POSITION'];
					?>
							<option value="<?=$selPosition?>"<? if ($col_prs_position == $selPosition){ echo " selected"; } ?>><?=$selPosition?></option>
					<?
						}
					?>
						</select-->
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="#df_join_extension">내선번호</label></th>
					<td>
						<input class="df_textinput" type="text" style="width:75px;" onKeyPress="javascript:com_onlyNumber();" name="extension" value="<?=$col_prs_extension?>" maxlength="3"/>
					</td>
					<th scope="row"><label for="#df_join_tel">직통번호</label></th>
					<td>
						070-
						<input class="df_textinput" type="text" onKeyPress="javascript:com_onlyNumber();" style="width:75px; ime-mode:disabled;" name="tel1" value="<?=$col_prs_tel_ex[1]?>" maxlength="4"/> - 
						<input class="df_textinput" type="text" onKeyPress="javascript:com_onlyNumber();" style="width:75px; ime-mode:disabled;" name="tel2" value="<?=$col_prs_tel_ex[2]?>"  maxlength="4" />
						<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;* 전화기 화면에 표시된 번호를 적어주세요.
					</td>
				</tr>
			</table>
		</div>
		<div class="edit_btn">
			<a href="javascript:Modify_MemberInfo();"><img src="../img/btn_ok.gif" alt="ok" /></a>
		<?	if ($col_prs_position == "파견사원") {	?>
			<a href="../main2.php"><img src="../img/btn_cancel.gif" alt="cancel" /></a>
		<?	} else {	?>
			<a href="../main.php"><img src="../img/btn_cancel.gif" alt="cancel" /></a>
		<?	}	?>
		</div>
	</div>

</form>
</div>
<? include INC_PATH."/bottom.php"; ?>
</body>
</html>
