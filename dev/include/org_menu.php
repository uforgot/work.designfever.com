			<p class="hello work_list">
				<a href="person_list.php"><? if (substr(CURRENT_URL,10,12) == "/person_list") { ?><strong> + 조직도</strong><? } else { ?> + 조직도<? } ?></a>
				<a href="work_list.php"><? if (substr(CURRENT_URL,10,10) == "/work_list") { ?><strong> + 근무중사원목록</strong><? } else { ?> + 근무중사원목록<? } ?></a>
				<a href="person_addr.php"><? if (substr(CURRENT_URL,10,12) == "/person_addr") { ?><strong> + 주소록</strong><? } else { ?> + 주소록<? } ?></a>
			</p>
