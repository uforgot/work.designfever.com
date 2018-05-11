<!-- 서브 네비게이션 시작 -->
<div class="sub-menu-7">
    <nav class="navbar has-shadow is-size-7-mobile">
        <div class="container">
            <div class="navbar-tabs">
            <? if (substr(CURRENT_URL,0,9) == "/booking/") { ?>
			    <a class="navbar-item is-tab is-active" href="/booking/booking_list.php" >회의실사용예약</a>
            <? } else { ?>
                <a class="navbar-item is-tab href="/booking/booking_list.php">회의실사용예약</a>
            <? } ?>

            <? if (substr(CURRENT_URL,0,7) == "/visit/") { ?>
			    <a class="navbar-item is-tab is-active" href="/visit/visit_list.php">사내방문예약</a>
             <? } else { ?>
                <a class="navbar-item is-tab" href="/visit/visit_list.php">사내방문예약</a>
             <? } ?>
            </div>
        </div>
    </nav>
</div>
