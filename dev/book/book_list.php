<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
	require_once CMN_PATH."/checkout_check.php"; //��ٽð� ����� ���� �߰�(��������� ���� �����ҵ�) ksyang
?>

<?
	//����üũ(�ٹ����)
	if ($prf_id == "6") 
	{ 
?>
	<meta http-equiv="Content-Type" content="text/html" charset="euc-kr">
	<script type="text/javascript">
		alert("Ż��ȸ�� �̿�Ұ� �������Դϴ�.");
		location.href="../main.php";
	</script>
<?
		exit;
	}

	$board = isset($_REQUEST['board']) ? $_REQUEST['board'] : "book"; 

	//����üũ(�ູ������)
	if ($board == "happy" && in_array($prs_id,$happyLab_arr) == false) 
	{
?>
	<meta http-equiv="Content-Type" content="text/html" charset="euc-kr">
	<script type="text/javascript">
		alert("�ູ������ Ȱ���������Ը� ������ �Խ����Դϴ�.");
		history.back();
	</script>
<?
		exit;
	}
	
	//����üũ(��Ʈ��)
	if ($board == "partner" && (!in_array($prf_id,array("2","3","4")) && in_array($prs_team,$partner_arr) == false)) 
	{
?>
	<meta http-equiv="Content-Type" content="text/html" charset="euc-kr">
	<script type="text/javascript">
		alert("��Ʈ�ʱ��� ������Ը� ������ �Խ����Դϴ�.");
		history.back();
	</script>
<?
		exit;
	}

	//����üũ(�Ƿ�������Ʈ)
	if ($board == "contact" && (!in_array($prf_id,array("2","3","4")) && $prs_team != '�濵������')) 
	{
?>
	<meta http-equiv="Content-Type" content="text/html" charset="euc-kr">
	<script type="text/javascript">
		alert("���� �Խ����� ��� ������ �����ϴ�.");
		history.back();
	</script>
<?
		exit;
	}

	$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; 

	$subject = isset($_REQUEST['subject']) ? $_REQUEST['subject'] : null; 
	$keyfield = isset($_REQUEST['keyfield']) ? $_REQUEST['keyfield'] : "ALL"; 
	$keyword = isset($_REQUEST['keyword']) ? $_REQUEST['keyword'] : null; 

	$searchSQL = " WHERE TMP3 = '$board'";

	if ($subject != "")
	{
		$searchSQL .= " AND TMP1 = '$subject'";
	}

	if ($keyword == "")
	{
		$searchSQL .= "";
	}
	else if ( $keyfield == "ALL")
	{
		$searchSQL .= " AND (TITLE LIKE '%$keyword%' OR CONTENTS LIKE '%$keyword%' OR PRS_NAME LIKE '%$keyword%')";
	} 
	else if ($keyfield =="TITLE_CONTENTS") 
	{
		$searchSQL .= " AND (TITLE LIKE '%$keyword%' OR CONTENTS LIKE '%$keyword%')";
	}
	else
	{
		$searchSQL .= " AND $keyfield like '%$keyword%'";
	}

	$sql = "SELECT COUNT(*) FROM DF_BOARD WITH(NOLOCK)". $searchSQL ."";
	$rs = sqlsrv_query($dbConn,$sql);

	$record = sqlsrv_fetch_array($rs);
	$total_cnt = $record[0];

	$per_page = 10;
	
	$sql = "SELECT
				SEQNO, PRS_ID, PRS_NAME, PRS_LOGIN, PRS_TEAM, PRS_POSITION, TITLE, CONTENTS, HIT, REP_DEPTH, NOTICE_YN, REG_DATE, FILE_1, FILE_2, FILE_3, TMP1 
			FROM
			(
				SELECT 
					ROW_NUMBER() OVER(ORDER BY SEQNO DESC) AS ROWNUM, 
					SEQNO, PRS_ID, PRS_NAME, PRS_LOGIN, PRS_TEAM, PRS_POSITION, TITLE, CONTENTS, HIT, REP_DEPTH, NOTICE_YN, CONVERT(CHAR(10),REG_DATE,120) AS REG_DATE, FILE_1, FILE_2, FILE_3, TMP1 
				FROM 
					DF_BOARD WITH(NOLOCK)
				$searchSQL
			) T
			WHERE
				T.ROWNUM BETWEEN(($page-1) * $per_page)+1 AND ($page * $per_page)";
	$rs = sqlsrv_query($dbConn,$sql);
?>

<? include INC_PATH."/top.php"; ?>

