				<div class="left-wrap">
					<div class="menu">
<?	if ($prf_id == 2 || $prf_id == 3 || $prf_id == 4) {	?>
						<ul  class="menu-1"><strong>���繮����</strong>
							<li><a href="approval_to_list.php"><? if (substr(CURRENT_URL,9,21) == "/approval_to_list.php") { ?><strong class="orange"> + �̰��繮��</strong><? } else { ?> + �̰��繮��<? } ?></a></li>
							<li><a href="approval_to_list_end.php"><? if (substr(CURRENT_URL,9,25) == "/approval_to_list_end.php") { ?><strong class="orange"> + ����Ϸ�</strong><? } else { ?> + ����Ϸ�<? } ?></a></li>
						</ul>
<?	}	?>
						<ul  class="menu-2"><strong>����&nbsp������</strong>
							<li><a href="approval_my_list.php"><? if (substr(CURRENT_URL,9,21) == "/approval_my_list.php") { ?><strong class="orange"> + ��Ź���</strong><? } else { ?> + ��Ź���<? } ?></a></li>
							<li><a href="approval_my_list_save.php"><? if (substr(CURRENT_URL,9,26) == "/approval_my_list_save.php") { ?><strong class="orange"> + �ӽ�����</strong><? } else { ?> + �ӽ�����<? } ?></a></li>
							<li><a href="approval_my_list_return.php"><? if (substr(CURRENT_URL,9,28) == "/approval_my_list_return.php") { ?><strong class="orange"> + �ݷ�����</strong><? } else { ?> + �ݷ�����<? } ?></a></li>
							<li><a href="approval_my_list_end.php"><? if (substr(CURRENT_URL,9,25) == "/approval_my_list_end.php") { ?><strong class="orange"> + ����Ϸ�</strong><? } else { ?> + ����Ϸ�<? } ?></a></li>
						</ul>
						<ul  class="menu-3">
							<li><a href="approval_cc_list.php"><? if (substr(CURRENT_URL,9,21) == "/approval_cc_list.php") { ?><strong class="orange"> + ���� ������</strong><? } else { ?> + ���� ������<? } ?></a></li>
							<li><a href="approval_partner_list.php"><? if (substr(CURRENT_URL,9,26) == "/approval_partner_list.php") { ?><strong class="orange"> + ���� ������</strong><? } else { ?> + ���� ������<? } ?></a></li>
						</ul>
					</div>
				</div>
