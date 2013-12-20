<?php

// Initially set all module values to unpublished

$vertex_positions = array('s5_pos_top_menu','s5_pos_middle_menu','s5_pos_bottom_menu','s5_pos_logo','s5_pos_breadcrumb','s5_pos_banner','s5_pos_search','s5_pos_login','s5_pos_register','s5_pos_top_row1_1','s5_pos_top_row1_2','s5_pos_top_row1_3','s5_pos_top_row1_4','s5_pos_top_row1_5','s5_pos_top_row1_6','s5_pos_top_row2_1','s5_pos_top_row2_2','s5_pos_top_row2_3','s5_pos_top_row2_4','s5_pos_top_row2_5','s5_pos_top_row2_6','s5_pos_top_row3_1','s5_pos_top_row3_2','s5_pos_top_row3_3','s5_pos_top_row3_4','s5_pos_top_row3_5','s5_pos_top_row3_6','above_columns_wrap_1','above_columns_wrap_2','above_columns_wrap_3','above_columns_wrap_4','above_columns_wrap_5','above_columns_wrap_6','s5_pos_left_top','s5_pos_left','s5_pos_left_inset','s5_pos_right_top','s5_pos_right','s5_pos_right_inset','s5_pos_middle_top_1','s5_pos_middle_top_2','s5_pos_middle_top_3','s5_pos_middle_top_4','s5_pos_middle_top_5','s5_pos_middle_top_6','s5_pos_above_body_1','s5_pos_above_body_2','s5_pos_above_body_3','s5_pos_above_body_4','s5_pos_above_body_5','s5_pos_above_body_6','s5_pos_left_bottom','s5_pos_right_bottom','s5_pos_middle_bottom_1','s5_pos_middle_bottom_2','s5_pos_middle_bottom_3','s5_pos_middle_bottom_4','s5_pos_middle_bottom_5','s5_pos_middle_bottom_6','s5_pos_below_body_1','s5_pos_below_body_2','s5_pos_below_body_3','s5_pos_below_body_4','s5_pos_below_body_5','s5_pos_below_body_6','below_columns_wrap_1','below_columns_wrap_2','below_columns_wrap_3','below_columns_wrap_4','below_columns_wrap_5','below_columns_wrap_6','s5_pos_bottom_row1_1','s5_pos_bottom_row1_2','s5_pos_bottom_row1_3','s5_pos_bottom_row1_4','s5_pos_bottom_row1_5','s5_pos_bottom_row1_6','s5_pos_bottom_row2_1','s5_pos_bottom_row2_2','s5_pos_bottom_row2_3','s5_pos_bottom_row2_4','s5_pos_bottom_row2_5','s5_pos_bottom_row2_6','s5_pos_bottom_row3_1','s5_pos_bottom_row3_2','s5_pos_bottom_row3_3','s5_pos_bottom_row3_4','s5_pos_bottom_row3_5','s5_pos_bottom_row3_6','s5_pos_debug','s5_pos_custom_1','s5_pos_custom_2','s5_pos_custom_3','s5_pos_custom_4','s5_pos_custom_5','s5_pos_custom_6','s5_pos_mobile_top_1','s5_pos_mobile_top_2','s5_pos_mobile_bottom_1','s5_pos_mobile_bottom_2','s5_pos_footer');

foreach($vertex_positions as $position) {

	$$position = 'unpublished';

	// Check if modules are published

	$position_name = str_replace('s5_pos_', '', "$position");

	if ($this->countModules("$position_name")) {
		$$position = 'published';
	}

}


// column size calculations

$s5_center_column_margin_right = 0;
$s5_center_column_margin_left = 0;
$s5_left_column_width = 0; // The combined total of left and left_inset - calculated below
$s5_right_column_width = 0; // The combined total of right and right_inset - calculated below
$s5_left_top_bottom_width = $s5_left_width + $s5_left_inset_width; // Used for left_top or left_bottom only if nothing is pubished to left or left_inset
$s5_right_top_bottom_width = $s5_right_width + $s5_right_inset_width; // Used for right_top or right_bottom only if nothing is pubished to right or right_inset

// $s5_left_width, $s5_left_inset_width, $s5_right_width, and $s5_right_inset_width are defined in parameters.php through the template configuration.

if ($s5_pos_left == "unpublished") {
	$s5_left_width = 0;
}

if ($s5_pos_left_inset == "unpublished") {
	$s5_left_inset_width = 0;
}

if ($s5_pos_right == "unpublished") {
	$s5_right_width = 0;
}

if ($s5_pos_right_inset == "unpublished") {
	$s5_right_inset_width = 0;
}

if ($s5_pos_left == "published") {
	$s5_center_column_margin_left = $s5_center_column_margin_left + $s5_left_width;
	$s5_left_column_width = $s5_left_column_width + $s5_left_width;
}

if ($s5_pos_left_inset == "published") {
	$s5_center_column_margin_left = $s5_center_column_margin_left + $s5_left_inset_width;
	$s5_left_column_width = $s5_left_column_width + $s5_left_inset_width;
}

if ($s5_pos_right == "published") {
	$s5_center_column_margin_right = $s5_center_column_margin_right + $s5_right_width;
	$s5_right_column_width = $s5_right_column_width + $s5_right_width;
}

if ($s5_pos_right_inset == "published") {
	$s5_center_column_margin_right = $s5_center_column_margin_right + $s5_right_inset_width;
	$s5_right_column_width = $s5_right_column_width + $s5_right_inset_width;
}

if ($s5_pos_right_top == "published" || $s5_pos_right_bottom == "published") {
	$s5_center_column_margin_right = $s5_right_width + $s5_right_inset_width;
	$s5_right_column_width = $s5_right_width + $s5_right_inset_width;
	if ($s5_pos_right == "unpublished" && $s5_pos_right_inset == "unpublished") {
		$s5_center_column_margin_right = $s5_right_top_bottom_width;
		$s5_right_column_width = $s5_right_top_bottom_width;
	}
}

if ($s5_pos_left_top == "published" || $s5_pos_left_bottom == "published") {
	$s5_center_column_margin_left = $s5_left_width + $s5_left_inset_width;
	$s5_left_column_width = $s5_left_width + $s5_left_inset_width;
	if ($s5_pos_left == "unpublished" && $s5_pos_left_inset == "unpublished") {
		$s5_center_column_margin_left = $s5_left_top_bottom_width;
		$s5_left_column_width = $s5_left_top_bottom_width;
	}
}

// top_row1 calculations

if ($s5_pos_top_row1_1 == "published" || $s5_pos_top_row1_2 == "published" || $s5_pos_top_row1_3 == "published" || $s5_pos_top_row1_4 == "published" || $s5_pos_top_row1_5 == "published" || $s5_pos_top_row1_6 == "published") {

	$s5_pos_top_row1_1_width = 0;
	$s5_pos_top_row1_2_width = 0;
	$s5_pos_top_row1_3_width = 0;
	$s5_pos_top_row1_4_width = 0;
	$s5_pos_top_row1_5_width = 0;
	$s5_pos_top_row1_6_width = 0;

	$s5_top_row1_counter = 0;

	if ($s5_pos_top_row1_1 == "published") { $s5_top_row1_counter = $s5_top_row1_counter + 1; }
	if ($s5_pos_top_row1_2 == "published") { $s5_top_row1_counter = $s5_top_row1_counter + 1; }
	if ($s5_pos_top_row1_3 == "published") { $s5_top_row1_counter = $s5_top_row1_counter + 1; }
	if ($s5_pos_top_row1_4 == "published") { $s5_top_row1_counter = $s5_top_row1_counter + 1; }
	if ($s5_pos_top_row1_5 == "published") { $s5_top_row1_counter = $s5_top_row1_counter + 1; }
	if ($s5_pos_top_row1_6 == "published") { $s5_top_row1_counter = $s5_top_row1_counter + 1; }

	if ($s5_top_row1_calculation == "automatic") {

		if ($s5_top_row1_counter == 1) {
			$s5_pos_top_row1_1_width = 100;
			$s5_pos_top_row1_2_width = 100;
			$s5_pos_top_row1_3_width = 100;
			$s5_pos_top_row1_4_width = 100;
			$s5_pos_top_row1_5_width = 100;
			$s5_pos_top_row1_6_width = 100;
		}
		
		if ($s5_top_row1_counter == 2) {
			$s5_pos_top_row1_1_width = 50;
			$s5_pos_top_row1_2_width = 50;
			$s5_pos_top_row1_3_width = 50;
			$s5_pos_top_row1_4_width = 50;
			$s5_pos_top_row1_5_width = 50;
			$s5_pos_top_row1_6_width = 50;
		}
		
		if ($s5_top_row1_counter == 3) {
			$s5_pos_top_row1_1_width = 33.3;
			$s5_pos_top_row1_2_width = 33.3;
			$s5_pos_top_row1_3_width = 33.3;
			$s5_pos_top_row1_4_width = 33.3;
			$s5_pos_top_row1_5_width = 33.3;
			$s5_pos_top_row1_6_width = 33.3;
		}
		
		if ($s5_top_row1_counter == 4) {
			$s5_pos_top_row1_1_width = 25;
			$s5_pos_top_row1_2_width = 25;
			$s5_pos_top_row1_3_width = 25;
			$s5_pos_top_row1_4_width = 25;
			$s5_pos_top_row1_5_width = 25;
			$s5_pos_top_row1_6_width = 25;
		}
		
		if ($s5_top_row1_counter == 5) {
			$s5_pos_top_row1_1_width = 20;
			$s5_pos_top_row1_2_width = 20;
			$s5_pos_top_row1_3_width = 20;
			$s5_pos_top_row1_4_width = 20;
			$s5_pos_top_row1_5_width = 20;
			$s5_pos_top_row1_6_width = 20;
		}
		
		if ($s5_top_row1_counter == 6) {
			$s5_pos_top_row1_1_width = 16.66;
			$s5_pos_top_row1_2_width = 16.66;
			$s5_pos_top_row1_3_width = 16.66;
			$s5_pos_top_row1_4_width = 16.66;
			$s5_pos_top_row1_5_width = 16.66;
			$s5_pos_top_row1_6_width = 16.66;
		}

	}

	if ($s5_top_row1_calculation == "manual") {

		$s5_top_row1_manual_widths = str_replace(" ","",$s5_top_row1_manual_widths);	
		$s5_top_row1_manual_widths = str_replace("%","",$s5_top_row1_manual_widths);	
		$s5_top_row1_manual_widths = str_replace(";",",",$s5_top_row1_manual_widths);	
		
		$s5_top_row1_manual_widths_exploded = explode(",", $s5_top_row1_manual_widths);
		$s5_pos_top_row1_1_width = $s5_top_row1_manual_widths_exploded[0];
		$s5_pos_top_row1_2_width = $s5_top_row1_manual_widths_exploded[1];
		$s5_pos_top_row1_3_width = $s5_top_row1_manual_widths_exploded[2];
		$s5_pos_top_row1_4_width = $s5_top_row1_manual_widths_exploded[3];
		$s5_pos_top_row1_5_width = $s5_top_row1_manual_widths_exploded[4];
		$s5_pos_top_row1_6_width = $s5_top_row1_manual_widths_exploded[5];
		
		if (substr_count($s5_top_row1_manual_widths, ',') != 5) {
		echo "<div style='background:#ff0000; border:solid 2px #C40202;color:#ffffff; padding:12px; font-family:arial; font-size:14px; line-height:140%'><div class=\"s5_frontenderrormsg\"></div>Error - You have chosen to manually set the width of the modules in the row top_row1, but you have not specified the width of all 6 modules in the template configuration. You do not need to use all 6 modules but all widths must be present. Use 0 for any modules you are not using, for example: 20,10,0,70,0,0 the total of these numbers must equal 100% and all 6 widths must be present. Please correct this in the template configuration page.</div>";
		}
		
		$s5_top_row1_width_check = 0;

		if ($s5_pos_top_row1_1 == "published") { $s5_top_row1_width_check = $s5_top_row1_width_check + $s5_pos_top_row1_1_width; }
		if ($s5_pos_top_row1_2 == "published") { $s5_top_row1_width_check = $s5_top_row1_width_check + $s5_pos_top_row1_2_width; }
		if ($s5_pos_top_row1_3 == "published") { $s5_top_row1_width_check = $s5_top_row1_width_check + $s5_pos_top_row1_3_width; }
		if ($s5_pos_top_row1_4 == "published") { $s5_top_row1_width_check = $s5_top_row1_width_check + $s5_pos_top_row1_4_width; }
		if ($s5_pos_top_row1_5 == "published") { $s5_top_row1_width_check = $s5_top_row1_width_check + $s5_pos_top_row1_5_width; }
		if ($s5_pos_top_row1_6 == "published") { $s5_top_row1_width_check = $s5_top_row1_width_check + $s5_pos_top_row1_6_width; }
		
		if ($s5_top_row1_width_check != 100) {
			echo "<div style='background:#ff0000; border:solid 2px #C40202;color:#ffffff; padding:12px; font-family:arial; font-size:14px; line-height:140%'><div class=\"s5_frontenderrormsg\"></div>Error - You have chosen to manually set the width of the modules in the row top_row1, but the totals of the modules published to this page only total ".$s5_top_row1_width_check."%. They must equal to 100%. Please correct this in the template configuration page.</div>";
		}
		
	}
	
}

