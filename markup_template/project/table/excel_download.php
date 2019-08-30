<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
	require_once CMN_PATH."/checkout_check.php"; //퇴근시간 출력을 위해 추가(모든페이지 공통 들어가야할듯) ksyang
?>
<?
//날짜
	$nowYear = date("Y");
	$nowMonth = date("M");
	$nowDay = date("D");	
	$rn = $_REQUEST['rn'];if($rn ==""){$rn=2;}	

//월별 현재 주 기준 전 주차 뽑아내기
	$sql = "SELECT WEEK_AREA, WEEK_ORD 
			  FROM (SELECT WEEK_AREA, WEEK_ORD, ROW_NUMBER() OVER(ORDER BY WEEK_ORD DESC) RN 
					  FROM DF_WEEKLY
	                 GROUP BY WEEK_AREA, WEEK_ORD) AS A
             WHERE RN=".$rn."";
	$rs = sqlsrv_query($dbConn,$sql);	
	$record = sqlsrv_fetch_array($rs);		
	$week_area = $record['WEEK_AREA'];					
	$week_1 = substr($week_area,0,4)."-".substr($week_area,5,2)."-".substr($week_area,8,2);
	$week_2 = substr($week_area,11,4)."-".substr($week_area,16,2)."-".substr($week_area,19,2);
	$last_week = $record['WEEK_ORD'];		
	$start_year= substr($week_area,0,4);
	$this_year = date("Y");
	
	
//부서	
	$p_team = isset($_REQUEST['team']) ? $_REQUEST['team'] : null;
//프로젝트 가로열 출력
	$project_title_arr="";
	$project_no_arr="";	
	//$project_cnt_arr="";		
	
	$sql = "SELECT A.TITLE      
				 , A.PROJECT_NO
			  FROM DF_PROJECT A
			 INNER JOIN DF_WEEKLY_DETAIL B
				ON A.PROJECT_NO = B.PROJECT_NO
			 INNER JOIN DF_WEEKLY C
				ON B.WEEKLY_NO = C.SEQNO	
			 WHERE A.USE_YN='Y'
			   AND C.WEEK_ORD = '$last_week'
			   AND B.THIS_WEEK_RATIO <> 0	
				OR A.PROJECT_NO IN('DF0000_ETC')
			 GROUP BY A.TITLE, A.PROJECT_NO
			UNION
			 SELECT A.TITLE     
				 , A.PROJECT_NO 
			  FROM DF_PROJECT A
			 INNER JOIN DF_WEEKLY_DETAIL B
				ON A.PROJECT_NO = B.PROJECT_NO
			 INNER JOIN DF_WEEKLY C
				ON B.WEEKLY_NO = C.SEQNO	
			 WHERE A.USE_YN='Y'
			   AND A.STATUS = 'ING'      	
				OR A.PROJECT_NO IN('DF0000_ETC')
			 GROUP BY A.TITLE , A.PROJECT_NO 
			 ORDER BY A.PROJECT_NO DESC";
	$rs = sqlsrv_query($dbConn,$sql);
	echo $sql;
	
	while ($record = sqlsrv_fetch_array($rs))
	{		
		$project_title_arr = $project_title_arr . $record['TITLE'] . "##";
		$project_no_arr = $project_no_arr . $record['PROJECT_NO'] . "##";		
		//$project_cnt_arr = $project_cnt_arr . $record['CNT'] . "##";				
	}
	
	$team_id = "";
	$team_name = "";
	$team_name2 = "";
	$team_color ="";
	$prs_team_no="";
	$edit_name ="";

