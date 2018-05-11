<!-- 서브 네비게이션 시작 -->
<div class="sub-menu-7">
    <nav class="navbar has-shadow is-size-7-mobile">
        <div class="container">
            <div class="navbar-tabs">
			    <? if (in_array($prs_position,$positionB_arr)) { ?>
                        <? if (substr(CURRENT_URL,7,17) == "/weekly_write.php" || substr(CURRENT_URL,7,16) == "/weekly_view.php") { ?>
                            <a class="navbar-item is-tab is-active" href="javascript:alert('주간보고서 목록에서 선택 후,\n보고서를 작성 또는 수정해 주세요.');">주간보고서 작성</a>
                        <? } else { ?>
                            <a class="navbar-item is-tab" href="javascript:alert('주간보고서 목록에서 선택 후,\n보고서를 작성 또는 수정해 주세요.');">주간보고서 작성</a>
                        <? } ?>

                        <? if (substr(CURRENT_URL,7,16) == "/weekly_list.php") { ?>
                            <a class="navbar-item is-tab is-active" href="weekly_list.php">주간보고서 목록</a>
                        <? } else { ?>
                            <a class="navbar-item is-tab" href="weekly_list.php">주간보고서 목록</a>
                        <? }
                } ?>
                <? if (substr(CURRENT_URL,7,25) == "/weekly_list_division.php") { ?>
                    <a class="navbar-item is-tab is-active" href="weekly_list_division.php">실/팀원 주간보고서</a>
                <? } else { ?>
                    <a class="navbar-item is-tab" href="weekly_list_division.php">실/팀원 주간보고서</a>
                <? } ?>

                <? if (in_array($prs_id,$positionC_arr) || in_array($prs_position,$positionA_arr)) { ?>
				    <? if (substr(CURRENT_URL,7,21) == "/weekly_list_team.php") { ?>
                        <a class="navbar-item is-tab is-active" href="weekly_list_team.php">실/팀원 주간보고서 관리</a>
                    <? } else { ?>
                        <a class="navbar-item is-tab" href="weekly_list_team.php">실/팀원 주간보고서 관리</a>
                    <? }
			     } ?>
			    <? if ($prs_id == "85" || $prs_id == "26") { ?>
				    <? if (substr(CURRENT_URL,7,21) == "/weekly_list_sort.php") { ?>
                        <a class="navbar-item is-tab is-active" href="weekly_list_sort.php">기간별 통계(엑셀생성)</a>
                    <? } else { ?>
                        <a class="navbar-item is-tab" href="weekly_list_sort.php">기간별 통계(엑셀생성)</a>
                    <? }
			     } ?>

	            <? if ($prs_id == "79") {?>
			        <? if (substr(CURRENT_URL,10,22) == "/weekly_list_df1.php") { ?>
                        <a class="navbar-item is-tab is-active" href="weekly_list_df1.php">df1 주간보고서</a>
                    <? } else { ?>
                        <a class="navbar-item is-tab" href="weekly_list_df1.php">df1 주간보고서</a>
                    <? }
		        }
	            ?>
            </div>
        </div>
    </nav>
</div>