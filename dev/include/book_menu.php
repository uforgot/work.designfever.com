<?	if ($prf_id == 7) {	?>

		<? if ($board == "edit2") { ?>			
			 <a href="/book/book_list.php?board=edit2" class="navbar-item is-tab is-active" >���¼�����û</a>
		<? } else { ?>			
			  <a href="/book/book_list.php?board=edit2" class="navbar-item is-tab">���¼�����û</a>
		<? } ?>
				
			
<?	} else { ?>
	
		<? if ($board == "book") { ?>			
			<a href="/book/book_list.php?board=book" class="navbar-item is-tab is-active" >ȸ���Ȱ�鼭</a>
		<? } else { ?>			
			<a href="/book/book_list.php?board=book" class="navbar-item is-tab">ȸ���Ȱ�鼭</a>
		<? } ?>
		<? if ($board == "free") { ?>
			<a href="/book/book_list.php?board=free" class="navbar-item is-tab is-active" >�����Խ���</a>
		<? } else { ?>
			<a href="/book/book_list.php?board=free" class="navbar-item is-tab">�����Խ���</a>
		<? } ?>
		<? if ($board == "ilab") { ?>
			<a href="/book/book_list.php?board=ilab" class="navbar-item is-tab is-active">iLab</a>
		<? } else { ?>
			<a href="/book/book_list.php?board=ilab" class="navbar-item is-tab">iLab</a>
		<? } ?>
		<? if ($board == "club") { ?>
			<a href="/book/book_list.php?board=club" class="navbar-item is-tab is-active"><strong>��ȣȸ�Խ���</strong></a>
		<? } else { ?>
			<a href="/book/book_list.php?board=club" class="navbar-item is-tab">��ȣȸ�Խ���</a>
		<? } ?>
		
		<? if ($board == "edit") { ?>			
			 <a href="/book/book_list.php?board=edit" class="navbar-item is-tab is-active">���¼�����û</a>
		<? } else { ?>
			 <a href="/book/book_list.php?board=edit" class="navbar-item is-tab">���¼�����û</a>
		<? } ?>				
		
<!--
		<? if ($board == "edit2") { ?>
			<a href="/book/book_list.php?board=edit2" class="navbar-item is-tab is-active">���¼�����û(�İ�/�����)</a>
		<? } else { ?>
			<? if ($prs_id == "79" || $prf_id == 4) { ?>
			<a href="/book/book_list.php?board=edit2" class="navbar-item is-tab">���¼�����û(�İ�/�����)</a>
			<? } ?>
		<? } ?>
-->
		<? if ($board == "happy") { ?>
			<a href="/book/book_list.php?board=happy" class="navbar-item is-tab is-active">�ູ������</a>
		<? } else { ?>
			<? if (in_array($prs_id,$happyLab_arr) == true) { ?>
				<a href="/book/book_list.php?board=happy" class="navbar-item is-tab">�ູ������</a>
			<? } ?>
		<? } ?>

		<? if ($board == "partner") { ?>
			<a href="/book/book_list.php?board=partner" class="navbar-item is-tab is-active">��Ʈ��</a>
		<? } else { ?>
			<? if (in_array($prf_id,array("2","3","4")) || in_array($prs_team,$partner_arr) == true) { ?>
				<a href="/book/book_list.php?board=partner" class="navbar-item is-tab">��Ʈ��</a>
			<? } ?>
		<? } ?>
	
<?	}	?>


		<!-- �׽�Ʈ -->
		<? 
			if($prs_id == '85') {
				//���� üũ(�����̻�)
				if (in_array($prf_id,array("2","3","4"))) 
				{ 
					//echo $prf_id." / ".$prs_team."<<<<<<<<";
				}
			} 
		?>