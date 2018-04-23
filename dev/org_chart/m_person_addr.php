<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";		
	require_once CMN_PATH."/login_check.php";
?>
<head>
    <title>DESIGN FEVER INTRANET | df members address book</title>
    <meta http-equiv="Content-Type" content="text/html" charset="utf-8">
    <meta name="viewport" content="initial-scale=1.0, width=device-width, user-scalable=no" />
    <link rel="stylesheet" href="../css/common.css" />
    <link rel="stylesheet" href="../css/jquery-ui.css" />

    <!-- css for web -->
    <link rel="stylesheet" href="../css/m_style_web_20161228.css" />

    <script src="../js/jquery.min.js"></script>
    <script src="../js/jquery.easing.1.3.js"></script>
    <script src="../js/jquery-ui.js"></script>
    <script src="../js/modernizr.custom.72169.js"></script>
    <script src="../js/jquery.cookie.js"></script>
    <script src="../js/custom.js"></script>

    <!-- 페이지 로딩바 표시 -->
    <link rel="stylesheet" href="../css/page.css" />
    <script src="../js/page.js"></script>
    <script src="../js/getMembersAddress_mobile.js"></script>

    <!-- FTP에 업로드할 때는 파일명 person_addr.html 올리기 -->
    <!-- //페이지 로딩바 표시 -->
    <script type="text/javascript">

        var chkMobile = false;

        $(document).ready(function(){
            var container = $("#inner-address");
            //getMembersAddress.init("person_addr.json", container);
            // 아래는 실서버에 올릴 때 적용하면 되는 경로
            getMembersAddress.init("http://work.designfever.com/org_chart/person_addr.json.php", container);

            // 처음부터 reset 버튼 보이게 하려면
            //$('#btnReset').on('click', clickBtnReset);

            checkMobileDevice();
        });

        function checkMobileDevice() {
            var browserW = $(window).width();
            var minBroserW = 767;
            var mobStyle = '<link class="mob-style" rel="stylesheet" type="text/css" href="../css/m_style_20161228.css">';

            if(navigator.userAgent.match(/Android|Mobile|iP(hone|od|ad)|BlackBerry|IEMobile|Kindle|NetFront|Silk-Accelerated|(hpw|web)OS|Fennec|Minimo|Opera M(obi|ini)|Blazer|Dolfin|Dolphin|Skyfire|Zune/)){
                //console.log("VERSION : MOBILE");
                if(chkMobile == false && browserW < minBroserW) {
                    //console.log("VERSION : TABLET");
                }
                $('head').append(mobStyle);
                $('body').css('min-width', '100%');
                chkMobile = true;
            }

            else {
                //console.log("VERSION : PC");
                chkMobile = false;
                $('.mob-style').remove();
            }
        }

        function m_showEmailAddr($this) {
            event.preventDefault();

            var this_email = $($this).parent().parent().find('p').last();
        }

        function clickBtnReset(e) {
            var text_input = document.getElementById("df_address_search");
            var text_val = text_input.value.toUpperCase();
            console.log("TEXT VALUE : " + text_val +" / CLICK RESET !");
            text_input.value = '';          // == $('#df_address_search').val('');

            myFunction();
        }

        function showBtnReset($val) {
            if(!$val) {
                $('#btnReset').css('display', 'none');
            } else {
                $('#btnReset').css('display', 'inline');
            }
            $('#btnReset').on('click', clickBtnReset);
        }

        function myFunction() {
            var input, filter, result_value, div, ul, li, i, p;
            var _chkText = false;
            input = document.getElementById("df_address_search");
            filter = input.value.toUpperCase();
            result_value = filter.replace(/\s/g,''); //특정문자 제거
            div = document.getElementById("inner-address");
            ul = div.getElementsByTagName("ul");

            showBtnReset(result_value);

            //console.log("filter : " + filter);                     //input search 박스에 찍히고 있는 text 실시간 체크
            //console.log("result value : " + result_value);         //input 박스에서 받은 value 값에서 공백 제거하여 result 값 출력

            // 스페이스바 막기 / return 되는 value 값 없애기
            /*
            var spaceBarKeyCode = 32;

            $('input[type="text"]').keypress(function (e) {
                if (e.keyCode ===  spaceBarKeyCode) {
                    //e.preventDefault();
                    event.returnValue = false;
                }
            });
            */

            // input search 박스 내 value 값을 체크하기 위한 본격적인 구문
            for (i = 0; i < ul.length; i++) {
                var li_length = ul[i].getElementsByTagName("li").length;        // ul에 속해있는 li의 갯수
                li = ul[i].getElementsByTagName("li");

                // 기존에 select 되었던 엘리먼트들의 타이틀이 보였다면 지금은 ul 자체를 선 숨김/ 후 등장 처리
                // 왜냐하면 ul 들이 갖고 있는 각각의 margin 들 때문에 쓸데없는 공백이 생겨서 아예 display:none 시켜야 함
                //$('#inner-address ul').eq(i).find('.group-name').css('display', 'none');
                $('#inner-address ul').eq(i).css('display', 'none');

                for (var j=0; j<li_length; j++) {
                    if (li) {
                        var select_li = li[j];
                        var select_name = $(select_li).find('p.name span.member-name').eq(0).text();

                        // 직급까지 찾게 하려면 select_name 대신 li[j].innerHTML 을 넣으면 됨
                        if (select_name.toUpperCase().indexOf(result_value) > -1) {
                            _chkText = true;
                            $('#inner-address ul').eq(i).css('display', 'block');
                            li[j].style.display = "";
                        }
                        else {        // 선택되지 못한 주소들
                            li[j].style.display = "none";
                        }
                    }
                    else {
                        _chkText = false;
                    }
                }
            }

            // 2글자 이상 입력 했을 때의 결과가 없을 경우 결과가 없음에 관한 메세지 출력
            var txt_length = result_value.length;
            if (txt_length >= 2 && !_chkText) {
                $('.result-txt-wrap').css('display', 'block');
            } else {
                $('.result-txt-wrap').css('display', 'none');
            }

        }
    </script>

    <style type="text/css">
        #address_wrapper {width:100%;padding:20px;box-sizing: border-box;}
        #address_wrapper header {display:block;width:100%;margin:50px 0 30px;}
        #address_wrapper header .img-holder {width:200px;margin:0 auto;}
        #address_wrapper header .img-holder img {display:block;width:100%;}
        #address_wrapper header .img-holder img.logo-mobile {display:none;}
        #address_wrapper header .txt-holder {width:100%; margin-top:20px; text-align: center; font-size:12px;line-height:2.3;color:#454545; font-weight: bold;}

        #address_wrapper footer {display:block;width:97%;margin:0 auto;padding-top:20px;border-top:2px solid #000;}
        #address_wrapper footer .img-holder {width:140px;margin:0 auto;}
        #address_wrapper footer .img-holder img {display:block; width:100%;}
        #address_wrapper footer .img-holder img.img-mobile {display:none;}
    </style>
