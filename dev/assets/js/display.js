$("document").ready(function() {
	// table
	$(".teamtable, .stattable, .vactable").find("tr:odd").addClass("odd");

	$("a[href='#registration']").click(function() {
		$("#registration").fadeIn();
	});
	$(".layerpop").children("button").click(function() {
		$(this).parent(".layerpop").fadeOut();
	});
	$(".layerpops").children("button").click(function() {
		$(this).parent(".layerpops").fadeOut();
	});
	$("#dfiddiv").children("button").click(function() {
		call_idoverlap();
	});	
	$("a[href='#section_findzip']").click(function() {
		call_findzip();
		return false;
	});	

	// selecttag
	var selecttagStatus = false;
	$(".selecttag").children("li").click(function() {
		if (!selecttagStatus)		{
			$(this).siblings("li").show(function() {
				$(this).parent("ul").addClass("open");
				selecttagStatus = true;
			});
		} else {
			$(this).siblings("li").removeClass("here").hide(function() {
				selecttagStatus = false;
			});
			$(this).addClass("here");
			$(this).parent("ul").removeClass("open");
		}
	});
	$(document).click(function() {
		if (selecttagStatus)		{
			$(".selecttag.open").removeClass("open");
			$(".selecttag").children("li").not(".here").hide();
			selecttagStatus = false;
		}
	}); //selecttag

	// teamchart table
	$(".status01, .status02").each(function() {
		$(this).children().wrapAll("<div class='hastatus' />");		
	});
	$(".teamstatusdiv").each(function() {
		$("<span class='arrow' />").appendTo(this);
	});

	// 
	$(".workchartdiv").children("nav").children("a").click(function() {
		var thsdiv = $(this).attr("href");
		$(".workchartdiv").hide();
		 $(thsdiv).show();
		 return false;
	});
});

function call_idoverlap() {
	$("#section_idoverlap").fadeIn();
}
function call_findzip() {
	$("#section_findzip").fadeIn();
}
function call_warning() {
	$("#section_warning").fadeIn();
}
function call_datemodify() {
	$("#section_datemodify").fadeIn();
}