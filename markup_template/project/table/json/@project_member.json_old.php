<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";			
	header("Content-Type: application/json; charset=EUC-KR");		
	
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
			
	$project_member=array();
								
								//////////////////////////////////////////// 팀별 가로열 table_header ////////////////////////////////////////////	
								$t_header_lists = array();
								
									$sql =" SELECT A.PRS_TEAM      
											  FROM DF_PERSON A WITH (NOLOCK) 
											 WHERE A.PRF_ID NOT IN(6)
											   AND A.PRS_ID NOT IN(351,352,102,362,22,87,148,15,24,29,48,59,60,71,79,80,164)											   
											 GROUP BY A.PRS_TEAM
											 ORDER BY A.PRS_TEAM";						
									$rs = sqlsrv_query($dbConn,$sql);
									
										while ($record=sqlsrv_fetch_array($rs))
										{
											$prs_team = urlencode($record['PRS_TEAM']);		//팀명 																						
											$prs_team_ = str_replace('+',' ',$prs_team); 	//팀명 문자열변환																						
											$members=array();											
											$label=array();
											
												  $sql3="SELECT TOP 1 
														 	    ROW_NUMBER() OVER(ORDER BY B.PROJECT_NO DESC) AS CNT
														   FROM DF_WEEKLY A WITH (NOLOCK)	 
														  INNER JOIN DF_WEEKLY_DETAIL B WITH (NOLOCK)
															 ON A.SEQNO = B.WEEKLY_NO	  
														  WHERE A.PRS_TEAM ='$prs_team_'
															AND A.WEEK_ORD='$last_week'
															AND B.THIS_WEEK_RATIO NOT IN(0)
														 GROUP BY B.PROJECT_NO
														 ORDER BY CNT DESC";															
													$rs3 = sqlsrv_query($dbConn,$sql3);													
													$record = sqlsrv_fetch_array($rs3); 					
													$point_total = $record['CNT']; if($point_total ==""){$point_total=0;} //팀별 참여한 프로젝트 총합
													
													
													$sql4=" SELECT B.PROJECT_NO	
																 , COUNT(B.PROJECT_NO) AS CNT     		
															  FROM DF_WEEKLY A WITH (NOLOCK)
															 INNER JOIN DF_WEEKLY_DETAIL B WITH (NOLOCK)
																ON A.SEQNO = B.WEEKLY_NO 	 
															 WHERE A.PRS_TEAM ='$prs_team_'
															   AND A.WEEK_ORD='$last_week'	    			
															   AND B.THIS_WEEK_RATIO NOT IN(0)					
															 GROUP BY B.PROJECT_NO		 
														UNION 
															SELECT A.PROJECT_NO
																 , NULL			 
															  FROM DF_PROJECT A WITH (NOLOCK)		 
															 WHERE A.USE_YN='Y'
															   AND A.STATUS ='ING'		
																AND A.PROJECT_NO 
																NOT IN(SELECT 
																			A.PROJECT_NO					 	 
																	   FROM DF_PROJECT A WITH (NOLOCK)
																	  INNER JOIN DF_WEEKLY_DETAIL B WITH (NOLOCK)
																		 ON A.PROJECT_NO = B.PROJECT_NO
																	  INNER JOIN DF_WEEKLY C WITH (NOLOCK)
																		 ON B.WEEKLY_NO = C.SEQNO 
																	  WHERE C.PRS_TEAM ='$prs_team_'
																		AND C.WEEK_ORD='$last_week'		    			
																		AND B.THIS_WEEK_RATIO NOT IN(0)
																		GROUP BY A.PROJECT_NO
																	 )
																	 GROUP BY A.PROJECT_NO
															ORDER BY B.PROJECT_NO DESC";  					//팀별 참여한 프로젝트 개별 합계
															 
													$rs4 = sqlsrv_query($dbConn,$sql4);
													
													$record = sqlsrv_fetch_array($rs4); 					
																	
												array_push($members, array("name"=>"","point"=>$point_total));																					

											/*팀명 줄이기*/
											if ($prs_team=="Art+Division"){$prs_team="AD";}  
												else if ($prs_team=="Business+Support+Team"           ){$prs_team="BST";} 
												else if ($prs_team=="Creative+Planning+1+Team"        ){$prs_team="CP1";} 
												else if ($prs_team=="Creative+Planning+2+Team"        ){$prs_team="CP2";} 
												//else if ($prs_team=="Creative+Planning+Division"      ){$prs_team="CP";}  
												else if ($prs_team=="Design+1+Division+1+Team"        ){$prs_team="D1-1";}
												//else if ($prs_team=="Design+2+Division"               ){$prs_team="D2";}  
												else if ($prs_team=="Design+2+Division+1+Team"        ){$prs_team="D2-1";}
												else if ($prs_team=="Design+2+Division+2+Team"        ){$prs_team="D2-2";}
												else if ($prs_team=="LAB"                             ){$prs_team="LAB";} 
												else if ($prs_team=="Marketing+Planning+Division"     ){$prs_team="MP";}  
												else if ($prs_team=="Motion+1+Team"                   ){$prs_team="M1";}  
												else if ($prs_team=="Motion+2+Team"                   ){$prs_team="M2";}  
												//else if ($prs_team=="Motion+Division"                 ){$prs_team="M";}   
												else if ($prs_team=="VID+1+Team"                      ){$prs_team="VID1";}
												else if ($prs_team=="VID+2+Team"                      ){$prs_team="VID2";}
												//else if ($prs_team=="Visual+Interaction+Development"  ){$prs_team="VID";} 

												
											array_push($t_header_lists, array("label"=>$prs_team, "members"=>$members));																						
										}
																	
								$t_header=(array(
													  "lists"=> $t_header_lists
												));

						//////////////////////////////////////////// 팀별 세로열  table_data ////////////////////////////////////////////
						
						
										$t_data_lists=array();		
										
											$sql =" SELECT A.PRS_TEAM      
													  FROM DF_PERSON A WITH (NOLOCK) 
													 WHERE A.PRF_ID NOT IN(6)
													   AND A.PRS_ID NOT IN(351,352,102,362,22,87,148,15,24,29,48,59,60,71,79,80,164)
													 GROUP BY A.PRS_TEAM
													 ORDER BY A.PRS_TEAM";						
											$rs = sqlsrv_query($dbConn,$sql);
											$record=sqlsrv_fetch_array($rs);	
									
													$sql = "SELECT A.SEQNO AS SEQNO
																 , A.PROJECT_NO AS PROJECT_NO
																 , A.TITLE AS TITLE
																 , A.START_DATE AS START_DATE
																 , A.END_DATE AS END_DATE
															  FROM DF_PROJECT A WITH (NOLOCK)
															  LEFT JOIN DF_PROJECT_DETAIL B WITH (NOLOCK)
																ON A.PROJECT_NO = B.PROJECT_NO  	  	 	 
															 WHERE A.STATUS='ING' 
															   AND A.USE_YN='Y'
																OR A.PROJECT_NO IN('DF0000_ETC')
															 GROUP BY A.TITLE, A.START_DATE, A.END_DATE, A.SEQNO, A.PROJECT_NO
															 ORDER BY A.SEQNO DESC";
															 
													$rs = sqlsrv_query($dbConn,$sql);

														while ($record=sqlsrv_fetch_array($rs))
														{	
															//프로젝트 기본정보 뽑아내기
																$project_name = urlencode($record['TITLE']);				//프로젝트 타이틀
																$start_time = date_format($record['START_DATE'], 'Y-m-d');	//시작
																$end_time = date_format($record['END_DATE'], 'Y-m-d');		//종료일			
																$project_no = urlencode($record['PROJECT_NO']); 			//프로젝트 넘버												
																
																if($project_no =="DF0000_ETC"){ //기타업무
																	$start_time="";
																	$end_time="";
																}
															
																//프로젝트 총괄자 뽑아내기
																	$sql1 = "SELECT TOP 1 ISNULL(PRS_NAME,'NONE')AS PRS_NAME FROM DF_PROJECT_DETAIL WHERE PROJECT_NO ='$project_no' AND PART IN('PM')";
																	$rs1 = sqlsrv_query($dbConn,$sql1);																	
																		if (sqlsrv_has_rows($rs1) == "")
																		{
																				$pm = "";
																		}else{
																			while ($record=sqlsrv_fetch_array($rs1))
																			{
																				$pm = urlencode($record['PRS_NAME']);			//프로젝트 총괄	
																			}																					
																		}																																						
																
																//팀별 프로젝트별 참여도 총합 뽑아내기
																
																$sql2 ="	SELECT COUNT(B.THIS_WEEK_RATIO) AS THIS_WEEK_RATIO 	 	
																		         , SUM(B.THIS_WEEK_RATIO) AS SUM_RATIO 	 	
																				 , A.PRS_TEAM
																			   FROM DF_WEEKLY A WITH (NOLOCK)
																			  INNER JOIN DF_WEEKLY_DETAIL B WITH (NOLOCK) 
																				 ON A.SEQNO = B.WEEKLY_NO
																			  WHERE B.PROJECT_NO ='$project_no'
																				AND A.WEEK_ORD ='$last_week'
																				AND B.THIS_WEEK_RATIO <> 0																					
																			  GROUP BY A.PRS_TEAM
																		 UNION 
																			SELECT 0
																				 , 0
																				 , C.PRS_TEAM
																			  FROM DF_PERSON C WITH (NOLOCK)
																			 WHERE C.PRS_TEAM NOT IN(
																									 SELECT  A.PRS_TEAM
																									   FROM DF_WEEKLY A WITH (NOLOCK)
																									  INNER JOIN DF_WEEKLY_DETAIL B WITH (NOLOCK) 
																										 ON A.SEQNO = B.WEEKLY_NO
																									  WHERE B.PROJECT_NO ='$project_no'
																										AND A.WEEK_ORD ='$last_week'
																										AND B.THIS_WEEK_RATIO <> 0																										
																									 GROUP BY A.PRS_TEAM
																									  )
																			  AND C.PRS_TEAM NOT IN('CEO') 
																			  AND C.PRS_ID NOT IN(351,352,102,362,22,87,148,15,24,29,48,59,60,71,79,80,164)
																			GROUP BY C.PRS_TEAM
																			ORDER BY PRS_TEAM";																	
																	$rs2= sqlsrv_query($dbConn,$sql2);
																	$col_ratio = "";
																	$col_ratio_arr = "";																	
																															
																	while ($record=sqlsrv_fetch_array($rs2))
																	{				
																		$col_ratio = $record['THIS_WEEK_RATIO'];
																		$col_ratio_arr = $col_ratio_arr . $col_ratio ."##";																													
																		$col_ratio_arr2 = substr($col_ratio_arr,0,-2);
																		
																	}																										
																	$col_ratio_ex = explode("##",$col_ratio_arr2);																															
																
															$sql3="SELECT COUNT(A.PRS_NAME) AS CNT      
																	 FROM DF_WEEKLY A WITH (NOLOCK)
																	INNER JOIN DF_WEEKLY_DETAIL B WITH (NOLOCK) 
																	   ON A.SEQNO = B.WEEKLY_NO  	
																	WHERE B.PROJECT_NO ='$project_no'
																	  AND A.WEEK_ORD ='$last_week'
																	  AND A.PRS_ID NOT IN(351,352,102,362,22,87,148,15,24,29,48,59,60,71,79,80,164)
																	  AND B.THIS_WEEK_RATIO <> 0";																
															$rs3= sqlsrv_query($dbConn,$sql3);	
															while ($record=sqlsrv_fetch_array($rs3))
																	{				
																		$to = $record['CNT'];															
																	}	
																			
														array_push($t_data_lists, array("project_name"=>$project_name, "pm"=>$pm, "start_time"=>$start_time, "end_time"=>$end_time, "to"=>$to, "points"=>$col_ratio_ex));												
														}	
																																													
								$t_data=(array(
													  "lists"=> $t_data_lists								  
												));			