// top_row2 calculations

if ($s5_pos_top_row2_1 == "published" || $s5_pos_top_row2_2 == "published" || $s5_pos_top_row2_3 == "published" || $s5_pos_top_row2_4 == "published" || $s5_pos_top_row2_5 == "published" || $s5_pos_top_row2_6 == "published") {

	$s5_pos_top_row2_1_width = 0;
	$s5_pos_top_row2_2_width = 0;
	$s5_pos_top_row2_3_width = 0;
	$s5_pos_top_row2_4_width = 0;
	$s5_pos_top_row2_5_width = 0;
	$s5_pos_top_row2_6_width = 0;

	$s5_top_row2_counter = 0;

	if ($s5_pos_top_row2_1 == "published") { $s5_top_row2_counter = $s5_top_row2_counter + 1; }
	if ($s5_pos_top_row2_2 == "published") { $s5_top_row2_counter = $s5_top_row2_counter + 1; }
	if ($s5_pos_top_row2_3 == "published") { $s5_top_row2_counter = $s5_top_row2_counter + 1; }
	if ($s5_pos_top_row2_4 == "published") { $s5_top_row2_counter = $s5_top_row2_counter + 1; }
	if ($s5_pos_top_row2_5 == "published") { $s5_top_row2_counter = $s5_top_row2_counter + 1; }
	if ($s5_pos_top_row2_6 == "published") { $s5_top_row2_counter = $s5_top_row2_counter + 1; }

	if ($s5_top_row2_calculation == "automatic") {

		if ($s5_top_row2_counter == 1) {
			$s5_pos_top_row2_1_width = 100;
			$s5_pos_top_row2_2_width = 100;
			$s5_pos_top_row2_3_width = 100;
			$s5_pos_top_row2_4_width = 100;
			$s5_pos_top_row2_5_width = 100;
			$s5_pos_top_row2_6_width = 100;
		}
		
		if ($s5_top_row2_counter == 2) {
			$s5_pos_top_row2_1_width = 50;
			$s5_pos_top_row2_2_width = 50;
			$s5_pos_top_row2_3_width = 50;
			$s5_pos_top_row2_4_width = 50;
			$s5_pos_top_row2_5_width = 50;
			$s5_pos_top_row2_6_width = 50;
		}
		
		if ($s5_top_row2_counter == 3) {
			$s5_pos_top_row2_1_width = 33.3;
			$s5_pos_top_row2_2_width = 33.3;
			$s5_pos_top_row2_3_width = 33.3;
			$s5_pos_top_row2_4_width = 33.3;
			$s5_pos_top_row2_5_width = 33.3;
			$s5_pos_top_row2_6_width = 33.3;
		}
		
		if ($s5_top_row2_counter == 4) {
			$s5_pos_top_row2_1_width = 25;
			$s5_pos_top_row2_2_width = 25;
			$s5_pos_top_row2_3_width = 25;
			$s5_pos_top_row2_4_width = 25;
			$s5_pos_top_row2_5_width = 25;
			$s5_pos_top_row2_6_width = 25;
		}
		
		if ($s5_top_row2_counter == 5) {
			$s5_pos_top_row2_1_width = 20;
			$s5_pos_top_row2_2_width = 20;
			$s5_pos_top_row2_3_width = 20;
			$s5_pos_top_row2_4_width = 20;
			$s5_pos_top_row2_5_width = 20;
			$s5_pos_top_row2_6_width = 20;
		}
		
		if ($s5_top_row2_counter == 6) {
			$s5_pos_top_row2_1_width = 16.66;
			$s5_pos_top_row2_2_width = 16.66;
			$s5_pos_top_row2_3_width = 16.66;
			$s5_pos_top_row2_4_width = 16.66;
			$s5_pos_top_row2_5_width = 16.66;
			$s5_pos_top_row2_6_width = 16.66;
		}

	}

	if ($s5_top_row2_calculation == "manual") {

		$s5_top_row2_manual_widths = str_replace(" ","",$s5_top_row2_manual_widths);	
		$s5_top_row2_manual_widths = str_replace("%","",$s5_top_row2_manual_widths);	
		$s5_top_row2_manual_widths = str_replace(";",",",$s5_top_row2_manual_widths);	
		
		$s5_top_row2_manual_widths_exploded = explode(",", $s5_top_row2_manual_widths);
		$s5_pos_top_row2_1_width = $s5_top_row2_manual_widths_exploded[0];
		$s5_pos_top_row2_2_width = $s5_top_row2_manual_widths_exploded[1];
		$s5_pos_top_row2_3_width = $s5_top_row2_manual_widths_exploded[2];
		$s5_pos_top_row2_4_width = $s5_top_row2_manual_widths_exploded[3];
		$s5_pos_top_row2_5_width = $s5_top_row2_manual_widths_exploded[4];
		$s5_pos_top_row2_6_width = $s5_top_row2_manual_widths_exploded[5];
		
		if (substr_count($s5_top_row2_manual_widths, ',') != 5) {
		echo "<div style='background:#ff0000; border:solid 2px #C40202;color:#ffffff; padding:12px; font-family:arial; font-size:14px; line-height:140%'><div class=\"s5_frontenderrormsg\"></div>Error - You have chosen to manually set the width of the modules in the row top_row2, but you have not specified the width of all 6 modules in the template configuration. You do not need to use all 6 modules but all widths must be present. Use 0 for any modules you are not using, for example: 20,10,0,70,0,0 the total of these numbers must equal 100% and all 6 widths must be present. Please correct this in the template configuration page.</div>";
		}
		
		$s5_top_row2_width_check = 0;

		if ($s5_pos_top_row2_1 == "published") { $s5_top_row2_width_check = $s5_top_row2_width_check + $s5_pos_top_row2_1_width; }
		if ($s5_pos_top_row2_2 == "published") { $s5_top_row2_width_check = $s5_top_row2_width_check + $s5_pos_top_row2_2_width; }
		if ($s5_pos_top_row2_3 == "published") { $s5_top_row2_width_check = $s5_top_row2_width_check + $s5_pos_top_row2_3_width; }
		if ($s5_pos_top_row2_4 == "published") { $s5_top_row2_width_check = $s5_top_row2_width_check + $s5_pos_top_row2_4_width; }
		if ($s5_pos_top_row2_5 == "published") { $s5_top_row2_width_check = $s5_top_row2_width_check + $s5_pos_top_row2_5_width; }
		if ($s5_pos_top_row2_6 == "published") { $s5_top_row2_width_check = $s5_top_row2_width_check + $s5_pos_top_row2_6_width; }
		
		if ($s5_top_row2_width_check != 100) {
			echo "<div style='background:#ff0000; border:solid 2px #C40202;color:#ffffff; padding:12px; font-family:arial; font-size:14px; line-height:140%'><div class=\"s5_frontenderrormsg\"></div>Error - You have chosen to manually set the width of the modules in the row top_row2, but the totals of the modules published to this page only total ".$s5_top_row2_width_check."%. They must equal to 100%. Please correct this in the template configuration page.</div>";
		}
		
	}
	
}

// top_row3 calculations

