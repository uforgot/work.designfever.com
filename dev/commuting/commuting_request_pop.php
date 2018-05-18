<?
require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
require_once CMN_PATH."/login_check.php";
$mode = isset($_REQUEST['mode']) ? $_REQUEST['mode'] : null;
$p_date = isset($_REQUEST['date']) ? $_REQUEST['date'] : null;
$p_id = isset($_REQUEST['id']) ? $_REQUEST['id'] : null;

// 기존근태 등록 데이터
$sql = "SELECT 
				GUBUN, GUBUN1, GUBUN2, CHECKTIME1, CHECKTIME2
			FROM 
				DF_CHECKTIME WITH(NOLOCK)
			WHERE 
				PRS_ID = '$p_id' AND DATE = '$p_date'";
$rs = sqlsrv_query($dbConn, $sql);

$record = sqlsrv_fetch_array($rs);
if (sizeof($record) > 0)
{
    $md_gubun = $record['GUBUN'];
    $md_gubun1 = $record['GUBUN1'];
    $md_gubun2 = $record['GUBUN2'];
    $md_checktime1 = $record['CHECKTIME1'];
    $md_checktime2 = $record['CHECKTIME2'];
}

// 근태수정 요청 데이터
$sql = "SELECT 
				TOP 1 *, CONVERT(CHAR(19), REGDATE, 20) as REGDATE, CONVERT(CHAR(19), OK_DATE, 20) as OK_DATE

			FROM 
				DF_CHECKTIME_REQUEST WITH(NOLOCK)
			WHERE 
				PRS_ID = '$p_id' AND DATE = '$p_date'
			ORDER BY
				SEQNO DESC";
$rs = sqlsrv_query($dbConn, $sql);

$record = sqlsrv_fetch_array($rs);
if (sizeof($record) > 0)
{
    $rd_seqno = $record['SEQNO'];
    $rd_name = $record['PRS_NAME'];
    $rd_login = $record['PRS_LOGIN'];
    $rd_gubun = $record['GUBUN'];
    $rd_gubun1 = $record['GUBUN1'];
    $rd_gubun2 = $record['GUBUN2'];
    $rd_checktime1 = $record['CHECKTIME1'];
    $rd_checktime2 = $record['CHECKTIME2'];
    $rd_memo = $record['MEMO'];
    $rd_answer = $record['ANSWER'];
    $rd_regdate = $record['REGDATE'];
    $rd_status = $record['STATUS'];
    $rd_ok_date = $record['OK_DATE'];
    $rd_ok_name = $record['OK_NAME'];

    $status_str = array('ING'=>'처리중', 'CANCEL'=>'반려', 'OK'=>'승인');
}

if($mode == "VIEW") {
    $gubun1_disabled = " disabled='disabled'";
    $gubun2_disabled = " disabled='disabled'";
    $memo_disabled = " disabled";
}

if(!$rd_gubun1) $rd_gubun1 = $md_gubun1;
if(!$rd_gubun2) $rd_gubun2 = $md_gubun2;
if(!$rd_checktime1) $rd_checktime1 = $md_checktime1;
if(!$rd_checktime2) $rd_checktime2 = $md_checktime2;
?>
<? include INC_PATH."/top.php"; ?>
<script type="text/javascript">
	function modify(){
		var frm = document.form;
		var flg = false;
        if ((!frm.gubun1_hour.value && !frm.gubun1_minute.value)
			&& (!frm.gubun2_hour.value && !frm.gubun2_minute.value)) 
		{
			alert("수정요청 할 출/퇴근시간을 입력해 주세요");
			frm.gubun1_hour.focus();
			return;
		}
		if ((frm.gubun1_hour.value && !frm.gubun1_minute.value) 
			|| (!frm.gubun1_hour.value && frm.gubun1_minute.value))
		{
			alert("출근시간을 정확하게 입력해 주세요");
			frm.gubun1_hour.focus();
			return;
		}
		else if (frm.gubun1_hour.value && frm.gubun1_minute.value) 
		{
			frm.gubun1.value = "1";	
			flg = true;
		}
		if ((frm.gubun2_hour.value && !frm.gubun2_minute.value) 
			|| (!frm.gubun2_hour.value && frm.gubun2_minute.value))
		{
			alert("퇴근시간을 정확하게 입력해 주세요");
			frm.gubun2_hour.focus();
			return;
		}
		else if (frm.gubun2_hour.value && frm.gubun2_minute.value) 
		{
			frm.gubun2.value = "2";	
			flg = true;
		}

		// 11시 이후 출근, 17시 이전 퇴근은 안내 메세지
		var gubun1_time = parseInt(frm.gubun1_hour.value+frm.gubun1_minute.value);
		var gubun2_time = parseInt(frm.gubun2_hour.value+frm.gubun2_minute.value);

		if (gubun1_time >= 1100 || gubun2_time <= 1700)
		{
			frm.flag.value = "N";
		}

		if (flg)
		{
			if(!confirm("근태수정을 요청 하시겠습니까?")) return;

			frm.target="hdnFrame";
			frm.action = 'commuting_request_act.php';
			frm.submit();
		} else {
			alert("출근시간 또는 퇴근시간을 입력해 주세요");
			frm.gubun1_hour.focus();
			return;
		}

	}
	function cancel() {
		parent.HidePop('DayEdit');
	}
