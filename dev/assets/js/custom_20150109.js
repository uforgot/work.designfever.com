var posM = {
	ArrXpos : [0, -200, 50, -100, -50, 100],
	ArrYpos : [-60, 0, -30, 50, -20, -40]
}

var topLineRound = 12;

$(document).ready(function(){
	var RanNum = parseInt(Math.random()*4);
	$('.graphic').animate({
		top:posM.ArrYpos[RanNum],
		marginLeft:posM.ArrXpos[RanNum]+'px'
	}, { duration:1000, easing: 'easeInOutQuad' }, function(){

	});
	
	$('.work_stats tbody tr:last-child').addClass('last')
	$('.work2 tr:first-child').addClass('first')
	$('.work3 tr th:last-child, .work3 tr td:last-child').addClass('last')
	
	var c_width = $('.view_foot').width();
	$('.c_textare, .c_add_input').css('width', c_width-324);
	$('.c_text').css('width', c_width-350);
	$('.c_re_text').css('width', c_width-451);

	// Frame, TopLine
	var topLineTag = $('<div class="line1_bg" id="abc"></div>');
	var w = $('.line1').width()-(topLineRound*3);
	topLineTag.css( {position:'absolute', top:0, left:topLineRound, width:w, height:8, background:'#000'} );
	$('.gnb').before( topLineTag );
});

$(window).resize(function(){
	var c_width = $('.view_foot').width();
	$('.c_textare, .c_add_input').css('width', c_width-324);
	$('.c_text').css('width', c_width-350);
	$('.c_re_text').css('width', c_width-451);

	var w = $('.line1').width()-(topLineRound*3);
	$('.line1_bg').css( {width:w} );
})

function logout()
{
	if(!confirm("로그아웃하시겠습니까?")){
        return;
	}else{
		hdnFrame.location.href = "/member/logout.php";
	}
}
function jobdone()
{
	alert("작업중입니다");
	return;
}

function MM_openBrWindow(theURL,winName,features) { //v2.0
	  window.open(theURL,winName,features);
}

function Paging(form,page,target)
{
	form.page.value = page;
	form.target = "_self";
	form.action = target;
	form.submit();	
}

function file_download(menu,file) {
	var agent = navigator.userAgent;

	if (agent.match(/Mobile|Windows CE|Opera Mini|POLARIS|iPad/) != null){
		window.open('/common/download.php?menu='+menu+'&file='+file,'download');
	} else {
		location.href = '/common/download.php?menu='+menu+'&file='+file;
	}
}

//POPUP LAYER SHOW/HIDE
function ShowPop(id)
{
	$("#pop"+id).attr("style","display:inline");
}
function HidePop(id)
{
	$("#pop"+id).attr("style","display:none");
	$("#hdnFrame").attr("src","");
}
function datepickerClose()
{
	$("#ui-datepicker-div").attr("style","display:none");
}

//오늘 하루 더 이상 보지 않기
function CheckPop(id,go)
{
	if ($("#"+id).is(":checked"))
	{
		//쿠키 생성
		$.cookie(id, 'close', { expires: 1 }); //쿠키의 유효기간 1일
	}
	if (go == "commuting")
	{
		location.href = '/commuting/commuting_list.php';
	}
	if (go == "edit")
	{
		location.href = '/book/book_list.php?board=edit';
	}
}