if ($s5_pos_top_row3_1 == "published" || $s5_pos_top_row3_2 == "published" || $s5_pos_top_row3_3 == "published" || $s5_pos_top_row3_4 == "published" || $s5_pos_top_row3_5 == "published" || $s5_pos_top_row3_6 == "published") {

	$s5_pos_top_row3_1_width = 0;
	$s5_pos_top_row3_2_width = 0;
	$s5_pos_top_row3_3_width = 0;
	$s5_pos_top_row3_4_width = 0;
	$s5_pos_top_row3_5_width = 0;
	$s5_pos_top_row3_6_width = 0;

	$s5_top_row3_counter = 0;

	if ($s5_pos_top_row3_1 == "published") { $s5_top_row3_counter = $s5_top_row3_counter + 1; }
	if ($s5_pos_top_row3_2 == "published") { $s5_top_row3_counter = $s5_top_row3_counter + 1; }
	if ($s5_pos_top_row3_3 == "published") { $s5_top_row3_counter = $s5_top_row3_counter + 1; }
	if ($s5_pos_top_row3_4 == "published") { $s5_top_row3_counter = $s5_top_row3_counter + 1; }
	if ($s5_pos_top_row3_5 == "published") { $s5_top_row3_counter = $s5_top_row3_counter + 1; }
	if ($s5_pos_top_row3_6 == "published") { $s5_top_row3_counter = $s5_top_row3_counter + 1; }

	if ($s5_top_row3_calculation == "automatic") {

		if ($s5_top_row3_counter == 1) {
			$s5_pos_top_row3_1_width = 100;
			$s5_pos_top_row3_2_width = 100;
			$s5_pos_top_row3_3_width = 100;
			$s5_pos_top_row3_4_width = 100;
			$s5_pos_top_row3_5_width = 100;
			$s5_pos_top_row3_6_width = 100;
		}
		
		if ($s5_top_row3_counter == 2) {
			$s5_pos_top_row3_1_width = 50;
			$s5_pos_top_row3_2_width = 50;
			$s5_pos_top_row3_3_width = 50;
			$s5_pos_top_row3_4_width = 50;
			$s5_pos_top_row3_5_width = 50;
			$s5_pos_top_row3_6_width = 50;
		}
		
		if ($s5_top_row3_counter == 3) {
			$s5_pos_top_row3_1_width = 33.3;
			$s5_pos_top_row3_2_width = 33.3;
			$s5_pos_top_row3_3_width = 33.3;
			$s5_pos_top_row3_4_width = 33.3;
			$s5_pos_top_row3_5_width = 33.3;
			$s5_pos_top_row3_6_width = 33.3;
		}
		
		if ($s5_top_row3_counter == 4) {
			$s5_pos_top_row3_1_width = 25;
			$s5_pos_top_row3_2_width = 25;
			$s5_pos_top_row3_3_width = 25;
			$s5_pos_top_row3_4_width = 25;
			$s5_pos_top_row3_5_width = 25;
			$s5_pos_top_row3_6_width = 25;
		}
		
		if ($s5_top_row3_counter == 5) {
			$s5_pos_top_row3_1_width = 20;
			$s5_pos_top_row3_2_width = 20;
			$s5_pos_top_row3_3_width = 20;
			$s5_pos_top_row3_4_width = 20;
			$s5_pos_top_row3_5_width = 20;
			$s5_pos_top_row3_6_width = 20;
		}
		
		if ($s5_top_row3_counter == 6) {
			$s5_pos_top_row3_1_width = 16.66;
			$s5_pos_top_row3_2_width = 16.66;
			$s5_pos_top_row3_3_width = 16.66;
			$s5_pos_top_row3_4_width = 16.66;
			$s5_pos_top_row3_5_width = 16.66;
			$s5_pos_top_row3_6_width = 16.66;
		}

	}

	if ($s5_top_row3_calculation == "manual") {

		$s5_top_row3_manual_widths = str_replace(" ","",$s5_top_row3_manual_widths);	
		$s5_top_row3_manual_widths = str_replace("%","",$s5_top_row3_manual_widths);	
		$s5_top_row3_manual_widths = str_replace(";",",",$s5_top_row3_manual_widths);	
		
		$s5_top_row3_manual_widths_exploded = explode(",", $s5_top_row3_manual_widths);
		$s5_pos_top_row3_1_width = $s5_top_row3_manual_widths_exploded[0];
		$s5_pos_top_row3_2_width = $s5_top_row3_manual_widths_exploded[1];
		$s5_pos_top_row3_3_width = $s5_top_row3_manual_widths_exploded[2];
		$s5_pos_top_row3_4_width = $s5_top_row3_manual_widths_exploded[3];
		$s5_pos_top_row3_5_width = $s5_top_row3_manual_widths_exploded[4];
		$s5_pos_top_row3_6_width = $s5_top_row3_manual_widths_exploded[5];
		
		if (substr_count($s5_top_row3_manual_widths, ',') != 5) {
		echo "<div style='background:#ff0000; border:solid 2px #C40202;color:#ffffff; padding:12px; font-family:arial; font-size:14px; line-height:140%'><div class=\"s5_frontenderrormsg\"></div>Error - You have chosen to manually set the width of the modules in the row top_row3, but you have not specified the width of all 6 modules in the template configuration. You do not need to use all 6 modules but all widths must be present. Use 0 for any modules you are not using, for example: 20,10,0,70,0,0 the total of these numbers must equal 100% and all 6 widths must be present. Please correct this in the template configuration page.</div>";
		}
		
		$s5_top_row3_width_check = 0;

		if ($s5_pos_top_row3_1 == "published") { $s5_top_row3_width_check = $s5_top_row3_width_check + $s5_pos_top_row3_1_width; }
		if ($s5_pos_top_row3_2 == "published") { $s5_top_row3_width_check = $s5_top_row3_width_check + $s5_pos_top_row3_2_width; }
		if ($s5_pos_top_row3_3 == "published") { $s5_top_row3_width_check = $s5_top_row3_width_check + $s5_pos_top_row3_3_width; }
		if ($s5_pos_top_row3_4 == "published") { $s5_top_row3_width_check = $s5_top_row3_width_check + $s5_pos_top_row3_4_width; }
		if ($s5_pos_top_row3_5 == "published") { $s5_top_row3_width_check = $s5_top_row3_width_check + $s5_pos_top_row3_5_width; }
		if ($s5_pos_top_row3_6 == "published") { $s5_top_row3_width_check = $s5_top_row3_width_check + $s5_pos_top_row3_6_width; }
		
		if ($s5_top_row3_width_check != 100) {
			echo "<div style='background:#ff0000; border:solid 2px #C40202;color:#ffffff; padding:12px; font-family:arial; font-size:14px; line-height:140%'><div class=\"s5_frontenderrormsg\"></div>Error - You have chosen to manually set the width of the modules in the row top_row3, but the totals of the modules published to this page only total ".$s5_top_row3_width_check."%. They must equal to 100%. Please correct this in the template configuration page.</div>";
		}
		
	}
	
}




// above_columns_wrap calculations

if ($above_columns_wrap_1 == "published" || $above_columns_wrap_2 == "published" || $above_columns_wrap_3 == "published" || $above_columns_wrap_4 == "published" || $above_columns_wrap_5 == "published" || $above_columns_wrap_6 == "published") {

	$above_columns_wrap_1_width = 0;
	$above_columns_wrap_2_width = 0;
	$above_columns_wrap_3_width = 0;
	$above_columns_wrap_4_width = 0;
	$above_columns_wrap_5_width = 0;
	$above_columns_wrap_6_width = 0;

	$above_columns_wrap_counter = 0;

	if ($above_columns_wrap_1 == "published") { $above_columns_wrap_counter = $above_columns_wrap_counter + 1; }
	if ($above_columns_wrap_2 == "published") { $above_columns_wrap_counter = $above_columns_wrap_counter + 1; }
	if ($above_columns_wrap_3 == "published") { $above_columns_wrap_counter = $above_columns_wrap_counter + 1; }
	if ($above_columns_wrap_4 == "published") { $above_columns_wrap_counter = $above_columns_wrap_counter + 1; }
	if ($above_columns_wrap_5 == "published") { $above_columns_wrap_counter = $above_columns_wrap_counter + 1; }
	if ($above_columns_wrap_6 == "published") { $above_columns_wrap_counter = $above_columns_wrap_counter + 1; }

	if ($s5_above_columns_wrap_calculation == "automatic") {

		if ($above_columns_wrap_counter == 1) {
			$above_columns_wrap_1_width = 100;
			$above_columns_wrap_2_width = 100;
			$above_columns_wrap_3_width = 100;
			$above_columns_wrap_4_width = 100;
			$above_columns_wrap_5_width = 100;
			$above_columns_wrap_6_width = 100;
		}
		
		if ($above_columns_wrap_counter == 2) {
			$above_columns_wrap_1_width = 50;
			$above_columns_wrap_2_width = 50;
			$above_columns_wrap_3_width = 50;
			$above_columns_wrap_4_width = 50;
			$above_columns_wrap_5_width = 50;
			$above_columns_wrap_6_width = 50;
		}
		
		if ($above_columns_wrap_counter == 3) {
			$above_columns_wrap_1_width = 33.3;
			$above_columns_wrap_2_width = 33.3;
			$above_columns_wrap_3_width = 33.3;
			$above_columns_wrap_4_width = 33.3;
			$above_columns_wrap_5_width = 33.3;
			$above_columns_wrap_6_width = 33.3;
		}
		
		if ($above_columns_wrap_counter == 4) {
			$above_columns_wrap_1_width = 25;
			$above_columns_wrap_2_width = 25;
			$above_columns_wrap_3_width = 25;
			$above_columns_wrap_4_width = 25;
			$above_columns_wrap_5_width = 25;
			$above_columns_wrap_6_width = 25;
		}
		
		if ($above_columns_wrap_counter == 5) {
			$above_columns_wrap_1_width = 20;
			$above_columns_wrap_2_width = 20;
			$above_columns_wrap_3_width = 20;
			$above_columns_wrap_4_width = 20;
			$above_columns_wrap_5_width = 20;
			$above_columns_wrap_6_width = 20;
		}
		
		if ($above_columns_wrap_counter == 6) {
			$above_columns_wrap_1_width = 16.66;
			$above_columns_wrap_2_width = 16.66;
			$above_columns_wrap_3_width = 16.66;
			$above_columns_wrap_4_width = 16.66;
			$above_columns_wrap_5_width = 16.66;
			$above_columns_wrap_6_width = 16.66;
		}

	}

	if ($s5_above_columns_wrap_calculation == "manual") {

		$above_columns_wrap_manual_widths = str_replace(" ","",$above_columns_wrap_manual_widths);	
		$above_columns_wrap_manual_widths = str_replace("%","",$above_columns_wrap_manual_widths);	
		$above_columns_wrap_manual_widths = str_replace(";",",",$above_columns_wrap_manual_widths);	
		
		$above_columns_wrap_manual_widths_exploded = explode(",", $above_columns_wrap_manual_widths);
		$above_columns_wrap_1_width = $above_columns_wrap_manual_widths_exploded[0];
		$above_columns_wrap_2_width = $above_columns_wrap_manual_widths_exploded[1];
		$above_columns_wrap_3_width = $above_columns_wrap_manual_widths_exploded[2];
		$above_columns_wrap_4_width = $above_columns_wrap_manual_widths_exploded[3];
		$above_columns_wrap_5_width = $above_columns_wrap_manual_widths_exploded[4];
		$above_columns_wrap_6_width = $above_columns_wrap_manual_widths_exploded[5];
		
		if (substr_count($above_columns_wrap_manual_widths, ',') != 5) {
		echo "<div style='background:#ff0000; border:solid 2px #C40202;color:#ffffff; padding:12px; font-family:arial; font-size:14px; line-height:140%'><div class=\"s5_frontenderrormsg\"></div>Error - You have chosen to manually set the width of the modules in the above_columns_wrap, but you have not specified the width of all 6 modules in the template configuration. You do not need to use all 6 modules but all widths must be present. Use 0 for any modules you are not using, for example: 20,10,0,70,0,0 the total of these numbers must equal 100% and all 6 widths must be present. Please correct this in the template configuration page.</div>";
		}
		
		$above_columns_wrap_width_check = 0;

		if ($above_columns_wrap_1 == "published") { $above_columns_wrap_width_check = $above_columns_wrap_width_check + $above_columns_wrap_1_width; }
		if ($above_columns_wrap_2 == "published") { $above_columns_wrap_width_check = $above_columns_wrap_width_check + $above_columns_wrap_2_width; }
		if ($above_columns_wrap_3 == "published") { $above_columns_wrap_width_check = $above_columns_wrap_width_check + $above_columns_wrap_3_width; }
		if ($above_columns_wrap_4 == "published") { $above_columns_wrap_width_check = $above_columns_wrap_width_check + $above_columns_wrap_4_width; }
		if ($above_columns_wrap_5 == "published") { $above_columns_wrap_width_check = $above_columns_wrap_width_check + $above_columns_wrap_5_width; }
		if ($above_columns_wrap_6 == "published") { $above_columns_wrap_width_check = $above_columns_wrap_width_check + $above_columns_wrap_6_width; }
		
		if ($above_columns_wrap_width_check != 100) {
			echo "<div style='background:#ff0000; border:solid 2px #C40202;color:#ffffff; padding:12px; font-family:arial; font-size:14px; line-height:140%'><div class=\"s5_frontenderrormsg\"></div>Error - You have chosen to manually set the width of the modules in the above_columns_wrap, but the totals of the modules published to this page only total ".$above_columns_wrap_width_check."%. They must equal to 100%. Please correct this in the template configuration page.</div>";
		}
		
	}
	
}








// middle_top calculations

