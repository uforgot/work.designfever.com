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
$col_prs_en_lastname = "";
$col_prs_en_firstname = "";
$col_prs_email = "";
$col_prs_team = "";
$col_prs_position = "";
$col_prs_position2 = "";
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
    $col_prs_en_lastname = $record['PRS_EN_LASTNAME'];
    $col_prs_en_firstname = $record['PRS_EN_FIRSTNAME'];
    $col_prs_email = $record['PRS_EMAIL'];
    $col_prs_team = $record['PRS_TEAM'];
    $col_prs_position = $record['PRS_POSITION'];
    $col_prs_position2 = $record['PRS_POSITION2'];
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

<script type="text/javascript" src="/assets/js/df_join.js"></script>
<script type="text/javascript" src="/assets/js/df_auth.js"></script>
</head>
<body>
<form name="form" method="post" action="modify_act.php" enctype="multipart/form-data">
<? include INC_PATH."/top_menu.php"; ?>
<input type="hidden" name="prs_id" value="<?=$col_prs_id?>">
<input type="hidden" name="add_img"/> <!-- 이미지 -->
<input type="hidden" name="file_img" value="<?=$col_file_img?>">
    <!-- 본문 시작 -->
    <section class="section df-member">
        <div class="container">
            <div class="content">

                <div class="columns">
                    <div class="column">

                        <article class="media">
                            <figure class="media-left" style="margin-left:0">
                                <p class="image is-128x128">
                                    <?=getProfileImg($prs_img,138);?>
                                </p>
                            </figure>
                            <div class="media-content">
                                <p class="title is-size-5"><?=$col_prs_name?> <?=$col_prs_login?></p>
                                <p class="title is-size-6"><?=$col_prs_team?></p>
                                <p class="title is-size-6"><?=$col_prs_position2?> / <?=$col_prs_position?></p>
                                <p class="title is-size-7">입사일 <?=$col_prs_join_ex[0]?>년 <?=$col_prs_join_ex[1]?>월 <?=$col_prs_join_ex[2]?>일</p>
                                <input type="hidden" name="join1" value="<?=$col_prs_join_ex[0]?>">
                                <input type="hidden" name="join2" value="<?=$col_prs_join_ex[1]?>">
                                <input type="hidden" name="join3" value="<?=$col_prs_join_ex[2]?>">
                            </div>
                        </article>

                        <div class="columns">
                            <div class="column  is-narrow">
                                <div class="field">
                                    <label class="label">새 비밀번호</label>
                                    <div class="field is-grouped">
                                        <div class="control">
                                            <input id="df_join_npw" class="input is-primary" type="password" name="PassWd" value="" maxlength="16">
                                        </div>
                                    </div>
                                </div>
                                <p class="help">* 변경을 원할 시에만 입력</p>
                            </div>
                            <div class="column">
                                <div class="field">
                                    <label class="label">새 비밀번호 확인</label>
                                    <div class="field is-grouped">
                                        <div class="control">
                                            <input id="df_join_npwc" class="input is-primary"  type="password" id="dfwpp" name="PassWdCon"  value="" maxlength="16"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="field">
                            <label class="label">사진등록</label>
                            <div class="field is-left">
                                <div class="file has-name is-right" style="justify-content: left">
                                    <label class="file-label">
                                        <input class="file-input" type="file" name="file_img2">
                                    <span class="file-cta">
                    <span class="file-icon">
                        <i class="fas fa-upload"></i>
                    </span>
                    <span class="file-label">
                        파일찾기…
                    </span>
                    </span>
                         <input type="text" class="file-name" name="file_name" id="file_name" value="<?=$col_file_img?>" readonly >
                                    </label>
                                </div>
                            </div>
                            <p class="help"><label class="checkbox"><input type="checkbox" name ="img_delete" value="1"> 이미지 삭제 하기</label></p>
                            <p class="help">* 이미지파일 용량 200 kb 이내 업로드 가능</p>
                        </div>

                        <div class="field ">
                            <label class="label">영문 이름</label>
                            <div class="field is-grouped">
                                <div class="control">
                                    <input id="ename1" class="input is-primary"type="text" name="ename1" maxlength="30" placeholder="성" value="<?=$col_prs_en_lastname?>" size="10"/>
                                </div>
                                <div class="control">
                                    <input id="ename2" class="input is-primary" type="text" name="ename2" maxlength="30" placeholder="이름" value="<?=$col_prs_en_firstname?>"/>

                                </div>
                            </div>
                        </div>

                        <div class="field ">
                            <label class="label">DF E-mail</label>
                            <div class="field is-grouped">
                                <div class="control">
                                    <input id="df_join_email" class="input is-primary" type="text" name="email" maxlength="20" value="<?=$col_prs_email?>"/>
                                </div>
                                <input class="input is-static" type="email" value="@designfever.com" readonly>
                            </div>
                        </div>

                    </div>
                    <div class="column">

                        <div class="columns">
                            <div class="column">
                                <div class="field ">
                                    <label class="label">생일</label>
                                    <div class="field is-grouped">
                                        <div class="control select is-primary">
                                            <select name="birth1">
                                                <option value="">--</option>
                                                <? for ($i=date("Y")-20; $i>=1970; $i--) { ?>
                                                    <option value="<?=$i?>"<? if ($col_prs_birth_ex[0] == $i) { echo " selected"; } ?>><?=$i?>년</option>
                                                <? } ?>
                                            </select>
                                        </div>
                                        <div class="control select is-primary">
                                            <select name="birth2">
                                                <option value="">--</option>
                                                <? for ($i=1; $i<=12; $i++) { ?>
                                                    <option value="<?=$i?>"<? if ($col_prs_birth_ex[1] == $i) { echo " selected"; } ?>><?=$i?>월</option>
                                                <? } ?>
                                            </select>
                                        </div>
                                        <div class="control select is-primary">
                                            <select name="birth3">
                                                <option value="">--</option>
                                                <? for ($i=1; $i<=31; $i++) { ?>
                                                    <option value="<?=$i?>"<? if ($col_prs_birth_ex[2] == $i) { echo " selected"; } ?>><?=$i?>일</option>
                                                <? } ?>
                                            </select>
                                        </div>
                                        <!--
                                        <div class="button">
                                            <input type="hidden" class="datepicker">
                                            <i class="fa fa-calendar-plus"></i>
                                        </div>
                                        -->
                                    </div>
                                </div>
                            </div>

                            <div class="column">
                                <div class="field ">
                                    <label class="label is-hidden-mobile">&nbsp;</label>
                                    <div class="field is-grouped">
                                        <div class="control" style="margin-top:0.4rem; margin-left:-0.4rem;">
                                            <input class="is-checkradio" id="exampleRadioInline1" type="radio" name="birth_type" value="양력" <? if ($col_prs_birth_type == "양력") { echo " checked"; } ?>>
                                            <label for="exampleRadioInline1">양력</label>
                                            <input class="is-checkradio" id="exampleRadioInline2" type="radio" name="birth_type" value="음력" <? if ($col_prs_birth_type == "음력") { echo " checked"; } ?>>
                                            <label for="exampleRadioInline2">음력</label>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>


                        <div class="field">
                            <label class="label">핸드폰</label>
                            <div class="field is-grouped">
                                <div class="control select is-primary">
                                    <select name="mobile1" id="df_join_cell">
                                        <option value = "">선 택 </option>
                                        <option value = "010"<? if ($col_prs_mobile_ex[0] == "010") { echo " selected"; } ?>>010</option>
                                        <option value = "011"<? if ($col_prs_mobile_ex[0] == "011") { echo " selected"; } ?>>011</option>
                                        <option value = "016"<? if ($col_prs_mobile_ex[0] == "016") { echo " selected"; } ?>>016</option>
                                        <option value = "017"<? if ($col_prs_mobile_ex[0] == "017") { echo " selected"; } ?>>017</option>
                                        <option value = "018"<? if ($col_prs_mobile_ex[0] == "018") { echo " selected"; } ?>>018</option>
                                        <option value = "019"<? if ($col_prs_mobile_ex[0] == "019") { echo " selected"; } ?>>019</option>
                                    </select>
                                </div>
                                <div class="control">
                                    <input class="input is-primary" type="text" style="width:75px;" onKeyPress="javascript:com_onlyNumber();"name="mobile2" value="<?=$col_prs_mobile_ex[1]?>"maxlength="4" size="6"/>
                                </div>
                                <div class="control">
                                    <input class="input is-primary" type="text" style="width:75px;" onKeyPress="javascript:com_onlyNumber();"name="mobile3" value="<?=$col_prs_mobile_ex[2]?>"maxlength="4" size="6"/>
                                </div>
                            </div>
                        </div>

                        <div class="field">
                            <label class="label">비상연락망</label>
                            <div class="field is-grouped">
                                <div class="control">
                                    <input class="input is-primary" name="e_tel1" value="<?=$col_prs_e_tel_ex[0]?>" onKeyPress="javascript:com_onlyNumber();" type="text" size="6" maxlength="4" id="df_join_e"/>
                                </div>
                                <div class="control">
                                    <input class="input is-primary" name="e_tel2" value="<?=$col_prs_e_tel_ex[1]?>" onKeyPress="javascript:com_onlyNumber();" type="text" size="6" maxlength="4"/>
                                </div>
                                <div class="control">
                                    <input class="input is-primary" name="e_tel3" value="<?=$col_prs_e_tel_ex[2]?>" onKeyPress="javascript:com_onlyNumber();" type="text" size="6"maxlength="4"/>
                                </div>
                            </div>
                        </div>

                        <div class="field ">
                            <label class="label">자택주소</label>
                            <div class="field is-grouped">
                                <div class="control">
                                    <input id="df_join_zipcode" class="input is-primary" type="text"name="zipcode_new" readonly value="<?=$col_prs_zipcode_new?>" maxlength="5"/ />
                                </div>
                                <div class="control">
                                    <a href="javascript:goPopup();" onFocus="this.blur()" class="button">우편번호 찾기</a>
                                </div>
                            </div>
                            <div class="field">
                                <div class="control">
                                    <input type="hidden" id="df_join_add1" class="df_textinput" name="addr1" value="<?=$col_prs_addr1?>" />
                                    <input type="hidden" id="df_join_add2" class="df_textinput" name="addr2" value="<?=$col_prs_addr2?>" />
                                    <input id="df_join_address" class="input is-primary" name="address_new" type="text" value="<?=$col_prs_address_new?>" readonly />
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
                                </div>
                            </div>
                        </div>

                        <div class="field">
                            <label class="label">부서 / 직급</label>
                            <div class="field is-grouped">
                                <div class="control">
                                    <input class="input has-text-primary is-static" type="text" placeholder="" value="<?=$col_prs_team?>" readonly ><input type="hidden" name="team" value="<?=$col_prs_team?>">
                                </div>
                                <div class="control">
                                    <input class="input has-text-primary is-static" type="text" placeholder="" value="<?=$col_prs_position?>" readonly size="6"><input type="hidden" name="position" value="<?=$col_prs_position?>">
                                </div>
                            </div>
                        </div>


                        <div class="field ">
                            <label class="label">내선번호</label>
                            <div class="field is-grouped">
                                <div class="control">
                                    <input class="input" type="text" placeholder="" onKeyPress="javascript:com_onlyNumber();" name="extension" value="<?=$col_prs_extension?>">
                                </div>
                            </div>
                        </div>


                        <div class="field">
                            <label class="label">직통번호</label>
                            <div class="field is-grouped">
                                <div class="control">
                                    <input class="input" type="text" size="3" placeholder="070" readonly>
                                </div>
                                <div class="control">
                                    <input class="input" type="text" onKeyPress="javascript:com_onlyNumber();" name="tel1" value="<?=$col_prs_tel_ex[1]?>" maxlength="4" size="6"/>
                                </div>
                                <div class="control">
                                    <input class="input" type="text" onKeyPress="javascript:com_onlyNumber();" name="tel2" value="<?=$col_prs_tel_ex[2]?>"  maxlength="4" size="6"/>
                                </div>
                            </div>
                            <p class="help">* 전화기 화면에 표시된 번호를 적어주세요.</p>
                        </div>

                    </div>
                </div>

                <hr>

                <div class="field is-grouped">
                    <div class="control">
                        <a class="button is-primary" href="javascript:Modify_MemberInfo();">
                        <span class="icon is-small">
                            <i class="fas fa-check"></i>
                        </span>
                            <span>확인</span>
                        </a>
                    </div>
                    <div class="control">
                        <a class="button" href="../main.php">
                        <span class="icon is-small">
                            <i class="fas fa-times"></i>
                        </span>
                            <span>취소</span>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>
    <!-- 본문 끌 -->

<? include INC_PATH."/bottom.php"; ?>
</body>
</html>
