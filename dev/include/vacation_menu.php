<!-- ���� �׺���̼� ���� -->
<div class="sub-menu-3">
    <nav class="navbar has-shadow is-size-7-mobile">
        <div class="container">
            <div class="navbar-tabs">
            	<? if (substr(CURRENT_URL,9,18) == "/vacation_list.php") { ?>
	                <a class="navbar-item is-tab is-active" href="vacation_list.php">�ް�����
               <? } else { ?>
  	            	<a class="navbar-item is-tab " href="vacation_list.php">�ް�����
               <? } ?>
               		</a>
               <? if ($prf_id == "4") { ?>
                    <? if (substr(CURRENT_URL,9,20) == "/vacation_member.php") { ?>
                            <a class="navbar-item is-tab is-active" href="vacation_member.php">�ް������Ȳ</a>
                    <? } else { ?>
                            <a class="navbar-item is-tab" href="vacation_member.php">�ް������Ȳ<? } ?></a>
                    <? if (substr(CURRENT_URL,9,19) == "/vacation_total.php") { ?>
                            <a class="navbar-item is-tab is-active"href="vacation_total.php">�ް����</a>
                    <? } else { ?>
                            <a class="navbar-item is-tab" href="vacation_total.php">�ް����</a>
                    <? } ?>
                <?}?>
            </div>
        </div>
    </nav>
</div>
<!-- ���� �׺���̼� ��-->			