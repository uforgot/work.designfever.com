<?	if ($prf_id == 7) {	?>

		<? if ($board == "edit2") { ?>			
			 <a href="/book/book_list.php?board=edit2" class="navbar-item is-tab is-active" >근태수정요청</a>
		<? } else { ?>			
			  <a href="/book/book_list.php?board=edit2" class="navbar-item is-tab">근태수정요청</a>
		<? } ?>
				
			
<?	} else { ?>
	
		<? if ($board == "book") { ?>			
			<a href="/book/book_list.php?board=book" class="navbar-item is-tab is-active" >회사생활백서</a>
		<? } else { ?>			
			<a href="/book/book_list.php?board=book" class="navbar-item is-tab">회사생활백서</a>
		<? } ?>
		<? if ($board == "free") { ?>
			<a href="/book/book_list.php?board=free" class="navbar-item is-tab is-active" >자유게시판</a>
		<? } else { ?>
			<a href="/book/book_list.php?board=free" class="navbar-item is-tab">자유게시판</a>
		<? } ?>
		<? if ($board == "ilab") { ?>
			<a href="/book/book_list.php?board=ilab" class="navbar-item is-tab is-active">iLab</a>
		<? } else { ?>
			<a href="/book/book_list.php?board=ilab" class="navbar-item is-tab">iLab</a>
		<? } ?>
		<? if ($board == "club") { ?>
			<a href="/book/book_list.php?board=club" class="navbar-item is-tab is-active"><strong>동호회게시판</strong></a>
		<? } else { ?>
			<a href="/book/book_list.php?board=club" class="navbar-item is-tab">동호회게시판</a>
		<? } ?>
		
		<? if ($board == "edit") { ?>			
			 <a href="/book/book_list.php?board=edit" class="navbar-item is-tab is-active">근태수정요청</a>
		<? } else { ?>
			 <a href="/book/book_list.php?board=edit" class="navbar-item is-tab">근태수정요청</a>
		<? } ?>				
		
<!--
		<? if ($board == "edit2") { ?>
			<a href="/book/book_list.php?board=edit2" class="navbar-item is-tab is-active">근태수정요청(파견/계약직)</a>
		<? } else { ?>
			<? if ($prs_id == "79" || $prf_id == 4) { ?>
			<a href="/book/book_list.php?board=edit2" class="navbar-item is-tab">근태수정요청(파견/계약직)</a>
			<? } ?>
		<? } ?>
-->
		<? if ($board == "happy") { ?>
			<a href="/book/book_list.php?board=happy" class="navbar-item is-tab is-active">행복연구소</a>
		<? } else { ?>
			<? if (in_array($prs_id,$happyLab_arr) == true) { ?>
				<a href="/book/book_list.php?board=happy" class="navbar-item is-tab">행복연구소</a>
			<? } ?>
		<? } ?>

		<? if ($board == "partner") { ?>
			<a href="/book/book_list.php?board=partner" class="navbar-item is-tab is-active">파트너</a>
		<? } else { ?>
			<? if (in_array($prf_id,array("2","3","4")) || in_array($prs_team,$partner_arr) == true) { ?>
				<a href="/book/book_list.php?board=partner" class="navbar-item is-tab">파트너</a>
			<? } ?>
		<? } ?>
	
<?	}	?>


		<!-- 테스트 -->
		<? 
			if($prs_id == '85') {
				//권한 체크(팀장이상)
				if (in_array($prf_id,array("2","3","4"))) 
				{ 
					//echo $prf_id." / ".$prs_team."<<<<<<<<";
				}
			} 
		?>