</head>

<body>
<div class="address_wrap" id="address_wrapper">
    <header>
        <div class="img-holder">
            <img src="../img/df_logo_new_w.png" alt="designfever logo" class="logo-mobile"/>
            <img src="../img/df_logo_new.png" alt="designfever logo" class="logo-web"/>
        </div>
        <div class="txt-holder">DF MEMBERS ADDRESS BOOK</div>
    </header>
    <div class="search-holder">
        <input type="text" id="df_address_search" name="search" onkeyup="myFunction()" placeholder=" SEARCH">
        <img src="../img/btn_reset_w.png" alt="리셋" id="btnReset" style="cursor:pointer;">
    </div>
    <div class="address-holder" style="margin-bottom:20px;">
        <div class="address-category">
            <div class="inner-category-holder">
                <p class="cat-team">Team / Name</p>
                <p class="cat-phone">Phone Number</p>
                <p class="cat-mail">Email Address</p>
                <p class="cat-ext">Ext.</p>        <!--내선번호 extension number-->
                <p class="cat-drc">Direct Ext.</p> <!--직통번호 direct extension number-->
            </div>
        </div>
        <div id="inner-address">

        </div>
        <div class="result-txt-wrap">
            <p>검색 결과가 없습니다.</p>
        </div>
    </div>
    <footer>
        <div class="img-holder"><img src="../img/footerLogo_w.png" class="img-mobile" alt="designfever"/></div>
        <div class="img-holder"><img src="../img/footerLogo.png" class="img-web" alt="designfever"/></div>
    </footer>
</div>

</body>
</html>