//-- �����ȣ, ���̵�, URL, ����ڹ�ȣ, ��Ÿ üũ
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
                alert("����ϰ����ϴ� ���̵� �Է����ֽʽÿ�.")
                frm.login.focus();
                return;
            }
            if(trim(strLogin).length <2){
                alert("���̵�� �ּ� 2�ڸ� �̻��Դϴ�.");
                frm.login.focus();
                return;
            }
            var strURL = "pop_idok.php?hiform=form&hifield=login&ID=" + strLogin;
            win = window.open(strURL,'win','width=475,height=275,left=780,top=410,scrollbars=no');
            win.focus();
            break;

    }
}

//-- ���θ��ּ� �ȳ��ý��� ���հ˻�â ����
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
    // �ּҰ˻��� ������ �˾� �������� ȣ���մϴ�.
    // ȣ��� ������(jusopopup.jsp)���� ���� �ּҰ˻�URL(http://www.juso.go.kr/addrlink/addrLinkUrl.do)�� ȣ���ϰ� �˴ϴ�.
    var pop = window.open("/member/jusoPopup.php","pop","width=570,height=420, scrollbars=yes, resizable=yes");
}

//�α��� ���� ���� ��ũ��Ʈ
function intNumber_Check()
{
    if (event.keyCode !=13){
        if(!((event.keyCode > 64) && (event.keyCode < 91)) && !((event.keyCode > 96) && (event.keyCode < 123))){
            if((event.keyCode<48) || (event.keyCode>57)){
                alert("������ �Ǵ� ���ڸ� �Է��ϼ���");
                event.returnValue=false;
            }
        }
    }
}

function fcHancheck(){
    if(document.form.login.value.replace(/[a-zA-Z0-9_\-]+/,"") != ""){
        alert("�ѱ۾��̵�� ����Ͻ� �� �����ϴ�.");
        document.form.login.value="";
        document.form.login.focus();
        //document.form.login.select();
        return;

    }
    if(document.form.login.value.search(/[\",\',<,>,_,-]/g) >= 0) {
        alert("���ڿ��� Ư������( \",  ',  <,  > )�� �ֽ��ϴ�.\nƯ�����ڸ� �����Ͽ� �ֽʽÿ�!");
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

//--------Caps Lock Ű üũ---------------------------------------------------------
function checkCapsLock( e ) {
    var myKeyCode=0;
    var myShiftKey=false;
    var myMsg='Caps Lock Ű�� ���� �ֽ��ϴ�.\n\nCaps Lock Ű�� ���� ��ȣ�� �Է����ֽñ� �ٶ��ϴ�.';

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

//--------Caps Lock Ű üũ ��---------------------------------------------------------



/************************************************************************************
 * �Լ���: StringValid(a,b,c,d,e)														*
 * �Լ�����: �ش� ��ü�� �䱸�Ǵ� Key�� �Է¿��� ����								    *
 * �μ� a: �߰��� ���ϴ� ��ȿ�� ����													*
 * �μ� b: ��ü�� Value																*
 * �μ� c: �ִ� Ȥ�� ��ġ�� ���ϴ� �ڸ���(0:�ڸ�����������, others:�ش���ڸ�ŭ����)		*
 * �μ� d: ��ġ����(0:�ڸ��������۰ų�����, 1:�ڸ�������ġ)							    *
 * �μ� e: ��ȿ�� ����(Key)���� ����ϴ� ���ڿ�(��. ����, ��¥, ...)					*
 *************************************************************************************/
function emailValid(addchar,invalue,max,fix,part) {
    var val="abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789@.-_";
    var numvalue = invalue;
    var len=numvalue.length;

    if (addchar!="") {
        val += addchar;
        addchar = " (+ " + addchar + ")";
    }

    //1.������ Key�� ���������� �����Ѵ�.
    var tempEmail = ".";
    var tempi = 0;
    for (i=0;i<len;i++) {
        if (tempEmail.indexOf(numvalue.substring(i,i+1))>=0) 	{
            tempi++;
        }
    }
    if (tempi < 2) {
        alert ("�߸��� �̸��� �ּ� �Դϴ�. \n�Է��Ͻ� �̸��� �κ��� �ٽ� Ȯ���� �ֽʽÿ�");
        return false;
    }

    //1.������ Key�� ���������� �����Ѵ�.
    for (i=0;i<len;i++) {
        if (val.indexOf(numvalue.substring(i,i+1))<0) 	{
            showAlert(part + " ��(��) ���õ��� ���� \n\nKeyBoard���� �ԷµǾ����ϴ�! \n\n�̰��� " + part + addchar + " ����Key���� �Է°����մϴ�!","input");
            return false;
        }
    }

    //2.�ԷµǴ� �ڸ����� ������ ������
    //(�Էµ� ���ڰ� �ڸ����� ������ �ְ� ���� �ϳ��̻� �Էµ� ���)
    //(����, max:�ڸ��� ������ ���ų� �Էµ� ���� �ϳ��� ���� ���� ���)
    if ((max != 0) && (len != 0)) {
        //2-1.�ڸ����� ��ġ�ؾ� �ϴ� ���
        if ((fix) && (len!=max)) {
            showAlert("�Էµ� �̸��ϰ��� �ڸ����� \n\n�䱸�Ǵ� �ڸ��� [ " + max + " ]�ڸ��� ��ġ���� �ʽ��ϴ�!","input");
            return false;
            //2-2.�ڸ����� ����ġ�� �ִ� ���
        } else if ((!fix) && (len > max)) {
            showAlert("�Էµ� �̸��ϰ��� �ڸ����� \n\n�Է°��� �ִ� �ڸ����� ���� [ " + max + " ]�ڸ��� \n\n�Ѿ����ϴ�!","input");
            return false;
        }
    }

    return true;
}