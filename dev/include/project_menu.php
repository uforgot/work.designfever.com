<!-- ���� �׺���̼� ���� -->
<div class="sub-menu-7">
    <nav class="navbar has-shadow is-size-7-mobile">
        <div class="container">
            <div class="navbar-tabs">
			<? if ($type == "ING") { ?>
				<a class="navbar-item is-tab is-active" href="/project/project_list.php?type=ING">���� ������Ʈ</a>
			<? } else { ?>
				<a class="navbar-item is-tab"href="/project/project_list.php?type=ING">���� ������Ʈ</a>
			<? } ?>
			<? if ($type == "END") { ?>
				<a class="navbar-item is-tab is-active" href="/project/project_list.php?type=END">�Ϸ� ������Ʈ</a>
			<? } else { ?>
				<a class="navbar-item is-tab" href="/project/project_list.php?type=END">�Ϸ� ������Ʈ</a>
			<? } ?>
				<? if (substr(CURRENT_URL,8,18) == "/project_total.php") { ?> <a class="navbar-item is-tab is-active" href="/project/project_total.php"> ���<? } else { ?><a class="navbar-item is-tab" href="/project/project_total.php"> ���<? } ?></a>
			<? if ($prf_id == 4) { ?>
				<? if (substr(CURRENT_URL,8,20) == "/project_collect.php" || substr(CURRENT_URL,8,19) == "/project_income.php" || substr(CURRENT_URL,8,20) == "/project_expense.php") { ?><a class="navbar-item is-tab is-active" href="/project/project_collect.php"> ������Ʈ ���ݳ���<? } else { ?><a class="navbar-item is-tab" href="/project/project_collect.php"> ������Ʈ ���ݳ���<? } ?></a>
			<? } ?>
			<? if ($board == "contact") { ?>
				<a class="navbar-item is-tab is-active" href="/project/contact_list.php">�Ƿ� ������Ʈ</a>
			<? } else { ?>
				<? if (in_array($prs_position,$positionS_arr) || in_array($prs_id,$positionC_arr) || $prs_team == '�濵������') { ?>
					<a class="navbar-item is-tab" href="/project/contact_list.php">�Ƿ� ������Ʈ</a>
				<? } ?>
			<? } ?>
            </div>
        </div>
    </nav>
</div>
<!-- ���� �׺���̼� ��-->
