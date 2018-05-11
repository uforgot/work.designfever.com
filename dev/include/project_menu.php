<!-- 서브 네비게이션 시작 -->
<div class="sub-menu-7">
    <nav class="navbar has-shadow is-size-7-mobile">
        <div class="container">
            <div class="navbar-tabs">
			<? if ($type == "ING") { ?>
				<a class="navbar-item is-tab is-active" href="/project/project_list.php?type=ING">진행 프로젝트</a>
			<? } else { ?>
				<a class="navbar-item is-tab"href="/project/project_list.php?type=ING">진행 프로젝트</a>
			<? } ?>
			<? if ($type == "END") { ?>
				<a class="navbar-item is-tab is-active" href="/project/project_list.php?type=END">완료 프로젝트</a>
			<? } else { ?>
				<a class="navbar-item is-tab" href="/project/project_list.php?type=END">완료 프로젝트</a>
			<? } ?>
				<? if (substr(CURRENT_URL,8,18) == "/project_total.php") { ?> <a class="navbar-item is-tab is-active" href="/project/project_total.php"> 통계<? } else { ?><a class="navbar-item is-tab" href="/project/project_total.php"> 통계<? } ?></a>
			<? if ($prf_id == 4) { ?>
				<? if (substr(CURRENT_URL,8,20) == "/project_collect.php" || substr(CURRENT_URL,8,19) == "/project_income.php" || substr(CURRENT_URL,8,20) == "/project_expense.php") { ?><a class="navbar-item is-tab is-active" href="/project/project_collect.php"> 프로젝트 수금내역<? } else { ?><a class="navbar-item is-tab" href="/project/project_collect.php"> 프로젝트 수금내역<? } ?></a>
			<? } ?>
			<? if ($board == "contact") { ?>
				<a class="navbar-item is-tab is-active" href="/project/contact_list.php">의뢰 프로젝트</a>
			<? } else { ?>
				<? if (in_array($prs_position,$positionS_arr) || in_array($prs_id,$positionC_arr) || $prs_team == '경영지원팀') { ?>
					<a class="navbar-item is-tab" href="/project/contact_list.php">의뢰 프로젝트</a>
				<? } ?>
			<? } ?>
            </div>
        </div>
    </nav>
</div>
<!-- 서브 네비게이션 끝-->
