<?	if ($prf_id == 7) {	?>
			<p class="approvalDocument-title">
				<a href="approval_write.php"><? if (substr(CURRENT_URL,9,19) == "/approval_write.php" || substr(CURRENT_URL,9,21) == "/approval_rewrite.php") { ?><strong> + ���ڰ����ۼ�</strong><? } else { ?> + ���ڰ����ۼ�<? } ?></a>
				<a href="approval_my_list.php"><? if (substr(CURRENT_URL,9,17) == "/approval_to_list" || substr(CURRENT_URL,9,17) == "/approval_my_list" || substr(CURRENT_URL,9,17) == "/approval_cc_list" || substr(CURRENT_URL,9,22) == "/approval_partner_list") { ?><strong> + ���ڰ��繮����</strong><? } else { ?> + ���ڰ��繮����<? } ?></a>
			</p>
<?	} else {	?>
			<p class="approvalDocument-title">
				<a href="approval_write.php"><? if (substr(CURRENT_URL,9,19) == "/approval_write.php" || substr(CURRENT_URL,9,21) == "/approval_rewrite.php") { ?><strong> + ���ڰ����ۼ�</strong><? } else { ?> + ���ڰ����ۼ�<? } ?></a>
				<a href="approval_my_list.php"><? if (substr(CURRENT_URL,9,17) == "/approval_to_list" || substr(CURRENT_URL,9,17) == "/approval_my_list" || substr(CURRENT_URL,9,17) == "/approval_cc_list" || substr(CURRENT_URL,9,22) == "/approval_partner_list") { ?><strong> + ���ڰ��繮����</strong><? } else { ?> + ���ڰ��繮����<? } ?></a>
				<a href="approval_list.php"><? if (substr(CURRENT_URL,9,18) == "/approval_list.php") { ?><strong> + ���ڰ��縮��Ʈ</strong><? } else { ?> + ���ڰ��縮��Ʈ<? } ?></a>
				<a href="signature.php"><? if (substr(CURRENT_URL,9,14) == "/signature.php") { ?><strong> + ����.��й�ȣ���</strong><? } else { ?> + ����.��й�ȣ���<? } ?></a>
			</p>
<?	}	?>
