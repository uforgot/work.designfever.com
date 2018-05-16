<?
/*
	$year : ���³�
	$month : ��
	$day : ��
	$yun : ������ ��� 1 

	$date=lun2sol($year,$month,$day,$yun);
	
	$date=lun2sol(1978,6,3,0);
	�̷� ���·� ����
	$date[year] ��ȯ�� ��� ��
	$date[month] ��
	$date[day] ��
	$date[yun] ������ ��� 1
	�̷��� ���� ���ɴϴ�......
*/

	function lun2sol($year,$month,$day,$yun){
			$min_year=1900;
			$max_year=2043;
			$scnt=0; //������� 1901�� 1�� 1�Ϻ��� ��������� ��¥...
			$lcnt=0; //�������� ��������� ��¥����� ���� ����
			$sdata=array(0,31,28,31,30,31,30,31,31,30,31,30,31);
			$ldata=array(        //���� ������ 1901�� 1�� ���� 1:29 2:30 3:29+29���� 4:29+30���� 5:30+29���� 6:30+30����
			  /*1900*/   "1", "2", "1", "1", "2", "1", "2", "5", "2", "2", "1", "2",
		  /*1901*/   "1", "2", "1", "1", "2", "1", "2", "1", "2", "2", "2", "1",
					 "2", "1", "2", "1", "1", "2", "1", "2", "1", "2", "2", "2",
					 "1", "2", "1", "2", "3", "2", "1", "1", "2", "2", "1", "2",
					 "2", "2", "1", "2", "1", "1", "2", "1", "1", "2", "2", "1",
					 "2", "2", "1", "2", "2", "1", "1", "2", "1", "2", "1", "2",
					 "1", "2", "2", "4", "1", "2", "1", "2", "1", "2", "1", "2",
					 "1", "2", "1", "2", "1", "2", "2", "1", "2", "1", "2", "1",
					 "2", "1", "1", "2", "2", "1", "2", "1", "2", "2", "1", "2",
					 "1", "5", "1", "2", "1", "2", "1", "2", "2", "2", "1", "2",
					 "1", "2", "1", "1", "2", "1", "2", "1", "2", "2", "2", "1",

		  /*1911*/   "2", "1", "2", "1", "1", "5", "1", "2", "2", "1", "2", "2",
					 "2", "1", "2", "1", "1", "2", "1", "1", "2", "2", "1", "2",
					 "2", "2", "1", "2", "1", "1", "2", "1", "1", "2", "1", "2",
					 "2", "2", "1", "2", "5", "1", "2", "1", "2", "1", "1", "2",
					 "2", "1", "2", "2", "1", "2", "1", "2", "1", "2", "1", "2",
					 "1", "2", "1", "2", "1", "2", "2", "1", "2", "1", "2", "1",
					 "2", "3", "2", "1", "2", "2", "1", "2", "2", "1", "2", "1",
					 "2", "1", "1", "2", "1", "2", "1", "2", "2", "2", "1", "2",
					 "1", "2", "1", "1", "2", "1", "5", "2", "2", "1", "2", "2",
					 "1", "2", "1", "1", "2", "1", "1", "2", "2", "1", "2", "2",

		  /*1921*/   "2", "1", "2", "1", "1", "2", "1", "1", "2", "1", "2", "2",
					 "2", "1", "2", "2", "3", "2", "1", "1", "2", "1", "2", "2",
					 "1", "2", "2", "1", "2", "1", "2", "1", "2", "1", "1", "2",
					 "2", "1", "2", "1", "2", "2", "1", "2", "1", "2", "1", "1",
					 "2", "1", "2", "5", "2", "1", "2", "2", "1", "2", "1", "2",
					 "1", "1", "2", "1", "2", "1", "2", "2", "1", "2", "2", "1",
					 "2", "1", "1", "2", "1", "2", "1", "2", "2", "1", "2", "2",
					 "1", "5", "1", "2", "1", "1", "2", "2", "1", "2", "2", "2",
					 "1", "2", "1", "1", "2", "1", "1", "2", "1", "2", "2", "2",
					 "1", "2", "2", "1", "1", "5", "1", "2", "1", "2", "2", "1",

		  /*1931*/   "2", "2", "2", "1", "1", "2", "1", "1", "2", "1", "2", "1",
					 "2", "2", "2", "1", "2", "1", "2", "1", "1", "2", "1", "2",
					 "1", "2", "2", "1", "6", "1", "2", "1", "2", "1", "1", "2",
					 "1", "2", "1", "2", "2", "1", "2", "2", "1", "2", "1", "2",
					 "1", "1", "2", "1", "2", "1", "2", "2", "1", "2", "2", "1",
					 "2", "1", "4", "1", "2", "1", "2", "1", "2", "2", "2", "1",
					 "2", "1", "1", "2", "1", "1", "2", "1", "2", "2", "2", "1",
					 "2", "2", "1", "1", "2", "1", "4", "1", "2", "2", "1", "2",
					 "2", "2", "1", "1", "2", "1", "1", "2", "1", "2", "1", "2",
					 "2", "2", "1", "2", "1", "2", "1", "1", "2", "1", "2", "1",

		  /*1941*/   "2", "2", "1", "2", "2", "4", "1", "1", "2", "1", "2", "1",
					 "2", "1", "2", "2", "1", "2", "2", "1", "2", "1", "1", "2",
					 "1", "2", "1", "2", "1", "2", "2", "1", "2", "2", "1", "2",
					 "1", "1", "2", "4", "1", "2", "1", "2", "2", "1", "2", "2",
					 "1", "1", "2", "1", "1", "2", "1", "2", "2", "2", "1", "2",
					 "2", "1", "1", "2", "1", "1", "2", "1", "2", "2", "1", "2",
					 "2", "5", "1", "2", "1", "1", "2", "1", "2", "1", "2", "2",
					 "2", "1", "2", "1", "2", "1", "1", "2", "1", "2", "1", "2",
					 "2", "2", "1", "2", "1", "2", "3", "2", "1", "2", "1", "2",
					 "2", "1", "2", "2", "1", "2", "1", "1", "2", "1", "2", "1",

		  /*1951*/   "2", "1", "2", "2", "1", "2", "1", "2", "1", "2", "1", "2",
					 "1", "2", "1", "2", "4", "2", "1", "2", "1", "2", "1", "2",
					 "1", "2", "1", "1", "2", "2", "1", "2", "2", "1", "2", "2",
					 "1", "1", "2", "1", "1", "2", "1", "2", "2", "1", "2", "2",
					 "2", "1", "4", "1", "1", "2", "1", "2", "1", "2", "2", "2",
					 "1", "2", "1", "2", "1", "1", "2", "1", "2", "1", "2", "2",
					 "2", "1", "2", "1", "2", "1", "1", "5", "2", "1", "2", "2",
					 "1", "2", "2", "1", "2", "1", "1", "2", "1", "2", "1", "2",
					 "1", "2", "2", "1", "2", "1", "2", "1", "2", "1", "2", "1",
					 "2", "1", "2", "1", "2", "5", "2", "1", "2", "1", "2", "1",

		  /*1961*/   "2", "1", "2", "1", "2", "1", "2", "2", "1", "2", "1", "2",
					 "1", "2", "1", "1", "2", "1", "2", "2", "1", "2", "2", "1",
					 "2", "1", "2", "3", "2", "1", "2", "1", "2", "2", "2", "1",
					 "2", "1", "2", "1", "1", "2", "1", "2", "1", "2", "2", "2",
					 "1", "2", "1", "2", "1", "1", "2", "1", "1", "2", "2", "1",
					 "2", "2", "5", "2", "1", "1", "2", "1", "1", "2", "2", "1",
					 "2", "2", "1", "2", "2", "1", "1", "2", "1", "2", "1", "2",
					 "1", "2", "2", "1", "2", "1", "5", "2", "1", "2", "1", "2",
					 "1", "2", "1", "2", "1", "2", "2", "1", "2", "1", "2", "1",
					 "2", "1", "1", "2", "2", "1", "2", "1", "2", "2", "1", "2",

		  /*1971*/   "1", "2", "1", "1", "5", "2", "1", "2", "2", "2", "1", "2",
					 "1", "2", "1", "1", "2", "1", "2", "1", "2", "2", "2", "1",
					 "2", "1", "2", "1", "1", "2", "1", "1", "2", "2", "2", "1",
					 "2", "2", "1", "5", "1", "2", "1", "1", "2", "2", "1", "2",
					 "2", "2", "1", "2", "1", "1", "2", "1", "1", "2", "1", "2",
					 "2", "2", "1", "2", "1", "2", "1", "5", "2", "1", "1", "2",
					 "2", "1", "2", "2", "1", "2", "1", "2", "1", "2", "1", "1",
					 "2", "2", "1", "2", "1", "2", "2", "1", "2", "1", "2", "1",
					 "2", "1", "1", "2", "1", "6", "1", "2", "2", "1", "2", "1",
					 "2", "1", "1", "2", "1", "2", "1", "2", "2", "1", "2", "2",

		  /*1981*/   "1", "2", "1", "1", "2", "1", "1", "2", "2", "1", "2", "2",
					 "2", "1", "2", "3", "2", "1", "1", "2", "2", "1", "2", "2",
					 "2", "1", "2", "1", "1", "2", "1", "1", "2", "1", "2", "2",
					 "2", "1", "2", "2", "1", "1", "2", "1", "1", "5", "2", "2",
					 "1", "2", "2", "1", "2", "1", "2", "1", "1", "2", "1", "2",
					 "1", "2", "2", "1", "2", "2", "1", "2", "1", "2", "1", "1",
					 "2", "1", "2", "2", "1", "5", "2", "2", "1", "2", "1", "2",
					 "1", "1", "2", "1", "2", "1", "2", "2", "1", "2", "2", "1",
					 "2", "1", "1", "2", "1", "2", "1", "2", "2", "1", "2", "2",
					 "1", "2", "1", "1", "5", "1", "2", "1", "2", "2", "2", "2",

		  /*1991*/   "1", "2", "1", "1", "2", "1", "1", "2", "1", "2", "2", "2",
					 "1", "2", "2", "1", "1", "2", "1", "1", "2", "1", "2", "2",
					 "1", "2", "5", "2", "1", "2", "1", "1", "2", "1", "2", "1",
					 "2", "2", "2", "1", "2", "1", "2", "1", "1", "2", "1", "2",
					 "1", "2", "2", "1", "2", "2", "1", "5", "2", "1", "1", "2",
					 "1", "2", "1", "2", "2", "1", "2", "1", "2", "2", "1", "2",
					 "1", "1", "2", "1", "2", "1", "2", "2", "1", "2", "2", "1",
					 "2", "1", "1", "2", "3", "2", "2", "1", "2", "2", "2", "1",
					 "2", "1", "1", "2", "1", "1", "2", "1", "2", "2", "2", "1",
					 "2", "2", "1", "1", "2", "1", "1", "2", "1", "2", "2", "1",

		  /*2001*/   "2", "2", "2", "3", "2", "1", "1", "2", "1", "2", "1", "2",
					 "2", "2", "1", "2", "1", "2", "1", "1", "2", "1", "2", "1",
					 "2", "2", "1", "2", "2", "1", "2", "1", "1", "2", "1", "2",
					 "1", "5", "2", "2", "1", "2", "1", "2", "2", "1", "1", "2",
					 "1", "2", "1", "2", "1", "2", "2", "1", "2", "2", "1", "2",
					 "1", "1", "2", "1", "2", "1", "5", "2", "2", "1", "2", "2",
					 "1", "1", "2", "1", "1", "2", "1", "2", "2", "2", "1", "2",
					 "2", "1", "1", "2", "1", "1", "2", "1", "2", "2", "1", "2",
					 "2", "2", "1", "1", "5", "1", "2", "1", "2", "1", "2", "2",
					 "2", "1", "2", "1", "2", "1", "1", "2", "1", "2", "1", "2",

		  /*2011*/   "2", "1", "2", "2", "1", "2", "1", "1", "2", "1", "2", "1",
					 "2", "1", "6", "2", "1", "2", "1", "1", "2", "1", "2", "1",
					 "2", "1", "2", "2", "1", "2", "1", "2", "1", "2", "1", "2",
					 "1", "2", "1", "2", "1", "2", "1", "2", "5", "2", "1", "2",
					 "1", "2", "1", "1", "2", "1", "2", "2", "2", "1", "2", "2",
					 "1", "1", "2", "1", "1", "2", "1", "2", "2", "1", "2", "2",
					 "2", "1", "1", "2", "3", "2", "1", "2", "1", "2", "2", "2",
					 "1", "2", "1", "2", "1", "1", "2", "1", "2", "1", "2", "2",
					 "2", "1", "2", "1", "2", "1", "1", "2", "1", "2", "1", "2",
					 "2", "1", "2", "5", "2", "1", "1", "2", "1", "2", "1", "2",

		  /*2021*/   "1", "2", "2", "1", "2", "1", "2", "1", "2", "1", "2", "1",
					 "2", "1", "2", "1", "2", "2", "1", "2", "1", "2", "1", "2",
					 "1", "5", "2", "1", "2", "1", "2", "2", "1", "2", "1", "2",
					 "1", "2", "1", "1", "2", "1", "2", "2", "1", "2", "2", "1",
					 "2", "1", "2", "1", "1", "5", "2", "1", "2", "2", "2", "1",
					 "2", "1", "2", "1", "1", "2", "1", "2", "1", "2", "2", "2",
					 "1", "2", "1", "2", "1", "1", "2", "1", "1", "2", "2", "2",
					 "1", "2", "2", "1", "5", "1", "2", "1", "1", "2", "2", "1",
					 "2", "2", "1", "2", "2", "1", "1", "2", "1", "1", "2", "2",
					 "1", "2", "1", "2", "2", "1", "2", "1", "2", "1", "2", "1",

		  /*2031*/   "2", "1", "5", "2", "1", "2", "2", "1", "2", "1", "2", "1",
					 "2", "1", "1", "2", "1", "2", "2", "1", "2", "2", "1", "2",
					 "1", "2", "1", "1", "2", "1", "5", "2", "2", "2", "1", "2",
					 "1", "2", "1", "1", "2", "1", "2", "1", "2", "2", "2", "1",
					 "2", "1", "2", "1", "1", "2", "1", "1", "2", "2", "1", "2",
					 "2", "2", "1", "2", "1", "4", "1", "1", "2", "1", "2", "2",
					 "2", "2", "1", "2", "1", "1", "2", "1", "1", "2", "1", "2",
					 "2", "2", "1", "2", "1", "2", "1", "2", "1", "1", "2", "1",
					 "2", "2", "1", "2", "5", "2", "1", "2", "1", "2", "1", "1",
					 "2", "1", "2", "2", "1", "2", "2", "1", "2", "1", "2", "1",

		  /*2041*/   "2", "1", "1", "2", "1", "2", "2", "1", "2", "2", "1", "2",
					 "1", "5", "1", "2", "1", "2", "1", "2", "2", "2", "1", "2",
					 "1", "2", "1", "1", "2", "1", "1", "2", "2", "1", "2", "2");

	//������ ���
			if($yun){
					if($ldata[($year-$min_year)*12+$month-1]=="3") $lcnt+=29; 
					else if($ldata[($year-$min_year)*12+$month-1]=="4") $lcnt+=29; 
					else if($ldata[($year-$min_year)*12+$month-1]=="5") $lcnt+=30; 
					else if($ldata[($year-$min_year)*12+$month-1]=="6") $lcnt+=30; 
					$date[yun]=true;
			}else{
					$date[yun]=false;
			}

	//�������� �Էµ� ��¥������ �� �� ���
			
			for($i=0;$i<$year-$min_year;$i++){
					for($j=0;$j<12;$j++){
							if($ldata[$i*12+$j]=="1") $lcnt+=29; 
							else if($ldata[$i*12+$j]=="2") $lcnt+=30; 
							else if($ldata[$i*12+$j]=="3") $lcnt+=58; 
							else if($ldata[$i*12+$j]=="4") $lcnt+=59; 
							else if($ldata[$i*12+$j]=="5") $lcnt+=59; 
							else if($ldata[$i*12+$j]=="6") $lcnt+=60; 
					}
			}
			for($i=0;$i<$month-1;$i++){
					if($ldata[($year-$min_year)*12+$i]=="1") $lcnt+=29; 
					else if($ldata[($year-$min_year)*12+$i]=="2") $lcnt+=30; 
					else if($ldata[($year-$min_year)*12+$i]=="3") $lcnt+=58; 
					else if($ldata[($year-$min_year)*12+$i]=="4") $lcnt+=59; 
					else if($ldata[($year-$min_year)*12+$i]=="5") $lcnt+=59; 
					else if($ldata[($year-$min_year)*12+$i]=="6") $lcnt+=60; 
			}
			$lcnt+=$day+30;
			$this_year=1900;
			$this_month=1;
			$this_cnt=0;
			while($this_cnt!=$lcnt){
					if($this_month==13){
							$this_month=1;
							$this_year+=1;
					}
					if($this_month==1){
							if($this_year%1000==0){
									$sdata[2]=29;
							}else if($this_year%100==0){
									$sdata[2]=28;
							}else if($this_year%4==0){
									$sdata[2]=29;
							}else{
									$sdata[2]=28;
							}                        
					}
					if($lcnt-$this_cnt>$sdata[$this_month]){
							$this_cnt+=$sdata[$this_month];
							$this_month++;
					}else{
							$date[year]=$this_year;
							$date[month]=$this_month;
							$date[day]=$lcnt-$this_cnt;
							$this_cnt=$lcnt;
					}
			}
			return $date;
	}
	//��� 70�� 1��1���� 69�� 11�� 24��
	//��1900.1.31=��1900.1.1
?>