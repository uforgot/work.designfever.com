				<div class="left-wrap">
					<div class="menu">
<?	if ($prf_id == 2 || $prf_id == 3 || $prf_id == 4) {	?>
						<ul  class="menu-1"><strong>결재문서함</strong>
							<li><a href="approval_to_list.php"><? if (substr(CURRENT_URL,9,21) == "/approval_to_list.php") { ?><strong class="orange"> + 미결재문서</strong><? } else { ?> + 미결재문서<? } ?></a></li>
							<li><a href="approval_to_list_end.php"><? if (substr(CURRENT_URL,9,25) == "/approval_to_list_end.php") { ?><strong class="orange"> + 결재완료</strong><? } else { ?> + 결재완료<? } ?></a></li>
						</ul>
<?	}	?>
						<ul  class="menu-2"><strong>개인&nbsp문서함</strong>
							<li><a href="approval_my_list.php"><? if (substr(CURRENT_URL,9,21) == "/approval_my_list.php") { ?><strong class="orange"> + 상신문서</strong><? } else { ?> + 상신문서<? } ?></a></li>
							<li><a href="approval_my_list_save.php"><? if (substr(CURRENT_URL,9,26) == "/approval_my_list_save.php") { ?><strong class="orange"> + 임시저장</strong><? } else { ?> + 임시저장<? } ?></a></li>
							<li><a href="approval_my_list_return.php"><? if (substr(CURRENT_URL,9,28) == "/approval_my_list_return.php") { ?><strong class="orange"> + 반려문서</strong><? } else { ?> + 반려문서<? } ?></a></li>
							<li><a href="approval_my_list_end.php"><? if (substr(CURRENT_URL,9,25) == "/approval_my_list_end.php") { ?><strong class="orange"> + 결재완료</strong><? } else { ?> + 결재완료<? } ?></a></li>
						</ul>
						<ul  class="menu-3">
							<li><a href="approval_cc_list.php"><? if (substr(CURRENT_URL,9,21) == "/approval_cc_list.php") { ?><strong class="orange"> + 참조 문서함</strong><? } else { ?> + 참조 문서함<? } ?></a></li>
							<li><a href="approval_partner_list.php"><? if (substr(CURRENT_URL,9,26) == "/approval_partner_list.php") { ?><strong class="orange"> + 동반 문서함</strong><? } else { ?> + 동반 문서함<? } ?></a></li>
						</ul>
					</div>
				</div>