<script type="text/javascript">
	$(document).ready(function(){
		
		//�˻�
		$("#btnSearch").attr("style","cursor:pointer;").click(function(){
			$("#page").val("1");
			$("#form").attr("target","_self");
			$("#form").attr("action","<?=CURRENT_URL?>"); 
			$("#form").submit();
		});
		
		//�ʱ�ȭ
		
		$("#btnReset").attr("style","cursor:pointer;").click(function(){
			$("#page").val("1");
			$("#subject").val("");
			$("#keyfield").val("");
			$("#keyword").val("");
			$("#form").attr("target","_self");
			$("#form").attr("action","<?=CURRENT_URL?>"); 
			$("#form").submit();
		});

		//���
		/*		
		$("#btnWrite").attr("style","cursor:pointer;").click(function(){			
			$("#page").val("1");
			$("#subject").val("");
			$("#keyfield").val("");
			$("#keyword").val("");
			$("#form").attr("target","_self");
			$("#form").attr("action","book_write.php"); 
			$("#form").submit();
		});
		*/
	});

	function funWrite(){		
			$("#page").val("1");
			$("#subject").val("");
			$("#keyfield").val("");
			$("#keyword").val("");
			$("#form").attr("target","_self");
			$("#form").attr("action","book_write.php"); 
			$("#form").submit();
		}
	
	//�Խù� �б�
	function funView(seqno)
	{
		$("#form").attr("target","_self");
		$("#form").attr("action","book_detail.php?seqno="+seqno); 
		$("#form").submit();
	}
</script>
</head>

<body>
<? include INC_PATH."/top_menu.php"; ?>
<form method="post" name="form" id="form" onKeyDown="javascript:if (event.keyCode == 13) {funSearch();}">
<input type="hidden" name="page" id="page" value="<?=$page?>">
<input type="hidden" name="board" id="board" value="<?=$board?>">
<div class="sub-menu-7">
<!-- ���� �׺���̼� ���� -->
<nav class="navbar has-shadow is-size-7-mobile">
        <div class="container">
            <div class="navbar-tabs">
             <? include INC_PATH."/book_menu.php"; ?>
            </div>
        </div>
    </nav>
