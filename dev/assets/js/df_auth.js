//-- 우편번호, 아이디, URL, 사업자번호, 기타 체크
function fcOpenNewWindow(check) {
    var frm = document.form
    switch (check) {
        case "zipcode" :
            zip = window.open('/member/pop_zipcode.php','zip','width=493 ,height=410,left=860,top=440,scrollbars=no');
            zip.focus();
            break;
        case "check_id" :
            var strLogin = frm.login.value;
            if(trim(strLogin).length == 0){
                alert("사용하고자하는 아이디를 입력해주십시오.")
                frm.login.focus();
                return;
            }
            if(trim(strLogin).length <2){
                alert("아이디는 최소 2자리 이상입니다.");
                frm.login.focus();
                return;
            }
            var strURL = "pop_idok.php?hiform=form&hifield=login&ID=" + strLogin;
            win = window.open(strURL,'win','width=475,height=275,left=780,top=410,scrollbars=no');
            win.focus();
            break;

    }
}

//-- 도로명주소 안내시스템 통합검색창 연계
function jusoCallBack(roadFullAddr,roadAddrPart1,addrDetail,roadAddrPart2,engAddr,jibunAddr,zipNo,admCd,rnMgtSn,bdMgtSn){

    if (roadFullAddr == "jibunAddr") { jibunAddr = ""; }
    if (roadFullAddr == "addrDetail") { addrDetail = ""; }
    if (zipNo == "zipNo") { zipNo = ""; }
    if (roadFullAddr == "roadFullAddr") { roadFullAddr = ""; }
    document.getElementById('roadFullAddr').value = roadFullAddr;
    document.getElementById('roadAddrPart1').value = roadAddrPart1;
    document.getElementById('addrDetail').value = addrDetail;
    document.getElementById('roadAddrPart2').value = roadAddrPart2;
    document.getElementById('engAddr').value = engAddr;
    document.getElementById('jibunAddr').value = jibunAddr;
    document.getElementById('zipNo').value = zipNo;
    document.getElementById('admCd').value = admCd;
    document.getElementById('rnMgtSn').value = rnMgtSn;
    document.getElementById('bdMgtSn').value = bdMgtSn;
    document.getElementById('df_join_add1').value = jibunAddr;
    document.getElementById('df_join_add2').value = addrDetail;
    document.getElementById('df_join_zipcode').value = zipNo;
    document.getElementById('df_join_address').value = roadFullAddr;
}

function goPopup(){
    // 주소검색을 수행할 팝업 페이지를 호출합니다.
    // 호출된 페이지(jusopopup.jsp)에서 실제 주소검색URL(http://www.juso.go.kr/addrlink/addrLinkUrl.do)를 호출하게 됩니다.
    var pop = window.open("/member/jusoPopup.php","pop","width=570,height=420, scrollbars=yes, resizable=yes");
}

//로그인 관련 예외 스크립트
function intNumber_Check()
{
    if (event.keyCode !=13){
        if(!((event.keyCode > 64) && (event.keyCode < 91)) && !((event.keyCode > 96) && (event.keyCode < 123))){
            if((event.keyCode<48) || (event.keyCode>57)){
                alert("영문자 또는 숫자를 입력하세요");
                event.returnValue=false;
            }
        }
    }
}