if ($s5_pos_middle_top_1 == "published" || $s5_pos_middle_top_2 == "published" || $s5_pos_middle_top_3 == "published" || $s5_pos_middle_top_4 == "published" || $s5_pos_middle_top_5 == "published" || $s5_pos_middle_top_6 == "published") {

	$s5_pos_middle_top_1_width = 0;
	$s5_pos_middle_top_2_width = 0;
	$s5_pos_middle_top_3_width = 0;
	$s5_pos_middle_top_4_width = 0;
	$s5_pos_middle_top_5_width = 0;
	$s5_pos_middle_top_6_width = 0;

	$s5_middle_top_counter = 0;

	if ($s5_pos_middle_top_1 == "published") { $s5_middle_top_counter = $s5_middle_top_counter + 1; }
	if ($s5_pos_middle_top_2 == "published") { $s5_middle_top_counter = $s5_middle_top_counter + 1; }
	if ($s5_pos_middle_top_3 == "published") { $s5_middle_top_counter = $s5_middle_top_counter + 1; }
	if ($s5_pos_middle_top_4 == "published") { $s5_middle_top_counter = $s5_middle_top_counter + 1; }
	if ($s5_pos_middle_top_5 == "published") { $s5_middle_top_counter = $s5_middle_top_counter + 1; }
	if ($s5_pos_middle_top_6 == "published") { $s5_middle_top_counter = $s5_middle_top_counter + 1; }

	if ($s5_middle_top_calculation == "automatic") {

		if ($s5_middle_top_counter == 1) {
			$s5_pos_middle_top_1_width = 100;
			$s5_pos_middle_top_2_width = 100;
			$s5_pos_middle_top_3_width = 100;
			$s5_pos_middle_top_4_width = 100;
			$s5_pos_middle_top_5_width = 100;
			$s5_pos_middle_top_6_width = 100;
		}
		
		if ($s5_middle_top_counter == 2) {
			$s5_pos_middle_top_1_width = 50;
			$s5_pos_middle_top_2_width = 50;
			$s5_pos_middle_top_3_width = 50;
			$s5_pos_middle_top_4_width = 50;
			$s5_pos_middle_top_5_width = 50;
			$s5_pos_middle_top_6_width = 50;
		}
		
		if ($s5_middle_top_counter == 3) {
			$s5_pos_middle_top_1_width = 33.3;
			$s5_pos_middle_top_2_width = 33.3;
			$s5_pos_middle_top_3_width = 33.3;
			$s5_pos_middle_top_4_width = 33.3;
			$s5_pos_middle_top_5_width = 33.3;
			$s5_pos_middle_top_6_width = 33.3;
		}
		
		if ($s5_middle_top_counter == 4) {
			$s5_pos_middle_top_1_width = 25;
			$s5_pos_middle_top_2_width = 25;
			$s5_pos_middle_top_3_width = 25;
			$s5_pos_middle_top_4_width = 25;
			$s5_pos_middle_top_5_width = 25;
			$s5_pos_middle_top_6_width = 25;
		}
		
		if ($s5_middle_top_counter == 5) {
			$s5_pos_middle_top_1_width = 20;
			$s5_pos_middle_top_2_width = 20;
			$s5_pos_middle_top_3_width = 20;
			$s5_pos_middle_top_4_width = 20;
			$s5_pos_middle_top_5_width = 20;
			$s5_pos_middle_top_6_width = 20;
		}
		
		if ($s5_middle_top_counter == 6) {
			$s5_pos_middle_top_1_width = 16.66;
			$s5_pos_middle_top_2_width = 16.66;
			$s5_pos_middle_top_3_width = 16.66;
			$s5_pos_middle_top_4_width = 16.66;
			$s5_pos_middle_top_5_width = 16.66;
			$s5_pos_middle_top_6_width = 16.66;
		}

	}

	if ($s5_middle_top_calculation == "manual") {

		$s5_middle_top_manual_widths = str_replace(" ","",$s5_middle_top_manual_widths);	
		$s5_middle_top_manual_widths = str_replace("%","",$s5_middle_top_manual_widths);	
		$s5_middle_top_manual_widths = str_replace(";",",",$s5_middle_top_manual_widths);	
		
		$s5_middle_top_manual_widths_exploded = explode(",", $s5_middle_top_manual_widths);
		$s5_pos_middle_top_1_width = $s5_middle_top_manual_widths_exploded[0];
		$s5_pos_middle_top_2_width = $s5_middle_top_manual_widths_exploded[1];
		$s5_pos_middle_top_3_width = $s5_middle_top_manual_widths_exploded[2];
		$s5_pos_middle_top_4_width = $s5_middle_top_manual_widths_exploded[3];
		$s5_pos_middle_top_5_width = $s5_middle_top_manual_widths_exploded[4];
		$s5_pos_middle_top_6_width = $s5_middle_top_manual_widths_exploded[5];
		
		if (substr_count($s5_middle_top_manual_widths, ',') != 5) {
		echo "<div style='background:#ff0000; border:solid 2px #C40202;color:#ffffff; padding:12px; font-family:arial; font-size:14px; line-height:140%'><div class=\"s5_frontenderrormsg\"></div>Error - You have chosen to manually set the width of the modules in the row middle_top, but you have not specified the width of all 6 modules in the template configuration. You do not need to use all 6 modules but all widths must be present. Use 0 for any modules you are not using, for example: 20,10,0,70,0,0 the total of these numbers must equal 100% and all 6 widths must be present. Please correct this in the template configuration page.</div>";
		}
		
		$s5_middle_top_width_check = 0;

		if ($s5_pos_middle_top_1 == "published") { $s5_middle_top_width_check = $s5_middle_top_width_check + $s5_pos_middle_top_1_width; }
		if ($s5_pos_middle_top_2 == "published") { $s5_middle_top_width_check = $s5_middle_top_width_check + $s5_pos_middle_top_2_width; }
		if ($s5_pos_middle_top_3 == "published") { $s5_middle_top_width_check = $s5_middle_top_width_check + $s5_pos_middle_top_3_width; }
		if ($s5_pos_middle_top_4 == "published") { $s5_middle_top_width_check = $s5_middle_top_width_check + $s5_pos_middle_top_4_width; }
		if ($s5_pos_middle_top_5 == "published") { $s5_middle_top_width_check = $s5_middle_top_width_check + $s5_pos_middle_top_5_width; }
		if ($s5_pos_middle_top_6 == "published") { $s5_middle_top_width_check = $s5_middle_top_width_check + $s5_pos_middle_top_6_width; }
		
		if ($s5_middle_top_width_check != 100) {
			echo "<div style='background:#ff0000; border:solid 2px #C40202;color:#ffffff; padding:12px; font-family:arial; font-size:14px; line-height:140%'><div class=\"s5_frontenderrormsg\"></div>Error - You have chosen to manually set the width of the modules in the row middle_top, but the totals of the modules published to this page only total ".$s5_middle_top_width_check."%. They must equal to 100%. Please correct this in the template configuration page.</div>";
		}
		
	}
	
}

// above_body calculations

if ($s5_pos_above_body_1 == "published" || $s5_pos_above_body_2 == "published" || $s5_pos_above_body_3 == "published" || $s5_pos_above_body_4 == "published" || $s5_pos_above_body_5 == "published" || $s5_pos_above_body_6 == "published") {

	$s5_pos_above_body_1_width = 0;
	$s5_pos_above_body_2_width = 0;
	$s5_pos_above_body_3_width = 0;
	$s5_pos_above_body_4_width = 0;
	$s5_pos_above_body_5_width = 0;
	$s5_pos_above_body_6_width = 0;

	$s5_above_body_counter = 0;

	if ($s5_pos_above_body_1 == "published") { $s5_above_body_counter = $s5_above_body_counter + 1; }
	if ($s5_pos_above_body_2 == "published") { $s5_above_body_counter = $s5_above_body_counter + 1; }
	if ($s5_pos_above_body_3 == "published") { $s5_above_body_counter = $s5_above_body_counter + 1; }
	if ($s5_pos_above_body_4 == "published") { $s5_above_body_counter = $s5_above_body_counter + 1; }
	if ($s5_pos_above_body_5 == "published") { $s5_above_body_counter = $s5_above_body_counter + 1; }
	if ($s5_pos_above_body_6 == "published") { $s5_above_body_counter = $s5_above_body_counter + 1; }

	if ($s5_above_body_calculation == "automatic") {

		if ($s5_above_body_counter == 1) {
			$s5_pos_above_body_1_width = 100;
			$s5_pos_above_body_2_width = 100;
			$s5_pos_above_body_3_width = 100;
			$s5_pos_above_body_4_width = 100;
			$s5_pos_above_body_5_width = 100;
			$s5_pos_above_body_6_width = 100;
		}
		
		if ($s5_above_body_counter == 2) {
			$s5_pos_above_body_1_width = 50;
			$s5_pos_above_body_2_width = 50;
			$s5_pos_above_body_3_width = 50;
			$s5_pos_above_body_4_width = 50;
			$s5_pos_above_body_5_width = 50;
			$s5_pos_above_body_6_width = 50;
		}
		
		if ($s5_above_body_counter == 3) {
			$s5_pos_above_body_1_width = 33.3;
			$s5_pos_above_body_2_width = 33.3;
			$s5_pos_above_body_3_width = 33.3;
			$s5_pos_above_body_4_width = 33.3;
			$s5_pos_above_body_5_width = 33.3;
			$s5_pos_above_body_6_width = 33.3;
		}
		
		if ($s5_above_body_counter == 4) {
			$s5_pos_above_body_1_width = 25;
			$s5_pos_above_body_2_width = 25;
			$s5_pos_above_body_3_width = 25;
			$s5_pos_above_body_4_width = 25;
			$s5_pos_above_body_5_width = 25;
			$s5_pos_above_body_6_width = 25;
		}
		
		if ($s5_above_body_counter == 5) {
			$s5_pos_above_body_1_width = 20;
			$s5_pos_above_body_2_width = 20;
			$s5_pos_above_body_3_width = 20;
			$s5_pos_above_body_4_width = 20;
			$s5_pos_above_body_5_width = 20;
			$s5_pos_above_body_6_width = 20;
		}
		
		if ($s5_above_body_counter == 6) {
			$s5_pos_above_body_1_width = 16.66;
			$s5_pos_above_body_2_width = 16.66;
			$s5_pos_above_body_3_width = 16.66;
			$s5_pos_above_body_4_width = 16.66;
			$s5_pos_above_body_5_width = 16.66;
			$s5_pos_above_body_6_width = 16.66;
		}

	}

	if ($s5_above_body_calculation == "manual") {

		$s5_above_body_manual_widths = str_replace(" ","",$s5_above_body_manual_widths);	
		$s5_above_body_manual_widths = str_replace("%","",$s5_above_body_manual_widths);	
		$s5_above_body_manual_widths = str_replace(";",",",$s5_above_body_manual_widths);	
		
		$s5_above_body_manual_widths_exploded = explode(",", $s5_above_body_manual_widths);
		$s5_pos_above_body_1_width = $s5_above_body_manual_widths_exploded[0];
		$s5_pos_above_body_2_width = $s5_above_body_manual_widths_exploded[1];
		$s5_pos_above_body_3_width = $s5_above_body_manual_widths_exploded[2];
		$s5_pos_above_body_4_width = $s5_above_body_manual_widths_exploded[3];
		$s5_pos_above_body_5_width = $s5_above_body_manual_widths_exploded[4];
		$s5_pos_above_body_6_width = $s5_above_body_manual_widths_exploded[5];
		
		if (substr_count($s5_above_body_manual_widths, ',') != 5) {
		echo "<div style='background:#ff0000; border:solid 2px #C40202;color:#ffffff; padding:12px; font-family:arial; font-size:14px; line-height:140%'><div class=\"s5_frontenderrormsg\"></div>Error - You have chosen to manually set the width of the modules in the row above_body, but you have not specified the width of all 6 modules in the template configuration. You do not need to use all 6 modules but all widths must be present. Use 0 for any modules you are not using, for example: 20,10,0,70,0,0 the total of these numbers must equal 100% and all 6 widths must be present. Please correct this in the template configuration page.</div>";
		}
		
		$s5_above_body_width_check = 0;

		if ($s5_pos_above_body_1 == "published") { $s5_above_body_width_check = $s5_above_body_width_check + $s5_pos_above_body_1_width; }
		if ($s5_pos_above_body_2 == "published") { $s5_above_body_width_check = $s5_above_body_width_check + $s5_pos_above_body_2_width; }
		if ($s5_pos_above_body_3 == "published") { $s5_above_body_width_check = $s5_above_body_width_check + $s5_pos_above_body_3_width; }
		if ($s5_pos_above_body_4 == "published") { $s5_above_body_width_check = $s5_above_body_width_check + $s5_pos_above_body_4_width; }
		if ($s5_pos_above_body_5 == "published") { $s5_above_body_width_check = $s5_above_body_width_check + $s5_pos_above_body_5_width; }
		if ($s5_pos_above_body_6 == "published") { $s5_above_body_width_check = $s5_above_body_width_check + $s5_pos_above_body_6_width; }
		
		if ($s5_above_body_width_check != 100) {
			echo "<div style='background:#ff0000; border:solid 2px #C40202;color:#ffffff; padding:12px; font-family:arial; font-size:14px; line-height:140%'><div class=\"s5_frontenderrormsg\"></div>Error - You have chosen to manually set the width of the modules in the row above_body, but the totals of the modules published to this page only total ".$s5_above_body_width_check."%. They must equal to 100%. Please correct this in the template configuration page.</div>";
		}
		
	}
	
}

