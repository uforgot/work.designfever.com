<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
    require_once CMN_PATH."/checkout_check.php"; //��ٽð� ����� ���� �߰�(��������� ���� �����ҵ�) ksyang

?>

<?
	if ($prs_id == "") {
?>
<meta http-equiv="Content-Type" content="text/html" charset="euc-kr">
<script type="text/javascript">
	alert("�α��� ������ ��Ȯ���� �ʽ��ϴ�.");
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
    $col_prs_en_first_name = "";
    $col_prs_en_last_name = "";
    $col_prs_position2 ="";

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
$col_prs_en_first_name = $record['PRS_EN_FIRSTNAME'];
$col_prs_en_last_name = $record['PRS_EN_LASTNAME'];
$col_prs_position2 =$record['PRS_POSITION2'];

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
<script src="/assets/js/jquery-1.11.3.min.js"></script>
<script src="/assets/js/html2canvas.min.js"></script>
<script src="/assets/js/mail_generator_php.js?v=0.08"></script>
<script>
    $(document).ready(function(){
        $("#copy").click(function(){
            var text =  $("#capture-area").html();
                 $("#clip_target").val(text);
                 $("#clip_target").select();

            try {
                var successful = document.execCommand('copy');
                   if(successful) alert('����Ϸ��Ͽ����ϴ�.');
                   else alert('��������Ͽ����ϴ�.');
            } catch (err) {
                alert('�� �������� �������� �ʽ��ϴ�.')
            }
        });
    });
</script>
</head>
<body>
<? include INC_PATH."/top_menu.php"; ?>
<!-- ���� ���� -->
<section class="section df-member">
    <div class="container">
        <div class="content">

            <p class="title is-size-5">�����</p>

            <ol>
                <li>�Ʒ� ���� ���� ������ �巡�� �� ����</li>
                <li>����ϴ� ���� ���α׷�(������, �ƿ��� ��)�� ������� �ٿ��ֱ� �ϰ� ����</li>
            </ol>

            <p class="help"> ���� :  <a href="http://work.designfever.com/board/board_detail.php?page=1&seqno=3319&board=default">"http://work.designfever.com/board/board_detail.php?page=1&seqno=3319&board=default</a></p>

            <br>

            <div class="border-box" style="border:1px solid #000; padding-top:50px;">
                <div id="clipboard-con" style="padding:30px 30px;">
                    <div id="mail-con" style="font-family:'Malgun Gothic', 'AppleGothicNeoSD', 'Apple SD �굹��� Neo', 'Microsoft NeoGothic', 'Droid sans', '���� ���', 'Dotum', '����', '����', 'arial', 'sans-serif'; font-size:0; font-weight:bold; color:#000;">

                        <p></p>
                        <div class="capture-area" id="capture-area" style="padding-bottom:30px;font-size:0;">
                            <table style="font-size:0;">
                                <caption></caption>
                                <tbody>
                                <tr style="font-size:0;">
                                    <td style="font-size:0;"><img src="assets/img/mail_logo.jpg" width="85" class="img-logo" alt="df"></td>
                                    <td style="font-size:0;"></td>
                                    <td style="font-size:0;"></td>
                                </tr>
                                <tr style="height:45px;font-size:0;">
                                    <td style="font-size:0;"></td>
                                    <td style="font-size:0;"></td>
                                    <td style="font-size:0;"></td>
                                </tr>
                                <tr style="font-size:0; line-height:1;">
                                    <td style="padding:0 10px 0 0;font-size:16px;line-height:1;color:#000;" class="df-name"><?=$col_prs_name?> <?=$col_prs_position2?> / <?=$col_prs_position?></td>
                                    <td style="width:119px;padding:0;line-height:1;color:#000;"><img src="assets/img/mail_hyphen.jpg?v20170210" alt="-" class="img-hyphen"></td>
                                    <td style="padding:0 0 0 10px;font-size:16px;line-height:1;color:#000;" class="df-name-eng"><?=$col_prs_en_last_name?> <?=$col_prs_en_first_name?></td>
                                </tr>

                                <tr style="font-size:0;">
                                    <td colspan="2" class="df-division" valign="top" style="padding:0; height:18px;font-size:12px;color:#000;"><?=$col_prs_team?></td>
                                    <td width="300" style="height:18px;padding:0 0 0 10px;font-size:12px; line-height:1.8;color:#000;"><a href="mailto:honggildong@designfever.com" class="df-email" style="text-decoration:none; color:#000;"><?=$col_prs_email?>g@designfever.com</a><br><a href="tel:010-1234-5678" class="df-mobile-number" style="text-decoration:none;color:#000;"><?=str_replace("-"," ",$col_prs_mobile)?></a><br><a href="tel:02-325-2767" style="text-decoration:none;color:#000;">02 325 2767</a> / <span class="df-ext-number"><?=$col_prs_extension?></span></td>
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
                                    <td style="font-size:11px;height:15px;padding:0;line-height:1.8;color:#000;">(��)�������ǹ� 04047 ����� ������ ��ȭ��10�� 40, DF����<br>DESIGNFEVER INC. 04047, 40, YANGHWA-RO 10GIL, MAPO-GU, SEOUL, KOREA<br><a href="http://www.designfever.com" target="_blank" style="text-decoration: none;color:#000;">WWW.DESIGNFEVER.COM</a></td>
                                </tr>
                                <tr>
                                    <td style="font-size:10px; text-align:justify; padding-top:28px; color:#999999;">��� ������ ������ �����θ��� ���Ѱ��̹Ƿ�, ����, ����, ���� �Ǵ� ����ϴ� ���� ������ �����˴ϴ�.<br>
                                        �� ������ �߸� ���۵� ���, �߽��ο��� �˷� �ֽð� ��� �����Ͽ� �ֽñ� �ٶ��ϴ�.</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <input id="clip_target" type="text" value="" style="position:absolute;top:-9999em;"/>
                    </div>
                </div>
            </div>

            <hr>

            <div class="field is-grouped">
                <!--
                <div class="control">
                    <a class="button is-primary" id ="copy">
                        <span class="icon is-small">
                            <i class="fas fa-check"></i>
                        </span>
                        <span>�����ϱ�</span>
                    </a>
                </div>
                -->
            </div>

        </div>
    </div>
</section>
<!-- ���� �� -->
<? include INC_PATH."/bottom.php"; ?>

</body>
</html>
