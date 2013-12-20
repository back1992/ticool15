//set active links for level 0
	var s5_fm_li2 = document.getElementById("s5_navv").getElementsByTagName("LI");
	var z2;
	var cc2 = 1;
	for (var z2=0; z2<s5_fm_li2.length; z2++) {
		if (s5_fm_li2[z2].className == "active") {
			if (s5_fm_li2[z2].parentNode.parentNode.tagName == "LI") {
				s5_fm_li2[z2].parentNode.parentNode.className = "active";
			}
			if (s5_fm_li2[z2].parentNode.parentNode.parentNode.parentNode.tagName == "LI") {
				s5_fm_li2[z2].parentNode.parentNode.parentNode.parentNode.className = "active";
			}
		}
	}
	
//set parent classes
	var s5_fm_li3 = document.getElementById("s5_navv").getElementsByTagName("LI");
	var z3;
	for (var z3=0; z3<s5_fm_li3.length; z3++) {
		if (s5_fm_li3[z3].parentNode.parentNode.id == "s5_navv") {
			if (s5_fm_li3[z3].innerHTML.indexOf("<UL") > 0 || s5_fm_li3[z3].innerHTML.indexOf("<ul") > 0) {
				if (s5_fm_li3[z3].className == "active") {
					s5_fm_li3[z3].className = "active s5_level_one_parent";
				}
				else if (s5_fm_li3[z3].className != "active") {
					s5_fm_li3[z3].className = "s5_level_one_parent";
				}
			}
		}
	}