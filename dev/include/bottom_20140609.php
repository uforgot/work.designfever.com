	<!-- wapper set close -->
	</div>				
	</div>				
	</div>		
	</div>
	<!-- //wapper set close -->

<?
	if (CURRENT_PAGE == "login.php") {
?>
<p class="login_txt2"><img src="/img/txt_copy.gif" alt="" /></p>
<?
	} else if (substr(CURRENT_URL,0,7) == "/member") {
?>
<div class="wrapper_login">
<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
	<p class="login_txt1"><img src="/img/txt_left.gif" alt="" /></p>
	<p class="login_txt2"><img src="/img/txt_copy.gif" alt="" /></p>
</div>
<?
	} else {
?>
<p class="footer"><img src="/img/txt_copy.gif" alt="" /></p>
<?
	}
?>

<? if ($prs_login == "dfadmin" || $prs_login == "romei78") { ?>
<iframe name="hdnFrame" id="hdnFrame" width="1000" height="100" style="border:1;"></iframe>
<iframe name="hdnFrame2" id="hdnFrame2" width="0" height="0" style="border:0;"></iframe>
<? } else { ?>
<iframe name="hdnFrame" id="hdnFrame" width="0" height="0" style="border:0;"></iframe>
<iframe name="hdnFrame2" id="hdnFrame2" width="0" height="0" style="border:0;"></iframe>
<? } ?>

	<!--<script type="text/javascript" src="http://code.jquery.com/jquery-latest.js"></script> -->