//////////////////////////////////////////// 직원별 가로열 table_header ////////////////////////////////////////////	

								$table_header_lists=array();			
								
									$sql =" SELECT A.PRS_TEAM      
											  FROM DF_PERSON A WITH (NOLOCK) 
											 WHERE A.PRF_ID NOT IN(6)
											   AND A.PRS_ID NOT IN(351,352,102,362,22,87,148,15,24,29,48,59,60,71,79,80,164)
											 GROUP BY A.PRS_TEAM
											 ORDER BY A.PRS_TEAM";						
									$rs = sqlsrv_query($dbConn,$sql);
									
										while ($record=sqlsrv_fetch_array($rs))
										{
											$prs_team = urlencode($record['PRS_TEAM']);		//팀명 																						
											$prs_team_ = str_replace('+',' ',$prs_team); 	//팀명 문자열변환
											
											$members=array();
											
											$label=array();
											$sql1="SELECT PRS_NAME	
														, PRS_ID
													 FROM DF_PERSON WITH (NOLOCK)
													WHERE PRS_TEAM ='$prs_team_'
													  AND PRF_ID NOT IN(6)
													  AND PRS_ID NOT IN(351,352,102,362,22,87,148,15,24,29,48,59,60,71,79,80,164)
													ORDER BY PRS_TEAM, PRS_JOIN_REAL, PRS_POSITION1, PRS_NAME";
											$rs1 = sqlsrv_query($dbConn,$sql1);		
																	
											while ($record=sqlsrv_fetch_array($rs1))
											{
												$col_prs_name = urlencode($record['PRS_NAME']);	 //직원 이름								
												$col_prs_id = $record['PRS_ID'];				 //직원 prs_id		
												
												$sql2 = " SELECT COUNT(A.PRS_ID) AS CNT
														   FROM DF_PERSON A WITH (NOLOCK)
														  INNER JOIN DF_WEEKLY B WITH (NOLOCK)
															 ON A.PRS_ID =B.PRS_ID
														  INNER JOIN DF_WEEKLY_DETAIL C WITH (NOLOCK)
															 ON B.SEQNO = C.WEEKLY_NO	 
														  WHERE A.PRS_ID =$col_prs_id
															AND B.WEEK_ORD='$last_week'
															AND THIS_WEEK_RATIO NOT IN(0)
														  GROUP BY A.PRS_NAME ";
												$rs2 = sqlsrv_query($dbConn,$sql2);		
												$record = sqlsrv_fetch_array($rs2);						
												$point = $record['CNT']; if($point ==""){$point=0;} //개인별 참여한 프로젝트 합												
																	
												array_push($members, array("name"=>$col_prs_name,"point"=>$point));															
											}																													
											
											array_push($table_header_lists, array("label"=>$prs_team, "members"=>$members));																						
										}
																
								$table_header=(array(
													  "lists"=> $table_header_lists
												));				
									