</script>
</head>
<body>
<form class="inlp" method='post' name='form'>
<input type="hidden" name="prs_login" value="<?=$prs_login?>">
<input type="hidden" name="prs_name" value="<?=$prs_name?>">
<input type="hidden" name="id" value="<?=$p_id?>">
<input type="hidden" name="date" value="<?=$p_date?>">
<input type="hidden" name="flag">
<input type="hidden" name="mode">
    <section class="modal-card-body modal-commuting-modify">
        <div class="content">
            <div class="columns is-mobile">
                <div class="column">
                    <div class="field is-horizontal">
                        <div class="field-label is-normal">
                            <label class="label">출근</label>
                            <input type="hidden" name="gubun1_prev" value="<?=$rd_checktime1?>">
                        </div>
                        <div class="field-body">
                            <div class="field is-grouped">
                                <div class="control select">
                                    <select name='gubun1_hour' <?=$gubun1_disabled?>>
                                        <option value="">--</option>
                                        <?
                                        for ($i=0; $i<=23; $i++)
                                        {
                                            if (strlen($i) == 1) { $j = "0".$i; }
                                            else { $j = $i; }
                                            ?>
                                            <option value="<?=$j?>"<? if ($j == substr($rd_checktime1,8,2)) { echo " selected"; } ?>><?=$j?></option>
                                            <?
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="control select">
                                    <select name='gubun1_minute' <?=$gubun1_disabled?>>
                                        <option value="">--</option>
                                        <?
                                        for ($i=0; $i<=59; $i++)
                                        {
                                            if (strlen($i) == 1) { $j = "0".$i; }
                                            else { $j = $i; }
                                            ?>
                                            <option value="<?=$j?>"<? if ($j == substr($rd_checktime1,10,2)) { echo " selected"; } ?>><?=$j?></option>
                                            <?
                                        }
                                        ?>
                                    </select>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="column">
                    <div class="field is-horizontal">
                        <div class="field-label is-normal">
                            <label class="label">퇴근</label>
                            <input type="hidden" name="gubun2_prev" value="<?=$rd_checktime2?>">
                        </div>
                        <div class="field-body">
                            <div class="field is-grouped">
                                <div class="control select">
                                    <select name='gubun2_hour' <?=$gubun2_disabled?>>
                                        <option value="">--</option>
                                        <?
                                        for ($i=0; $i<=48; $i++)
                                        {
                                            if (strlen($i) == 1) { $j = "0".$i; }
                                            else { $j = $i; }
                                            ?>
                                            <option value="<?=$j?>"<? if ($j == substr($rd_checktime2,8,2)) { echo " selected"; } ?>><?=$j?></option>
                                            <?
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="control select">
                                    <select name='gubun2_minute' <?=$gubun2_disabled?>>
                                        <option value="">--</option>
                                        <?
                                        for ($i=0; $i<=59; $i++)
                                        {
                                            if (strlen($i) == 1) { $j = "0".$i; }
                                            else { $j = $i; }
                                            ?>
                                            <option value="<?=$j?>"<? if ($j == substr($rd_checktime2,10,2)) { echo " selected"; } ?>><?=$j?></option>
                                            <?
                                        }
                                        ?>
                                    </select>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="field is-horizontal">
                <div class="field-body">
                    <div class="field">
                        <div class="control">
                            <textarea class="textarea" placeholder="사유" rows="2"name="memo" <?=$memo_disabled?>><?=$rd_memo?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="field is-horizontal">
                <div class="field-body">
                    <div class="field">
                        <div class="control">
                            <textarea class="textarea" placeholder="결과"  name="answer" disabled <?=$rd_answer?> rows="2"></textarea>
                        </div>
                        <p class="help is-danger">
                            <?
                            if ($mode == "VIEW")
                            {
                                if ($rd_status == "OK")
                                {
                                    echo "* 승인일 : ". $rd_ok_date. " (". $rd_ok_name. ")";
                                }
                                else if ($rd_status == "ING")
                                {
                                    echo "* 요청일 : ". $rd_regdate. " (<label style='color:#FF0000'>". $status_str[$rd_status]. "</label>)";
                                }
                                else if ($rd_status == "CANCEL")
                                {
                                    echo "* 반려일 : ". $rd_ok_date. " (". $rd_ok_name. ")";
                                }
                            }
                            else
                            {
                                echo "* 정상 출퇴근이 아니면, 게시판에 요청 요망";
                            }
                            ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="modal-card-foot">
        <? if ($mode != "VIEW") { ?>
            <a class="button is-primary" href="javascript:modify();">확인</a>
            <!--<a class="button" href="javascript:cancel();">취소</a>-->
        <? } else { ?>
            <!--<a class="button" href="javascript:cancel();">취소</a>-->
        <? } ?>
    </footer>
    </div>
<!-- //pop -->
</form>
<? include INC_PATH."/pop_bottom.php"; ?>
</body>
</html>
