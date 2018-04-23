<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<? include INC_PATH."/top.php"; ?>
    <script src="../js/getMembersAddress.js"></script>

    <!-- //페이지 로딩바 표시 -->
    <script type="text/javascript">

        $(document).ready(function(){
            var container = $("#inner-address");

            getMembersAddress.init("http://work.designfever.com/org_chart/person_addr.json.php", container);
        });

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

            for (i = 0; i < ul.length; i++) {
                var li_length = ul[i].getElementsByTagName("li").length;        // ul에 속해있는 li의 갯수
                li = ul[i].getElementsByTagName("li");

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

</head>

<body>
<div class="wrapper">
	<? include INC_PATH."/top_menu.php"; ?>

		<div class="inner-home">
			<? include INC_PATH."/org_menu.php"; ?>

			<div class="work_wrap clearfix">

				<div class="address_wrap" id="address_wrapper">
					<div class="search-holder">
						<input type="text" id="df_address_search" name="search" onkeyup="myFunction()" placeholder=" SEARCH">
						<img src="../img/btn_reset.gif" alt="리셋" id="btnReset" style="cursor:pointer;">
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
				</div>

			</div>
	</div>
<? include INC_PATH."/bottom.php"; ?>
</div>

</body>
</html>