// middle_bottom calculations

if ($s5_pos_middle_bottom_1 == "published" || $s5_pos_middle_bottom_2 == "published" || $s5_pos_middle_bottom_3 == "published" || $s5_pos_middle_bottom_4 == "published" || $s5_pos_middle_bottom_5 == "published" || $s5_pos_middle_bottom_6 == "published") {

	$s5_pos_middle_bottom_1_width = 0;
	$s5_pos_middle_bottom_2_width = 0;
	$s5_pos_middle_bottom_3_width = 0;
	$s5_pos_middle_bottom_4_width = 0;
	$s5_pos_middle_bottom_5_width = 0;
	$s5_pos_middle_bottom_6_width = 0;

	$s5_middle_bottom_counter = 0;

	if ($s5_pos_middle_bottom_1 == "published") { $s5_middle_bottom_counter = $s5_middle_bottom_counter + 1; }
	if ($s5_pos_middle_bottom_2 == "published") { $s5_middle_bottom_counter = $s5_middle_bottom_counter + 1; }
	if ($s5_pos_middle_bottom_3 == "published") { $s5_middle_bottom_counter = $s5_middle_bottom_counter + 1; }
	if ($s5_pos_middle_bottom_4 == "published") { $s5_middle_bottom_counter = $s5_middle_bottom_counter + 1; }
	if ($s5_pos_middle_bottom_5 == "published") { $s5_middle_bottom_counter = $s5_middle_bottom_counter + 1; }
	if ($s5_pos_middle_bottom_6 == "published") { $s5_middle_bottom_counter = $s5_middle_bottom_counter + 1; }

	if ($s5_middle_bottom_calculation == "automatic") {

		if ($s5_middle_bottom_counter == 1) {
			$s5_pos_middle_bottom_1_width = 100;
			$s5_pos_middle_bottom_2_width = 100;
			$s5_pos_middle_bottom_3_width = 100;
			$s5_pos_middle_bottom_4_width = 100;
			$s5_pos_middle_bottom_5_width = 100;
			$s5_pos_middle_bottom_6_width = 100;
		}
		
		if ($s5_middle_bottom_counter == 2) {
			$s5_pos_middle_bottom_1_width = 50;
			$s5_pos_middle_bottom_2_width = 50;
			$s5_pos_middle_bottom_3_width = 50;
			$s5_pos_middle_bottom_4_width = 50;
			$s5_pos_middle_bottom_5_width = 50;
			$s5_pos_middle_bottom_6_width = 50;
		}
		
		if ($s5_middle_bottom_counter == 3) {
			$s5_pos_middle_bottom_1_width = 33.3;
			$s5_pos_middle_bottom_2_width = 33.3;
			$s5_pos_middle_bottom_3_width = 33.3;
			$s5_pos_middle_bottom_4_width = 33.3;
			$s5_pos_middle_bottom_5_width = 33.3;
			$s5_pos_middle_bottom_6_width = 33.3;
		}
		
		if ($s5_middle_bottom_counter == 4) {
			$s5_pos_middle_bottom_1_width = 25;
			$s5_pos_middle_bottom_2_width = 25;
			$s5_pos_middle_bottom_3_width = 25;
			$s5_pos_middle_bottom_4_width = 25;
			$s5_pos_middle_bottom_5_width = 25;
			$s5_pos_middle_bottom_6_width = 25;
		}
		
		if ($s5_middle_bottom_counter == 5) {
			$s5_pos_middle_bottom_1_width = 20;
			$s5_pos_middle_bottom_2_width = 20;
			$s5_pos_middle_bottom_3_width = 20;
			$s5_pos_middle_bottom_4_width = 20;
			$s5_pos_middle_bottom_5_width = 20;
			$s5_pos_middle_bottom_6_width = 20;
		}
		
		if ($s5_middle_bottom_counter == 6) {
			$s5_pos_middle_bottom_1_width = 16.66;
			$s5_pos_middle_bottom_2_width = 16.66;
			$s5_pos_middle_bottom_3_width = 16.66;
			$s5_pos_middle_bottom_4_width = 16.66;
			$s5_pos_middle_bottom_5_width = 16.66;
			$s5_pos_middle_bottom_6_width = 16.66;
		}

	}

	if ($s5_middle_bottom_calculation == "manual") {

		$s5_middle_bottom_manual_widths = str_replace(" ","",$s5_middle_bottom_manual_widths);	
		$s5_middle_bottom_manual_widths = str_replace("%","",$s5_middle_bottom_manual_widths);	
		$s5_middle_bottom_manual_widths = str_replace(";",",",$s5_middle_bottom_manual_widths);	
		
		$s5_middle_bottom_manual_widths_exploded = explode(",", $s5_middle_bottom_manual_widths);
		$s5_pos_middle_bottom_1_width = $s5_middle_bottom_manual_widths_exploded[0];
		$s5_pos_middle_bottom_2_width = $s5_middle_bottom_manual_widths_exploded[1];
		$s5_pos_middle_bottom_3_width = $s5_middle_bottom_manual_widths_exploded[2];
		$s5_pos_middle_bottom_4_width = $s5_middle_bottom_manual_widths_exploded[3];
		$s5_pos_middle_bottom_5_width = $s5_middle_bottom_manual_widths_exploded[4];
		$s5_pos_middle_bottom_6_width = $s5_middle_bottom_manual_widths_exploded[5];
		
		if (substr_count($s5_middle_bottom_manual_widths, ',') != 5) {
		echo "<div style='background:#ff0000; border:solid 2px #C40202;color:#ffffff; padding:12px; font-family:arial; font-size:14px; line-height:140%'><div class=\"s5_frontenderrormsg\"></div>Error - You have chosen to manually set the width of the modules in the row middle_bottom, but you have not specified the width of all 6 modules in the template configuration. You do not need to use all 6 modules but all widths must be present. Use 0 for any modules you are not using, for example: 20,10,0,70,0,0 the total of these numbers must equal 100% and all 6 widths must be present. Please correct this in the template configuration page.</div>";
		}
		
		$s5_middle_bottom_width_check = 0;

		if ($s5_pos_middle_bottom_1 == "published") { $s5_middle_bottom_width_check = $s5_middle_bottom_width_check + $s5_pos_middle_bottom_1_width; }
		if ($s5_pos_middle_bottom_2 == "published") { $s5_middle_bottom_width_check = $s5_middle_bottom_width_check + $s5_pos_middle_bottom_2_width; }
		if ($s5_pos_middle_bottom_3 == "published") { $s5_middle_bottom_width_check = $s5_middle_bottom_width_check + $s5_pos_middle_bottom_3_width; }
		if ($s5_pos_middle_bottom_4 == "published") { $s5_middle_bottom_width_check = $s5_middle_bottom_width_check + $s5_pos_middle_bottom_4_width; }
		if ($s5_pos_middle_bottom_5 == "published") { $s5_middle_bottom_width_check = $s5_middle_bottom_width_check + $s5_pos_middle_bottom_5_width; }
		if ($s5_pos_middle_bottom_6 == "published") { $s5_middle_bottom_width_check = $s5_middle_bottom_width_check + $s5_pos_middle_bottom_6_width; }
		
		if ($s5_middle_bottom_width_check != 100) {
			echo "<div style='background:#ff0000; border:solid 2px #C40202;color:#ffffff; padding:12px; font-family:arial; font-size:14px; line-height:140%'><div class=\"s5_frontenderrormsg\"></div>Error - You have chosen to manually set the width of the modules in the row middle_bottom, but the totals of the modules published to this page only total ".$s5_middle_bottom_width_check."%. They must equal to 100%. Please correct this in the template configuration page.</div>";
		}
		
	}
	
}

// below_body calculations

