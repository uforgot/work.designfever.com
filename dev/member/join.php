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

<script type="text/javascript" src="/assets/js/df_join.js"></script>
<script type="text/javascript" src="/assets/js/df_auth.js"></script>
<script>
    $(document).ready(function() {
        //선택된 파일명 표시
        $("#file_img").change(function () {
            var str = this.value;
            var arr_str = str.split("\\");
            var arr_len = arr_str.length;
            $("#file_name").val(this.value);
        });
    });
</script>
</head>
<body onload="jusoCallBack('roadFullAddr','roadAddrPart1','addrDetail','roadAddrPart2','engAddr','jibunAddr','zipNo','admCd','rnMgtSn','bdMgtSn');">
<form name="form" method="post" action="join_act.php" enctype="multipart/form-data">
<input type="hidden" name="IdCheck">            <!-- 로그인 아이디 중복체크 완료 히든값 -->
<div class="top">
    <section class="hero is-link is-hidden-mobile">
        <div class="hero-body">
            <div class="container">
                <div class="columns is-vcentered">
                    <div class="column">
                      <a href="/index.php">
                        <img src="/assets/images/df_logo_w.svg" width="120">
                      </a>
                    </div>
                    <div class="column is-narrow">
                        <span class="is-size-6 is-italic">a difference that matters.</span>
                    </div>
                </div>

            </div>
        </div>
    </section>
    <section class="hero is-link is-hidden-tablet">
        <div class="hero-body">
            <div class="container">
                <div class="level is-mobile">
                    <div class="level-item level-left">
                        <img src="/assets/images/df_logo_w.svg" width="50" style="vertical-align:middle;">
                    </div>
                    <div class="level-item level-right is-expanded">
                        <span class="is-italic has-text-right is-fullwidth">a difference that matters.</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

</div>

