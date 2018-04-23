<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
	// ���� ���� ����
	$winfo = getWeekInfo(date('Y-m-d'));

	$s_date = isset($_REQUEST['s_date'])?$_REQUEST['s_date']:$winfo['cur_week'];	// ������
	$e_date = isset($_REQUEST['e_date'])?$_REQUEST['e_date']:$winfo['cur_week'];	// ������

	$selected1[$s_date] = "selected";
	$selected2[$e_date] = "selected";	

	// ���� ����
	$sql = "SELECT DISTINCT WEEK_ORD, WEEK_AREA FROM DF_WEEKLY ORDER BY WEEK_ORD DESC";
	$rs = sqlsrv_query($dbConn,$sql);

	while ($record = sqlsrv_fetch_array($rs))
	{
		$select[] = array
						(
							'week_ord'=>$record['WEEK_ORD'],
							'week_area'=>$recoed['WEEK_AREA']
						);
	}

	//����Ʈ ����
	$searchSQL = " WHERE B.THIS_WEEK_RATIO > 0 AND (A.WEEK_ORD >= '$s_date' AND A.WEEK_ORD <= '$e_date')";

	$sql = "SELECT 
				A.WEEK_AREA, A.PRS_NAME, B.PROJECT_NO, B.THIS_WEEK_RATIO,
				(SELECT DISTINCT TITLE FROM DF_PROJECT WHERE PROJECT_NO = B.PROJECT_NO) PROJECT_NAME
			FROM 
				DF_WEEKLY A WITH(NOLOCK) 
				INNER JOIN DF_WEEKLY_DETAIL B WITH(NOLOCK) 
				ON A.SEQNO = B.WEEKLY_NO
			$searchSQL
			ORDER BY
				B.PROJECT_NO DESC, A.PRS_NAME, B.WEEKLY_NO DESC";

	$rs = sqlsrv_query($dbConn,$sql);

	while ($record = sqlsrv_fetch_array($rs))
	{
		if($record['PROJECT_NO'] == "DF0000_ETC") $record['PROJECT_NAME'] = "��Ÿ����";

		$list[] = array
						(
							'week_area'=>$record['WEEK_AREA'],
							'name'=>$record['PRS_NAME'],
							'project_name'=>$record['PROJECT_NAME'],
							'this_ratio'=>$record['THIS_WEEK_RATIO']
						);
	}
?>

<? include INC_PATH."/top.php"; ?>

<script type="text/javascript">
	function sort_list() 
	{
		var frm = document.form;	

		var s_date = frm.s_date.value;
		var e_date = frm.e_date.value;
		
		if(!s_date || !e_date) {
			alert("�˻� �����ϰ� �������� ������ �ּ���.");
			return;
		}

		if(s_date > e_date) {
			alert("�˻� �������� �����Ϻ��� �����Դϴ�.");
			return;
		}

		frm.action = "weekly_list_sort.php";
		frm.submit();
	}

	function excel_download()
	{
		var frm = document.form;

		frm.target = "hdnFrame";
		frm.action = "weekly_list_sort_excel.php";
		frm.submit();
	}
</script>
</head>
<body>
<div class="wrapper">
<form name="form" method="post">

	<? include INC_PATH."/top_menu.php"; ?>

		<div class="inner-home">
			<? include INC_PATH."/weekly_menu.php"; ?>

			<div class="work_wrap clearfix">

				<div class="vacation_stats clearfix">
					<table class="notable" width="100%">
						<tr>
							<th scope="row">&nbsp;</th>
<!-- 							<th width="50%" scope="row">���� �ְ�����</th> -->
							<td>
								<select name="s_date">
									<!--<option value="">+ ������ +</option>-->
<?
							foreach($select as $key => $val)
							{
								$week_ord = $val['week_ord'];
								$y = substr($week_ord,0,4);
								$m = substr($week_ord,4,2);
								$w = substr($week_ord,6,1);
								$week_str = $y."�� ".$m."�� ".$w."����";

								echo "<option value='".$week_ord."' ".$selected1[$week_ord].">".$week_str."</option>";
							}
?>
								</select>~&nbsp;
								<select name="e_date">
									<!--<option value="">+ ������ +</option>-->
<?
							foreach($select as $key => $val)
							{
								$week_ord = $val['week_ord'];
								$y = substr($week_ord,0,4);
								$m = substr($week_ord,4,2);
								$w = substr($week_ord,6,1);
								$week_str = $y."�� ".$m."�� ".$w."����";

								echo "<option value='".$week_ord."' ".$selected2[$week_ord].">".$week_str."</option>";
							}
?>
								</select>
								<a href="javascript:sort_list();"><img src="../img/project/btn_search_p.gif" alt="�˻�"></a>
								<a href="javascript:excel_download();"><img src="../img/btn_excell.gif" alt="�����ٿ�ε�"></a>
							</td>
						</tr>
					</table>
				</div>


				<table class="vacation notable work1 work_stats" width="100%" style="margin-bottom:10px;">
					<caption>���� �ְ����� ���̺�</caption>
					<colgroup>
						<col width="10%" />
						<col width="18%" />
						<col width="15%" />
						<col width="*" />
						<col width="15%" />
					</colgroup>

					<thead>
						<tr>
							<th>��ȣ</th>
							<th>�Ⱓ</th>
							<th>����</th>
							<th>������Ʈ</th>
							<th>��������(%)</th>
						</tr>
					</thead>

					<tbody>
<?
	if (count($list)==0) 
	{
?>
						<tr>
							<td colspan="5" class="bold">�ش� ������ �����ϴ�.</td>
						</tr>
<?
	}
	else
	{
		$cnt = count($list);

		foreach($list as $key => $val)
		{
			$border = "border-bottom:1px solid #e3e3e3;";

			$contents .= "<tr>";
			$contents .= "	<td style='text-align:center;vertical-align:top;$border'>".$cnt."</td>";
			$contents .= "	<td style='text-align:center;vertical-align:top;$border'>".$val['week_area']."</td>";
			$contents .= "	<td style='text-align:center;vertical-align:top;$border'>".$val['name']."</td>";
			$contents .= "	<td style='text-align:left;vertical-align:top;$border'>".$val['project_name']."</td>";
			$contents .= "	<td style='text-align:center;vertical-align:top;$border'>".$val['this_ratio']."</td>";
			$contents .= "</tr>";

			$cnt--;
		}

		echo $contents;
	}
?>
					</tbody>
					<tfoot>

					</tfoot>					
				</table>

			</div>
		</div>
</form>
<? include INC_PATH."/bottom.php"; ?>
</div>
</body>
</html>