if ($s5_pos_below_body_1 == "published" || $s5_pos_below_body_2 == "published" || $s5_pos_below_body_3 == "published" || $s5_pos_below_body_4 == "published" || $s5_pos_below_body_5 == "published" || $s5_pos_below_body_6 == "published") {

	$s5_pos_below_body_1_width = 0;
	$s5_pos_below_body_2_width = 0;
	$s5_pos_below_body_3_width = 0;
	$s5_pos_below_body_4_width = 0;
	$s5_pos_below_body_5_width = 0;
	$s5_pos_below_body_6_width = 0;

	$s5_below_body_counter = 0;

	if ($s5_pos_below_body_1 == "published") { $s5_below_body_counter = $s5_below_body_counter + 1; }
	if ($s5_pos_below_body_2 == "published") { $s5_below_body_counter = $s5_below_body_counter + 1; }
	if ($s5_pos_below_body_3 == "published") { $s5_below_body_counter = $s5_below_body_counter + 1; }
	if ($s5_pos_below_body_4 == "published") { $s5_below_body_counter = $s5_below_body_counter + 1; }
	if ($s5_pos_below_body_5 == "published") { $s5_below_body_counter = $s5_below_body_counter + 1; }
	if ($s5_pos_below_body_6 == "published") { $s5_below_body_counter = $s5_below_body_counter + 1; }

	if ($s5_below_body_calculation == "automatic") {

		if ($s5_below_body_counter == 1) {
			$s5_pos_below_body_1_width = 100;
			$s5_pos_below_body_2_width = 100;
			$s5_pos_below_body_3_width = 100;
			$s5_pos_below_body_4_width = 100;
			$s5_pos_below_body_5_width = 100;
			$s5_pos_below_body_6_width = 100;
		}
		
		if ($s5_below_body_counter == 2) {
			$s5_pos_below_body_1_width = 50;
			$s5_pos_below_body_2_width = 50;
			$s5_pos_below_body_3_width = 50;
			$s5_pos_below_body_4_width = 50;
			$s5_pos_below_body_5_width = 50;
			$s5_pos_below_body_6_width = 50;
		}
		
		if ($s5_below_body_counter == 3) {
			$s5_pos_below_body_1_width = 33.3;
			$s5_pos_below_body_2_width = 33.3;
			$s5_pos_below_body_3_width = 33.3;
			$s5_pos_below_body_4_width = 33.3;
			$s5_pos_below_body_5_width = 33.3;
			$s5_pos_below_body_6_width = 33.3;
		}
		
		if ($s5_below_body_counter == 4) {
			$s5_pos_below_body_1_width = 25;
			$s5_pos_below_body_2_width = 25;
			$s5_pos_below_body_3_width = 25;
			$s5_pos_below_body_4_width = 25;
			$s5_pos_below_body_5_width = 25;
			$s5_pos_below_body_6_width = 25;
		}
		
		if ($s5_below_body_counter == 5) {
			$s5_pos_below_body_1_width = 20;
			$s5_pos_below_body_2_width = 20;
			$s5_pos_below_body_3_width = 20;
			$s5_pos_below_body_4_width = 20;
			$s5_pos_below_body_5_width = 20;
			$s5_pos_below_body_6_width = 20;
		}
		
		if ($s5_below_body_counter == 6) {
			$s5_pos_below_body_1_width = 16.66;
			$s5_pos_below_body_2_width = 16.66;
			$s5_pos_below_body_3_width = 16.66;
			$s5_pos_below_body_4_width = 16.66;
			$s5_pos_below_body_5_width = 16.66;
			$s5_pos_below_body_6_width = 16.66;
		}

	}

	if ($s5_below_body_calculation == "manual") {

		$s5_below_body_manual_widths = str_replace(" ","",$s5_below_body_manual_widths);	
		$s5_below_body_manual_widths = str_replace("%","",$s5_below_body_manual_widths);	
		$s5_below_body_manual_widths = str_replace(";",",",$s5_below_body_manual_widths);	
		
		$s5_below_body_manual_widths_exploded = explode(",", $s5_below_body_manual_widths);
		$s5_pos_below_body_1_width = $s5_below_body_manual_widths_exploded[0];
		$s5_pos_below_body_2_width = $s5_below_body_manual_widths_exploded[1];
		$s5_pos_below_body_3_width = $s5_below_body_manual_widths_exploded[2];
		$s5_pos_below_body_4_width = $s5_below_body_manual_widths_exploded[3];
		$s5_pos_below_body_5_width = $s5_below_body_manual_widths_exploded[4];
		$s5_pos_below_body_6_width = $s5_below_body_manual_widths_exploded[5];
		
		if (substr_count($s5_below_body_manual_widths, ',') != 5) {
		echo "<div style='background:#ff0000; border:solid 2px #C40202;color:#ffffff; padding:12px; font-family:arial; font-size:14px; line-height:140%'><div class=\"s5_frontenderrormsg\"></div>Error - You have chosen to manually set the width of the modules in the row below_body, but you have not specified the width of all 6 modules in the template configuration. You do not need to use all 6 modules but all widths must be present. Use 0 for any modules you are not using, for example: 20,10,0,70,0,0 the total of these numbers must equal 100% and all 6 widths must be present. Please correct this in the template configuration page.</div>";
		}
		
		$s5_below_body_width_check = 0;

		if ($s5_pos_below_body_1 == "published") { $s5_below_body_width_check = $s5_below_body_width_check + $s5_pos_below_body_1_width; }
		if ($s5_pos_below_body_2 == "published") { $s5_below_body_width_check = $s5_below_body_width_check + $s5_pos_below_body_2_width; }
		if ($s5_pos_below_body_3 == "published") { $s5_below_body_width_check = $s5_below_body_width_check + $s5_pos_below_body_3_width; }
		if ($s5_pos_below_body_4 == "published") { $s5_below_body_width_check = $s5_below_body_width_check + $s5_pos_below_body_4_width; }
		if ($s5_pos_below_body_5 == "published") { $s5_below_body_width_check = $s5_below_body_width_check + $s5_pos_below_body_5_width; }
		if ($s5_pos_below_body_6 == "published") { $s5_below_body_width_check = $s5_below_body_width_check + $s5_pos_below_body_6_width; }
		
		if ($s5_below_body_width_check != 100) {
			echo "<div style='background:#ff0000; border:solid 2px #C40202;color:#ffffff; padding:12px; font-family:arial; font-size:14px; line-height:140%'><div class=\"s5_frontenderrormsg\"></div>Error - You have chosen to manually set the width of the modules in the row below_body, but the totals of the modules published to this page only total ".$s5_below_body_width_check."%. They must equal to 100%. Please correct this in the template configuration page.</div>";
		}
		
	}
	
}


// below_columns_wrap calculations

if ($below_columns_wrap_1 == "published" || $below_columns_wrap_2 == "published" || $below_columns_wrap_3 == "published" || $below_columns_wrap_4 == "published" || $below_columns_wrap_5 == "published" || $below_columns_wrap_6 == "published") {

	$below_columns_wrap_1_width = 0;
	$below_columns_wrap_2_width = 0;
	$below_columns_wrap_3_width = 0;
	$below_columns_wrap_4_width = 0;
	$below_columns_wrap_5_width = 0;
	$below_columns_wrap_6_width = 0;

	$below_columns_wrap_counter = 0;

	if ($below_columns_wrap_1 == "published") { $below_columns_wrap_counter = $below_columns_wrap_counter + 1; }
	if ($below_columns_wrap_2 == "published") { $below_columns_wrap_counter = $below_columns_wrap_counter + 1; }
	if ($below_columns_wrap_3 == "published") { $below_columns_wrap_counter = $below_columns_wrap_counter + 1; }
	if ($below_columns_wrap_4 == "published") { $below_columns_wrap_counter = $below_columns_wrap_counter + 1; }
	if ($below_columns_wrap_5 == "published") { $below_columns_wrap_counter = $below_columns_wrap_counter + 1; }
	if ($below_columns_wrap_6 == "published") { $below_columns_wrap_counter = $below_columns_wrap_counter + 1; }

	if ($s5_below_columns_wrap_calculation == "automatic") {

		if ($below_columns_wrap_counter == 1) {
			$below_columns_wrap_1_width = 100;
			$below_columns_wrap_2_width = 100;
			$below_columns_wrap_3_width = 100;
			$below_columns_wrap_4_width = 100;
			$below_columns_wrap_5_width = 100;
			$below_columns_wrap_6_width = 100;
		}
		
		if ($below_columns_wrap_counter == 2) {
			$below_columns_wrap_1_width = 50;
			$below_columns_wrap_2_width = 50;
			$below_columns_wrap_3_width = 50;
			$below_columns_wrap_4_width = 50;
			$below_columns_wrap_5_width = 50;
			$below_columns_wrap_6_width = 50;
		}
		
		if ($below_columns_wrap_counter == 3) {
			$below_columns_wrap_1_width = 33.3;
			$below_columns_wrap_2_width = 33.3;
			$below_columns_wrap_3_width = 33.3;
			$below_columns_wrap_4_width = 33.3;
			$below_columns_wrap_5_width = 33.3;
			$below_columns_wrap_6_width = 33.3;
		}
		
		if ($below_columns_wrap_counter == 4) {
			$below_columns_wrap_1_width = 25;
			$below_columns_wrap_2_width = 25;
			$below_columns_wrap_3_width = 25;
			$below_columns_wrap_4_width = 25;
			$below_columns_wrap_5_width = 25;
			$below_columns_wrap_6_width = 25;
		}
		
		if ($below_columns_wrap_counter == 5) {
			$below_columns_wrap_1_width = 20;
			$below_columns_wrap_2_width = 20;
			$below_columns_wrap_3_width = 20;
			$below_columns_wrap_4_width = 20;
			$below_columns_wrap_5_width = 20;
			$below_columns_wrap_6_width = 20;
		}
		
		if ($below_columns_wrap_counter == 6) {
			$below_columns_wrap_1_width = 16.66;
			$below_columns_wrap_2_width = 16.66;
			$below_columns_wrap_3_width = 16.66;
			$below_columns_wrap_4_width = 16.66;
			$below_columns_wrap_5_width = 16.66;
			$below_columns_wrap_6_width = 16.66;
		}

	}

	if ($s5_below_columns_wrap_calculation == "manual") {

		$below_columns_wrap_manual_widths = str_replace(" ","",$below_columns_wrap_manual_widths);	
		$below_columns_wrap_manual_widths = str_replace("%","",$below_columns_wrap_manual_widths);	
		$below_columns_wrap_manual_widths = str_replace(";",",",$below_columns_wrap_manual_widths);	
		
		$below_columns_wrap_manual_widths_exploded = explode(",", $below_columns_wrap_manual_widths);
		$below_columns_wrap_1_width = $below_columns_wrap_manual_widths_exploded[0];
		$below_columns_wrap_2_width = $below_columns_wrap_manual_widths_exploded[1];
		$below_columns_wrap_3_width = $below_columns_wrap_manual_widths_exploded[2];
		$below_columns_wrap_4_width = $below_columns_wrap_manual_widths_exploded[3];
		$below_columns_wrap_5_width = $below_columns_wrap_manual_widths_exploded[4];
		$below_columns_wrap_6_width = $below_columns_wrap_manual_widths_exploded[5];
		
		if (substr_count($below_columns_wrap_manual_widths, ',') != 5) {
		echo "<div style='background:#ff0000; border:solid 2px #C40202;color:#ffffff; padding:12px; font-family:arial; font-size:14px; line-height:140%'><div class=\"s5_frontenderrormsg\"></div>Error - You have chosen to manually set the width of the modules in the below_columns_wrap, but you have not specified the width of all 6 modules in the template configuration. You do not need to use all 6 modules but all widths must be present. Use 0 for any modules you are not using, for example: 20,10,0,70,0,0 the total of these numbers must equal 100% and all 6 widths must be present. Please correct this in the template configuration page.</div>";
		}
		
		$below_columns_wrap_width_check = 0;

		if ($below_columns_wrap_1 == "published") { $below_columns_wrap_width_check = $below_columns_wrap_width_check + $below_columns_wrap_1_width; }
		if ($below_columns_wrap_2 == "published") { $below_columns_wrap_width_check = $below_columns_wrap_width_check + $below_columns_wrap_2_width; }
		if ($below_columns_wrap_3 == "published") { $below_columns_wrap_width_check = $below_columns_wrap_width_check + $below_columns_wrap_3_width; }
		if ($below_columns_wrap_4 == "published") { $below_columns_wrap_width_check = $below_columns_wrap_width_check + $below_columns_wrap_4_width; }
		if ($below_columns_wrap_5 == "published") { $below_columns_wrap_width_check = $below_columns_wrap_width_check + $below_columns_wrap_5_width; }
		if ($below_columns_wrap_6 == "published") { $below_columns_wrap_width_check = $below_columns_wrap_width_check + $below_columns_wrap_6_width; }
		
		if ($below_columns_wrap_width_check != 100) {
			echo "<div style='background:#ff0000; border:solid 2px #C40202;color:#ffffff; padding:12px; font-family:arial; font-size:14px; line-height:140%'><div class=\"s5_frontenderrormsg\"></div>Error - You have chosen to manually set the width of the modules in the below_columns_wrap, but the totals of the modules published to this page only total ".$below_columns_wrap_width_check."%. They must equal to 100%. Please correct this in the template configuration page.</div>";
		}
		
	}
	
}