//직원 목록 ,근태 수정 횟수
$sql = "SELECT PRS_ID
		     , PRS_NAME
	         , PRS_TEAM
			 , PRS_POSITION2 AS PRS_POSITION
			 , CASE WHEN PRS_POSITION1 ='사원' THEN '08'
				    WHEN PRS_POSITION1 ='주임' THEN '07'
					WHEN PRS_POSITION1 ='대리' THEN '06'
					WHEN PRS_POSITION1 ='과장' THEN '05'
					WHEN PRS_POSITION1 ='차장' THEN '04'
					WHEN PRS_POSITION1 ='부장' THEN '03'
					WHEN PRS_POSITION1 ='이사' THEN '02'
					WHEN PRS_POSITION1 ='대표' THEN '01'
				ELSE '00' END AS PRS_POSITION1	 		  
	         , CASE
					WHEN PRS_TEAM='CEO' THEN '01'
					WHEN PRS_TEAM ='Creative Planning Division'THEN '02'		 
					WHEN PRS_TEAM ='Creative Planning 1 Team'THEN '03'
					WHEN PRS_TEAM ='Creative Planning 2 Team'THEN '04'
					WHEN PRS_TEAM ='Marketing Planning Division'THEN '05'
					WHEN PRS_TEAM ='Design 1 Division 1 Team'THEN '06'
					WHEN PRS_TEAM ='Design 2 Division'THEN '07'
					WHEN PRS_TEAM ='Design 2 Division 1 Team'THEN '08'
					WHEN PRS_TEAM ='Design 2 Division 2 Team'THEN '09'	
					WHEN PRS_TEAM ='Motion Division'THEN '10'	
					WHEN PRS_TEAM ='Motion 1 Team'THEN '11'
					WHEN PRS_TEAM ='Motion 2 Team'THEN '12'
					WHEN PRS_TEAM ='Art Division'THEN '13'
					WHEN PRS_TEAM ='Visual Interaction Development'THEN '14'		 
					WHEN PRS_TEAM ='VID 1 Team'THEN '15'
					WHEN PRS_TEAM ='VID 2 Team'THEN '16'
					WHEN PRS_TEAM ='LAB'THEN '17'
					WHEN PRS_TEAM ='Business Support Team'THEN '18'
					ELSE '19' END AS PRS_TEAM_NO
			 , (SELECT COUNT(SEQNO) FROM DF_CHECKTIME_REQUEST WITH(NOLOCK)	WHERE PRS_ID = T.PRS_ID AND DATE LIKE '".$nowYear."%') AS
			    EDIT_LOG_CNT
		  FROM (SELECT PRS_ID,PRS_NAME,PRS_TEAM,PRS_POSITION2,PRS_POSITION1 FROM DF_PERSON WITH(NOLOCK) 
		 		 WHERE PRF_ID IN (1,2,3,4,5,7) 
		           AND PRS_ID NOT IN(351,352,102,362,22,87,148,15,24,29,48,59,60,71,79,80,164)	  	
				   AND PRS_TEAM LIKE '%".$p_team."%'
		        ) T 
		 ORDER BY PRS_TEAM_NO, PRS_POSITION1 ,PRS_NAME		 
		 ";
$rs = sqlsrv_query($dbConn,$sql);

while ($record = sqlsrv_fetch_array($rs))
{
    $team_id = $team_id. $record['PRS_ID'] . "##";
    $team_name = $team_name. $record['PRS_NAME'] . "##";    	
	$team_name2 = $team_name2. $record['PRS_TEAM'] . "##";	
	$prs_team_no = $prs_team_no. $record['PRS_TEAM_NO'] . "##";
}

$sql = "SELECT PRS_NAME
		      , COUNT(SEQNO) AS EDIT_LOG_CNT 
		   FROM DF_CHECKTIME_REQUEST WITH(NOLOCK)			  
		  WHERE DATE BETWEEN  '".$week_1."' AND  '".$week_2."'
		  GROUP BY PRS_NAME
		  ORDER BY EDIT_LOG_CNT DESC";
$rs = sqlsrv_query($dbConn,$sql);	   

while ($record = sqlsrv_fetch_array($rs))
{
    $edit_name = $edit_name. $record['PRS_NAME'] . "##";   
	$team_edit_cnt = $team_edit_cnt. $record['EDIT_LOG_CNT'] . "##";		
}	   

	header( "Content-type: application/vnd.ms-excel;charset=EUC-KR");
	header( "Expires: 0" );
	header( "Cache-Control: must-revalidate, post-check=0,pre-check=0" );
	header( "Pragma: public" );
	header( "Content-Disposition: attachment; filename=df_chart_total_".$last_week.".xls" );


