<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";

	/** Error reporting */
	error_reporting(E_ALL);
	ini_set('display_errors', TRUE);
	ini_set('display_startup_errors', TRUE);
	date_default_timezone_set('Europe/London');

	if (PHP_SAPI == 'cli')
		die('This example should only be run from a Web Browser');

	/** Include PHPExcel */
	require_once dirname(__FILE__) . '/../Classes/PHPExcel.php';

	$year = "2017"; 
	$project = isset($_REQUEST['project']) ? $_REQUEST['project'] : null;

	$sql = "SELECT 
				TITLE
			FROM 
				DF_PROJECT
			WHERE 
				PROJECT_NO = '". $project ."'";
	$rs = sqlsrv_query($dbConn,$sql);
	$record = sqlsrv_fetch_array($rs);

	$project_title = $record['TITLE'];

	// Create new PHPExcel object
	$objPHPExcel = new PHPExcel();
	$sheet = $objPHPExcel->getActiveSheet();
	$sheet->getDefaultStyle()->getFont()->setName('돋움')->setSize(10);
	 
	$sheet->getStyle("A")->getFont()->setBold(true); 
	$sheet->getStyle("A")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); 
	$sheet->getStyle("1")->getFont()->setBold(true); 
	$sheet->getStyle("1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); 

	// Set document properties
	$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
								 ->setLastModifiedBy("Maarten Balliauw")
								 ->setTitle("Office 2007 XLSX Test Document")
								 ->setSubject("Office 2007 XLSX Test Document")
								 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
								 ->setKeywords("office 2007 openxml php")
								 ->setCategory("Test result file");

	$str_arr = "A#B#C#D#E#F#G#H#I#J#K#L#M#N#O#P#Q#R#S#T#U#V#W#X#Y#Z#AA#AB#AC#AD#AE#AF#AG#AH#AI#AJ#AK#AL#AM#AN#AO#AP#AQ#AR#AS#AT#AU#AV#AW#AX#AY#AZ";
	$str_ex = explode("#",$str_arr);

	$id_arr = "";
	$name_arr = "";
	$sql = "SELECT 
				DISTINCT PRS_NAME, PRS_ID
			FROM 
				DF_PROJECT_DETAIL 
			WHERE 
				PROJECT_NO = '". $project ."'";
	$rs = sqlsrv_query($dbConn,$sql);


	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A1','');

	$l = 0;
	while ($record = sqlsrv_fetch_array($rs))
	{
		$m = $l + 1;
//		$n = $l + 2;
		$name = iconv('EUC-KR','UTF-8',$record['PRS_NAME']);
		$id = $record['PRS_ID'];
		
		$m_col = $str_ex[$m] ."1";
//		$n_col = $str_ex[$n] ."1";

		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue($m_col,$name);
//					->setCellValue($n_col,'참여비율');

//		$l = $n;
		$l = $m;

		$name_arr = $name_arr . $name ."##";
		$id_arr = $id_arr . $id . "##";
	}

	$weekly_arr = "";
	$sql = "SELECT DISTINCT WEEK_ORD FROM DF_WEEKLY WHERE WEEK_ORD LIKE '". $year ."%'";
	$rs = sqlsrv_query($dbConn,$sql);

	while ($record = sqlsrv_fetch_array($rs))
	{
		$weekly_arr = $weekly_arr . $record['WEEK_ORD'] . "##";
	}

	$weekly_ex = explode("##",$weekly_arr);

	for ($i=0; $i<sizeof($weekly_ex); $i++)
	{
		if ($weekly_ex[$i] != "")
		{
			$j = $i + 2;
			$week = number_format(substr($weekly_ex[$i],4,2),0).'월 '.substr($weekly_ex[$i],6,1) .'주';

			$week_col = "A". $j;
			$objPHPExcel->setActiveSheetIndex(0)
						->setCellValue($week_col,$week);

			$sql = "SELECT
						T.PRS_NAME, T.THIS_WEEK_CONTENT, T.THIS_WEEK_RATIO
					FROM
						(
							SELECT 
								A.PRS_NAME, B.THIS_WEEK_CONTENT, ISNULL(B.THIS_WEEK_RATIO,0) AS THIS_WEEK_RATIO
							FROM
								(
									SELECT PRS_NAME FROM DF_PROJECT_DETAIL WHERE PROJECT_NO = '". $project ."'

								) A
								LEFT OUTER JOIN
								(
									SELECT 
										M.PRS_ID, M.PRS_NAME, N.WEEKLY_NO, N.THIS_WEEK_CONTENT, ISNULL(N.THIS_WEEK_RATIO ,0) AS THIS_WEEK_RATIO, M.WEEK_ORD
									FROM 
										DF_WEEKLY M INNER JOIN DF_WEEKLY_DETAIL N
									ON
										M.SEQNO = N.WEEKLY_NO
									WHERE
										N.PROJECT_NO = '". $project ."' AND M.WEEK_ORD = '". $weekly_ex[$i] ."'
								) B
							ON 
								A.PRS_NAME = B.PRS_NAME
						) T
					ORDER BY 
						T.PRS_NAME";
			$rs = sqlsrv_query($dbConn,$sql);

			$l = 0;
			while ($record = sqlsrv_fetch_array($rs))
			{
				$m = $l + 1;
//				$n = $l + 2;
				$my_content = iconv('EUC-KR','UTF-8//IGNORE',$record['THIS_WEEK_CONTENT']);
				//$my_content = iconv("UTF-8", "ISO-8859-1//IGNORE",$record['THIS_WEEK_CONTENT']);
				$my_ratio = $record['THIS_WEEK_RATIO'];
				if ($my_ratio > 0) { $my_ratio = $my_ratio ."%"; } else { $my_ratio = ""; }
				
				$m_col = $str_ex[$m] . $j;
//				$n_col = $str_ex[$n] . $j;

				$objPHPExcel->setActiveSheetIndex(0)
							->setCellValue($m_col,$my_ratio);
//							->setCellValue($m_col,$my_content)
//							->setCellValue($n_col,$my_ratio);

//				$l = $n;
				$l = $m;
			}
		}
	}

	// Rename worksheet
	$objPHPExcel->getActiveSheet()->setTitle($project);

	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);


	// Redirect output to a client’s web browser (Excel5)
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename=['. $project .'] '. $project_title .'.xls');
//	header("Content-Type:text/html; charset=euc-kr");
//	header("Content-Encoding:euc-kr");
	header('Cache-Control: max-age=0');
	// If you're serving to IE 9, then the following may be needed
	header('Cache-Control: max-age=1');

	// If you're serving to IE over SSL, then the following may be needed
	header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
	header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
	header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
	header ('Pragma: public'); // HTTP/1.0

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');
	exit;
?>