// bottom_row1 calculations

if ($s5_pos_bottom_row1_1 == "published" || $s5_pos_bottom_row1_2 == "published" || $s5_pos_bottom_row1_3 == "published" || $s5_pos_bottom_row1_4 == "published" || $s5_pos_bottom_row1_5 == "published" || $s5_pos_bottom_row1_6 == "published") {

	$s5_pos_bottom_row1_1_width = 0;
	$s5_pos_bottom_row1_2_width = 0;
	$s5_pos_bottom_row1_3_width = 0;
	$s5_pos_bottom_row1_4_width = 0;
	$s5_pos_bottom_row1_5_width = 0;
	$s5_pos_bottom_row1_6_width = 0;

	$s5_bottom_row1_counter = 0;

	if ($s5_pos_bottom_row1_1 == "published") { $s5_bottom_row1_counter = $s5_bottom_row1_counter + 1; }
	if ($s5_pos_bottom_row1_2 == "published") { $s5_bottom_row1_counter = $s5_bottom_row1_counter + 1; }
	if ($s5_pos_bottom_row1_3 == "published") { $s5_bottom_row1_counter = $s5_bottom_row1_counter + 1; }
	if ($s5_pos_bottom_row1_4 == "published") { $s5_bottom_row1_counter = $s5_bottom_row1_counter + 1; }
	if ($s5_pos_bottom_row1_5 == "published") { $s5_bottom_row1_counter = $s5_bottom_row1_counter + 1; }
	if ($s5_pos_bottom_row1_6 == "published") { $s5_bottom_row1_counter = $s5_bottom_row1_counter + 1; }

	if ($s5_bottom_row1_calculation == "automatic") {

		if ($s5_bottom_row1_counter == 1) {
			$s5_pos_bottom_row1_1_width = 100;
			$s5_pos_bottom_row1_2_width = 100;
			$s5_pos_bottom_row1_3_width = 100;
			$s5_pos_bottom_row1_4_width = 100;
			$s5_pos_bottom_row1_5_width = 100;
			$s5_pos_bottom_row1_6_width = 100;
		}
		
		if ($s5_bottom_row1_counter == 2) {
			$s5_pos_bottom_row1_1_width = 50;
			$s5_pos_bottom_row1_2_width = 50;
			$s5_pos_bottom_row1_3_width = 50;
			$s5_pos_bottom_row1_4_width = 50;
			$s5_pos_bottom_row1_5_width = 50;
			$s5_pos_bottom_row1_6_width = 50;
		}
		
		if ($s5_bottom_row1_counter == 3) {
			$s5_pos_bottom_row1_1_width = 33.3;
			$s5_pos_bottom_row1_2_width = 33.3;
			$s5_pos_bottom_row1_3_width = 33.3;
			$s5_pos_bottom_row1_4_width = 33.3;
			$s5_pos_bottom_row1_5_width = 33.3;
			$s5_pos_bottom_row1_6_width = 33.3;
		}
		
		if ($s5_bottom_row1_counter == 4) {
			$s5_pos_bottom_row1_1_width = 25;
			$s5_pos_bottom_row1_2_width = 25;
			$s5_pos_bottom_row1_3_width = 25;
			$s5_pos_bottom_row1_4_width = 25;
			$s5_pos_bottom_row1_5_width = 25;
			$s5_pos_bottom_row1_6_width = 25;
		}
		
		if ($s5_bottom_row1_counter == 5) {
			$s5_pos_bottom_row1_1_width = 20;
			$s5_pos_bottom_row1_2_width = 20;
			$s5_pos_bottom_row1_3_width = 20;
			$s5_pos_bottom_row1_4_width = 20;
			$s5_pos_bottom_row1_5_width = 20;
			$s5_pos_bottom_row1_6_width = 20;
		}
		
		if ($s5_bottom_row1_counter == 6) {
			$s5_pos_bottom_row1_1_width = 16.66;
			$s5_pos_bottom_row1_2_width = 16.66;
			$s5_pos_bottom_row1_3_width = 16.66;
			$s5_pos_bottom_row1_4_width = 16.66;
			$s5_pos_bottom_row1_5_width = 16.66;
			$s5_pos_bottom_row1_6_width = 16.66;
		}

	}

	if ($s5_bottom_row1_calculation == "manual") {

		$s5_bottom_row1_manual_widths = str_replace(" ","",$s5_bottom_row1_manual_widths);	
		$s5_bottom_row1_manual_widths = str_replace("%","",$s5_bottom_row1_manual_widths);	
		$s5_bottom_row1_manual_widths = str_replace(";",",",$s5_bottom_row1_manual_widths);	
		
		$s5_bottom_row1_manual_widths_exploded = explode(",", $s5_bottom_row1_manual_widths);
		$s5_pos_bottom_row1_1_width = $s5_bottom_row1_manual_widths_exploded[0];
		$s5_pos_bottom_row1_2_width = $s5_bottom_row1_manual_widths_exploded[1];
		$s5_pos_bottom_row1_3_width = $s5_bottom_row1_manual_widths_exploded[2];
		$s5_pos_bottom_row1_4_width = $s5_bottom_row1_manual_widths_exploded[3];
		$s5_pos_bottom_row1_5_width = $s5_bottom_row1_manual_widths_exploded[4];
		$s5_pos_bottom_row1_6_width = $s5_bottom_row1_manual_widths_exploded[5];
		
		if (substr_count($s5_bottom_row1_manual_widths, ',') != 5) {
		echo "<div style='background:#ff0000; border:solid 2px #C40202;color:#ffffff; padding:12px; font-family:arial; font-size:14px; line-height:140%'><div class=\"s5_frontenderrormsg\"></div>Error - You have chosen to manually set the width of the modules in the row bottom_row1, but you have not specified the width of all 6 modules in the template configuration. You do not need to use all 6 modules but all widths must be present. Use 0 for any modules you are not using, for example: 20,10,0,70,0,0 the total of these numbers must equal 100% and all 6 widths must be present. Please correct this in the template configuration page.</div>";
		}
		
		$s5_bottom_row1_width_check = 0;

		if ($s5_pos_bottom_row1_1 == "published") { $s5_bottom_row1_width_check = $s5_bottom_row1_width_check + $s5_pos_bottom_row1_1_width; }
		if ($s5_pos_bottom_row1_2 == "published") { $s5_bottom_row1_width_check = $s5_bottom_row1_width_check + $s5_pos_bottom_row1_2_width; }
		if ($s5_pos_bottom_row1_3 == "published") { $s5_bottom_row1_width_check = $s5_bottom_row1_width_check + $s5_pos_bottom_row1_3_width; }
		if ($s5_pos_bottom_row1_4 == "published") { $s5_bottom_row1_width_check = $s5_bottom_row1_width_check + $s5_pos_bottom_row1_4_width; }
		if ($s5_pos_bottom_row1_5 == "published") { $s5_bottom_row1_width_check = $s5_bottom_row1_width_check + $s5_pos_bottom_row1_5_width; }
		if ($s5_pos_bottom_row1_6 == "published") { $s5_bottom_row1_width_check = $s5_bottom_row1_width_check + $s5_pos_bottom_row1_6_width; }
		
		if ($s5_bottom_row1_width_check != 100) {
			echo "<div style='background:#ff0000; border:solid 2px #C40202;color:#ffffff; padding:12px; font-family:arial; font-size:14px; line-height:140%'><div class=\"s5_frontenderrormsg\"></div>Error - You have chosen to manually set the width of the modules in the row bottom_row1, but the totals of the modules published to this page only total ".$s5_bottom_row1_width_check."%. They must equal to 100%. Please correct this in the template configuration page.</div>";
		}
		
	}
	
}

// bottom_row2 calculations

