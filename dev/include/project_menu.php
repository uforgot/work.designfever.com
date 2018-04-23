			<p class="hello work_list">
			<? if ($type == "ING") { ?>
				<a href="/project/project_list.php?type=ING"><strong> + 진행 프로젝트</strong></a>
			<? } else { ?>
				<a href="/project/project_list.php?type=ING">+  진행 프로젝트</a>
			<? } ?>
			<? if ($type == "END") { ?>
				<a href="/project/project_list.php?type=END"><strong> + 완료 프로젝트</strong></a>
			<? } else { ?>
				<a href="/project/project_list.php?type=END">+  완료 프로젝트</a>
			<? } ?>
				<a href="/project/project_total.php"><? if (substr(CURRENT_URL,8,18) == "/project_total.php") { ?><strong> + 통계</strong><? } else { ?> + 통계<? } ?></a>
			<? if ($prf_id == 4) { ?>
				<a href="/project/project_collect.php"><? if (substr(CURRENT_URL,8,20) == "/project_collect.php" || substr(CURRENT_URL,8,19) == "/project_income.php" || substr(CURRENT_URL,8,20) == "/project_expense.php") { ?><strong> + 프로젝트 수금내역</strong><? } else { ?> + 프로젝트 수금내역<? } ?></a>
			<? } ?>
			<? if ($board == "contact") { ?>
				<a href="/project/contact_list.php"><strong>+  의뢰 프로젝트</strong></a>
			<? } else { ?>
				<? if (in_array($prs_position,$positionS_arr) || in_array($prs_id,$positionC_arr) || $prs_team == '경영지원팀') { ?>
					<a href="/project/contact_list.php">+  의뢰 프로젝트</a>
				<? } ?>
			<? } ?>
			</p>
