			<p class="hello work_list">
			<a href="commuting_list.php"><? if (substr(CURRENT_URL,10,19) == "/commuting_list.php") { ?><strong> + ���� ��Ȳ</strong><? } else { ?> + ���� ��Ȳ<? } ?></a>
	<? 
		if ($prf_id == "2" || $prf_id == "3" || $prf_id == "4" || $prs_position_tmp == "����") { 
	?>
			<a href="commuting_member.php"><? if (substr(CURRENT_URL,10,21) == "/commuting_member.php") { ?><strong> + ���� ��Ȳ</strong><? } else { ?> + ���� ��Ȳ<? } ?></a>
	<?	}	?>
	<?
		if ($prf_id == "4") { 
	?>
			<a href="commuting_total.php"><? if (substr(CURRENT_URL,10,20) == "/commuting_total.php" || substr(CURRENT_URL,10,25) == "/commuting_total_team.php") { ?><strong> + ��/���� �������</strong><? } else { ?> + ��/���� �������<? } ?></a>
			<a href="commuting_total_year.php"><? if (substr(CURRENT_URL,10,25) == "/commuting_total_year.php") { ?><strong> + ���� �������</strong><? } else { ?> + ���� �������<? } ?></a>
			<a href="commuting_pay.php"><? if (substr(CURRENT_URL,10,18) == "/commuting_pay.php") { ?><strong> + �������</strong><? } else { ?> + �������<? } ?></a>
			<a href="commuting_approval.php"><? if (substr(CURRENT_URL,10,23) == "/commuting_approval.php") { ?><strong> + �ް�/�ܱ�/���� ��Ȳ</strong><? } else { ?> + �ް�/�ܱ�/���� ��Ȳ<? } ?></a>
	<?	}	?>
<!--
	<?
		if ($prs_id == "79" || $prf_id == "4") {
	?>
			<a href="commuting_member2.php"><? if (substr(CURRENT_URL,10,22) == "/commuting_member2.php") { ?><strong> + �ǿ� �İ� ��Ȳ</strong><? } else { ?> + �ǿ� �İ� ��Ȳ<? } ?></a></a>
	<? 
		} 
	?>
	<?
		if ($prf_id == "4") {
	?>
			<a href="commuting_member3.php"><? if (substr(CURRENT_URL,10,22) == "/commuting_member3.php") { ?><strong> + �İ�/����� ���� ��Ȳ</strong><? } else { ?> + �İ�/����� ���� ��Ȳ<? } ?></a></a>
	<?	} ?>
-->