if ($s5_pos_bottom_row2_1 == "published" || $s5_pos_bottom_row2_2 == "published" || $s5_pos_bottom_row2_3 == "published" || $s5_pos_bottom_row2_4 == "published" || $s5_pos_bottom_row2_5 == "published" || $s5_pos_bottom_row2_6 == "published") {

	$s5_pos_bottom_row2_1_width = 0;
	$s5_pos_bottom_row2_2_width = 0;
	$s5_pos_bottom_row2_3_width = 0;
	$s5_pos_bottom_row2_4_width = 0;
	$s5_pos_bottom_row2_5_width = 0;
	$s5_pos_bottom_row2_6_width = 0;

	$s5_bottom_row2_counter = 0;

	if ($s5_pos_bottom_row2_1 == "published") { $s5_bottom_row2_counter = $s5_bottom_row2_counter + 1; }
	if ($s5_pos_bottom_row2_2 == "published") { $s5_bottom_row2_counter = $s5_bottom_row2_counter + 1; }
	if ($s5_pos_bottom_row2_3 == "published") { $s5_bottom_row2_counter = $s5_bottom_row2_counter + 1; }
	if ($s5_pos_bottom_row2_4 == "published") { $s5_bottom_row2_counter = $s5_bottom_row2_counter + 1; }
	if ($s5_pos_bottom_row2_5 == "published") { $s5_bottom_row2_counter = $s5_bottom_row2_counter + 1; }
	if ($s5_pos_bottom_row2_6 == "published") { $s5_bottom_row2_counter = $s5_bottom_row2_counter + 1; }

	if ($s5_bottom_row2_calculation == "automatic") {

		if ($s5_bottom_row2_counter == 1) {
			$s5_pos_bottom_row2_1_width = 100;
			$s5_pos_bottom_row2_2_width = 100;
			$s5_pos_bottom_row2_3_width = 100;
			$s5_pos_bottom_row2_4_width = 100;
			$s5_pos_bottom_row2_5_width = 100;
			$s5_pos_bottom_row2_6_width = 100;
		}
		
		if ($s5_bottom_row2_counter == 2) {
			$s5_pos_bottom_row2_1_width = 50;
			$s5_pos_bottom_row2_2_width = 50;
			$s5_pos_bottom_row2_3_width = 50;
			$s5_pos_bottom_row2_4_width = 50;
			$s5_pos_bottom_row2_5_width = 50;
			$s5_pos_bottom_row2_6_width = 50;
		}
		
		if ($s5_bottom_row2_counter == 3) {
			$s5_pos_bottom_row2_1_width = 33.3;
			$s5_pos_bottom_row2_2_width = 33.3;
			$s5_pos_bottom_row2_3_width = 33.3;
			$s5_pos_bottom_row2_4_width = 33.3;
			$s5_pos_bottom_row2_5_width = 33.3;
			$s5_pos_bottom_row2_6_width = 33.3;
		}
		
		if ($s5_bottom_row2_counter == 4) {
			$s5_pos_bottom_row2_1_width = 25;
			$s5_pos_bottom_row2_2_width = 25;
			$s5_pos_bottom_row2_3_width = 25;
			$s5_pos_bottom_row2_4_width = 25;
			$s5_pos_bottom_row2_5_width = 25;
			$s5_pos_bottom_row2_6_width = 25;
		}
		
		if ($s5_bottom_row2_counter == 5) {
			$s5_pos_bottom_row2_1_width = 20;
			$s5_pos_bottom_row2_2_width = 20;
			$s5_pos_bottom_row2_3_width = 20;
			$s5_pos_bottom_row2_4_width = 20;
			$s5_pos_bottom_row2_5_width = 20;
			$s5_pos_bottom_row2_6_width = 20;
		}
		
		if ($s5_bottom_row2_counter == 6) {
			$s5_pos_bottom_row2_1_width = 16.66;
			$s5_pos_bottom_row2_2_width = 16.66;
			$s5_pos_bottom_row2_3_width = 16.66;
			$s5_pos_bottom_row2_4_width = 16.66;
			$s5_pos_bottom_row2_5_width = 16.66;
			$s5_pos_bottom_row2_6_width = 16.66;
		}

	}

	if ($s5_bottom_row2_calculation == "manual") {

		$s5_bottom_row2_manual_widths = str_replace(" ","",$s5_bottom_row2_manual_widths);	
		$s5_bottom_row2_manual_widths = str_replace("%","",$s5_bottom_row2_manual_widths);	
		$s5_bottom_row2_manual_widths = str_replace(";",",",$s5_bottom_row2_manual_widths);	
		
		$s5_bottom_row2_manual_widths_exploded = explode(",", $s5_bottom_row2_manual_widths);
		$s5_pos_bottom_row2_1_width = $s5_bottom_row2_manual_widths_exploded[0];
		$s5_pos_bottom_row2_2_width = $s5_bottom_row2_manual_widths_exploded[1];
		$s5_pos_bottom_row2_3_width = $s5_bottom_row2_manual_widths_exploded[2];
		$s5_pos_bottom_row2_4_width = $s5_bottom_row2_manual_widths_exploded[3];
		$s5_pos_bottom_row2_5_width = $s5_bottom_row2_manual_widths_exploded[4];
		$s5_pos_bottom_row2_6_width = $s5_bottom_row2_manual_widths_exploded[5];
		
		if (substr_count($s5_bottom_row2_manual_widths, ',') != 5) {
		echo "<div style='background:#ff0000; border:solid 2px #C40202;color:#ffffff; padding:12px; font-family:arial; font-size:14px; line-height:140%'><div class=\"s5_frontenderrormsg\"></div>Error - You have chosen to manually set the width of the modules in the row bottom_row2, but you have not specified the width of all 6 modules in the template configuration. You do not need to use all 6 modules but all widths must be present. Use 0 for any modules you are not using, for example: 20,10,0,70,0,0 the total of these numbers must equal 100% and all 6 widths must be present. Please correct this in the template configuration page.</div>";
		}
		
		$s5_bottom_row2_width_check = 0;

		if ($s5_pos_bottom_row2_1 == "published") { $s5_bottom_row2_width_check = $s5_bottom_row2_width_check + $s5_pos_bottom_row2_1_width; }
		if ($s5_pos_bottom_row2_2 == "published") { $s5_bottom_row2_width_check = $s5_bottom_row2_width_check + $s5_pos_bottom_row2_2_width; }
		if ($s5_pos_bottom_row2_3 == "published") { $s5_bottom_row2_width_check = $s5_bottom_row2_width_check + $s5_pos_bottom_row2_3_width; }
		if ($s5_pos_bottom_row2_4 == "published") { $s5_bottom_row2_width_check = $s5_bottom_row2_width_check + $s5_pos_bottom_row2_4_width; }
		if ($s5_pos_bottom_row2_5 == "published") { $s5_bottom_row2_width_check = $s5_bottom_row2_width_check + $s5_pos_bottom_row2_5_width; }
		if ($s5_pos_bottom_row2_6 == "published") { $s5_bottom_row2_width_check = $s5_bottom_row2_width_check + $s5_pos_bottom_row2_6_width; }
		
		if ($s5_bottom_row2_width_check != 100) {
			echo "<div style='background:#ff0000; border:solid 2px #C40202;color:#ffffff; padding:12px; font-family:arial; font-size:14px; line-height:140%'><div class=\"s5_frontenderrormsg\"></div>Error - You have chosen to manually set the width of the modules in the row bottom_row2, but the totals of the modules published to this page only total ".$s5_bottom_row2_width_check."%. They must equal to 100%. Please correct this in the template configuration page.</div>";
		}
		
	}
	
}

// bottom_row3 calculations

if ($s5_pos_bottom_row3_1 == "published" || $s5_pos_bottom_row3_2 == "published" || $s5_pos_bottom_row3_3 == "published" || $s5_pos_bottom_row3_4 == "published" || $s5_pos_bottom_row3_5 == "published" || $s5_pos_bottom_row3_6 == "published") {

	$s5_pos_bottom_row3_1_width = 0;
	$s5_pos_bottom_row3_2_width = 0;
	$s5_pos_bottom_row3_3_width = 0;
	$s5_pos_bottom_row3_4_width = 0;
	$s5_pos_bottom_row3_5_width = 0;
	$s5_pos_bottom_row3_6_width = 0;

	$s5_bottom_row3_counter = 0;

	if ($s5_pos_bottom_row3_1 == "published") { $s5_bottom_row3_counter = $s5_bottom_row3_counter + 1; }
	if ($s5_pos_bottom_row3_2 == "published") { $s5_bottom_row3_counter = $s5_bottom_row3_counter + 1; }
	if ($s5_pos_bottom_row3_3 == "published") { $s5_bottom_row3_counter = $s5_bottom_row3_counter + 1; }
	if ($s5_pos_bottom_row3_4 == "published") { $s5_bottom_row3_counter = $s5_bottom_row3_counter + 1; }
	if ($s5_pos_bottom_row3_5 == "published") { $s5_bottom_row3_counter = $s5_bottom_row3_counter + 1; }
	if ($s5_pos_bottom_row3_6 == "published") { $s5_bottom_row3_counter = $s5_bottom_row3_counter + 1; }

	if ($s5_bottom_row3_calculation == "automatic") {

		if ($s5_bottom_row3_counter == 1) {
			$s5_pos_bottom_row3_1_width = 100;
			$s5_pos_bottom_row3_2_width = 100;
			$s5_pos_bottom_row3_3_width = 100;
			$s5_pos_bottom_row3_4_width = 100;
			$s5_pos_bottom_row3_5_width = 100;
			$s5_pos_bottom_row3_6_width = 100;
		}
		
		if ($s5_bottom_row3_counter == 2) {
			$s5_pos_bottom_row3_1_width = 50;
			$s5_pos_bottom_row3_2_width = 50;
			$s5_pos_bottom_row3_3_width = 50;
			$s5_pos_bottom_row3_4_width = 50;
			$s5_pos_bottom_row3_5_width = 50;
			$s5_pos_bottom_row3_6_width = 50;
		}
		
		if ($s5_bottom_row3_counter == 3) {
			$s5_pos_bottom_row3_1_width = 33.3;
			$s5_pos_bottom_row3_2_width = 33.3;
			$s5_pos_bottom_row3_3_width = 33.3;
			$s5_pos_bottom_row3_4_width = 33.3;
			$s5_pos_bottom_row3_5_width = 33.3;
			$s5_pos_bottom_row3_6_width = 33.3;
		}
		
		if ($s5_bottom_row3_counter == 4) {
			$s5_pos_bottom_row3_1_width = 25;
			$s5_pos_bottom_row3_2_width = 25;
			$s5_pos_bottom_row3_3_width = 25;
			$s5_pos_bottom_row3_4_width = 25;
			$s5_pos_bottom_row3_5_width = 25;
			$s5_pos_bottom_row3_6_width = 25;
		}
		
		if ($s5_bottom_row3_counter == 5) {
			$s5_pos_bottom_row3_1_width = 20;
			$s5_pos_bottom_row3_2_width = 20;
			$s5_pos_bottom_row3_3_width = 20;
			$s5_pos_bottom_row3_4_width = 20;
			$s5_pos_bottom_row3_5_width = 20;
			$s5_pos_bottom_row3_6_width = 20;
		}
		
		if ($s5_bottom_row3_counter == 6) {
			$s5_pos_bottom_row3_1_width = 16.66;
			$s5_pos_bottom_row3_2_width = 16.66;
			$s5_pos_bottom_row3_3_width = 16.66;
			$s5_pos_bottom_row3_4_width = 16.66;
			$s5_pos_bottom_row3_5_width = 16.66;
			$s5_pos_bottom_row3_6_width = 16.66;
		}

	}

	if ($s5_bottom_row3_calculation == "manual") {

		$s5_bottom_row3_manual_widths = str_replace(" ","",$s5_bottom_row3_manual_widths);	
		$s5_bottom_row3_manual_widths = str_replace("%","",$s5_bottom_row3_manual_widths);	
		$s5_bottom_row3_manual_widths = str_replace(";",",",$s5_bottom_row3_manual_widths);	
		
		$s5_bottom_row3_manual_widths_exploded = explode(",", $s5_bottom_row3_manual_widths);
		$s5_pos_bottom_row3_1_width = $s5_bottom_row3_manual_widths_exploded[0];
		$s5_pos_bottom_row3_2_width = $s5_bottom_row3_manual_widths_exploded[1];
		$s5_pos_bottom_row3_3_width = $s5_bottom_row3_manual_widths_exploded[2];
		$s5_pos_bottom_row3_4_width = $s5_bottom_row3_manual_widths_exploded[3];
		$s5_pos_bottom_row3_5_width = $s5_bottom_row3_manual_widths_exploded[4];
		$s5_pos_bottom_row3_6_width = $s5_bottom_row3_manual_widths_exploded[5];
		
		if (substr_count($s5_bottom_row3_manual_widths, ',') != 5) {
		echo "<div style='background:#ff0000; border:solid 2px #C40202;color:#ffffff; padding:12px; font-family:arial; font-size:14px; line-height:140%'><div class=\"s5_frontenderrormsg\"></div>Error - You have chosen to manually set the width of the modules in the row bottom_row3, but you have not specified the width of all 6 modules in the template configuration. You do not need to use all 6 modules but all widths must be present. Use 0 for any modules you are not using, for example: 20,10,0,70,0,0 the total of these numbers must equal 100% and all 6 widths must be present. Please correct this in the template configuration page.</div>";
		}
		
		$s5_bottom_row3_width_check = 0;

		if ($s5_pos_bottom_row3_1 == "published") { $s5_bottom_row3_width_check = $s5_bottom_row3_width_check + $s5_pos_bottom_row3_1_width; }
		if ($s5_pos_bottom_row3_2 == "published") { $s5_bottom_row3_width_check = $s5_bottom_row3_width_check + $s5_pos_bottom_row3_2_width; }
		if ($s5_pos_bottom_row3_3 == "published") { $s5_bottom_row3_width_check = $s5_bottom_row3_width_check + $s5_pos_bottom_row3_3_width; }
		if ($s5_pos_bottom_row3_4 == "published") { $s5_bottom_row3_width_check = $s5_bottom_row3_width_check + $s5_pos_bottom_row3_4_width; }
		if ($s5_pos_bottom_row3_5 == "published") { $s5_bottom_row3_width_check = $s5_bottom_row3_width_check + $s5_pos_bottom_row3_5_width; }
		if ($s5_pos_bottom_row3_6 == "published") { $s5_bottom_row3_width_check = $s5_bottom_row3_width_check + $s5_pos_bottom_row3_6_width; }
		
		if ($s5_bottom_row3_width_check != 100) {
			echo "<div style='background:#ff0000; border:solid 2px #C40202;color:#ffffff; padding:12px; font-family:arial; font-size:14px; line-height:140%'><div class=\"s5_frontenderrormsg\"></div>Error - You have chosen to manually set the width of the modules in the row bottom_row3, but the totals of the modules published to this page only total ".$s5_bottom_row3_width_check."%. They must equal to 100%. Please correct this in the template configuration page.</div>";
		}
		
	}
	
}

?>