<!-- 본문 시작 -->
<section class="section is-resize">
    <div class="container">
        <div class="content">

            <div class="columns">
                <div class="column">
                    <div class="field ">
                        <label class="label" style="color: #eb6100 !important;">"!" 표시는 필수 기입 항목입니다</label>
                        <label class="label"><strong style="color: #eb6100 !important;">!</strong> 아이디</label>
                        <div class="field is-grouped">
                            <div class="control">
                                <input id="df_join_id"  class="input is-primary" type="text" placeholder="" name="login" onBlur="fcHancheck();" onKeyPress="intNumber_Check();checkCapsLock(event);">
                            </div>
                            <div class="control">
                                <a href="javascript:fcOpenNewWindow('check_id');" onFocus="this.blur()" class="button">중복확인</a>
                            </div>
                        </div>
                        <p class="help">* 영문과 숫자만 입력가능</p>
                    </div>

                    <div class="columns">
                        <div class="column  is-narrow">
                            <div class="field">
                                <label class="label"><strong style="color: #eb6100 !important;">!</strong> 비밀번호</label>
                                <div class="field is-grouped">
                                    <div class="control">
                                        <input id="df_join_pw" name="PassWd" maxlength="16" type="password" class="input is-primary" placeholder="">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="column">
                            <div class="field">
                                <label class="label"><strong style="color: #eb6100 !important;">!</strong> 비밀번호 확인</label>
                                <div class="field is-grouped">
                                    <div class="control">
                                        <input id="df_join_pwc" type="password" name="PassWdCon" maxlength="16"  class="input is-primary" placeholder="">
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
                                    <input class="file-input" type="file" name="file_img" id="file_img"/>
                                    <span class="file-cta">
                    <span class="file-icon">
                        <i class="fas fa-upload"></i>
                    </span>
                    <span class="file-label">
                        파일찾기
                    </span>
                    </span>
                            <input type="text" class="file-name" name="file_name" id="file_name" readonly>
                        </div>
                        </div>
                        <p class="help">* 이미지파일 용량 200 kb 이내 업로드 가능</p>
                    </div>

                    <div class="field ">
                        <label class="label"><strong style="color: #eb6100 !important;">!</strong> 이름</label>
                        <div class="field is-grouped">
                            <div class="control">
                                <input id="df_join_name" class="input is-primary" type="text" placeholder="" name="name" maxlength="16">
                            </div>
                        </div>
                    </div>

                    <div class="field ">
                        <label class="label"><strong style="color: #eb6100 !important;">!</strong> 영문 이름</label>
                        <div class="field is-grouped">
                            <div class="control">
                                <input id="ename1" class="input is-primary" type="text" size="10" name="ename1" maxlength="20" placeholder="성" />

                            </div>
                            <div class="control">
                                <input id="ename2" class="input is-primary" type="text" name="ename2" maxlength="30" placeholder="이름"/>
                            </div>
                        </div>
                    </div>
                    <div class="field ">
                        <label class="label">DF E-mail</label>
                        <div class="field is-grouped">
                            <div class="control">
                                <input id="df_join_email" class="input is-primary" type="text" name="email">
                            </div>
                            <input class="input is-static" type="email" value="@designfever.com" readonly>
                        </div>
                    </div>

                    <div class="field ">
                        <label class="label"><strong style="color: #eb6100 !important;">!</strong> 입사일</label>
                        <div class="field is-grouped">
                            <div class="control select is-primary">
                                <select name="join1">
                                    <? for ($i=2000; $i<=date("Y"); $i++) { ?>
                                        <option value="<?=$i?>"<?if ($i==date("Y")){ echo " selected"; } ?>><?=$i?>년</option>
                                    <? } ?>
                                </select>
                            </div>
                            <div class="control select is-primary">
                                <select name="join2">
                                    <? for ($i=1; $i<=12; $i++) { ?>
                                        <option value="<?=$i?>"<?if ($i==date("m")){ echo " selected"; } ?>><?=$i?>월</option>
                                    <? } ?>
                                </select>
                            </div>
                            <div class="control select is-primary">
                                <select name="join3">
                                <? for ($i=1; $i<=31; $i++) { ?>
                                    <option value="<?=$i?>"<?if ($i==date("d")){ echo " selected"; } ?>><?=$i?>일</option>
                                <? } ?>
                                </select>
                            </div>
                            <!--
                            <div class="button">
                                <input type="hidden" id="fr_date" class="datepicker">
                                <i class="fa fa-calendar-plus"></i>
                            </div>
                            -->
                        </div>
                    </div>

                </div>
                <div class="column">
                    <div class="columns">
                        <div class="column">
                            <div class="field ">
                                <label class="label"><strong style="color: #eb6100 !important;">!</strong> 생일</label>
                                <div class="field is-grouped">
                                    <div class="control select is-primary">
                                        <select name="birth1">
                                            <option value="">----</option>
                                            <? for ($i=1970; $i<=date("Y")-20; $i++) { ?>
                                                <option value="<?=$i?>"><?=$i?>년</option>
                                            <? } ?>
                                        </select>
                                    </div>
                                    <div class="control select is-primary">
                                        <select name="birth2">
                                            <option value="">--</option>
                                            <? for ($i=1; $i<=12; $i++) { ?>
                                                <option value="<?=$i?>"><?=$i?>월</option>
                                            <? } ?>
                                        </select>
                                    </div>
                                    <div class="control select is-primary">
                                        <select name="birth3">
                                            <option value="">--</option>
                                            <? for ($i=1; $i<=31; $i++) { ?>
                                                <option value="<?=$i?>"><?=$i?>일</option>
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
                                        <input class="is-checkradio" id="exampleRadioInline1" type="radio" name="exampleRadioInline" name="birth_type" value="양력" checked="checked">
                                        <label for="exampleRadioInline1">양력</label>
                                        <input class="is-checkradio" id="exampleRadioInline2" type="radio" name="exampleRadioInline" name="birth_type" value="음력">
                                        <label for="exampleRadioInline2">음력</label>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>


                    <div class="field">
                        <label class="label"><strong style="color: #eb6100 !important;">!</strong> 핸드폰</label>
                        <div class="field is-grouped">
                            <div class="control select is-primary">
                                <select name="mobile1" id="df_join_cell">
                                    <option value="" >선택</option>
                                    <option value="010">010 </option>
                                    <option value="011">011 </option>
                                    <option value="016">016 </option>
                                    <option value="017">017 </option>
                                    <option value="018">018 </option>
                                    <option value="019">019 </option>
                                </select>
                            </div>
                            <div class="control">
                                <input class="input is-primary" size="6" type="text" onKeyPress="javascript:com_onlyNumber();" name="mobile2" maxlength="4"/>
                            </div>
                            <div class="control">
                                <input class="input is-primary" size="6"  type="text" onKeyPress="javascript:com_onlyNumber();" name="mobile3" maxlength="4" />
                            </div>
                        </div>
                    </div>

                    <div class="field">
                        <label class="label"><strong style="color: #eb6100 !important;">!</strong> 비상연락망</label>
                        <div class="field is-grouped">
                            <div class="control">
                                <input class="input is-primary" type="text" style="width:75px; ime-mode:disabled" maxlength="4" name="e_tel1" type="text"  onKeyPress="javascript:com_onlyNumber();" id="df_join_e"/>
                            </div>
                            <div class="control">
                                <input class="input is-primary"  type="text" size="6" maxlength="4" name="e_tel2" type="text"  onKeyPress="javascript:com_onlyNumber();"/>
                            </div>
                            <div class="control">
                                <input class="input is-primary"  type="text" size="6" maxlength="4" name="e_tel3" type="text"  onKeyPress="javascript:com_onlyNumber();"/>
                            </div>
                        </div>
                    </div>

                    <div class="field ">
                        <label class="label"><strong style="color: #eb6100 !important;">!</strong> 자택주소</label>
                        <div class="field is-grouped">
                            <div class="control">
                                <input id="df_join_zipcode" class="input is-primary" type="text" name="zipcode_new" readonly value="<?=$col_prs_zipcode_new?>" maxlength="5"/ />
                            </div>
                            <div class="control">
                                <a href="javascript:goPopup();" onFocus="this.blur()" class="button">우편번호 찾기</a>
                            </div>
                        </div>
                        <div class="field">
                            <div class="control">
                                <input type="hidden" id="df_join_add1" class="df_textinput" name="addr1" />
                                <input type="hidden" id="df_join_add2" class="df_textinput" name="addr2" value="<?=$col_prs_addr2?>" />
                                <input class="input is-primary" id="df_join_address" name="address_new" type="text" value="" readonly />
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
                        <label class="label"><strong style="color: #eb6100 !important;">!</strong> 부서 / <strong style="color: #eb6100 !important;">!</strong> 직급</label>
                        <div class="field is-grouped-multiline">
                            <div class="control select is-primary">
                                <select name="team" id="df_join_depart">
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
                            </div>

                            <div class="control select is-primary">
                                <select name="position" id="df_join_position">
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
                            </div>
                        </div>
                    </div>


                    <div class="field ">
                        <label class="label">내선번호</label>
                        <div class="field is-grouped">
                            <div class="control">
                                <input class="input" type="text" onKeyPress="javascript:com_onlyNumber();" name="extension" value="" maxlength="3"/>
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
                                <input class="input" type="text" size="6" onKeyPress="javascript:com_onlyNumber();" style="width:75px; ime-mode:disabled;" name="tel1" maxlength="4"/>

                            </div>
                            <div class="control">
                                <input class="input" type="text" size="6" onKeyPress="javascript:com_onlyNumber();" style="width:75px; ime-mode:disabled;" name="tel2" maxlength="4" />
                            </div>
                        </div>
                        <p class="help">* 전화기 화면에 표시된 번호를 적어주세요.</p>
                    </div>

                </div>
            </div>

            <hr>

            <div class="field is-grouped">
                <div class="control">
                    <a href="javascript:Inert_MemberInfo()" class="button is-primary">
                        <span class="icon is-small">
                            <i class="fas fa-user-plus"></i>
                        </span>
                        <span>회원가입</span>
                    </a>
                </div>
                <div class="control">
                    <a href="login.php" class="button">
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