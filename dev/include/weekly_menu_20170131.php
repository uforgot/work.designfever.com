			<p class="hello work_list">
			<? if (in_array($prs_position,$positionB_arr)) { ?>
				<a href="javascript:alert('�ְ����� ��Ͽ��� ���� ��,\n������ �ۼ� �Ǵ� ������ �ּ���.');"><? if (substr(CURRENT_URL,7,17) == "/weekly_write.php" || substr(CURRENT_URL,7,16) == "/weekly_view.php") { ?><strong> + �ְ����� �ۼ�</strong><? } else { ?> + �ְ����� �ۼ�<? } ?></a>
				<a href="weekly_list.php"><? if (substr(CURRENT_URL,7,16) == "/weekly_list.php") { ?><strong> + �ְ����� ���</strong><? } else { ?> + �ְ����� ���<? } ?></a>
			<? } ?>
			<? if ($prs_position == '����' || $prs_position_tmp == '����') { ?>
				<a href="weekly_list_team.php"><? if (substr(CURRENT_URL,7,21) == "/weekly_list_team.php") { ?><strong> + ���� �ְ�����</strong><? } else { ?> + ���� �ְ�����<? } ?></a>
			<? } else if (in_array($prs_position,$positionA_arr)) { ?>
				<a href="weekly_list_team.php"><? if (substr(CURRENT_URL,7,21) == "/weekly_list_team.php") { ?><strong> + �� �ְ�����</strong><? } else { ?> + �� �ְ�����<? } ?></a>
			<? } ?>
			<? if (in_array($prs_position,$positionS_arr)) { ?>
				<a href="javascript:alert('�غ��� �Դϴ�.');">+  ������Ȳ</a>
			<? } ?>
			</p>
