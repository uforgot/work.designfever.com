<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
?>

<?
	$dong = isset($_POST['dong']) ? $_POST['dong'] : null; 
?>

<? include INC_PATH."/pop_top.php"; ?>

<script type="text/JavaScript">
	<!--
	function fct_Dong() {

		if (document.fmzipcode.dong.value.length == 0){
			alert("�˻��� ���Ͻô� ��/��/�� �̸��� �Է��� �ֽʽÿ�.");
			document.fmzipcode.dong.focus();
			return;
		}
		document.fmzipcode.action="pop_zipcode.php";
		document.fmzipcode.submit();
	}

	function init() {
		var intHeight = document.body.scrollHeight+50;
		if (intHeight > 800) intHeight = 800;

		self.resizeTo(660, intHeight);
		document.fmzipcode.dong.focus();
	}

	function quotation(){
		if (event.keyCode == 34 || event.keyCode == 39)
			event.returnValue = false;
	}

	function input_dong(){
		document.fmzipcode.dong.focus();
		document.fmzipcode.dong.value = "";
	}

	function fct_ZipCodePut(strPostValue, strAddrValue) {
		 
		opener.document.form.zipcode1.value = strPostValue.substring(0, 3);
		opener.document.form.zipcode2.value = strPostValue.substring(4, 7);
		opener.document.form.addr1.value = strAddrValue;
	
		self.close();
		return;
	}
	//-->
</script>
</head>
<body onLoad='input_dong()'>
<form name="fmzipcode" method="post"  onkeydown="javascript:if (event.keyCode == 13) {fct_Dong();}">
<div class="zip_pop">
	<div class="pop_top">
		<p class="pop_title">�����ȣ �˻�</p>
		<a href="javascript: self.close();" class="close">�ݱ�</a>
	</div>
	<div class="pop_body">
		<p class=""><strong>ã�� ������ �ּ��� �� (��./ ��) �̸��� �Է��ϼ���.</strong></p>
		<p>��) û�� 1��, �Ѱ��� 3��</p>
		<div class="search">
			<dl class="clearfix">
				<dt><label for="#df_search">������ �Է�</label></dt>
				<dd><input id="df_search" class="df_textinput" type="text" name="dong" style="width:160px;" onKeyPress="quotation();" value="<?=$dong?>"/>
				<a href="javascript:fct_Dong();" class="ml_6"><img src="../img/btn_search.gif" alt="�˻�" /></a></dd>
			</dl>
			<p class="search_info color_o">�˻� ����� �ش� �ּҸ� Ŭ�� �Ͻø� �ڵ� �Էµ˴ϴ�.</p>
		</div>
		<div class="search_re">
			<ul>
<?
	if ($dong != "")
	{
		$sql = "SELECT ZIPCODE, SIDO, GUGUN, DONG, BUNJI FROM ZIPCODE WITH(NOLOCK) WHERE DONG LIKE '%$dong%' ORDER BY SIDO, GUGUN, DONG, BUNJI";
		$rs = sqlsrv_query($dbConn, $sql);

		if (sqlsrv_has_rows($rs) > 0)
		{
			while ($record=sqlsrv_fetch_array($rs))
			{
				$zipcode = $record['ZIPCODE'];
				$sido = $record['SIDO'];
				$gugun = $record['GUGUN'];
				$dong = $record['DONG'];
				$bunji = $record['BUNJI'];
?>
			<li><a href="javascript:fct_ZipCodePut('<?=$zipcode?>','<?=$sido?> <?=$gugun?> <?=$dong?>');"><span><?=$zipcode?></span><span class="add"><?=$sido?> <?=$gugun?> <?=$dong?> <?=$bunji?></a></span></li>
<?
			}
		}
		else
		{
?>
			<br>
			<br>
			<center><strong>�˻������ �����ϴ�.</strong></center>
<?
		}
	}
?>
			</ul>
		</div>
	</div>
</div>
</form>
</body>
</html>
