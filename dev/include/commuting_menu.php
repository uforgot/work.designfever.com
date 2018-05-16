<!-- 서브 네비게이션 시작 -->
<div class="sub-menu-7">
    <nav class="navbar has-shadow is-size-7-mobile">
        <div class="container">
            <div class="navbar-tabs">
                <? if (substr(CURRENT_URL,10,19) == "/commuting_list.php") { ?>
                    <a class="navbar-item is-tab is-active" href="commuting_list.php">근태 현황</a>
                 <? } else { ?>
                     <a class="navbar-item is-tab" href="commuting_list.php">근태 현황</a>
                 <? } ?>
			    
                <? if (substr(CURRENT_URL,10,21) == "/commuting_member.php") { ?>
                    <a class="navbar-item is-tab is-active" href="commuting_member.php">실/팀원 근태현황</a>
                <? } else { ?>
                    <a class="navbar-item is-tab  "href="commuting_member.php">실/팀원 근태현황</a>
                <? } ?>

		        <?	if ($prf_id == "4") { ?>
                    <? if (substr(CURRENT_URL,10,20) == "/commuting_total.php" || substr(CURRENT_URL,10,25) == "/commuting_total_team.php") { ?>
                        <a class="navbar-item is-tab is-active" href="commuting_total.php">일/월별 근태통계</a>
                    <? } else { ?>
                        <a class="navbar-item is-tab" href="commuting_total.php">일/월별 근태통계</a>
                    <? } ?>
			        <? if (substr(CURRENT_URL,10,25) == "/commuting_total_year.php") { ?>
                        <a class="navbar-item is-tab is-active" href="commuting_total_year.php">연별 근태통계</a>
                    <? } else { ?>
                        <a class="navbar-item is-tab" href="commuting_total_year.php">연별 근태통계</a>
                    <? } ?>
			        <? if (substr(CURRENT_URL,10,18) == "/commuting_pay.php") { ?>
                        <a class="navbar-item is-tab is-active" href="commuting_pay.php">비용정산</a>
                    <? } else { ?>
                        <a class="navbar-item is-tab" href="commuting_pay.php">비용정산</a>
                    <? } ?>
			        <? if (substr(CURRENT_URL,10,23) == "/commuting_approval.php") { ?>
                        <a class="navbar-item is-tab is-active" href="commuting_approval.php">휴가/외근/출장 현황</a>
                    <? } else { ?>
                        <a class="navbar-item is-tab" href="commuting_approval.php">휴가/외근/출장 현황</a>
                    <? } ?>
		    <?	}	?>
<!--
	<?
		if ($prf_id == "4") {
	?>
			<? if (substr(CURRENT_URL,10,22) == "/commuting_member3.php") { ?><a class="navbar-item is-tab is-active" href="commuting_member3.php">파견/계약직 근태 현황</a><? } else { ?> <a class="navbar-item is-tab " href="commuting_member3.php">파견/계약직 근태 현황</a><? } ?>
	<?	} ?>
-->
            </div>
        </div>
    </nav>
</div>
<!-- 서브 네비게이션 끝-->