//////////////////////////////////////////// 직원별 세로열  table_data ////////////////////////////////////////////

										$table_data_lists_detail=array();						
													$sql = "SELECT A.SEQNO AS SEQNO
																 , A.PROJECT_NO AS PROJECT_NO
																 , A.TITLE AS TITLE
																 , A.START_DATE AS START_DATE
																 , A.END_DATE AS END_DATE
															  FROM DF_PROJECT A WITH (NOLOCK)
															  LEFT JOIN DF_PROJECT_DETAIL B WITH (NOLOCK)
																ON A.PROJECT_NO = B.PROJECT_NO  	  	 	 
															 WHERE A.STATUS='ING' 
															   AND A.USE_YN='Y'
																OR A.PROJECT_NO IN('DF0000_ETC')
															 GROUP BY A.TITLE, A.START_DATE, A.END_DATE, A.SEQNO, A.PROJECT_NO
															 ORDER BY A.SEQNO DESC";
															 
													$rs = sqlsrv_query($dbConn,$sql);
																						
														while ($record=sqlsrv_fetch_array($rs))
														{	
															//프로젝트 기본정보 뽑아내기
																$project_name = urlencode($record['TITLE']);				//프로젝트 타이틀
																$start_time = date_format($record['START_DATE'], 'Y-m-d');	//시작
																$end_time = date_format($record['END_DATE'], 'Y-m-d');		//종료일			
																$project_no = urlencode($record['PROJECT_NO']); 			//프로젝트 넘버												
																
																if($project_no =="DF0000_ETC"){ //기타업무
																	$start_time="";
																	$end_time="";
																}
															
																//프로젝트 총괄자 뽑아내기
																	$sql1 = "SELECT TOP 1 ISNULL(PRS_NAME,'NONE')AS PRS_NAME FROM DF_PROJECT_DETAIL WHERE PROJECT_NO ='$project_no' AND PART IN('PM')";
																	$rs1 = sqlsrv_query($dbConn,$sql1);
																		if (sqlsrv_has_rows($rs1) == "")
																		{
																				$pm = "";
																		}else{
																			while ($record=sqlsrv_fetch_array($rs1))
																			{
																				$pm = urlencode($record['PRS_NAME']);			//프로젝트 총괄	
																			}																					
																		}
																
																//프로젝트별 참여도 총합 뽑아내기
																$sql2 ="SELECT B.THIS_WEEK_RATIO AS THIS_WEEK_RATIO 	 																		  
																			 , C.PRS_POSITION1
																			 , C.PRS_TEAM
																			 , C.PRS_NAME
																			 , C.PRS_JOIN_REAL
																		  FROM DF_WEEKLY A WITH (NOLOCK)
																		 INNER JOIN DF_WEEKLY_DETAIL B WITH (NOLOCK) 
																			ON A.SEQNO = B.WEEKLY_NO
																		 INNER JOIN DF_PERSON C WITH (NOLOCK)
																		   ON A.PRS_ID = C.PRS_ID		
																		 WHERE B.PROJECT_NO ='$project_no'
																		   AND A.WEEK_ORD ='$last_week'
																		   AND A.PRS_ID NOT IN(351,352,102,362,22,87,148,15,24,29,48,59,60,71,79,80,164)
																	UNION 
																		SELECT 0																		  
																			 , C.PRS_POSITION1
																			 , C.PRS_TEAM
																			 , C.PRS_NAME
																			 , C.PRS_JOIN_REAL
																		  FROM DF_PERSON C WITH (NOLOCK)
																		 WHERE C.PRF_ID NOT IN(6)
																		   AND C.PRS_ID NOT IN(351,352,102,362,22,87,148,15,24,29,48,59,60,71,79,80,164)	  	
																		   AND C.PRS_ID NOT IN(SELECT A.PRS_ID
																							   FROM DF_WEEKLY A WITH (NOLOCK)
																							  INNER JOIN DF_WEEKLY_DETAIL B WITH (NOLOCK) 
																								 ON A.SEQNO = B.WEEKLY_NO  	
																							  WHERE B.PROJECT_NO ='$project_no'
																								AND A.WEEK_ORD ='$last_week') 	   
																		 GROUP BY C.PRS_NAME,C.PRS_JOIN_REAL, C.PRS_POSITION1,C.PRS_TEAM
																		 ORDER BY PRS_TEAM, PRS_JOIN_REAL, PRS_POSITION1, PRS_NAME";
																		 
																	$rs2= sqlsrv_query($dbConn,$sql2);
																	$col_ratio = "";
																	$col_ratio_arr = "";																		
																															
																	while ($record=sqlsrv_fetch_array($rs2))
																	{				
																		$col_ratio = $record['THIS_WEEK_RATIO'] / 100;	
																		$col_ratio_arr = $col_ratio_arr . $col_ratio ."##";																													
																		$col_ratio_arr2 = substr($col_ratio_arr,0,-2);																		
																	}																										
																	$col_ratio_ex = explode("##",$col_ratio_arr2);																															
															$sql3="SELECT COUNT(A.PRS_NAME) AS CNT      
																	 FROM DF_WEEKLY A WITH (NOLOCK)
																	INNER JOIN DF_WEEKLY_DETAIL B WITH (NOLOCK) 
																	   ON A.SEQNO = B.WEEKLY_NO  	
																	WHERE B.PROJECT_NO ='$project_no'
																	  AND A.WEEK_ORD ='$last_week'
																	  AND A.PRS_ID NOT IN(351,352,102,362,22,87,148,15,24,29,48,59,60,71,79,80,164)
																	  AND B.THIS_WEEK_RATIO <> 0";		
															$rs3= sqlsrv_query($dbConn,$sql3);	
															while ($record=sqlsrv_fetch_array($rs3))
																	{				
																		$to = $record['CNT'];															
																	}	
																	
														array_push($table_data_lists_detail, array("project_name"=>$project_name, "pm"=>$pm, "start_time"=>$start_time, "end_time"=>$end_time, "to"=>$to, "points"=>$col_ratio_ex));												
														}	
																																		
									$table_data_lists=(array(
													  "detail"=> $table_data_lists_detail								  
												));											
								
								$table_data=(array(
													  "lists"=> $table_data_lists								  
												));			
												
												
							
