<!-- ���� �׺���̼� ���� -->
<div class="sub-menu-4">
    <nav class="navbar has-shadow is-size-7-mobile">
        <div class="container">
            <div class="navbar-tabs">
<?	if ($prf_id == 7) {	?>
                <? if (substr(CURRENT_URL,9,19) == "/approval_write.php" || substr(CURRENT_URL,9,21) == "/approval_rewrite.php") { ?>
                    <a class="navbar-item is-tab is-active" href="approval_write.php">���ڰ����ۼ�</a>
                <? } else { ?>
                    <a class="navbar-item is-tab" href="approval_write.php">���ڰ����ۼ�</a><? } ?>

                <? if (substr(CURRENT_URL,9,17) == "/approval_to_list" || substr(CURRENT_URL,9,17) == "/approval_my_list" || substr(CURRENT_URL,9,17) == "/approval_cc_list" || substr(CURRENT_URL,9,22) == "/approval_partner_list") { ?>
                    <a class="navbar-item is-tab is-active" href="approval_my_list.php">���ڰ��繮����</a>
                <? } else { ?>
                    <a class="navbar-item is-tab" href="approval_my_list.php">���ڰ��繮����</a><? } ?>

<?	} else {	?>
                <? if (substr(CURRENT_URL,9,19) == "/approval_write.php" || substr(CURRENT_URL,9,21) == "/approval_rewrite.php") { ?>
                    <a class="navbar-item is-tab is-active" href="approval_write.php">���ڰ����ۼ�</a>
                <? } else { ?>
                    <a class="navbar-item is-tab" href="approval_write.php">���ڰ����ۼ�</a><? } ?>

                <? if (substr(CURRENT_URL,9,17) == "/approval_to_list" || substr(CURRENT_URL,9,17) == "/approval_my_list" || substr(CURRENT_URL,9,17) == "/approval_cc_list" || substr(CURRENT_URL,9,22) == "/approval_partner_list") { ?>
                    <a class="navbar-item is-tab is-active" href="approval_my_list.php">���ڰ��繮����</a>
                 <? } else { ?>
                    <a class="navbar-item is-tab" href="approval_my_list.php">���ڰ��繮����</a><? } ?>

                <? if (substr(CURRENT_URL,9,18) == "/approval_list.php") { ?>
                        <a class="navbar-item is-tab is-active" href="approval_list.php"> ���ڰ��縮��Ʈ</a>
                <? } else { ?>
                        <a class="navbar-item is-tab" href="approval_list.php"> ���ڰ��縮��Ʈ</a><? } ?>

                <? if (substr(CURRENT_URL,9,14) == "/signature.php") { ?>
                    <a class="navbar-item is-tab is-active" href="signature.php">����.��й�ȣ���</a>
                <? } else { ?>
                    <a class="navbar-item is-tab" href="signature.php">����.��й�ȣ���</a><? } ?>

<?	}	?>

            </div>
        </div>
    </nav>
</div>
<!-- ���� �׺���̼� ��-->
