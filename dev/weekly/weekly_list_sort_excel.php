<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";

	/** Include PHPExcel */
	require_once dirname(__FILE__) . '/../Classes/PHPExcel.php';

	// 엑셀생성 조건
	$s_date = isset($_REQUEST['s_date'])?$_REQUEST['s_date']:null;	// 시작일
	$e_date = isset($_REQUEST['e_date'])?$_REQUEST['e_date']:null;	// 종료일

	// 엑셀 파일명
	$area_txt = substr($s_date,0,4)."년".substr($s_date,4,2)."월".substr($s_date,-1)."주차-";
	$area_txt .= substr($e_date,0,4)."년".substr($e_date,4,2)."월".substr($e_date,-1)."주차";

	ini_set("memory_limit", -1);
	ini_set('max_execution_time', 14000);

	// PHPExcel 클래스 선언
	$objPHPExcel = new PHPExcel();
	
	// 엑셀시트 속성 설정
	$objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setName('돋움')->setSize(10);
	$objPHPExcel->getActiveSheet()->setTitle("프로젝트 업무비율");

	// 엑셀 문서 속성 설정
	$objPHPExcel->getProperties()->setCreator('Maarten Balliauw')
								 ->setLastModifiedBy('Maarten Balliauw')
								 ->setTitle('Office 2007 XLSX Test Document')
								 ->setSubject('Office 2007 XLSX Test Document')
								 ->setDescription('Test document for Office 2007 XLSX, generated using PHP classes.');

	// 엑셀 워크시트 생성
	$objPHPExcel->setActiveSheetIndex(0);
	$objPHPExcel->getActiveSheet()->setCellValue('A1', '번호')
	                              ->setCellValue('B1', '기간')
	                              ->setCellValue('C1', '성명')
	                              ->setCellValue('D1', '프로젝트')
  	                              ->setCellValue('E1', '참여비율(%)');

	// 엑셀 데이터 배열
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
		if($record['PROJECT_NO'] == "DF0000_ETC") $project_name = "기타업무";
		else $project_name = iconv('euc-kr','utf-8',$record['PROJECT_NAME']);

		$tmpArray[] = array
						(
							'week_area'=>$record['WEEK_AREA'],
							'name'=>iconv('euc-kr','utf-8',$record['PRS_NAME']),
							'project_name'=>$project_name,
							'this_ratio'=>$record['THIS_WEEK_RATIO']
						);
	}

	$tot_cnt = count($tmpArray);

	// 위 배열 $tmpArray 를 A2 부터 차례대로 씀
	//$objPHPExcel->getActiveSheet()->fromArray($tmpArray, NULL, 'A2');

	$i = 2;
	foreach($tmpArray as $val) {
		$objPHPExcel->getActiveSheet()->getCell('A'.$i)->setValueExplicit($tot_cnt);
		$objPHPExcel->getActiveSheet()->getCell('B'.$i)->setValueExplicit($val['week_area']);
		$objPHPExcel->getActiveSheet()->getCell('C'.$i)->setValueExplicit($val['name']);
		$objPHPExcel->getActiveSheet()->getCell('D'.$i)->setValueExplicit($val['project_name']);
		$objPHPExcel->getActiveSheet()->getCell('E'.$i)->setValueExplicit($val['this_ratio']);

		$i++;
		$tot_cnt--;
	}

	// A1 에서 D1 까지를 Bold 처리 함
	//$objPHPExcel->getActiveSheet()->getStyle('A1:E1')->getFont()->setBold(true);

	// A1 에서 D1 까지의 스타일을 정의 함
	$objPHPExcel->getActiveSheet()->getStyle('A1:E1')->applyFromArray(
		array('fill'   =>array(
								'type'=>PHPExcel_Style_Fill::FILL_SOLID,
								'color'=>array('rgb'=>'c0c0c0')
							),
			  'borders'=>array(
								'bottom'=>array('style'=>PHPExcel_Style_Border::BORDER_THIN),
								'right'=>array('style'=>PHPExcel_Style_Border::BORDER_MEDIUM)
							)
		 )
	);

	// 각각의 셀 크기를 지정함
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(60);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);

	$objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(15);

	// 영역을 지정하여 가로 세로의 정렬을 정의함
	$objPHPExcel->getActiveSheet()->getStyle('A1:H1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	//$objPHPExcel->getActiveSheet()->getStyle('A1:E'.(count($tmpArray)+1))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

    // 엑셀파일 이름
	$fileName = "df_weekly_project(".$area_txt.")_list.xls";
	//$fileName = iconv("UTF-8", "EUC-KR", "한글 파일명");

	// 위에서 쓴 엑셀을 저장하고 다운로드 합니다.
 	header('Content-Type: application/vnd.ms-excel;charset=utf-8');
	header('Content-type: application/x-msexcel;charset=utf-8');
	header('Content-Disposition: attachment;filename="'.$fileName.'"');
	header('Cache-Control: max-age=0');
 
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');

	//echo "<xmp>";
	//print_r($EventArr);
	//echo "</xmp>";
?>