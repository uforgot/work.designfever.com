	<h1 class="logo"><a href="../main.php">df intranet</a></h1>
	<div class="graphic"><a href="../main.php"><img src="../img/logo_A_1.gif" alt="" /></a></div>
	<div class="topinfo">
		<?=$prs_team?><span>|</span> <strong><?=$prs_position ?>&nbsp;<?=$prs_name ?></strong>
		<? if ($prf_id == "4") { ?><a href="/admin/approval_list.php"><img src="/img/icon_topinfo1.png" alt="관리자"></a><? } ?>
		<a href="javascript:logout();"><img src="../img/bn_logout.gif" /></a>
	</div>
	<div class="beyond"><img src="../img/txt_beyond.gif" alt="" /></div>

	<!-- wapper set open -->
	<div class="line1">
	<div class="line2">
	<div class="line4">
	<div class="line3">
	<!-- //wapper set open -->

		<ul class="gnb">
			<li<? if (substr(CURRENT_URL,0,5) == "/main") { ?> class="on"<? } ?>><a href="/main.php">홈</a></li>
			<li<? if (substr(CURRENT_URL,0,10) == "/commuting") { ?> class="on"<? } ?>><a href="/commuting/commuting_list.php">근태</a></li>
			<li<? if (substr(CURRENT_URL,0,9) == "/vacation") { ?> class="on"<? } ?>><a href="/vacation/vacation_list.php">휴가</a></li>
			<li<? if (substr(CURRENT_URL,0,9) == "/approval") { ?> class="on"<? } ?>><a href="/approval/approval_my_list.php">전자결재</a></li>
			<li<? if (substr(CURRENT_URL,0,8) == "/project") { ?> class="on"<? } ?>><a href="/project/project_list.php">프로젝트</a></li>
			<!-- 2014.09.17 주간보고서 추가-->
			<li<? if (substr(CURRENT_URL,0,7) == "/weekly") { ?> class="on"<? } ?>><a href="/weekly/weekly_list.php">주간보고서</a></li>
			<!-- 2014.09.17 주간보고서 끝-->
			<li<? if (substr(CURRENT_URL,0,6) == "/board") { ?> class="on"<? } ?>><a href="/board/board_list.php">공지사항</a></li>
			<li<? if (substr(CURRENT_URL,0,5) == "/book") { ?> class="on"<? } ?>><a href="/book/book_list.php">게시판</a></li>
			<li<? if (substr(CURRENT_URL,0,10) == "/org_chart") { ?> class="on"<? } ?>><a href="/org_chart/person_list.php">조직도</a></li>
		</ul>	
