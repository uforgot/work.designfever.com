<!-- ���� �׺���̼� ���� -->
<div class="sub-menu-7">
    <nav class="navbar has-shadow is-size-7-mobile">
        <div class="container">
            <div class="navbar-tabs">
                <? if (substr(CURRENT_URL,10,19) == "/commuting_list.php") { ?>
                    <a class="navbar-item is-tab is-active" href="commuting_list.php">���� ��Ȳ</a>
                 <? } else { ?>
                     <a class="navbar-item is-tab" href="commuting_list.php">���� ��Ȳ</a>
                 <? } ?>
			    
                <? if (substr(CURRENT_URL,10,21) == "/commuting_member.php") { ?>
                    <a class="navbar-item is-tab is-active" href="commuting_member.php">��/���� ������Ȳ</a>
                <? } else { ?>
                    <a class="navbar-item is-tab  "href="commuting_member.php">��/���� ������Ȳ</a>
                <? } ?>

		        <?	if ($prf_id == "4") { ?>
                    <? if (substr(CURRENT_URL,10,20) == "/commuting_total.php" || substr(CURRENT_URL,10,25) == "/commuting_total_team.php") { ?>
                        <a class="navbar-item is-tab is-active" href="commuting_total.php">��/���� �������</a>
                    <? } else { ?>
                        <a class="navbar-item is-tab" href="commuting_total.php">��/���� �������</a>
                    <? } ?>
			        <? if (substr(CURRENT_URL,10,25) == "/commuting_total_year.php") { ?>
                        <a class="navbar-item is-tab is-active" href="commuting_total_year.php">���� �������</a>
                    <? } else { ?>
                        <a class="navbar-item is-tab" href="commuting_total_year.php">���� �������</a>
                    <? } ?>
			        <? if (substr(CURRENT_URL,10,18) == "/commuting_pay.php") { ?>
                        <a class="navbar-item is-tab is-active" href="commuting_pay.php">�������</a>
                    <? } else { ?>
                        <a class="navbar-item is-tab" href="commuting_pay.php">�������</a>
                    <? } ?>
			        <? if (substr(CURRENT_URL,10,23) == "/commuting_approval.php") { ?>
                        <a class="navbar-item is-tab is-active" href="commuting_approval.php">�ް�/�ܱ�/���� ��Ȳ</a>
                    <? } else { ?>
                        <a class="navbar-item is-tab" href="commuting_approval.php">�ް�/�ܱ�/���� ��Ȳ</a>
                    <? } ?>
		    <?	}	?>
<!--
	<?
		if ($prf_id == "4") {
	?>
			<? if (substr(CURRENT_URL,10,22) == "/commuting_member3.php") { ?><a class="navbar-item is-tab is-active" href="commuting_member3.php">�İ�/����� ���� ��Ȳ</a><? } else { ?> <a class="navbar-item is-tab " href="commuting_member3.php">�İ�/����� ���� ��Ȳ</a><? } ?>
	<?	} ?>
-->
            </div>
        </div>
    </nav>
</div>
<!-- ���� �׺���̼� ��-->
