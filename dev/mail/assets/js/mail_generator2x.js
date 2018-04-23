var paramObj, jsonData, mailVal
var isLoading = false;

$(document).ready(function(){
	init();
});


function init(){
	//paramObj = getUrlParameter();
	$(document).on("keydown", onKeydown)
	$(".btn-ok").on("click", onClick_btnOk).css( 'cursor', 'pointer' );
	$(window).on("resize", onResize);
	$("#input-mail").focus();

	drawImage(false);

	
}

function onResize(e){
	$(".img-con").css("width", $("window").width())
}


function onKeydown(e){
	var keycode = e.keyCode;
	if(keycode == 13){
		//dataLoad();
		checkInputValue();
	}
}

function onClick_btnOk(e){
	//dataLoad();
	checkInputValue();
	return false;
}

function checkInputValue(){
	if($("#input-mail").val() == ""){
		$(".error-holder-email").css("display", "block").text("이메일을 입력해 주세요.");
	}else if($("#input-name-eng").val() == ""){
		$(".error-holder-email").css("display", "none");
		$(".error-holder-name").css("display", "block");
		$("#input-name-eng").focus();
	}else{
		$(".error-holder-email").css("display", "none");
		$(".error-holder-name").css("display", "none");
		dataLoad();
	}
}

function dataLoad(){
	if(isLoading) return;
	isLoading = true;
	mailVal = $("#input-mail").val();
	var dataUrl = "/org_chart/person_addr.json.php";
	getJsonData(dataUrl, {}, dataLoadComplete);
}

function dataLoadComplete(json){
	jsonData = json;
	searchInfo();
}


function searchInfo(){
	var i = 0;
	var j = 0;
	var groupLen = jsonData.address.length;
	var count = 0;
	for(i=0; i<groupLen; i++){
		var memberLen = jsonData.address[i].members.length;
		for(j=0; j<memberLen; j++){
			var mail_addr = jsonData.address[i].members[j].mail_addr.split("@")[0];
			count++
			//console.log(count+" : "+mail_addr, $("#input-mail").val())
			if(mail_addr == mailVal){
				searchSuccess(jsonData.address[i].members[j]);
				return;
			}
		}
	}

	searchFail()
}

function searchSuccess(infoData){
	console.log(infoData)
	setData(infoData)
	$(".error-holder-email").css("display", "none");
	isLoading = false;
}

function searchFail(){
	$(".error-holder-email").css("display", "block").text("해당하는 이메일이 없습니다.");
	$("#input-mail").focus();
	isLoading = false;
}

function setData(infoData){

	var strName = infoData.name;
	var strPosition = " "+infoData.position;
	var targetName = strName+" "+strPosition;
	$(".df-name").text(targetName);

	/*
	var strPosition = " "+infoData.position;
	$(".df-position").text(strPosition.toUpperCase());
	*/

	var strNameEng = $("#input-name-eng").val();
	$(".df-name-eng").text(strNameEng);

	var strDivision = infoData.division;
	$(".df-division").text(strDivision.toUpperCase());

	
	
	var strTeam = " / "+infoData.team;
	
	if(strDivision == "" || infoData.team == ""){
		strTeam = infoData.team
	}

	$(".df-team").text(strTeam.toUpperCase());

	var strEmail = infoData.mail_addr;
	$(".df-email").text(strEmail.toUpperCase()).attr("href", "mailto:"+strEmail);
	$(".df-email-img").text(strEmail).attr("href", "mailto:"+strEmail);

	var arrMobileNumber = infoData.tel.split("-");
	var strMobileNumber = arrMobileNumber[0]+" "+arrMobileNumber[1]+" "+arrMobileNumber[2];
	$(".df-mobile-number").text(strMobileNumber).attr("href", "tel:"+infoData.tel);
	$(".df-mobile-number-img").text(infoData.tel).attr("href", "tel:"+infoData.tel);

	var strExtNumber = "("+infoData.ext_tel+")";
	$(".df-ext-number").text(strExtNumber);


	var str = $("#clipboard-con").html();
	$("#targetTa").val(str)

	drawImage(true);
}

