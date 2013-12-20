<?php if ($s5_tooltips  == "yes") { ?>
	<script type="text/javascript" language="javascript" src="<?php echo $s5_directory_path ?>/js/tooltips.js"></script>
	<?php } ?>
	
	<?php if ($s5_multibox  == "yes") { ?>
	<script type="text/javascript">
		var s5mbox = {};
		function s5_multiboxloadit() { s5_multiboxg();}
		window.setTimeout(s5_multiboxloadit,400);

		function s5_multiboxg() {	s5mbox = new MultiBox('s5mb', {descClassName: 's5_multibox', <?php if ($s5_multioverlay  == "yes") { ?>useOverlay: true<?php } else {?>useOverlay: false<?php } ?>, <?php if ($s5_multicontrols  == "yes") { ?>showControls: true<?php } else {?>showControls: false<?php } ?>});	};
	</script>
<?php } ?>