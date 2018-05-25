<!-- 서브 네비게이션 시작 -->
<div class="sub-menu-3">
    <nav class="navbar has-shadow is-size-7-mobile">
        <div class="container">
            <div class="navbar-tabs">
            	<? if (substr(CURRENT_URL,9,18) == "/vacation_list.php") { ?>
	                <a class="navbar-item is-tab is-active" href="vacation_list.php">휴가내역
               <? } else { ?>
  	            	<a class="navbar-item is-tab " href="vacation_list.php">휴가내역
               <? } ?>
               		</a>
               <? if ($prf_id == "4") { ?>
                    <? if (substr(CURRENT_URL,9,20) == "/vacation_member.php") { ?>
                            <a class="navbar-item is-tab is-active" href="vacation_member.php">휴가사용현황</a>
                    <? } else { ?>
                            <a class="navbar-item is-tab" href="vacation_member.php">휴가사용현황<? } ?></a>
                    <? if (substr(CURRENT_URL,9,19) == "/vacation_total.php") { ?>
                            <a class="navbar-item is-tab is-active"href="vacation_total.php">휴가통계</a>
                    <? } else { ?>
                            <a class="navbar-item is-tab" href="vacation_total.php">휴가통계</a>
                    <? } ?>
                <?}?>
            </div>
        </div>
    </nav>
</div>
<!-- 서브 네비게이션 끝-->			