function drawImage(isUpload){
	$(".canvas-con").css("display", "block");
	$(".result-img").css("display", "none")
	
	if($('canvas').length != 0) $('canvas').remove();

	html2canvas($(".capture-area")[0], {
		onrendered: function(eleCanvas) {

			$(".canvas-con").append(eleCanvas);
			
			/*
			var canvas = $('canvas')[0];
			var context = canvas.getContext('2d');
			
			
			var logoImg = new Image();
			logoImg.setAttribute('crossOrigin', 'anonymous');
			logoImg.onload = function(){
				context.drawImage(logoImg, 0, 0, 85, 115);
			}
			
			logoImg.src = $(".img-logo").attr("src");

			var hyphenImg = new Image();
			hyphenImg.setAttribute('crossOrigin', 'anonymous');
			hyphenImg.onload = function(){
				context.drawImage(hyphenImg, 97, 172, 119, 23);
				//console.log(canvas.toDataURL());
				if(isUpload) uploadImage();
			}
			
			hyphenImg.src = $(".img-hyphen").attr("src");
			*/
			if(isUpload) uploadImage();
			
		},

		width:960
	});
}


function copyToClipboard(tagToCopy, textarea){

		textarea.parentNode.style.display = "block"; 
         var textToClipboard = ""; 
        if ( "value" in tagToCopy ){    textToClipboard = tagToCopy.value;    } 
        else {    textToClipboard = ( tagToCopy.innerText ) ? tagToCopy.innerText : tagToCopy.textContent;    } 


        var success = false; 

        if ( window.clipboardData ){ 
                window.clipboardData.setData ( "Text", textToClipboard ); 
                success = true; 
        } 
        else { 
                textarea.value = textToClipboard; 

                var rangeToSelect = document.createRange(); 

                rangeToSelect.selectNodeContents( textarea ); 

                var selection = window.getSelection(); 
                selection.removeAllRanges(); 
                selection.addRange( rangeToSelect ); 

                success = true; 

                try { 
                    if ( window.netscape && (netscape.security && netscape.security.PrivilegeManager) ){ 
                        netscape.security.PrivilegeManager.enablePrivilege( "UniversalXPConnect" ); 
                    } 

                    textarea.select(); 
                    success = document.execCommand( "copy", false, null ); 
                } 
                catch ( error ){ 
                    success = false; 
                    console.log( error ); 
                } 
        } 

        textarea.parentNode.style.display = "none"; 
        textarea.value = ""; 

        if ( success ){ alert( ' 클립보드에 복사되었습니다. \n "Ctrl+v"를 사용하여 원하는 곳에 붙여넣기 하세요. ' ); } 
        else {    alert( " 복사하지 못했습니다. " );    } 
}


function uploadImage() {
	console.log($('canvas'));
	var email = $("#input-mail").val();
	var path = "file/"+email+".png";
	var drawCanvas = $('canvas')[0];
	var request = $.ajax({
		type:'POST',
		data: {imgUpload:drawCanvas.toDataURL('image/png'), filename:path},
		url:'canvasupload.php',
		success:function(result){
			//alert(result);
			imageUploadComplete();
		}
	});
}

function imageUploadComplete(){
	$(".canvas-con").css("display", "none");
	$(".result-img").css("display", "block")
	
	var email = $("#input-mail").val();
	var imgSrc = "file/"+email+".png";
	$(".result-img img").attr("src", imgSrc);

	var fullPath = "http://work.designfever.com/mail/"+imgSrc;
	$(".img-path a").text(fullPath).attr("href", fullPath)
}

/////////////////////////////////////////////
//	서버 통신
/////////////////////////////////////////////
function getJsonData(url, data, callback){
	$.ajax({
		url : url,
		data : data,
		dataType: "json",
		error : function(e){
			console.error('json parse error');
		},
		success : function(json){
			callback(json);
		}
	});
}


// 주소창 파라미터 가져오기
function getUrlParameter(){
	var ParameterObject = new Object();
	var locate = location.href;

	if(locate.indexOf("?")==-1){
		return ParameterObject;
	}

	var parameter = locate.split("?")[1];
	parameter = parameter.split("#")[0];
	var paramAreay = parameter.split("&");
	for ( var i=0; i<paramAreay.length; i++ )
	{
		var tem = paramAreay[i].split("=");
		ParameterObject[tem[0]] = tem[1];
	}
	getUrlParameter = function () { return ParameterObject; }
	return ParameterObject;
}



function is_ie() {
  if(navigator.userAgent.toLowerCase().indexOf("chrome") != -1) return false;
  if(navigator.userAgent.toLowerCase().indexOf("msie") != -1) return true;
  if(navigator.userAgent.toLowerCase().indexOf("windows nt") != -1) return true;
  return false;
}