var s5_tm = document.getElementsByTagName("SPAN");
var s5_tm_aa = 1;
	for (var s5_tm_y=0; s5_tm_y<s5_tm.length; s5_tm_y++) {
		if (s5_tm[s5_tm_y].className == "s5_bottom_text") {
			s5_tm[s5_tm_y].id="s5_tm_" + s5_tm_aa;
			s5_tm_aa = s5_tm_aa + 1;
		}
	}
	
	if (document.getElementById("s5_tm_1")) {
		document.getElementById("s5_tm_1").innerHTML = s5_text_menu_1;
	}
	
	if (document.getElementById("s5_tm_2")) {
		document.getElementById("s5_tm_2").innerHTML = s5_text_menu_2;
	}
	
	if (document.getElementById("s5_tm_3")) {
		document.getElementById("s5_tm_3").innerHTML = s5_text_menu_3;
	}
	
	if (document.getElementById("s5_tm_4")) {
		document.getElementById("s5_tm_4").innerHTML = s5_text_menu_4;
	}
	
	if (document.getElementById("s5_tm_5")) {
		document.getElementById("s5_tm_5").innerHTML = s5_text_menu_5;
	}
	
	if (document.getElementById("s5_tm_6")) {
		document.getElementById("s5_tm_6").innerHTML = s5_text_menu_6;
	}
	
	if (document.getElementById("s5_tm_7")) {
		document.getElementById("s5_tm_7").innerHTML = s5_text_menu_7;
	}
	
	if (document.getElementById("s5_tm_8")) {
		document.getElementById("s5_tm_8").innerHTML = s5_text_menu_8;
	}
	
	if (document.getElementById("s5_tm_9")) {
		document.getElementById("s5_tm_9").innerHTML = s5_text_menu_9;
	}
	
	if (document.getElementById("s5_tm_10")) {
		document.getElementById("s5_tm_10").innerHTML = s5_text_menu_10;
	}
