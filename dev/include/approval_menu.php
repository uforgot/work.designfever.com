<!-- 서브 네비게이션 시작 -->
<div class="sub-menu-4">
    <nav class="navbar has-shadow is-size-7-mobile">
        <div class="container">
            <div class="navbar-tabs">
<?	if ($prf_id == 7) {	?>
                <? if (substr(CURRENT_URL,9,19) == "/approval_write.php" || substr(CURRENT_URL,9,21) == "/approval_rewrite.php") { ?>
                    <a class="navbar-item is-tab is-active" href="approval_write.php">전자결재작성</a>
                <? } else { ?>
                    <a class="navbar-item is-tab" href="approval_write.php">전자결재작성</a><? } ?>

                <? if (substr(CURRENT_URL,9,17) == "/approval_to_list" || substr(CURRENT_URL,9,17) == "/approval_my_list" || substr(CURRENT_URL,9,17) == "/approval_cc_list" || substr(CURRENT_URL,9,22) == "/approval_partner_list") { ?>
                    <a class="navbar-item is-tab is-active" href="approval_my_list.php">전자결재문서함</a>
                <? } else { ?>
                    <a class="navbar-item is-tab" href="approval_my_list.php">전자결재문서함</a><? } ?>

<?	} else {	?>
                <? if (substr(CURRENT_URL,9,19) == "/approval_write.php" || substr(CURRENT_URL,9,21) == "/approval_rewrite.php") { ?>
                    <a class="navbar-item is-tab is-active" href="approval_write.php">전자결재작성</a>
                <? } else { ?>
                    <a class="navbar-item is-tab" href="approval_write.php">전자결재작성</a><? } ?>

                <? if (substr(CURRENT_URL,9,17) == "/approval_to_list" || substr(CURRENT_URL,9,17) == "/approval_my_list" || substr(CURRENT_URL,9,17) == "/approval_cc_list" || substr(CURRENT_URL,9,22) == "/approval_partner_list") { ?>
                    <a class="navbar-item is-tab is-active" href="approval_my_list.php">전자결재문서함</a>
                 <? } else { ?>
                    <a class="navbar-item is-tab" href="approval_my_list.php">전자결재문서함</a><? } ?>

                <? if (substr(CURRENT_URL,9,18) == "/approval_list.php") { ?>
                        <a class="navbar-item is-tab is-active" href="approval_list.php"> 전자결재리스트</a>
                <? } else { ?>
                        <a class="navbar-item is-tab" href="approval_list.php"> 전자결재리스트</a><? } ?>

                <? if (substr(CURRENT_URL,9,14) == "/signature.php") { ?>
                    <a class="navbar-item is-tab is-active" href="signature.php">서명.비밀번호등록</a>
                <? } else { ?>
                    <a class="navbar-item is-tab" href="signature.php">서명.비밀번호등록</a><? } ?>

<?	}	?>

            </div>
        </div>
    </nav>
</div>
<!-- 서브 네비게이션 끝-->
