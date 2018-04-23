<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
?>

<?
	if (!in_array(REMOTE_IP, $ok_ip_arr))
	{
?>
	<meta http-equiv="Content-Type" content="text/html" charset="euc-kr">
	<script type="text/javascript">
		location.href="/";
	</script>
<?
		exit;
	}
?>

<? include INC_PATH."/top.php"; ?>

<script type="text/javascript" src="/js/df_join.js"></script>
<script type="text/javascript" src="/js/df_auth.js"></script>
</head>
<body onload="jusoCallBack('roadFullAddr','roadAddrPart1','addrDetail','roadAddrPart2','engAddr','jibunAddr','zipNo','admCd','rnMgtSn','bdMgtSn');">
<form name="form" method="post" action="join_act.php" enctype="multipart/form-data">
<input type="hidden" name="IdCheck">            <!-- 로그인 아이디 중복체크 완료 히든값 -->
<div class="intra_pop work_join_pop mem_pop">
	<div class="pop_top">
		<p class="pop_title">회원가입</p>
		<a href="login.php" class="close">닫기</a>
	</div>
	<div class="pop_body">
		<div class="df_join">
			<p class="color_o"><strong>"!" 표시는 필수 기입 항목입니다</strong></p>
			<table class="df_join_table">
				<caption>회원가입을 위한 테이블</caption>
				<colgroup>
					<col width="105px" />
					<col width="242px" />
					<col width="95px" />
					<col width="*" />
				</colgroup>
				<tr>
					<th scope="row"><strong class="color_o">! </strong><label for="#df_join_id">아이디</label></th>
					<td colspan="3">
						<input id="df_join_id" class="df_textinput" type="text" style="width:200px;" name="login" onBlur="fcHancheck();" onKeyPress="intNumber_Check();checkCapsLock(event);"/>
						<a href="javascript:fcOpenNewWindow('check_id');" onFocus="this.blur()" class="ml_6"><img src="../img/btn_recheck.gif" alt="중복확인" /></a>
						<span class="color_o inner_table_info">* 영문과 숫자만 입력가능</span>
					</td>
				</tr>
				<tr>
					<th scope="row"><strong class="color_o">! </strong><label for="#df_join_pw">비밀번호</label></th>
					<td>
						<input id="df_join_pw" class="df_textinput" type="password" style="width:200px;" name="PassWd" maxlength="16"/>
					</td>
					<th scope="row"><label for="#df_join_pwc">비밀번호 확인</label></th>
					<td>
						<input id="df_join_pwc" class="df_textinput" type="password" style="width:200px;" name="PassWdCon" maxlength="16" />
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="#df_join_photo">사진등록</label></th>
					<td colspan="3">
					<!-- 
					<input id="df_join_photo" class="df_textinput" type="text" style="width:200px;" />
					 -->
					<div class="input_file">
						<span class="file"><img src="../img/btn_file_long.gif" alt="파일찾기" /></span>
					<input type="file" name="file_img"/>
				</div><span class="color_o inner_table_info">* 이미지파일 용량 200 kb 이내 업로드 가능</span></td>
				</tr>
				<tr>
					<th scope="row"><strong class="color_o">! </strong><label for="#df_join_name">이름</label></th>
					<td colspan="3">
						<input id="df_join_name" class="df_textinput" type="text" style="width:200px;" name="name" maxlength="16"/>
					</td>
				</tr>
				<tr>
					<th scope="row"><!--strong class="color_o">! </strong--><label for="#df_join_name">DF E-mail</label></th>
					<td colspan="3">
						<input id="df_join_email" class="df_textinput" type="text" style="width:100px;" name="email" maxlength="20"/> @designfever.com
					</td>
				</tr>
				<tr>
					<th scope="row"><strong class="color_o">! </strong><label for="#df_join_joinl">입사일</label></th>
					<td colspan="3">
						<select name="join1" style="width:100px; height:31px; margin-right:5px;">
						<? for ($i=2000; $i<=date("Y"); $i++) { ?>
							<option value="<?=$i?>"<?if ($i==date("Y")){ echo " selected"; } ?>><?=$i?></option>
						<? } ?>
						</select>년 
						<select name="join2" style="width:100px; height:31px; margin-right:5px;">
						<? for ($i=1; $i<=12; $i++) { ?>
							<option value="<?=$i?>"<?if ($i==date("m")){ echo " selected"; } ?>><?=$i?></option>
						<? } ?>
						</select>월 
						<select name="join3" style="width:100px; height:31px; margin-right:5px;">
						<? for ($i=1; $i<=31; $i++) { ?>
							<option value="<?=$i?>"<?if ($i==date("d")){ echo " selected"; } ?>><?=$i?></option>
						<? } ?>
						</select>일 
					</td>
				</tr>
				<tr>
					<th scope="row"><strong class="color_o">! </strong><label for="#df_birth_birthl">생일</label></th>
					<td colspan="3">
						<select name="birth1" style="width:100px; height:31px; margin-right:5px;">
							<option value="">--</option>
						<? for ($i=1970; $i<=date("Y")-20; $i++) { ?>
							<option value="<?=$i?>"><?=$i?></option>
						<? } ?>
						</select>년 
						<select name="birth2" style="width:100px; height:31px; margin-right:5px;">
							<option value="">--</option>
						<? for ($i=1; $i<=12; $i++) { ?>
							<option value="<?=$i?>"><?=$i?></option>
						<? } ?>
						</select>월 
						<select name="birth3" style="width:100px; height:31px; margin-right:5px;">
							<option value="">--</option>
						<? for ($i=1; $i<=31; $i++) { ?>
							<option value="<?=$i?>"><?=$i?></option>
						<? } ?>
						</select>일 
						<input type="radio" name="birth_type" value="양력" checked>양력
						<input type="radio" name="birth_type" value="음력">음력
					</td>
				</tr>
				<tr>
					<th scope="row"><strong class="color_o">! </strong><label for="#df_join_cell">핸드폰</label></th>
					<td colspan="3">
						
						<select name="mobile1" style="width:100px; height:31px; margin-right:5px;" id="df_join_cell">
                              	<option value="" >선택</option>
							  	<option value="010">010 </option>
							  	<option value="011">011 </option>
								<option value="016">016 </option>
								<option value="017">017 </option>
								<option value="018">018 </option>
								<option value="019">019 </option>
						</select>-  
						<input class="df_textinput" type="text" onKeyPress="javascript:com_onlyNumber();" style="width:75px; ime-mode:disabled;" name="mobile2" maxlength="4"/> - 
						<input class="df_textinput" type="text" onKeyPress="javascript:com_onlyNumber();" style="width:75px; ime-mode:disabled;" name="mobile3" maxlength="4" />
					</td>
				</tr>
				<tr>
					<th scope="row"><strong class="color_o">! </strong><label for="#df_join_e">비상연락망</label></th>
					<td colspan="3">
						<input class="df_textinput"	 type="text" style="width:75px; ime-mode:disabled" maxlength="4" name="e_tel1" type="text"  onKeyPress="javascript:com_onlyNumber();" id="df_join_e"/> - 
						<input class="df_textinput" type="text" style="width:75px; ime-mode:disabled" maxlength="4" name="e_tel2" type="text"  onKeyPress="javascript:com_onlyNumber();"/> - 
						<input class="df_textinput" type="text" style="width:75px; ime-mode:disabled" maxlength="4" name="e_tel3" type="text"  onKeyPress="javascript:com_onlyNumber();"/>
					</td>
				</tr>
				<tr>
					<th scope="row"><strong class="color_o">! </strong><label for="#df_join_zipcode">우편번호</label></th>
					<td colspan="3">
						<!--input id="df_join_add" class="df_textinput" type="text" style="width:75px;" name="zipcode1" readonly maxlength="3"/> - 
						<input class="df_textinput" type="text" style="width:75px;" name="zipcode2" readonly maxlength="3"/>
						<a href="javascript:fcOpenNewWindow('zipcode')" onFocus="this.blur()" class="ml_6"><img src="../img/btn_post.gif" alt="우편번호 찾기" /></a-->
						<input id="df_join_zipcode" class="df_textinput" type="text" style="width:75px;" name="zipcode_new" readonly value="<?=$col_prs_zipcode_new?>" maxlength="5"/ />
						<a href="javascript:goPopup();" onFocus="this.blur()" class="ml_6"><img src="../img/btn_post.gif" alt="우편번호 찾기" /></a>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="#df_join_address"><strong class="color_o">! </strong>자택주소</span></th>
					<td colspan="3">
						<!--input id="df_join_add2" class="df_textinput" type="text" style="width:400px; margin-bottom:5px;" name="addr1" readonly/>
						<input id="df_join_add2" class="df_textinput" type="text" style="width:400px; margin-bottom:5px;" name="addr2"/-->
						<input type="hidden" id="df_join_add1" class="df_textinput" name="addr1" />
						<input type="hidden" id="df_join_add2" class="df_textinput" name="addr2" value="<?=$col_prs_addr2?>" />
						<input id="df_join_address" class="df_textinput" name="address_new" type="text" style="width:580px; margin-bottom:5px;" value="" readonly />
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
					<th scope="row"><strong class="color_o">! </strong><label for="#df_join_depart">부서</label></th>
					<td>
						<select name="team" id="df_join_depart" style="width:200px;height:31px;">
                            <option value="">선택</option>    
					<?
						$selSQL = "SELECT STEP, TEAM FROM DF_TEAM_2018 WITH(NOLOCK) WHERE VIEW_YN = 'Y' ORDER BY SORT";
						$selRs = sqlsrv_query($dbConn,$selSQL);

						while ($selRecord = sqlsrv_fetch_array($selRs))
						{
							$selStep = $selRecord['STEP'];
							$selTeam = $selRecord['TEAM'];
							
							if ($selStep == 2) {
								$selTeam2 = $selTeam;
							}
							else if ($selStep == 3) {
								$selTeam2 = "&nbsp;&nbsp;└ ". $selTeam;
							}
					?>
							<option value="<?=$selTeam?>"<? if ($p_team == $selTeam){ echo " selected"; } ?>><?=$selTeam2?></option>
					<?
						}
					?>
						</select>
					</td>
					<th scope="row"><strong class="color_o" style="margin-left:45px;">! </strong><label for="#df_join_position">직급</label></th>
					<td>
						<select name="position" id="df_join_position" style="width:100px;height:31px;">
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
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="#df_join_extension">내선번호</label></th>
					<td>
						<input class="df_textinput" type="text" style="width:75px;" onKeyPress="javascript:com_onlyNumber();" name="extension" value="" maxlength="3"/>
					</td>
					<th scope="row"><label for="#df_join_tel">직통번호</label></th>
					<td>
						070-
						<input class="df_textinput" type="text" onKeyPress="javascript:com_onlyNumber();" style="width:75px; ime-mode:disabled;" name="tel1" maxlength="4"/> - 
						<input class="df_textinput" type="text" onKeyPress="javascript:com_onlyNumber();" style="width:75px; ime-mode:disabled;" name="tel2" maxlength="4" />
						<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;* 전화기 화면에 표시된 번호를 적어주세요.
					</td>
				</tr>
			</table>
		</div>
		<div class="edit_btn">
			<a href="javascript:Inert_MemberInfo()"><img src="../img/btn_join.gif" alt="회원가입" /></a>
		</div>
	</div>
</form>
</div>
<? include INC_PATH."/bottom.php"; ?>
</body>
</html>
