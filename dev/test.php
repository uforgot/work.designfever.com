


<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>DESIGN FEVER INTRANET</title>
<meta http-equiv="Content-Type" content="text/html" charset="euc-kr"> 
<meta name="viewport" content="width=device-width, initial-scale=1">    
<link rel="stylesheet" href="/assets/css/work.df.css">

<script src="/assets/js/jquery-1.11.0.min.js"></script>
<script defer src="/assets/js/all.js"></script>
<script defer src="/assets/js/bulma-calendar.min.js"></script>

<!--������ �����ִ� css �� ��ũ��Ʈ Ȯ�� �� ���￹��-->
<style type="text/css">
	#ui-datepicker-div { padding:0 30px 0 30px; border:1px solid #000; }
	.ui-widget-content { border:0px; }
	.ui-widget-header { border:0px; }
	.ui-icon, .ui-widget-content .ui-icon { background-image: url("/img/ui-icons_222222_256x240.png"); }
	.ui-widget-header .ui-icon { background-image: url("/img/ui-icons_222222_256x240.png"); }
	.ui-state-hover .ui-icon, .ui-state-focus .ui-icon { background-image: url("/img/ui-icons_454545_256x240.png"); }
	.ui-state-active .ui-icon { background-image: url("/img/ui-icons_454545_256x240.png"); }
	.ui-datepicker-header { margin-top:9px; height:35px; }
	.ui-datepicker-calendar thead { border-top:1px solid #000; border-bottom:1px solid #000; }
	.ui-datepicker-calendar tbody { border-bottom:12px solid #fff; }
	.ui-datepicker-calendar tbody tr:first-child td { padding-top:24px; }
	.ui-datepicker-calendar tbody a { text-align:center; }
	.ui-datepicker-calendar tbody .ui-datepicker-week-end:nth-child(1) a { color:#eb6100; }
</style>
<script src="/assets/js/jquery.min.js"></script>
<script src="/assets/js/jquery.easing.1.3.js"></script>
<script src="/assets/js/jquery-ui.js"></script>
<script src="/assets/js/modernizr.custom.72169.js"></script>
<script src="/assets/js/jquery.cookie.js"></script>
<script src="/assets/js/custom.js"></script>
<!--���￹��--><!-- ������ ��� ���� checkout_check.php �� �߰�-->

<script type="text/javascript">

function startTime() {
    var today = new Date();
    var h = today.getHours();
    var m = today.getMinutes();
    var s = today.getSeconds();
    m = checkTime(m);
    s = checkTime(s);
    document.getElementById('clock').innerHTML =
    h + ":" + m + ":" + s;
    var t = setTimeout(startTime, 500);
}
function checkTime(i) {
    if (i < 10) {i = "0" + i}; // ���ڰ� 10���� ���� ��� �տ� 0�� �ٿ���
    return i;
}
	//��� üũ
	function go_office(){
		frm = document.form;
		frm.target	= "hdnFrame";
		frm.action = "commuting/commute_check_act.php";
		frm.submit();
		return;
	}

	//���� üũ
	function off_office(idx){
		frm = document.form;
		frm.target	= "hdnFrame";
		frm.action = "commuting/off_check_act.php?idx="+idx;
		frm.submit();
		return;
	}

	//���ΰԽù��б�
	function funView(seqno,type)
	{
		var goUrl;

		if (type == "default")
		{
			goUrl = "/board/board_detail.php?board=default&seqno="+seqno;
		}
		else
		{
			goUrl = "/book/book_detail.php?board="+ type + "&seqno="+seqno;
		}
		var frm = document.form;
		frm.target="_self";
		frm.action = goUrl;
		frm.submit();		
		
	}

	//�������� �ۼ�
	function go_weekly(){
		document.location.href="/weekly/weekly_list.php";
	}

	$(document).ready(function(){
			//$("#popAlert1").css("display","none");
			$("#popAlert1").attr('class','modal');
		
			//$("#popAlert2").css("display","none");
			$("#popAlert2").attr('class','modal');
		
			//$("#popAlert3").css("display","none");
			$("#popAlert2").attr('class','modal');
		});
</script>
    
<body onload="startTime()">
<form method="post" name="form">
<script type="text/javascript">
//��� üũ
	function leave_office(gubun,commute,working){

		if (gubun == 1)
		{
			var msg = "���üũ�� �Ͻðڽ��ϱ�?";
		}
		else if (gubun == 2)
		{
			var msg = "�̹� ���üũ�� �ϼ̽��ϴ�. ���üũ�� �Ͻðڽ��ϱ�?";
		}
		else if (gubun == 3)
		{
			var msg = "��������� ���üũ�� �Ǿ����� �ʽ��ϴ�. \n\���üũ�� �Ͻðڽ��ϱ�? \n\(��ٽð��� ���� ��ٽð����ݿ��˴ϴ�.";
		}
		else if (gubun == 4)
		{
			var msg = "�̹� ���� ���üũ�� �ϼ̽��ϴ�.  \n\���üũ�� �Ͻðڽ��ϱ�? \n\(��ٽð��� ���� ��ٽð��� �ݿ��˴ϴ�.";
		}

		if(confirm(msg)){
			frm = document.form;
			frm.target	= "hdnFrame";
			frm.action = "commuting/commute_check_act2.php";
			frm.submit();
		}else{
			return;
		}
	}	
</script>
<div class="top">
<!--�ֻ�� �����̸� ��/���/����/ �α׾ƿ� ��ư �ְ����� ��ư-->
 <nav id="navbar" class="navbar has-shadow is-fixed-top">
    <div class="container">
            <div class="navbar-brand">
                <div class="navbar-item is-size-7">
               VID 2 Team&nbsp;/&nbsp;<strong>å�� ����</strong>
              </div>
                <div class="navbar-burger burger" data-target="navbarExampleTransparentExample">
                    <span></span>
                    <span></span>
                    <span></span>
            </div>
        </div>
       <div class="navbar-menu">
            <div class="navbar-end">
                <div class="navbar-item is-size-7">
                                            ��� ���� �ð���&nbsp;<strong>17:55 </strong>&nbsp;�Դϴ�.
                                          </div>
                <div class="navbar-item">
                    <div class="field is-grouped">
                    	
                    	<!--��� �ϱ� ��ư ��ºκ�-->
                    	<p class='control'>
											                           	 	<a href=javascript:leave_office(2,'20180420085530','0826'); class='button' href='#'>
											                                <span>����ϱ�</span>
											                            	</a>
											                        		</p>   
																		                 	                        
                        	<p class="control">
                            <a class="button is-primary" href="javascript:logout();">
                                <span>�α׾ƿ�</span>
                             </a>
                          </p>
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </nav>	
	<section class="section-fixed-margin"></section>
	
	<section class="hero is-link is-hidden-mobile">
      <div class="hero-body">
          <div class="container">
              <div class="columns is-vcentered">
                  <div class="column">
                      <a href="/main.php"><img src="/assets/images/df_logo_w.svg" width="150"></a>
                  </div>
                  <div class="column is-narrow">
                      <span class="is-size-6 is-italic">a difference that matters.</span>
                  </div>
              </div>

       </div>
        </div>
        <div class="hero-foot">
            <div class="container">
                <nav class="tabs is-boxed is-fullwidth">
                     <ul>
	                   											<li class="is-active"><a href="/main.php">Ȩ</a></li>
											<li><a href="/commuting/commuting_list.php">����</a></li>
											<li><a href="/vacation/vacation_list.php">�ް�</a></li>
											<li><a href="/approval/approval_my_list.php">���ڰ���</a></li>
											<li><a href="/project/project_list.php">������Ʈ</a></li>
											<!-- 2014.09.17 �ְ����� �߰�-->
											<li><a href="/weekly/weekly_list.php">�ְ�����</a></li>
											<!-- 2014.09.17 �ְ����� ��-->
											<li><a href="/board/board_list.php">��������</a></li>
											<li><a href="/book/book_list.php">�Խ���</a></li>
											<li><a href="/booking/booking_list.php">����</a></li>
											<li><a href="/org_chart/person_list.php">������</a></li>
										                	  </ul>
                </nav>
         </div>
        </div>
    </section>
    
    <section class="hero is-link is-hidden-tablet">
        <div class="hero-body">
            <div class="container">
                <div class="level is-mobile">
                    <div class="level-item level-left">
                        <a href="main.php"><img src="/assets/images/df_logo_w.svg" width="50" style="vertical-align:middle;"></a>
                    </div>
                    <div class="level-item level-right is-expanded">
                        <span class="is-italic has-text-right is-fullwidth">a difference that matters.</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
</div>

								
												
													<!-- ���� ���� -->
<section class="section is-main">
    <div class="container">
        <div class="columns">
    
            <!-- mobile �� ������ �κ� ���� -->
            <div class="column is-hidden-tablet">
    
                <div class="card">
                    <div class="card-content">
                        <article class="media">
                            <figure class="media-left">
                                <p class="image is-128x128">
                                    <img src='/file/IMG_0228.jpg' alt='profile_img'/>                                    
                                </p>
                            </figure>
                            <div class="media-content">
                                <div class="content">
                                    <p>
                                        <span class="title is-4">����</span>
                                        <br>
                                        <span class="title is-6">�Ŵ���</span>
                                        <br>
                                        <span class="subtitle is-7">VID 2 Team&nbsp;/&nbsp;å��</span>
                                    </p>
                                </div>
                                <nav class="buttons has-addons main-mobile-profile-buttons">
                                        <a class="button is-small" href="/member/modify.php">
                                            ��������
                                        </a>                                                                            
                                        <a class="button is-small" href="/mail/mail-sign-generator.php" target="_blank">
                                            �������
                                        </a>              
                                        <!--�ְ������ۼ� ���-->                          
                                        																													
																					 <a class='button is-small' href='javascript:go_weekly();' title='�ְ������ۼ�'>�ְ������ۼ�</a>              																		
																					
                                </nav>
                            </div>
                        </article>
                    </div>
                  <!--����Ͽ� ���� ����Ʈ-->
                  							
                      <div class="card-footer">
											                    
											 <a href="/approval/approval_to_list.php" class="card-footer-item">                        
	                        	<span class="icon is-small is-hidden-tablet-only">
	                                <i class="fas fa-check"></i>
	                            </span>
	                            &nbsp;
	                            <span>0</span>                                                                                
	                   	 </a>
	                     <a href="/approval/approval_my_list.php" class="card-footer-item">                        
	                        	<span class="icon is-small is-hidden-tablet-only">
	                                <i class="fas fa-arrow-up"></i>
	                            </span>
	                            &nbsp;
	                            <span>0</span>                                                    
	                     </a>
	                     <a href="/approval/approval_cc_list.php" class="card-footer-item">                        
	                        	<span class="icon is-small is-hidden-tablet-only">
	                                <i class="fas fa-dot-circle"></i>
	                            </span>
	                            &nbsp;
	                            <span>0</span>                                                    
	                     </a>					
	                  																
                    </div>
                </div>
            </div>
            <!-- mobile �� ������ �κ� �� -->
            
            <!-- pc tablet �� ������ �κ� ���� -->
            <div class="column is-one-fifth is-hidden-mobile">
    
                <!-- pc tablet �� ������ -->
                <div class="wrapper">
                    <div class="card">
                        <div class="card-content">
                            <div class="image is-fullwidth is-hidden-mobile">                                
                                 <img src='/file/IMG_0228.jpg' alt='profile_img'/>                                
                            </div>
                            <hr class="is-hidden-mobile"/>
                            <div class="content">
                                <p class="title  is-4"><span>����</span> <span class="title is-5">/ �Ŵ���</span></p>
                                <p class="subtitle is-7">VID 2 Team<br>å��</p>
                            </div>
                        </div>
                        <div class="card-footer">
                            <a class="card-footer-item" href="/member/modify.php"><span>��������</span></a>                            
                            <a class="card-footer-item" href="/mail/mail-sign-generator.php"><span>�������</span></a>                           
                        </div>
                    </div>
                </div>
                
                
                <div class="wrapper">                          	
                	<!--�ְ������ۼ� ���-->     	                										
             	    									
										<div class="field">
											<a class='button is-fullwidth is-danger' href='javascript:go_weekly();' title='�ְ������ۼ�'>�ְ������ۼ�</a>             	    		
    								</div>												
																											
									
									<!-- pc tablet �� ���� ���� -->                
                    <div class="field has-addons">
                    	 
                        <div class="control is-expanded">
                            <a class="button is-fullwidth is-info" href="/approval/approval_to_list.php">
                                <span class="icon is-small is-hidden-tablet-only">
                                    <i class="fas fa-check"></i>
                                </span>
                                <span>0</span>
                            </a>
                        </div>
                        <div class="control is-expanded">
                            <a class="button is-fullwidth" href="/approval/approval_my_list.php">
                                <span class="icon is-small is-hidden-tablet-only">
                                    <i class="fas fa-arrow-up"></i>
                                </span>
                                <span>0</span>
                            </a>
                        </div>
                        <div class="control is-expanded">
                            <a class="button is-fullwidth" href="/approval/approval_cc_list.php">
                            <span class="icon is-small is-hidden-tablet-only">
                                <i class="fas fa-dot-circle"></i>
                            </span>
                                <span>0</span>
                            </a>
                        </div>
                        
                       
                    </div>                  
                </div>
                
                
                <!--�̴��� ������-->
                               <div class="wrapper">
                    <article class="card">
                        <header class="card-header">
                            <p class="card-header-title">
                                <span class="icon is-small"><i class="fas fa-birthday-cake"></i></span>
                                <span class="main-card-title">�̴��� ����</span>
                            </p>
                        </header>
                        <div class="card-content field is-grouped is-grouped-multiline">
    								 <div class='birthday-profile'>
																	<p class='image is-rounded-image'>
                                    <img src='/file/gongon.jpg' alt='profile_img'/>
                                	</p><span class='has-text-centered'>������ 04.02 (02.17)</span></div> <div class='birthday-profile'>
																	<p class='image is-rounded-image'>
                                    <img src='/file/hj.jpg' alt='profile_img'/>
                                	</p><span class='has-text-centered'>��ȿ��  04.04</span></div> <div class='birthday-profile'>
																	<p class='image is-rounded-image'>
                                    <img src='/file/bjk.jpg' alt='profile_img'/>
                                	</p><span class='has-text-centered'>������  04.06</span></div> <div class='birthday-profile'>
																	<p class='image is-rounded-image'>
                                    <img src='/file/maya.jpg' alt='profile_img'/>
                                	</p><span class='has-text-centered'>������  04.09</span></div> <div class='birthday-profile'>
																	<p class='image is-rounded-image'>
                                    <img src='/file/new_han3001.jpg' alt='profile_img'/>
                                	</p><span class='has-text-centered'>�Ѽ���  04.09</span></div> <div class='birthday-profile'>
																	<p class='image is-rounded-image'>
                                    <img src='/file/image_5.JPG' alt='profile_img'/>
                                	</p><span class='has-text-centered'>�����  04.17</span></div> <div class='birthday-profile'>
																	<p class='image is-rounded-image'>
                                    <img src='/file/dd_5.jpg' alt='profile_img'/>
                                	</p><span class='has-text-centered'>���μ�  04.17</span></div> <div class='birthday-profile'>
																	<p class='image is-rounded-image'>
                                    <img src='/file/dd_4.jpg' alt='profile_img'/>
                                	</p><span class='has-text-centered'>��ΰ�  04.19</span></div>		
                        </div>
                    </article>
                                  </div>                
            </div>
            <!-- pc tablet �� ������ �κ� �� -->            
            <!-- ���� ������ ���� -->
            <div class="column">
                <div class="tile is-ancestor">
                    <div class="tile is-vertical">
                        <div class="tile">
 													<input type="hidden" name="time_gubun" id="time_gubun" value="after">
 													
 													<!--������ºκ�-->
                            <div class="tile is-parent is-vertical">
                                <article class="tile is-child card">                                    
                                    <div class="main-clock-content">                                        	
                                        <div class="clock-area">
                                  																						
                                            <div class="title has-text-centered has-text-white clock-txt">
                                            	<input type="hidden" size="30" name="timeContent" readonly>																							 			
																							 <p class="clock-txt-now">																								
																							 	<span id="clock"></span>		<!--�ð�-->																					 	
																							 </p>											
																							 												
																				 <!--�ð� ��� �κ� ����-->
																				 	 <p class='clock-txt-start'>��� : 08:55&nbsp;&nbsp;��� : 13:50																							 			
                                            </div>
                                        </div>																																																																								
																				<div class="field has-addons clock-buttons">    																	
																					<div class='control is-expanded'>
                                                				<a href=javascript:leave_office(2,'20180420085530','0826'); class='button is-fullwidth is-info clock-button'>
                                                    			<div class='title is-5 has-text-white'>���</div>
                                                				</a>
                                            					</div><div class='control is-expanded'>
                                                				<a href='javascript:off_office("goout");' class='button is-fullwidth is-primary clock-button'>
                                                    			<div class='title is-5 has-text-white'>����</div>
                                                				</a>
                                            					</div>																					</div>
																																				

                                   </div>
           											</a>
                                </article>
                            </div>
               
                
                						<!--�޷�-->   
                						          						
                            <div class="tile is-parent">                            	                            		
                            
<script type="text/javascript">		
	$( document ).ready( function() {                		        
    	 var cal_cnt = $( '.day_num' ).length;       
   		$('#cal_value').attr('value', cal_cnt);   		
	}); 		
	function sSubmit(f)
	{
		f.target="_self";
		f.action="/main.php";
		f.submit();
	}
	//��������
	function preMonth()
	{
			var frm = document.form;		
		frm.year.value = "2018";
		frm.month.value = "03";
		frm.submit();
		}
	//����������
	function nextMonth()
	{
		var frm = document.form;
		frm.year.value = "2018";
		frm.month.value = "05";
		frm.submit();
	 }
</script>
  <article class="tile is-child card">
  	<div class="calendar main-calendar">
  		<!--�޷»��-->
	  		<div class="calendar-nav">
	          <div class="calendar-nav-previous-month">
	              <a href="javascript:preMonth();"  class="button is-text is-small is-primary">
	                  <i class="fa fa-chevron-left"></i>
	              </a>
	          </div>
	          <input type="hidden" name="year">
	          <input type="hidden" name="month">
	          <div>
	          		<span class="title is-6 has-text-white">2018��04��</span>
	          </div>
	          <div class="calendar-nav-next-month">
	              <a href="javascript:nextMonth();" class="button is-text is-small is-primary">
	                  <i class="fa fa-chevron-right"></i>
	              </a>
	          </div>
	      </div>
	    <!--�޷»��-->
	    
	    <!-- ��¥ ��ºκ�-->	      	
			<div class="calendar-container">				
				<div class="calendar-header">
          <div class="calendar-date">Sun</div>
          <div class="calendar-date">Mon</div>
          <div class="calendar-date">Tue</div>
          <div class="calendar-date">Wed</div>
          <div class="calendar-date">Thu</div>
          <div class="calendar-date">Fri</div>
          <div class="calendar-date">Sat</div>
         </div>									
           <div class="calendar-body">
          	<input type="hidden" id="cal_value" name="cal_value" value="">  	<!--�������� ����� ä���� ��-->
                <div id='cal' class='calendar-date'><button class='date-item'><span class='day_num' style='color:#ff0000;'>1<br></span></button></div><div id='cal' class='calendar-date'><button class='date-item'><span class='day_num'>2<br></span></button></div><div id='cal' class='calendar-date'><button class='date-item'><span class='day_num'>3<br></span></button></div><div id='cal' class='calendar-date'><button class='date-item'><span class='day_num'>4<br></span></button></div><div id='cal' class='calendar-date'><button class='date-item'><span class='day_num'>5<br></span></button></div><div id='cal' class='calendar-date'><button class='date-item'><span class='day_num'>6<br></span></button></div><div id='cal' class='calendar-date'><button class='date-item'><span class='day_num' style='color:#ff0000;'>7<br></span></button></div><div id='cal' class='calendar-date'><button class='date-item'><span class='day_num' style='color:#ff0000;'>8<br></span></button></div><div id='cal' class='calendar-date'><button class='date-item'><span class='day_num'>9<br></span></button></div><div id='cal' class='calendar-date'><button class='date-item'><span class='day_num'>10<br></span></button></div><div id='cal' class='calendar-date'><button class='date-item'><span class='day_num'>11<br></span></button></div><div id='cal' class='calendar-date'><button class='date-item'><span class='day_num'>12<br></span></button></div><div id='cal' class='calendar-date'><button class='date-item'><span class='day_num'>13<br></span></button></div><div id='cal' class='calendar-date'><button class='date-item'><span class='day_num' style='color:#ff0000;'>14<br></span></button></div><div id='cal' class='calendar-date'><button class='date-item'><span class='day_num' style='color:#ff0000;'>15<br></span></button></div><div id='cal' class='calendar-date'><button class='date-item'><span class='day_num'>16<br></span></button></div><div id='cal' class='calendar-date'><button class='date-item'><span class='day_num'>17<br></span></button></div><div id='cal' class='calendar-date'><button class='date-item'><span class='day_num'>18<br></span></button></div><div id='cal' class='calendar-date'><button class='date-item'><span class='day_num'>19<br></span></button></div><div id='cal' class='calendar-date tooltip' data-tooltip='Today'><span class='day_num'><button class='date-item is-today'>20</span></button></div><div id='cal' class='calendar-date'><button class='date-item'><span class='day_num' style='color:#ff0000;'>21<br></span></button></div><div id='cal' class='calendar-date'><button class='date-item'><span class='day_num' style='color:#ff0000;'>22<br></span></button></div><div id='cal' class='calendar-date'><button class='date-item'><span class='day_num'>23<br></span></button></div><div id='cal' class='calendar-date'><button class='date-item'><span class='day_num'>24<br></span></button></div><div id='cal' class='calendar-date'><button class='date-item'><span class='day_num'>25<br></span></button></div><div id='cal' class='calendar-date'><button class='date-item'><span class='day_num'>26<br></span></button></div><div id='cal' class='calendar-date'><button class='date-item'><span class='day_num'>27<br></span></button></div><div id='cal' class='calendar-date'><button class='date-item'><span class='day_num' style='color:#ff0000;'>28<br></span></button></div><div id='cal' class='calendar-date'><button class='date-item'><span class='day_num' style='color:#ff0000;'>29<br></span></button></div><div id='cal' class='calendar-date'><button class='date-item'><span class='day_num'>30<br></span></button></div><div id='cal' class='calendar-date is-disabled'><span class='day_num'><button class='date-item'></span></button></div><div id='cal' class='calendar-date is-disabled'><span class='day_num'><button class='date-item'></span></button></div><div id='cal' class='calendar-date is-disabled'><span class='day_num'><button class='date-item'></span></button></div><div id='cal' class='calendar-date is-disabled'><span class='day_num'><button class='date-item'></span></button></div><div id='cal' class='calendar-date is-disabled'><span class='day_num'><button class='date-item'></span></button></div>				
     </div>			
		</div>	
	</div>
</article>

                           
                            </div>				
                            
                            <!--�޷�-->
                            																																
                        </div>
                        <!--�������� ����Ʈ-->
                        <div class="tile">
                            <div class="tile is-parent">
                                <article class="tile is-child card">
                                    <header class="card-header">
                                        <p class="card-header-title">
                                            <span class="icon is-small">
                                                <i class="fas fa-exclamation-circle"></i>
                                            </span>
                                            <span class="main-card-title">��������</span>
                                        </p>
                                        <a href="board/board_list.php">
                                        <p class="card-header-icon">
                                            <span class="icon is-small">
                                                <i class="fas fa-plus"></i>
                                            </span>
                                        </p>
                                    	  </a>
                                    </header>
                                    <div class="card-content">
                                        <table class="table is-fullwidth is-hoverable is-marginless">
                                            <colgroup>
                                                <col width="*">
                                                <col width="30%">
                                            </colgroup>
                                            <tbody>
                                            						
                                            <tr>
                                                <td>                                                	
                                                    <a href="javascript:funView('4525','default');" style="cursor:hand">
                                                       3�� Best Team, Best Player                                                                                                                                     
																											 <span class='tag is-rounded td-tag'>10</span>                                                    </a>
                                                </td>
                                                <td class="has-text-right">�̻� �ڼ�õ</td>
                                            </tr>      
                                            				
                                            <tr>
                                                <td>                                                	
                                                    <a href="javascript:funView('4524','default');" style="cursor:hand">
                                                       [����̽�] ���� ����� ���� ����                                                                                                                                     
																											 <span class='tag is-rounded td-tag'>1</span>                                                    </a>
                                                </td>
                                                <td class="has-text-right">���� ������</td>
                                            </tr>      
                                            				
                                            <tr>
                                                <td>                                                	
                                                    <a href="javascript:funView('4517','default');" style="cursor:hand">
                                                       3�� ���׸��� ���� �ȳ�                                                                                                                                     
																											                                                     </a>
                                                </td>
                                                <td class="has-text-right">��� ������</td>
                                            </tr>      
                                            				
                                            <tr>
                                                <td>                                                	
                                                    <a href="javascript:funView('1658','default');" style="cursor:hand">
                                                       ������ �� �ҵ����� ���� �߱� ��û �ȳ�                                                                                                                                     
																											 <span class='tag is-rounded td-tag'>5</span>                                                    </a>
                                                </td>
                                                <td class="has-text-right">�븮 �����</td>
                                            </tr>      
                                                                                  
                                            </tbody>                                                                                                                                    																
                                        </table>
                                    </div>
                                </article>
                            </div>
                        </div>
                        
                        <!--�Խ��� ����Ʈ-->
                        <div class="tile">
                            <div class="tile is-parent">
                                <article class="tile is-child card">
                                    <header class="card-header">
                                        <p class="card-header-title">
                                            <span class="icon is-small">
                                                <i class="fas fa-plus-circle"></i>
                                            </span>
                                            <span class="main-card-title">�ֽżҽ�</span>
                                        </p>
                                        <a href="book/book_list.php">
                                        <p class="card-header-icon">
                                            <span class="icon is-small">
                                                <i class="fas fa-plus"></i>
                                            </span>
                                        </p>
                                      	</a>
                                    </header>
                                    <div class="card-content">
                                        <table class="table is-fullwidth is-hoverable is-marginless">
                                            <colgroup>
                                                <col width="*">
                                                <col width="30%">
                                            </colgroup>
                                            <tbody>
                                            	                                            <tr>
                                                <td>                                                	                                                	
                                                   	<a href="javascript:funView('4526','club');" style="cursor:hand">
                                                        [���̴ټ���] ���̴ټ��� 4�� �����ı�                                                      
                                                                                                                                      
																											  <span class='tag is-rounded td-tag'>10</span>                                                    </a>
                                                </td>
                                                <td class="has-text-right">���� �輺��</td>
                                            </tr>
                                            	                                            <tr>
                                                <td>                                                	                                                	
                                                   	<a href="javascript:funView('4518','club');" style="cursor:hand">
                                                        [Ŭ���ռ���] Ŭ���ռ��� 4�� �����ı�                                                      
                                                                                                                                      
																											  <span class='tag is-rounded td-tag'>5</span>                                                    </a>
                                                </td>
                                                <td class="has-text-right">���� �����</td>
                                            </tr>
                                            	                                            <tr>
                                                <td>                                                	                                                	
                                                   	<a href="javascript:funView('4515','default');" style="cursor:hand">
                                                        3�� ���׸��� ����� ���� ���� �̵� ����                                                      
                                                                                                                                      
																											                                                      </a>
                                                </td>
                                                <td class="has-text-right">��� ������</td>
                                            </tr>
                                            	                                            <tr>
                                                <td>                                                	                                                	
                                                   	<a href="javascript:funView('4514','default');" style="cursor:hand">
                                                        3�� ������ ������ �� ����� û�� ��û ����                                                      
                                                                                                                                      
																											                                                      </a>
                                                </td>
                                                <td class="has-text-right">��� ������</td>
                                            </tr>
                                            	                                            <tr>
                                                <td>                                                	                                                	
                                                   	<a href="javascript:funView('4513','default');" style="cursor:hand">
                                                        4�� ����Ÿ�� �̿��� ����                                                      
                                                                                                                                      
																											  <span class='tag is-rounded td-tag'>2</span>                                                    </a>
                                                </td>
                                                <td class="has-text-right">��� ������</td>
                                            </tr>
                                            	                                            </tbody>
                                        </table>
                                    </div>
                                </article>
                            </div>
                        </div>                        
                        
                                <div class="tile is-hidden-tablet">
                            <div class="tile is-parent">
                                <article class="tile is-child card">
        
                                <header class="card-header">
                                    <p class="card-header-title">
                                        <span class="icon is-small"><i class="fas fa-birthday-cake"></i></span>
                                        <span class="main-card-title">�̴��� ����</span>
                                    </p>
                                </header>
                                <div class="card-content field is-grouped is-grouped-multiline">
    								 <div class='birthday-profile mobile'>
																	<p class='image is-rounded-image'>
                                    <img src='/file/gongon.jpg' alt='profile_img'/>
                                	</p><span class='has-text-centered'>������ 04.02 (02.17)</span></div> <div class='birthday-profile mobile'>
																	<p class='image is-rounded-image'>
                                    <img src='/file/hj.jpg' alt='profile_img'/>
                                	</p><span class='has-text-centered'>��ȿ��  04.04</span></div> <div class='birthday-profile mobile'>
																	<p class='image is-rounded-image'>
                                    <img src='/file/bjk.jpg' alt='profile_img'/>
                                	</p><span class='has-text-centered'>������  04.06</span></div> <div class='birthday-profile mobile'>
																	<p class='image is-rounded-image'>
                                    <img src='/file/maya.jpg' alt='profile_img'/>
                                	</p><span class='has-text-centered'>������  04.09</span></div> <div class='birthday-profile mobile'>
																	<p class='image is-rounded-image'>
                                    <img src='/file/new_han3001.jpg' alt='profile_img'/>
                                	</p><span class='has-text-centered'>�Ѽ���  04.09</span></div> <div class='birthday-profile mobile'>
																	<p class='image is-rounded-image'>
                                    <img src='/file/image_5.JPG' alt='profile_img'/>
                                	</p><span class='has-text-centered'>�����  04.17</span></div> <div class='birthday-profile mobile'>
																	<p class='image is-rounded-image'>
                                    <img src='/file/dd_5.jpg' alt='profile_img'/>
                                	</p><span class='has-text-centered'>���μ�  04.17</span></div> <div class='birthday-profile mobile'>
																	<p class='image is-rounded-image'>
                                    <img src='/file/dd_4.jpg' alt='profile_img'/>
                                	</p><span class='has-text-centered'>��ΰ�  04.19</span></div>		
                            </div>
                            </article>
                        	</div>
                        </div>
                                                                                         
                        
                    </div>
    
                </div>
            </div>
        </div>
        <!-- ���� ������ �� -->
        
    </div>
</section>
<!-- ���� �� -->
</body>
</form>
<iframe name="hdnFrame" id="hdnFrame" width="0" height="0" style="border:0;"></iframe>
<iframe name="hdnFrame2" id="hdnFrame2" width="0" height="0" style="border:0;"></iframe>


<!--new �˾�1-->
<div id="popAlert1" class="modal">
  <div class="modal-background"></div>
  <div class="modal-card">
    <header class="modal-card-head">
      <p class="modal-card-title">�˸�</p>
     <a href="javascript:HidePop('Alert1');"><button class="delete" aria-label="close"></button></a>
    </header>
    <section class="modal-card-body" style="text-align:center">
      			�� �ٹ��� ���(���)�� ���������� ��ϵ��� �ʾҽ��ϴ�. �����Ͽ� �ް� ����� ����ϰų� ���¼�����û �Խ����� �̿��� �ּ���.
		    </section>
    <footer class="modal-card-foot">
      <a href="javascript:CheckPop('check_todayView1','commuting');"><button class="button is-success">Ȯ��</button></a>
      <input type="checkbox" id="check_todayView1" name="check_todayView1" style="vertical-align: middle;">
			<label for="check_todayView1" style="cursor:pointer;">&nbsp;���� �Ϸ� �� �̻� ���� �ʱ�</label>
    </footer>
  </div>
</div>


<!--new �˾�2-->
<div id="popAlert2" class="modal">
  <div class="modal-background"></div>
  <div class="modal-card">
    <header class="modal-card-head">
      <p class="modal-card-title">�˸�</p>
      <a href="javascript:HidePop('Alert2');"><button class="delete" aria-label="close"></button></a>
    </header>
    <section class="modal-card-body">
     			�� �ٹ��� �ٹ� �ð��� �̴� �Ǿ����ϴ�.
			���ڰ��� �޴����� �������� �ۼ��� �ּ���.
		    </section>
    <footer class="modal-card-foot">
      <a href="javascript:CheckPop('check_todayView2','commuting');"><button class="button is-success">Ȯ��</button></a>
      <input type="checkbox" id="check_todayView2" name="check_todayView2" style="vertical-align: middle;">
			<label for="check_todayView2" style="cursor:pointer;">&nbsp;���� �Ϸ� �� �̻� ���� �ʱ�</label>
    </footer>
  </div>
</div>

<!--new �˾�3-->
<div id="popAlert3" class="modal">
  <div class="modal-background"></div>
  <div class="modal-card">
    <header class="modal-card-head">
      <p class="modal-card-title">�˸�</p>
      <a href="javascript:HidePop('Alert3');" class="close"><button class="delete" aria-label="close"></button></a>
    </header>
    <section class="modal-card-body">
      			���� ��� üũ�� ���� �ʾҽ��ϴ�.
			���¼�����û �Խ����� �̿��� �ּ���.
		    </section>
    <footer class="modal-card-foot">
     
    	<a href="javascript:CheckPop('check_todayView3','edit');"><button class="button is-success">Ȯ��</button></a>
    	
      <input type="checkbox" id="check_todayView3" name="check_todayView3" style="vertical-align: middle;">
			<label for="check_todayView3" style="cursor:pointer;">&nbsp;���� �Ϸ� �� �̻� ���� �ʱ�</label>
    </footer>
  </div>
</div>


</html>