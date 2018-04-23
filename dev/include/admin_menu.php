			<p class="hello work_list">
				<a href="vacation_set.php"><? if (substr(CURRENT_URL,0,19) == "/admin/vacation_set") { ?><strong> + 휴가 설정</strong><? } else { ?> + 휴가 설정<? } ?></a>
				<a href="vacation_use.php"><? if (substr(CURRENT_URL,0,19) == "/admin/vacation_use") { ?><strong> + 휴가 사용 현황</strong><? } else { ?> + 휴가 사용 현황<? } ?></a>
				<!--a href="rfid_list.php"><? if (substr(CURRENT_URL,0,12) == "/admin/rfid_") { ?><strong> + 출/퇴근 카드 설정</strong><? } else { ?> + 출/퇴근 카드 설정<? } ?></a-->
				<a href="approval_list.php"><? if (substr(CURRENT_URL,0,16) == "/admin/approval_" || substr(CURRENT_URL,0,11) == "/admin/doc_") { ?><strong> + 전자결재</strong><? } else { ?> + 전자결재<? } ?></a>
				<a href="auth_set.php"><? if (substr(CURRENT_URL,0,12) == "/admin/auth_") { ?><strong> + 부서/직급/권한 관리</strong><? } else { ?> + 부서/직급/권한 관리<? } ?></a>
				<a href="id_change.php"><? if (substr(CURRENT_URL,0,10) == "/admin/id_") { ?><strong> + ID 변경</strong><? } else { ?> + ID 변경<? } ?></a>
				<!--a href="beacon_log.php"><? if (substr(CURRENT_URL,0,14) == "/admin/beacon_") { ?><strong> + BEACON 로그</strong><? } else { ?> + BEACON 로그<? } ?></a-->
			</p>