?>
<html>
<body>
<form method="post" name="form">
<input type="hidden" name="rn" value="">     	                    									
			<div>
				<span>(<?=$week_area?>) <?=substr($last_week,0,4)."년 ".substr($last_week,4,2)."월 ".substr($last_week,6,1)."주차 주간보고";?></span>
			</div>															
			<br>
                <div class="table-holder">
                    <table border=1>
					<!-- 프로젝트 리스트 가로열-->
						<thead>
                        <tr>                            
                            <th><br>부서</th>
							<th><br>이름</th>    							
                            <?							
                            $project_title_arr_ex = explode("##",$project_title_arr);                          																					
							
								for ($i=1; $i<sizeof($project_title_arr_ex); $i++)
								{
									echo "<th id='".$i."'><br>". $project_title_arr_ex[$i-1] ."</th>";
								}																
                            ?>									
                        </tr>		
                        </thead>
					<!-- 프로젝트 리스트 가로열-->					
					<!-- 개인별 프로젝트별 참여 부분 가로세로열-->
                        <tbody>						
                        <?  
							$team_id_ex = explode("##",$team_id);						
							$team_name_ex = explode("##",$team_name);												
							$team_name_ex2 = explode("##",$team_name2);												
							$team_edit_cnt_ex = explode("##",$team_edit_cnt);
							$prs_team_no_ex = explode("##",$prs_team_no);
							$edit_name_ex = explode("##",$edit_name);
							
							for ($i=0; $i<sizeof($team_id_ex); $i++)
							{
								if ($team_id_ex[$i] != "")
								{								
									$sql = "EXEC SP_PROJECT_MEMBER_02 '%$last_week%', $team_id_ex[$i]";									
									
									$rs = sqlsrv_query($dbConn,$sql);														
									$sql1 = "  SELECT PROJECT_NO												  
											     FROM DF_PROJECT 
											    WHERE USE_YN='Y' 
												  AND STATUS='ING' 	
												   OR PROJECT_NO IN('DF0000_ETC') ";
									$rs1 = sqlsrv_query($dbConn,$sql1);
									
									$col_project_no ="";
									$col_title="";
									$col_ratio ="";
									$col_cnt ="";
									
									$col_project_no_arr="";
									$col_title_arr="";
									$col_ratio_arr="";			
									$col_ratio_arr_2="";			
									$col_cnt_arr ="";
																		
									while ($record = sqlsrv_fetch_array($rs))
									{								
									   //$col_project_no = $record['PROJECT_NO'];
									   $col_title = $record['TITLE'];
									   $col_ratio = $record['THIS_WEEK_RATIO'] / 100;			
									   $col_ratio_2 = $record['THIS_WEEK_RATIO'];			
									   $col_cnt = $record['CNT'];
									   
									  // $col_project_no_arr = $col_project_no_arr . $col_project_no ."##";																	  								 
									   $col_title_arr = $col_title_arr . $col_title ."##";	
									   $col_ratio_arr = $col_ratio_arr . $col_ratio ."##";	
									   $col_ratio_arr_2 = $col_ratio_arr_2 . $col_ratio_2 ."##";	
									   $col_cnt_arr = $col_cnt_arr. $col_cnt ."##"; 
									   																	   
									}
										//$col_project_no_ex = explode("##",$col_project_no_arr);
										$col_title_ex = explode("##",$col_title_arr);
										$col_ratio_ex = explode("##",$col_ratio_arr);	
										$col_ratio_ex_2 = explode("##",$col_ratio_arr_2);	
										//$col_cnt_ex = explode("##",$col_cnt_arr);
										
									while($record1 = sqlsrv_fetch_array($rs1))
									{
										$col_project_no = $record1['PROJECT_NO'];
										$col_project_no_arr = $col_project_no_arr . $col_project_no ."##";											
									}
										$col_project_no_ex = explode("##",$col_project_no_arr);										
                            ?>
                                <tr>									
                                    <td><?=$team_name_ex2[$i]?> </td> <!-- 부서명 세로 출력-->
									<td><?=$team_name_ex[$i]?> </td> <!-- 직원명 세로 출력-->
									<!--주간업무목록 가로 출력-->
									<?	
										for ($j=0; $j<sizeof($col_project_no_ex)-1; $j++)											
										{										
											 if ($col_title_ex[$j] != "")
											{
												if ($col_ratio_ex[$j] == "")
												{											
													$ratio = "";													
												}
												else
												{													
													$ratio = $col_ratio_ex[$j];													
													if($ratio =="0"){
														$ratio ="";
													}else{
														$ratio ="<span>".$col_ratio_ex[$j]."</i></span>";																												
													}																												
												}
									?>																			
											  <td valign="top" id="<?=$team_name_ex2[$i]?>">
												<?=$ratio?>												
											  </td>
									<? 
										}else{?>
											<td valign="top" id="<?=$team_name_ex2[$i]?>">																					
											 </td>											
										<?}
										}
									?>
									<!--주간업무목록 가로 출력-->
						<?							
                            $project_title_arr_ex = explode("##",$project_title_arr);                          							
							//$project_cnt_arr_ex = explode("##",$project_cnt_arr);																																						
                        ?>											
                                </tr>                                                                  
                        <?
								}
							}
                        ?>												
					<!-- 프로젝트 리스트 가로열-->
						<thead>
                        <tr>                            
                            <th><br>부서</th>
							<th><br>이름</th>    							
                            <?							
                            $project_title_arr_ex = explode("##",$project_title_arr);                          																					
							
								for ($i=1; $i<sizeof($project_title_arr_ex); $i++)
								{
									echo "<th id='".$i."'><br>". $project_title_arr_ex[$i-1] ."</th>";
								}																
                            ?>									
                        </tr>		
                        </thead>
					<!-- 프로젝트 리스트 가로열-->
                    </table>
				</div>					
</form>
</body>
</html>
