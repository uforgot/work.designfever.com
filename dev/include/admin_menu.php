			<p class="hello work_list">
				<a href="vacation_set.php"><? if (substr(CURRENT_URL,0,19) == "/admin/vacation_set") { ?><strong> + �ް� ����</strong><? } else { ?> + �ް� ����<? } ?></a>
				<a href="vacation_use.php"><? if (substr(CURRENT_URL,0,19) == "/admin/vacation_use") { ?><strong> + �ް� ��� ��Ȳ</strong><? } else { ?> + �ް� ��� ��Ȳ<? } ?></a>
				<!--a href="rfid_list.php"><? if (substr(CURRENT_URL,0,12) == "/admin/rfid_") { ?><strong> + ��/��� ī�� ����</strong><? } else { ?> + ��/��� ī�� ����<? } ?></a-->
				<a href="approval_list.php"><? if (substr(CURRENT_URL,0,16) == "/admin/approval_" || substr(CURRENT_URL,0,11) == "/admin/doc_") { ?><strong> + ���ڰ���</strong><? } else { ?> + ���ڰ���<? } ?></a>
				<a href="auth_set.php"><? if (substr(CURRENT_URL,0,12) == "/admin/auth_") { ?><strong> + �μ�/����/���� ����</strong><? } else { ?> + �μ�/����/���� ����<? } ?></a>
				<a href="id_change.php"><? if (substr(CURRENT_URL,0,10) == "/admin/id_") { ?><strong> + ID ����</strong><? } else { ?> + ID ����<? } ?></a>
				<!--a href="beacon_log.php"><? if (substr(CURRENT_URL,0,14) == "/admin/beacon_") { ?><strong> + BEACON �α�</strong><? } else { ?> + BEACON �α�<? } ?></a-->
			</p>
