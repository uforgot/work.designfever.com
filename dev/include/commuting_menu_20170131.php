			<p class="hello work_list">
			<a href="commuting_list.php"><? if (substr(CURRENT_URL,10,19) == "/commuting_list.php") { ?><strong> + 근태 현황</strong><? } else { ?> + 근태 현황<? } ?></a>
	<? 
		if ($prf_id == "2" || $prf_id == "3" || $prf_id == "4" || $prs_position_tmp == "팀장") { 
	?>
			<a href="commuting_member.php"><? if (substr(CURRENT_URL,10,21) == "/commuting_member.php") { ?><strong> + 팀원 현황</strong><? } else { ?> + 팀원 현황<? } ?></a>
	<?	}	?>
	<?
		if ($prf_id == "4") { 
	?>
			<a href="commuting_total.php"><? if (substr(CURRENT_URL,10,20) == "/commuting_total.php" || substr(CURRENT_URL,10,25) == "/commuting_total_team.php") { ?><strong> + 일/월별 근태통계</strong><? } else { ?> + 일/월별 근태통계<? } ?></a>
			<a href="commuting_total_year.php"><? if (substr(CURRENT_URL,10,25) == "/commuting_total_year.php") { ?><strong> + 연별 근태통계</strong><? } else { ?> + 연별 근태통계<? } ?></a>
			<a href="commuting_pay.php"><? if (substr(CURRENT_URL,10,18) == "/commuting_pay.php") { ?><strong> + 비용정산</strong><? } else { ?> + 비용정산<? } ?></a>
			<a href="commuting_approval.php"><? if (substr(CURRENT_URL,10,23) == "/commuting_approval.php") { ?><strong> + 휴가/외근/출장 현황</strong><? } else { ?> + 휴가/외근/출장 현황<? } ?></a>
	<?	}	?>
<!--
	<?
		if ($prs_id == "79" || $prf_id == "4") {
	?>
			<a href="commuting_member2.php"><? if (substr(CURRENT_URL,10,22) == "/commuting_member2.php") { ?><strong> + 의왕 파견 현황</strong><? } else { ?> + 의왕 파견 현황<? } ?></a></a>
	<? 
		} 
	?>
	<?
		if ($prf_id == "4") {
	?>
			<a href="commuting_member3.php"><? if (substr(CURRENT_URL,10,22) == "/commuting_member3.php") { ?><strong> + 파견/계약직 근태 현황</strong><? } else { ?> + 파견/계약직 근태 현황<? } ?></a></a>
	<?	} ?>
-->
