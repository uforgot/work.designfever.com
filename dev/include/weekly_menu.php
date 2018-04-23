			<p class="hello work_list">
			<? if (in_array($prs_position,$positionB_arr)) { ?>
				<a href="javascript:alert('주간보고서 목록에서 선택 후,\n보고서를 작성 또는 수정해 주세요.');">
				<? if (substr(CURRENT_URL,7,17) == "/weekly_write.php" || substr(CURRENT_URL,7,16) == "/weekly_view.php") { ?><strong> + 주간보고서 작성</strong><? } else { ?> + 주간보고서 작성<? } ?></a>
				<a href="weekly_list.php"><? if (substr(CURRENT_URL,7,16) == "/weekly_list.php") { ?><strong> + 주간보고서 목록</strong><? } else { ?> + 주간보고서 목록<? } ?></a>
			<? } ?>
				<a href="weekly_list_division.php"><? if (substr(CURRENT_URL,7,25) == "/weekly_list_division.php") { ?><strong> + 실/팀원 주간보고서</strong><? } else { ?> + 실/팀원 주간보고서<? } ?></a>
			<? if (in_array($prs_id,$positionC_arr) || in_array($prs_position,$positionA_arr)) { ?>
				<a href="weekly_list_team.php"><? if (substr(CURRENT_URL,7,21) == "/weekly_list_team.php") { ?><strong> + 실/팀원 주간보고서 관리</strong><? } else { ?> + 실/팀원 주간보고서 관리<? } ?></a>
			<? } ?>
			<? if ($prs_id == "85" || $prs_id == "26") { ?>
				<a href="weekly_list_sort.php"><? if (substr(CURRENT_URL,7,21) == "/weekly_list_sort.php") { ?><strong> + 기간별 통계(엑셀생성)</strong><? } else { ?> + 기간별 통계(엑셀생성)<? } ?></a>
			<? } ?>
	<?
		if ($prs_id == "79") {
	?>
			<a href="weekly_list_df1.php"><? if (substr(CURRENT_URL,10,22) == "/weekly_list_df1.php") { ?><strong> + df1 주간보고서</strong><? } else { ?> + df1 주간보고서<? } ?></a></a>
	<? 
		} 
	?>
			</p>



