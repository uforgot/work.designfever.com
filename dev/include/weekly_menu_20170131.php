			<p class="hello work_list">
			<? if (in_array($prs_position,$positionB_arr)) { ?>
				<a href="javascript:alert('주간보고서 목록에서 선택 후,\n보고서를 작성 또는 수정해 주세요.');"><? if (substr(CURRENT_URL,7,17) == "/weekly_write.php" || substr(CURRENT_URL,7,16) == "/weekly_view.php") { ?><strong> + 주간보고서 작성</strong><? } else { ?> + 주간보고서 작성<? } ?></a>
				<a href="weekly_list.php"><? if (substr(CURRENT_URL,7,16) == "/weekly_list.php") { ?><strong> + 주간보고서 목록</strong><? } else { ?> + 주간보고서 목록<? } ?></a>
			<? } ?>
			<? if ($prs_position == '팀장' || $prs_position_tmp == '팀장') { ?>
				<a href="weekly_list_team.php"><? if (substr(CURRENT_URL,7,21) == "/weekly_list_team.php") { ?><strong> + 팀원 주간보고서</strong><? } else { ?> + 팀원 주간보고서<? } ?></a>
			<? } else if (in_array($prs_position,$positionA_arr)) { ?>
				<a href="weekly_list_team.php"><? if (substr(CURRENT_URL,7,21) == "/weekly_list_team.php") { ?><strong> + 실 주간보고서</strong><? } else { ?> + 실 주간보고서<? } ?></a>
			<? } ?>
			<? if (in_array($prs_position,$positionS_arr)) { ?>
				<a href="javascript:alert('준비중 입니다.');">+  업무현황</a>
			<? } ?>
			</p>
