<!-- 서브 네비게이션 시작 -->
<div class="sub-menu-7">
    <nav class="navbar has-shadow is-size-7-mobile">
        <div class="container">
            <div class="navbar-tabs">
                <? if (substr(CURRENT_URL,10,12) == "/person_list") { ?>
				    <a class="navbar-item is-tab is-active" href="person_list.php">조직도
                <? } else { ?>
                    <a class="navbar-item is-tab" href="person_list.php">조직도
                <? } ?>
                    </a>
                <? if (substr(CURRENT_URL,10,12) == "/person_addr") { ?>
				    <a class="navbar-item is-tab is-active"  href="person_addr.php">주소록
                <? } else { ?>
                    <a class="navbar-item is-tab" href="person_addr.php">주소록
                <? } ?></a>
            </div>
        </div>
    </nav>
</div>
<!-- 서브 네비게이션 끝-->