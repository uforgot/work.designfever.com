<?	if ($prf_id == 7) {	?>
			<p class="approvalDocument-title">
				<a href="approval_write.php"><? if (substr(CURRENT_URL,9,19) == "/approval_write.php" || substr(CURRENT_URL,9,21) == "/approval_rewrite.php") { ?><strong> + 전자결재작성</strong><? } else { ?> + 전자결재작성<? } ?></a>
				<a href="approval_my_list.php"><? if (substr(CURRENT_URL,9,17) == "/approval_to_list" || substr(CURRENT_URL,9,17) == "/approval_my_list" || substr(CURRENT_URL,9,17) == "/approval_cc_list" || substr(CURRENT_URL,9,22) == "/approval_partner_list") { ?><strong> + 전자결재문서함</strong><? } else { ?> + 전자결재문서함<? } ?></a>
			</p>
<?	} else {	?>
			<p class="approvalDocument-title">
				<a href="approval_write.php"><? if (substr(CURRENT_URL,9,19) == "/approval_write.php" || substr(CURRENT_URL,9,21) == "/approval_rewrite.php") { ?><strong> + 전자결재작성</strong><? } else { ?> + 전자결재작성<? } ?></a>
				<a href="approval_my_list.php"><? if (substr(CURRENT_URL,9,17) == "/approval_to_list" || substr(CURRENT_URL,9,17) == "/approval_my_list" || substr(CURRENT_URL,9,17) == "/approval_cc_list" || substr(CURRENT_URL,9,22) == "/approval_partner_list") { ?><strong> + 전자결재문서함</strong><? } else { ?> + 전자결재문서함<? } ?></a>
				<a href="approval_list.php"><? if (substr(CURRENT_URL,9,18) == "/approval_list.php") { ?><strong> + 전자결재리스트</strong><? } else { ?> + 전자결재리스트<? } ?></a>
				<a href="signature.php"><? if (substr(CURRENT_URL,9,14) == "/signature.php") { ?><strong> + 서명.비밀번호등록</strong><? } else { ?> + 서명.비밀번호등록<? } ?></a>
			</p>
<?	}	?>