//////////////////////////////////////////// 팀프로젝트  가로열 team_header ////////////////////////////////////////////	
/*
						$team_header_lists=array();			
								
									$sql ="SELECT A.PRS_TEAM      
											  FROM DF_PERSON A WITH (NOLOCK) 
											 WHERE A.PRF_ID NOT IN(6)
											   AND A.PRS_ID NOT IN(351,352,102,362,22,87,148,15,24,29,48,59,60,71,79,80,164)
											 GROUP BY A.PRS_TEAM
											 ORDER BY A.PRS_TEAM";						
									$rs = sqlsrv_query($dbConn,$sql);
									
										while ($record=sqlsrv_fetch_array($rs))
										{
											$prs_team = urlencode($record['PRS_TEAM']);		//팀명 																						
											$prs_team_ = str_replace('+',' ',$prs_team); 	//팀명 문자열변환
											
											$members=array();
											
											$label=array();
											$sql1="SELECT PRS_NAME	
														, PRS_ID
													 FROM DF_PERSON WITH (NOLOCK)
													WHERE PRS_TEAM ='$prs_team_'
													  AND PRF_ID NOT IN(6)
													  AND PRS_ID NOT IN(351,352,102,362,22,87,148,15,24,29,48,59,60,71,79,80,164)
													ORDER BY PRS_TEAM, PRS_JOIN_REAL, PRS_POSITION1, PRS_NAME";
											$rs1 = sqlsrv_query($dbConn,$sql1);		
																	
											while ($record=sqlsrv_fetch_array($rs1))
											{
												$col_prs_name = urlencode($record['PRS_NAME']);	 //직원 이름								
												$col_prs_id = $record['PRS_ID'];				 //직원 prs_id		
												
												$sql2 = " SELECT COUNT(A.PRS_ID) AS CNT
														   FROM DF_PERSON A WITH (NOLOCK)
														  INNER JOIN DF_WEEKLY B WITH (NOLOCK)
															 ON A.PRS_ID =B.PRS_ID
														  INNER JOIN DF_WEEKLY_DETAIL C WITH (NOLOCK)
															 ON B.SEQNO = C.WEEKLY_NO	 
														  WHERE A.PRS_ID =$col_prs_id
															AND B.WEEK_ORD='$last_week'
															AND THIS_WEEK_RATIO NOT IN(0)
														  GROUP BY A.PRS_NAME ";
												$rs2 = sqlsrv_query($dbConn,$sql2);		
												$record = sqlsrv_fetch_array($rs2);						
												$point = $record['CNT']; if($point ==""){$point=0;} //개인별 참여한 프로젝트 합												
																	
												array_push($members, array("name"=>$col_prs_name,"point"=>$point));															
											}																													
											
											array_push($team_header_lists, array("label"=>$prs_team, "members"=>$members));																						
										}
																
								$team_header=(array(
													  "lists"=> $team_header_lists
												));								
//////////////////////////////////////////// 팀프로젝트  세로열  team_data ////////////////////////////////////////////
										
										$team_data_lists=array();						
													$sql =" SELECT A.PRS_TEAM      
															  FROM DF_PERSON A WITH (NOLOCK) 
															 WHERE A.PRF_ID NOT IN(6)
															   AND A.PRS_ID NOT IN(351,352,102,362,22,87,148,15,24,29,48,59,60,71,79,80,164)
															 GROUP BY A.PRS_TEAM
															 ORDER BY A.PRS_TEAM";						
													$rs = sqlsrv_query($dbConn,$sql);
													
																						
														while ($record=sqlsrv_fetch_array($rs))
														{
															$prs_team = urlencode($record['PRS_TEAM']);		//팀명 																						
															$prs_team_ = str_replace('+',' ',$prs_team); 	//팀명 문자열변환
															
													        $sql2= "SELECT A.SEQNO AS SEQNO
																		 , A.PROJECT_NO AS PROJECT_NO
																		 , A.TITLE AS TITLE
																		 , A.START_DATE AS START_DATE
																		 , A.END_DATE AS END_DATE
																	  FROM DF_PROJECT A WITH (NOLOCK)
																	  LEFT JOIN DF_PROJECT_DETAIL B WITH (NOLOCK)
																		ON A.PROJECT_NO = B.PROJECT_NO
																	  LEFT JOIN DF_WEEKLY_DETAIL C WITH (NOLOCK)
																		ON A.PROJECT_NO = C.PROJECT_NO	  	
																	  LEFT JOIN DF_WEEKLY D WITH (NOLOCK)
																		ON C.WEEKLY_NO = D.SEQNO	  	 	 
																	 WHERE A.STATUS='ING' 
																	   AND D.PRS_TEAM = '$prs_team_'
																	   AND D.WEEK_ORD='$last_week'
																	   AND A.USE_YN='Y'
																	   AND A.STATUS ='ING'   
																		OR A.PROJECT_NO IN('DF0000_ETC')
																	 GROUP BY A.TITLE, A.START_DATE, A.END_DATE, A.SEQNO, A.PROJECT_NO
																	 ORDER BY A.SEQNO DESC";
															$rs2 = sqlsrv_query($dbConn,$sql2);	
															while ($record=sqlsrv_fetch_array($rs2))
															{
															//프로젝트 기본정보 뽑아내기
																$project_name = urlencode($record['TITLE']);				//프로젝트 타이틀
																$start_time = date_format($record['START_DATE'], 'Y-m-d');	//시작
																$end_time = date_format($record['END_DATE'], 'Y-m-d');		//종료일			
																$project_no = urlencode($record['PROJECT_NO']); 			//프로젝트 넘버												
																
																if($project_no =="DF0000_ETC"){ //기타업무
																	$start_time="";
																	$end_time="";
																}
															
																//프로젝트 총괄자 뽑아내기
																	$sql1 = "SELECT TOP 1 ISNULL(PRS_NAME,'NONE')AS PRS_NAME FROM DF_PROJECT_DETAIL WHERE PROJECT_NO ='$project_no' AND PART IN('PM')";
																	$rs1 = sqlsrv_query($dbConn,$sql1);
																		if (sqlsrv_has_rows($rs1) == "")
																		{
																				$pm = "";
																		}else{
																			while ($record=sqlsrv_fetch_array($rs1))
																			{
																				$pm = urlencode($record['PRS_NAME']);			//프로젝트 총괄	
																			}																					
																		}
																	
																//프로젝트별 참여도 총합 뽑아내기
																 	$sql2="SELECT B.THIS_WEEK_RATIO AS THIS_WEEK_RATIO 	 																		  
																				 , C.PRS_POSITION1
																				 , C.PRS_TEAM
																				 , C.PRS_NAME
																				 , C.PRS_JOIN_REAL
																			  FROM DF_WEEKLY A WITH (NOLOCK)
																			 INNER JOIN DF_WEEKLY_DETAIL B WITH (NOLOCK) 
																				ON A.SEQNO = B.WEEKLY_NO
																			 INNER JOIN DF_PERSON C WITH (NOLOCK)
																				ON A.PRS_ID = C.PRS_ID		
																			 WHERE B.PROJECT_NO ='$project_no'
																				AND B.THIS_WEEK_RATIO <>0
																				AND A.WEEK_ORD='$last_week'
																				AND C.PRS_TEAM = '$prs_team_'																			   
																			   AND A.PRS_ID NOT IN(351,352,102,362,22,87,148,15,24,29,48,59,60,71,79,80,164)      
																			 UNION 
																			   SELECT 0
																					,PRS_POSITION1
																					,PRS_TEAM
																					, PRS_NAME
																					,PRS_JOIN_REAL
																				FROM DF_PERSON 
																			   WHERE PRS_TEAM='ART DIVISION'
																				AND PRF_ID NOT IN(6)
																				AND PRS_NAME NOT IN
																							(SELECT  	 																		  	 
																								  C.PRS_NAME	
																							  FROM DF_WEEKLY A WITH (NOLOCK)
																							 INNER JOIN DF_WEEKLY_DETAIL B WITH (NOLOCK) 
																								ON A.SEQNO = B.WEEKLY_NO
																							 INNER JOIN DF_PERSON C WITH (NOLOCK)
																								ON A.PRS_ID = C.PRS_ID		
																							WHERE B.PROJECT_NO ='$project_no'
																							  AND B.THIS_WEEK_RATIO <>0
																							  AND A.WEEK_ORD='$last_week'
																							  AND C.PRS_TEAM = '$prs_team_'																			   
																							)	
																				AND PRS_ID NOT IN(351,352,102,362,22,87,148,15,24,29,48,59,60,71,79,80,164)  
																			  GROUP BY PRS_POSITION1, PRS_TEAM,PRS_NAME,PRS_JOIN_REAL
																			  ORDER BY PRS_TEAM, PRS_JOIN_REAL, PRS_POSITION1, PRS_NAME";
																	$rs2= sqlsrv_query($dbConn,$sql2);																	
																	$col_ratio = "";
																	$col_ratio_arr = "";																		
																															
																	while ($record=sqlsrv_fetch_array($rs2))
																	{				
																		$col_ratio = $record['THIS_WEEK_RATIO'] / 100;	
																		$col_ratio_arr = $col_ratio_arr . $col_ratio ."##";																													
																		$col_ratio_arr2 = substr($col_ratio_arr,0,-2);																		
																	}																										
																	$col_ratio_ex = explode("##",$col_ratio_arr2);																				  
																
																$sql3="SELECT COUNT(A.PRS_NAME) AS CNT      
																		 FROM DF_WEEKLY A WITH (NOLOCK)
																		INNER JOIN DF_WEEKLY_DETAIL B WITH (NOLOCK) 
																		   ON A.SEQNO = B.WEEKLY_NO  	
																		WHERE B.PROJECT_NO ='$project_no'
																		  AND A.WEEK_ORD ='$last_week'
																		  AND A.PRS_ID NOT IN(351,352,102,362,22,87,148,15,24,29,48,59,60,71,79,80,164)
																		  AND B.THIS_WEEK_RATIO <> 0";		
																$rs3= sqlsrv_query($dbConn,$sql3);	
																while ($record=sqlsrv_fetch_array($rs3))
																		{				
																			$to = $record['CNT'];															
																		}	
																		
																array_push($team_data_lists, array("project_name"=>$project_name, "pm"=>$pm, "start_time"=>$start_time, "end_time"=>$end_time, "to"=>$to, "points"=>$col_ratio_ex));
															}									
														}																												
								$team_data=(array(
													  "lists"=> $team_data_lists
												));			
												
	*/											
								
//result
		
	//array_push($project_member, array("table_header"=>$t_header,"table_data"=> $t_data), array("table_header"=>$table_header,"table_data"=> $table_data),array("table_header"=>$team_header,"table_data"=> $team_data));					
	array_push($project_member, array("table_header"=>$t_header,"table_data"=> $t_data), array("table_header"=>$table_header,"table_data"=> $table_data));					

	$status=200;
	
//TOTAL RESULT
	$output = json_encode(array(
				"status" =>$status,
				"project_member"=>$project_member,
			));	
			
	echo urldecode($output);
	
	$data = iconv("euc-kr","utf-8", urldecode($output));
	file_put_contents('myfile.json', urldecode($data));
	
	
	exit;	
	
?>