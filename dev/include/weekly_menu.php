			<p class="hello work_list">
			<? if (in_array($prs_position,$positionB_arr)) { ?>
				<a href="javascript:alert('�ְ����� ��Ͽ��� ���� ��,\n������ �ۼ� �Ǵ� ������ �ּ���.');">
				<? if (substr(CURRENT_URL,7,17) == "/weekly_write.php" || substr(CURRENT_URL,7,16) == "/weekly_view.php") { ?><strong> + �ְ����� �ۼ�</strong><? } else { ?> + �ְ����� �ۼ�<? } ?></a>
				<a href="weekly_list.php"><? if (substr(CURRENT_URL,7,16) == "/weekly_list.php") { ?><strong> + �ְ����� ���</strong><? } else { ?> + �ְ����� ���<? } ?></a>
			<? } ?>
				<a href="weekly_list_division.php"><? if (substr(CURRENT_URL,7,25) == "/weekly_list_division.php") { ?><strong> + ��/���� �ְ�����</strong><? } else { ?> + ��/���� �ְ�����<? } ?></a>
			<? if (in_array($prs_id,$positionC_arr) || in_array($prs_position,$positionA_arr)) { ?>
				<a href="weekly_list_team.php"><? if (substr(CURRENT_URL,7,21) == "/weekly_list_team.php") { ?><strong> + ��/���� �ְ����� ����</strong><? } else { ?> + ��/���� �ְ����� ����<? } ?></a>
			<? } ?>
			<? if ($prs_id == "85" || $prs_id == "26") { ?>
				<a href="weekly_list_sort.php"><? if (substr(CURRENT_URL,7,21) == "/weekly_list_sort.php") { ?><strong> + �Ⱓ�� ���(��������)</strong><? } else { ?> + �Ⱓ�� ���(��������)<? } ?></a>
			<? } ?>
	<?
		if ($prs_id == "79") {
	?>
			<a href="weekly_list_df1.php"><? if (substr(CURRENT_URL,10,22) == "/weekly_list_df1.php") { ?><strong> + df1 �ְ�����</strong><? } else { ?> + df1 �ְ�����<? } ?></a></a>
	<? 
		} 
	?>
			</p>



