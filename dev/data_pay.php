<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
//step 1	- Query????? setp1 ???? ???? ???? ?? ?????????? ?? ?????? ????

/*
UPDATE DF_CHECKTIME SET PAY1 = 'N', PAY2 = 'N', PAY3 = 'N', PAY4 = 'N'

UPDATE DF_CHECKTIME SET PAY1 = 'Y' WHERE OVERTIME > '0400' AND REPLACE(DATE,'-','') IN (SELECT DATE FROM HOLIDAY WHERE DATEKIND IN ('LAW','FIN'))
UPDATE DF_CHECKTIME SET PAY2 = 'Y' WHERE OVERTIME > '0600' AND REPLACE(DATE,'-','') IN (SELECT DATE FROM HOLIDAY WHERE DATEKIND IN ('LAW','FIN'))
UPDATE DF_CHECKTIME SET PAY3 = 'Y' WHERE OVERTIME > '0700' AND REPLACE(DATE,'-','') IN (SELECT DATE FROM HOLIDAY WHERE DATEKIND IN ('LAW','FIN'))
UPDATE DF_CHECKTIME SET PAY4 = 'Y' WHERE OVERTIME > '0500' AND SUBSTRING(CHECKTIME2,9,4) >= '2400' AND SUBSTRING(CHECKTIME2,9,4) <= '3000' AND REPLACE(DATE,'-','') IN (SELECT DATE FROM HOLIDAY WHERE DATEKIND = 'BIZ')

UPDATE DF_CHECKTIME SET PAY2 = 'Y' WHERE OVERTIME > '0300' AND REPLACE(DATE,'-','') IN (SELECT DATE FROM HOLIDAY WHERE DATEKIND = 'BIZ')
UPDATE DF_CHECKTIME SET PAY3 = 'Y' WHERE OVERTIME > '0400' AND REPLACE(DATE,'-','') IN (SELECT DATE FROM HOLIDAY WHERE DATEKIND = 'BIZ')
UPDATE DF_CHECKTIME SET PAY4 = 'Y' WHERE OVERTIME > '0400' AND SUBSTRING(CHECKTIME2,9,4) >= '2400' AND SUBSTRING(CHECKTIME2,9,4) <= '3000' AND REPLACE(DATE,'-','') IN (SELECT DATE FROM HOLIDAY WHERE DATEKIND = 'BIZ')
*/
?>

<?
//step 2
// ???? ???
// ???? ???
	$sql = "SELECT 
				T.SEQNO, T.PRS_ID, T.TOTALTIME, T.OVERTIME, T.CHECKTIME2, T.PREV_DATEKIND, T.PREV_OVERTIME 
			FROM 
			(
				SELECT 
					A.SEQNO, A.PRS_ID, A.TOTALTIME, A.OVERTIME, A.CHECKTIME2, 
					(SELECT DATEKIND FROM HOLIDAY WHERE DATE = SUBSTRING(REPLACE(CONVERT(char(10),DATEADD(d,-1,A.DATE),120),'-',''),1,8)) AS PREV_DATEKIND, 
					(SELECT OVERTIME FROM DF_CHECKTIME WHERE DATE = DATEADD(d,-1,CONVERT(char(10),A.DATE,120)) AND PRS_ID = A.PRS_ID) AS PREV_OVERTIME
				FROM 
					DF_CHECKTIME A WITH(NOLOCK) 
				WHERE 
					REPLACE(A.DATE,'-','') IN (SELECT DATE FROM HOLIDAY WHERE DATEKIND IN ('LAW','FIN')) AND A.DATE > '2014-01-31'
			) T
			ORDER BY 
				T.SEQNO";
	$rs = sqlsrv_query($dbConn,$sql);
echo $sql;
	while ($record = sqlsrv_fetch_array($rs))
	{
		$seqno = $record['SEQNO'];
		$prs_id = $record['PRS_ID'];
		$totaltime = $record['TOTALTIME'];
		$overtime = $record['OVERTIME'];
		$checktime2 = $record['CHECKTIME2'];
		$prev_datekind = $record['PREV_DATEKIND'];
		$prev_overtime = $record['PREV_OVERTIME'];

		$pay1 = "N";
		$pay2 = "N";
		$pay3 = "N";
		$pay4 = "N";

		if ($overtime >= "0400") { $pay1 = "Y"; }
		if ($overtime >= "0600") { $pay2 = "Y"; }
		if ($overtime >= "0700") { $pay3 = "Y"; }
		if ($overtime >= "0500" && substr($checktime22,8,4) >= "2400" && substr($checktime2,8,4) <= "3000") { $pay4 = "Y"; }

		if ($prev_datekind == "BIZ")
		{
			if ($prev_overtime >= "0700") { $shift = "0300"; }
			else if ($prev_overtime >= "0600" && $prev_overtime < "0700" ) { $shift = "0200"; }
			else if ($prev_overtime >= "0500" && $prev_overtime < "0600" ) { $shift = "0100"; }
			else { $shift = "0000"; }
		}
		else
		{
			if ($prev_overtime >= "0900") { $shift = "0300"; }
			else if ($prev_overtime >= "0800" && $prev_overtime < "0900" ) { $shift = "0200"; }
			else if ($prev_overtime >= "0700" && $prev_overtime < "0800" ) { $shift = "0100"; }
			else { $shift = "0000"; }
		}

		$imsi = $totaltime + $shift;
		if (strlen($imsi) == 3) { $imsi = "0". $imsi; }

		if ($imsi >= "0400") { $pay1 = "Y"; }
		if ($imsi >= "0600") { $pay2 = "Y"; }
		if ($imsi >= "0700") { $pay3 = "Y"; }
		if ($imsi >= "0500" && substr($checktime22,8,4) >= "2400" && substr($checktime2,8,4) <= "3000") { $pay4 = "Y"; }
		
		$upSQL = "UPDATE DF_CHECKTIME SET PAY1 = '$pay1', PAY2 = '$pay2', PAY3 = '$pay3', PAY4 = '$pay4' WHERE SEQNO = $seqno";
		$rs = sqlsrv_query($dbConn,$sql);
	}
?>

<?
//step 3
/*
*/
?>