function fcHancheck(){
    if(document.form.login.value.replace(/[a-zA-Z0-9_\-]+/,"") != ""){
        alert("한글아이디는 사용하실 수 없습니다.");
        document.form.login.value="";
        document.form.login.focus();
        //document.form.login.select();
        return;

    }
    if(document.form.login.value.search(/[\",\',<,>,_,-]/g) >= 0) {
        alert("문자열에 특수문자( \",  ',  <,  > )가 있습니다.\n특수문자를 제거하여 주십시오!");
        //document.form.login.select();
        document.form.login.value="";
        document.form.login.focus();
        return;
    }

}




function isLength(string_data)
{
    return string_data.length
}

function isInteger(string_data)
{
    var obj_data = string_data;
    var obj_length = obj_data.length;
    var num ="0123456789";
    returnValue = true;

    for (var i=0;i<obj_length;i++) {
        if(-1 == num.indexOf(obj_data.charAt(i)))
            returnValue = false;
    }
    return returnValue;
}


function intNumber_Check()
{

    if (event.keyCode !=13)
    {
        if(!((event.keyCode > 64) && (event.keyCode < 91)) && !((event.keyCode > 96) && (event.keyCode < 123)))
        {
            if((event.keyCode<48) || (event.keyCode>57))
            {
                event.returnValue=false;
            }

        }
    }

}

//--------Caps Lock 키 체크---------------------------------------------------------
function checkCapsLock( e ) {
    var myKeyCode=0;
    var myShiftKey=false;
    var myMsg='Caps Lock 키가 켜져 있습니다.\n\nCaps Lock 키를 끄고 암호를 입력해주시기 바랍니다.';

    // Internet Explorer 4+
    if ( document.all ) {
        myKeyCode=e.keyCode;
        myShiftKey=e.shiftKey;

        // Netscape 4
    } else if ( document.layers ) {
        myKeyCode=e.which;
        myShiftKey=( myKeyCode == 16 ) ? true : false;

        // Netscape 6
    } else if ( document.getElementById ) {
        myKeyCode=e.which;
        myShiftKey=( myKeyCode == 16 ) ? true : false;

    }

    if ( ( myKeyCode >= 65 && myKeyCode <= 90 ) && !myShiftKey ) {
        alert( myMsg );

    } else if ( ( myKeyCode >= 97 && myKeyCode <= 122 ) && myShiftKey ) {
        alert( myMsg );
    }
}

//--------Caps Lock 키 체크 끝---------------------------------------------------------



/************************************************************************************
 * 함수명: StringValid(a,b,c,d,e)														*
 * 함수설명: 해당 객체에 요구되는 Key의 입력여부 검증								    *
 * 인수 a: 추가를 원하는 유효한 문자													*
 * 인수 b: 객체의 Value																*
 * 인수 c: 최대 혹은 일치를 요하는 자리수(0:자리수에무제한, others:해당숫자만큼제한)		*
 * 인수 d: 일치여부(0:자리수보다작거나같음, 1:자리수와일치)							    *
 * 인수 e: 유효한 문자(Key)들을 명명하는 문자열(예. 숫자, 날짜, ...)					*
 *************************************************************************************/
function emailValid(addchar,invalue,max,fix,part) {
    var val="abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789@.-_";
    var numvalue = invalue;
    var len=numvalue.length;

    if (addchar!="") {
        val += addchar;
        addchar = " (+ " + addchar + ")";
    }

    //1.적절한 Key가 눌려졌는지 검증한다.
    var tempEmail = ".";
    var tempi = 0;
    for (i=0;i<len;i++) {
        if (tempEmail.indexOf(numvalue.substring(i,i+1))>=0) 	{
            tempi++;
        }
    }
    if (tempi < 2) {
        alert ("잘못된 이메일 주소 입니다. \n입력하신 이메일 부분을 다시 확인해 주십시오");
        return false;
    }

    //1.적절한 Key가 눌려졌는지 검증한다.
    for (i=0;i<len;i++) {
        if (val.indexOf(numvalue.substring(i,i+1))<0) 	{
            showAlert(part + " 와(과) 관련되지 않은 \n\nKeyBoard값이 입력되었습니다! \n\n이곳은 " + part + addchar + " 관련Key만이 입력가능합니다!","input");
            return false;
        }
    }

    //2.입력되는 자리수에 제한이 있을때
    //(입력된 숫자가 자리수에 제한이 있고 값이 하나이상 입력된 경우)
    //(따라서, max:자리수 제한이 없거나 입력된 값이 하나도 없는 경우는 통과)
    if ((max != 0) && (len != 0)) {
        //2-1.자리수가 일치해야 하는 경우
        if ((fix) && (len!=max)) {
            showAlert("입력된 이메일값의 자리수가 \n\n요구되는 자리수 [ " + max + " ]자리와 일치하지 않습니다!","input");
            return false;
            //2-2.자리수에 상한치가 있는 경우
        } else if ((!fix) && (len > max)) {
            showAlert("입력된 이메일값의 자리수가 \n\n입력가능 최대 자리수의 범위 [ " + max + " ]자리를 \n\n넘었습니다!","input");
            return false;
        }
    }

    return true;
}