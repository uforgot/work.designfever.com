			<p class="hello work_list">
			<? if ($type == "ING") { ?>
				<a href="/project/project_list.php?type=ING"><strong> + ���� ������Ʈ</strong></a>
			<? } else { ?>
				<a href="/project/project_list.php?type=ING">+  ���� ������Ʈ</a>
			<? } ?>
			<? if ($type == "END") { ?>
				<a href="/project/project_list.php?type=END"><strong> + �Ϸ� ������Ʈ</strong></a>
			<? } else { ?>
				<a href="/project/project_list.php?type=END">+  �Ϸ� ������Ʈ</a>
			<? } ?>
				<a href="/project/project_total.php"><? if (substr(CURRENT_URL,8,18) == "/project_total.php") { ?><strong> + ���</strong><? } else { ?> + ���<? } ?></a>
			<? if ($prf_id == 4) { ?>
				<a href="/project/project_collect.php"><? if (substr(CURRENT_URL,8,20) == "/project_collect.php" || substr(CURRENT_URL,8,19) == "/project_income.php" || substr(CURRENT_URL,8,20) == "/project_expense.php") { ?><strong> + ������Ʈ ���ݳ���</strong><? } else { ?> + ������Ʈ ���ݳ���<? } ?></a>
			<? } ?>
			<? if ($board == "contact") { ?>
				<a href="/project/contact_list.php"><strong>+  �Ƿ� ������Ʈ</strong></a>
			<? } else { ?>
				<? if (in_array($prs_position,$positionS_arr) || in_array($prs_id,$positionC_arr) || $prs_team == '�濵������') { ?>
					<a href="/project/contact_list.php">+  �Ƿ� ������Ʈ</a>
				<? } ?>
			<? } ?>
			</p>