</div>    
<!-- ���� �׺���̼� ��-->
<!-- ���� ���� -->
<section class="section is-resize">
    <div class="container">
        <div class="columns is-vcentered">
            <!-- Left side -->
            <div class="column">
                    <div class="field is-grouped">
                    		<? if ($board == "ilab") { ?>
                    		<div class="control select">
														<select name="subject" id="subject">
																<option value="">��ü</option>
																<option value="IX"<? if ($subject == "IX") { echo " selected"; } ?>>IX</option>
																<option value="����"<? if ($subject == "����") { echo " selected"; } ?>>����</option>
																<option value="������"<? if ($subject == "������") { echo " selected"; } ?>>������</option>
																<option value="���"<? if ($subject == "���") { echo " selected"; } ?>>���</option>
																<option value="�ڵ�"<? if ($subject == "�ڵ�") { echo " selected"; } ?>>�ڵ�</option>
																<option value="��ȹ"<? if ($subject == "��ȹ") { echo " selected"; } ?>>��ȹ</option>
													 </select>
												</div> 
											<? } elseif ($board == "club") { ?>
												<div class="control select">
														<select name="subject" id="subject">
																<option value="">��ü</option>
																<option value="�������̴���"<? if ($subject == "�������̴���") { echo " selected"; } ?>>�������̴���</option>
																<option value="���Ͷ��̴���"<? if ($subject == "���Ͷ��̴���") { echo " selected"; } ?>>���Ͷ��̴���</option>
																<option value="Ŭ�� �ռ���"<? if ($subject == "Ŭ�� �ռ���") { echo " selected"; } ?>>Ŭ�� �ռ���</option>
																<option value="�÷��̱׶���"<? if ($subject == "�÷��̱׶���") { echo " selected"; } ?>>�÷��̱׶���</option>
																<option value="ưư����"<? if ($subject == "ưư����") { echo " selected"; } ?>>ưư����</option>
																<option value="�б�� ����"<? if ($subject == "�б�� ����") { echo " selected"; } ?>>�б�� ����</option>
																<option value="���̴ټ���"<? if ($subject == "���̴ټ���") { echo " selected"; } ?>>���̴ټ���</option>
														</select>
												</div>	
										<? } ?>                    	
                        <div class="control select">
                           <select name="keyfield" id="keyfield" >
															<option value="ALL"<? if ($keyfield == "ALL") { echo " selected"; } ?>>��ü</option>
                              <option value="TITLE_CONTENTS"<? if ($keyfield == "TITLE_CONTENTS") { echo " selected"; } ?>>����+����</option>
                              <option value="TITLE"<? if ($keyfield == "TITLE") { echo " selected"; } ?>>����</option>
                              <option value="CONTENTS"<? if ($keyfield == "CONTENTS") { echo " selected"; } ?>>����</option>
															<option value="PRS_NAME"<? if ($keyfield == "PRS_NAME") { echo " selected"; } ?>>�ۼ���</option>
												</select>
                        </div>
                        
                        <div class="control is-expanded">                           
                             <input id="keyword" class="input" type="text" placeholder="" type="text" name ="keyword" value="<?=$keyword?>">			
                        </div>
                        <div class="control is-hidden-mobile">
                            <button class="button is-link" id="btnSearch">
                                <span class="icon is-small">
                                    <i class="fas fa-search"></i>
                                </span>
                                <span>�˻�</span>
                            </button>
                        </div>
    
      									<div class="control is-hidden-tablet">
                             <button class="button is-link" >
                                <span class="icon is-small">
                                    <i class="fas fa-search"></i>
                                </span>                            
                        </div>
                        
    										<div class="control is-hidden-tablet" >
                           <a href="javascript:funWrite();" class="button is-danger" id="btnWrite">
                                <span class="icon is-small">
                                    <i class="fas fa-pencil-alt"></i>
                                </span>     
                            </a>                           
                        </div>

                    </div>
            </div>
            <!-- Right side -->
            <div class="column is-hidden-mobile">
                <div class="control has-text-right">
                <a href="javascript:funWrite();" class="button is-danger" id="btnWrite">
                    <span class="icon is-small">
                        <i class="fas fa-pencil-alt"></i>
                    </span>
                    <span>�Խù� �ۼ�</span>
                </a>
                </div>
            </div>
        </div>

        <table class="table is-fullwidth is-hoverable is-resize">
            <colgroup>
                <col width="8%">
                <col width="*">
                <col width="15%">
                <col width="15%">
                <col width="10%">
            </colgroup>
            <thead>
            <tr>
                <th><span class="is-hidden-mobile">No.</span></th>
                <th>����</th>
                <th class="has-text-centered">�ۼ���</th>
                <th class="has-text-centered">��¥</th>
                <th class="has-text-centered">��ȸ��</th>
            </tr>
            </thead>            
             <tbody>
            
            <?
							
							$i = $total_cnt-($page-1)*$per_page;

							if ($i == 0)
							{
						?>
						</tr>
            	<tr>
								<td colspan="5" style="text-align: center;">�˻��� ����� �����ϴ�.</td>
							</tr>
						<?
							}
							else
							{
								while ($record = sqlsrv_fetch_array($rs))
								{
									$book_seqno = $record['SEQNO'];
									$book_id = $record['PRS_ID'];
									$book_name = $record['PRS_NAME'];
									$book_login = $record['PRS_LOGIN'];
									$book_team = $record['PRS_TEAM'];
									$book_position = $record['PRS_POSITION'];
									$book_title = $record['TITLE'];
									$book_contents = $record['CONTENTS'];
									$book_hit = $record['HIT'];
									$book_depth = $record['REP_DEPTH'];
									$book_notice = $record['NOTICE_YN'];
									$book_date = $record['REG_DATE'];
									$book_file1 = trim($record['FILE_1']);
									$book_file2 = trim($record['FILE_2']);
									$book_file3 = trim($record['FILE_3']);
									$book_tmp1 = $record['TMP1'];
						?> 
					</tbody>                     
            <!-- �Ϲ� ����Ʈ -->            
            <tbody class="list">
            <tr>
                <td><?=$i?></td>
                <td>
                    <a href="javascript:funView(<?=$book_seqno?>);" style="cursor:hand">	                    	                    	
                    	<? if ($board == "ilab" && $book_tmp1 != "") { ?>													
													<span>[<?=$book_tmp1?>] <?=getCutString($book_title,60);?></span>
											<? } else { ?>
											  	<span><?=getCutString($book_title,60);?></span>											  	
											<? } ?>                    	                      
                       <? if ($book_depth != "0") { ?>
													 <span class="tag is-rounded td-tag"><?=$book_depth?></span>
											 <? } ?>
				               <? if ($book_file1 != "" || $book_file2 != "" || $book_file3 != "") { ?>
													<span class="icon is-small td-icon"><i class="fas fa-file"></i></span>
											 <? } ?>                      
                    </a>
                </td>
                <td class="has-text-centered"><?=$book_position?>&nbsp;<?=$book_name?></td>
                <td class="has-text-centered"><?=$book_date?></td>
                <td class="has-text-centered"><?=$book_hit?></td>
            </tr>
            <?
									$i--;
								}
							}
						?>	            
         </tbody>
        </table>							
				<!--����¡ó��-->        
					<nav class="pagination" role="navigation" aria-label="pagination">
        		<?=getPaging($total_cnt,$page,$per_page);?>
      		</ul>        	
     		</nav>
  		 <!--����¡ó��-->
    </div>  
</section>
</form>
<? include INC_PATH."/bottom.php"; ?>
</div>
</body>
</html>
