var QuizFactory = {}, QuizElements = {id : 0,pid : 0,option : null};
(function($){
	QuizFactory.init_create_edit_quiz = function(){
		$('.navigation').find('a').button();
		$('#btn-continue').click(function(){
			$('#quiz-form').submit();
		});
		$("#quiz-form").validate();
	};

	QuizFactory.init_quiz_form = function() {
		QuizElements.init( {
			id : $("#id").html(),
			pid : $("#pid").html(),
			option : $("#txt_option").html()
		});
		QuizElements.init_page_elements();
		QuizElements.load_questions(null);
	};
	
	QuizFactory.init_quiz_list = function(){
		$("#quiz-wrapper-list").find('.actionbar .bookmarks').buttonset();
		$("#quiz-wrapper-list").find('.actionbar .actions').buttonset();
		$("#quiz-wrapper-list").find(".ui-state-active").mouseover(function(){$(this).removeClass("ui-state-active");}).mouseout(function(){$(this).addClass("ui-state-active");});
		$("#btn_home").button( "option", "icons", {primary:'ui-icon-home'});
        $('#cq_search').inlineFieldLabel({
            label: $('#lbl_search').html()
        });
        $('#btn_search').button();
        $('#btn_create_new').button();
        $(".star-rating").stars({
            disabled: true
        });
    };
    
    QuizFactory.init_quiz_intro = function(){
    	$('.navigation').find('a').button();
    	$('#btn_continue').click(function(){
    		$('#intro_form').submit();
    	});
    };
    
    QuizFactory.init_quiz_response = function(){
    	$('.navigation').find('a').button();
    	$(".qn-page-header").parent().insertAfter($("#dummyelement"));
    	$('#btn_submit').click(function(){
    		$('#response-form').attr('action', $(this).attr('href'));
    		$('#response-form').submit();
    	});
		$("#response-form").validate({
			errorPlacement: function(error, element) {
				error.html($("#default_error_required").html());
				if(element.parents(".quiz-question").find("label.error").length == 0){
					error.insertAfter( element.parents("div.quiz-question").find(".question_title") );
				}
			}
		});
		
		var quiz_duration = $("#quiz_duration").text();
		var seconds = 0;
		try{
			seconds = parseInt(quiz_duration);
		}catch(e){}
		if(seconds >= 0){
			QuizFactory.seconds = seconds;
			QuizFactory.form_submitted = false;
			setInterval(
				function(){
					if(QuizFactory.seconds <= 1){
						if(QuizFactory.form_submitted == false){
							$('#response-form').find("input[name='finalize']").val('1');
							$('#response-form')[0].submit();
							QuizFactory.form_submitted = true;
						}
					}else{
						$('.quiztimer').html(QuizFactory.seconds--);
					}
				},
				1000
			);			
		}
		
        $(".rating-wrapper").stars({
        	inputType: "select"
        });
    };
    
    QuizFactory.init_quiz_results = function(){
    	$('.navigation').find('a').button();
    };
    
    QuizFactory.init_quiz_choose_answers = function(){
    	$('.navigation').find('a').button();
    	$('#btn_submit').click(function(){
    		$('#response-form').attr('action', $(this).attr('href'));
    		$('#response-form').submit();
    	});
		$("#response-form").validate({
			errorPlacement: function(error, element) {
				error.html($("#default_error_required").html());
				if(element.parents(".quiz-question").find("label.error").length == 0){
					error.insertAfter( element.parents("div.quiz-question").find(".question_title") );
				}
			}
		});    	
    };
    
    QuizFactory.init_quiz_reports = function(){
    	$('.navigation').find('a').button();
    };
    
	QuizElements.init = function(options) {
		this.id = options.id;
		this.pid = options.pid;
		this.option = options.option;
	};

	// Elements
	QuizElements.qn_title = function(qid) {
		return $("<div>").attr( {"class" : "qn_title"}).append($("<div>").append($("<input>").attr( {
			id : "title_" + qid,
			name : "question_title",
			type : "text",
			size : "40",
			"class" : "input-text"
		})));
	};
	
	QuizElements.qn_description = function(qid){
		return $("<div>", {"class":"rich_editor"}).append(
				$("<textarea>", {"id":"description_"+qid,"name":"question_description", "rows":"5", "cols":"50", "style":"width:99%"})
			);
	};

	QuizElements.qn_choice = function(qid) {
		var choices = $("<div>").attr({
			"id" : "choices_" + qid,
			"class" : "choice_options"
		});
		for ( var i = 0; i < 5; i++) {
			choices.append($("<div>").attr( {"class" : "choice"}).append(
				$("<input>", {"style" : "margin-right: 5px;"}).attr({
					id : "choice_" + i + "_" + qid,
					name : "choices[]",
					type : "text",
					size : "40",
					"class" : "input-text"
				}), 
				$("#drag_handle").html())
			);
		}
		return choices;
	};

	QuizElements.qn_grid = function(qid) {
		var grid = $("<table>").attr( {"class" : "grid"}).append(
			$("<thead>").append(
				$("<tr>").append(
					$("<th>").append($("#txt_questions").html()),
					$("<th>").append($("#txt_columns").html())
				)
			)
		);

		var tr = $("<tr>");
		for ( var i = 0; i < 2; i++) {
			var td = $("<td>").attr( {"class" : "sortable"}).css("vertical-align", "top");
			for ( var j = 0; j < 5; j++) {
				td.append(
					$("<div>").append($("<input>", {"style" : "margin-right: 5px;"}).attr( {
						id : "grid_" + i + "_" + j + "_" + qid,
						name : (i == 0) ? "grid_rows[]" : "grid_columns[]",
						type : "text",
						size : "28",
						"class" : "input-text"
					}), 
					$("#drag_handle").html())
				);
			}
			tr.append(td);
		}
		grid.append($("<tbody>").append(
			tr,
			$("<tr>").append(
				$("<td>").append(this.qn_add_row(qid)),
				$("<td>").append(this.qn_add_column(qid)))
			)
		);
		return grid;
	};

	// Element Options
	QuizElements.qn_custom_choice = function(qid) {
		return $("<li>").attr( {"class" : "qn_option"}).append(
			$("<input>", {
				id : "optcustomchoice_" + qid,
				name : "optcustomchoice",
				type : "checkbox",
				value : "1"
			}), 
			$("<label>", {"for" : "optcustomchoice_" + qid}).append($("#txt_custom_choice").html())
		);
	};

	QuizElements.qn_choice_type = function(qid) {
		return $("<li>").attr( {"class" : "qn_option"}).append(
			$("<select>").attr( {id : "optchoice_" + qid,name : "optchoicetype",size : "1"}).append(
				$("<option>").attr( {value : "2"}).append($("#txt_radio").html()),
				$("<option>").attr( {value : "3"}).append($("#txt_checkbox").html()), 
				$("<option>").attr( {value : "4"}).append($("#txt_select").html())
			)
		);
	};

	QuizElements.qn_text_type = function(qid) {
		return $("<li>").attr( {"class" : "qn_option"}).append(
			$("<select>").attr( {id : "opttexttype_" + qid,name : "opttexttype",size : "1"}).append(
				$("<option>").attr( {value : "7"}).append($("#txt_single_line").html()), 
				$("<option>").attr( {value : "8"}).append($("#txt_multi_line").html()), 
				$("<option>").attr( {value : "9"}).append($("#txt_password").html()),
				$("<option>").attr( {value : "10"}).append($("#txt_rich_text").html())
			)
		);
	};

	QuizElements.qn_mandatory = function(qid) {
		return $("<li>").attr( {"class" : "qn_option"}).append(
			$("<input>").attr( {id : "optmandatory_" + qid, name : "optmandatory",type : "checkbox", value : "1"}), 
			$("<label>", {"for" : "optmandatory_" + qid}).append($("#txt_mandatory").html())
		);
	};

	// Element Groups
	QuizElements.page_header = function(qid) {
		return $("<li>", {id : "question_" + qid,"class" : "question_item ui-widget-content ui-corner-all"}).append(
				$("<form>", {"id" : "form_" + qid,"action" : "index.php"}).append(
					$("<div>", {"class" : "qn_toolbox"}).append(this.btn_delete_qn(qid)),
					$("<div>", {"class" : "element_title"}).append($("#lbl_element_title_page_header").html()),
					$("<div>", {"class" : "clear"}),
					$("<hr>", {"class":"ui-widget-content", "style":"border-bottom: 1px;"}),
					$("<span>").attr( {"class" : "question_title"}).append($("#txt_page_header_title").html()),
					this.qn_title(qid), 
					$("<span>").attr( {"class" : "question_title"}).append($("#txt_enter_description").html()),
					this.qn_description(qid),
					$("<hr>", {"class":"ui-widget-content", "style":"border-bottom: 1px;", "id" : "ruler_" + qid}), 
					$("<div>", {"id" : "action_" + qid,"style" : "float: right;"}).append(this.qn_save(qid)), 
					$("<div>", {"style" : "clear: right;"}), 
					$("<input>", {"type" : "hidden","name" : "qtype","id" : "qtype_" + qid,"value" : 1}), 
					$("<input>", {"type" : "hidden","name" : "order","value" : 1}), 
					$("<input>", {"type" : "hidden","name" : "id","value" : this.id}), 
					$("<input>", {"type" : "hidden","name" : "pid","value" : this.pid})
				)
			);
	};

	QuizElements.multiple_choice = function(qid) {
		return $("<li>", {id : "question_" + qid,"class" : "question_item ui-widget-content ui-corner-all"}).append(
				$("<form>", {"id" : "form_" + qid,"action" : "index.php"}).append(
					$("<div>", {"class" : "qn_toolbox"}).append(this.btn_delete_qn(qid), this.btn_move_up(qid), this.btn_move_down(qid)),
					$("<div>", {"class" : "element_title"}).append($("#lbl_element_title_multiple_choice").html()),
					$("<div>", {"class" : "clear"}),
					$("<hr>", {"class":"ui-widget-content", "style":"border-bottom: 1px;"}),
					$("<span>").attr( {"class" : "question_title"}).append($("#txt_enter_question_title").html()),
					this.qn_title(qid),
					$("<span>").attr( {"class" : "question_title"}).append($("#txt_enter_description").html()),
					this.qn_description(qid),
					$("<div>", {"id" : "optionshelp_" + qid}).html($("#txt_enter_options").html() + ":"),
					this.qn_choice(qid),
					$("<div>", {"id" : "btn_add_choice_" + qid}).append(this.qn_add_choice(qid)),
					$("<div>", {"id" : "choice_empty_" + qid}).append($("#info_empty_choice_ignore").html()),
					$("<hr>", {"class":"ui-widget-content", "style":"border-bottom: 1px;", "id" : "ruler_" + qid}),
					$("<div>", {"id" : "action_" + qid,"style" : "float: right;"}).append(this.qn_save(qid)),
					$("<ul>", {"class" : "qn_options"}).append(this.qn_mandatory(qid),this.qn_custom_choice(qid),this.qn_choice_type(qid)), 
					$("<div>", {"style" : "clear: right;"}), 
					$("<input>", {"type" : "hidden","name" : "qtype","id" : "qtype_" + qid,"value" : 2}), 
					$("<input>", {"type" : "hidden","name" : "order","value" : qid+1}), 
					$("<input>", {"type" : "hidden", "name" : "id","value" : this.id}), 
					$("<input>", {"type" : "hidden","name" : "pid","value" : this.pid})
				)
			);
	};

	QuizElements.grid = function(qid) {
		return $("<li>", {id : "question_" + qid,"class" : "question_item ui-widget-content ui-corner-all"}).append(
				$("<form>", {"id" : "form_" + qid,"action" : "index.php"}).append(
					$("<div>", {"class" : "qn_toolbox"}).append(this.btn_delete_qn(qid), this.btn_move_up(qid), this.btn_move_down(qid)),
					$("<div>", {"class" : "element_title"}).append($("#lbl_element_title_grid").html()),
					$("<div>", {"class" : "clear"}),
					$("<hr>", {"class":"ui-widget-content", "style":"border-bottom: 1px;"}),
					$("<span>").attr( {"class" : "question_title"}).append($("#txt_enter_question_title").html()),
					this.qn_title(qid), 
					$("<span>").attr( {"class" : "question_title"}).append($("#txt_enter_description").html()),
					this.qn_description(qid),
					$("<div>", {"id" : "optionshelp_" + qid}).html($("#txt_enter_options").html() + ":"),
					this.qn_grid(qid), 
					$("<div>", {"id" : "choice_empty_" + qid}).append($("#info_empty_choice_ignore").html()),
					$("<hr>", {"class":"ui-widget-content", "style":"border-bottom: 1px;", "id" : "ruler_" + qid}), 
					$("<div>", {"id" : "action_" + qid,"style" : "float: right;"}).append(this.qn_save(qid)), 
					$("<ul>", {"class" : "qn_options"}).append(this.qn_mandatory(qid),this.qn_custom_choice(qid)), 
					$("<div>", {"style" : "clear: right;"}), 
					$("<input>", {"type" : "hidden","name" : "qtype","id" : "qtype_" + qid,"value" : 5}), 
					$("<input>", {"type" : "hidden","name" : "order","value" : qid+1}), 
					$("<input>", {"type" : "hidden","name" : "id","value" : this.id}), 
					$("<input>", {"type" : "hidden","name" : "pid","value" : this.pid})
				)
			);
	};

	QuizElements.free_text = function(qid) {
		return $("<li>", {id : "question_" + qid,"class" : "question_item ui-widget-content ui-corner-all"}).append(
				$("<form>", {"id" : "form_" + qid,"action" : "index.php"}).append(
					$("<div>", {"class" : "qn_toolbox"}).append(this.btn_delete_qn(qid), this.btn_move_up(qid), this.btn_move_down(qid)),
					$("<div>", {"class" : "element_title"}).append($("#lbl_element_title_free_text").html()),
					$("<div>", {"class" : "clear"}),
					$("<hr>", {"class":"ui-widget-content", "style":"border-bottom: 1px;"}),
					$("<span>").attr( {"class" : "question_title"}).append($("#txt_enter_question_title").html()),
					this.qn_title(qid),
					$("<span>").attr( {"class" : "question_title"}).append($("#txt_enter_description").html()),
					this.qn_description(qid),
					$("<hr>", {"class":"ui-widget-content", "style":"border-bottom: 1px;", "id" : "ruler_" + qid}),
					$("<div>", {"id" : "action_" + qid, "style" : "float: right;"}).append(this.qn_save(qid)),
					$("<ul>", {"class" : "qn_options"}).append(this.qn_mandatory(qid),this.qn_text_type(qid)), 
					$("<div>", {"style" : "clear: right;"}), 
					$("<input>", {"type" : "hidden","name" : "qtype","id" : "qtype_" + qid,"value" : 7}), 
					$("<input>", {"type" : "hidden","name" : "order","value" : qid+1}), 
					$("<input>", {"type" : "hidden","name" : "id","value" : this.id}), 
					$("<input>", {"type" : "hidden","name" : "pid","value" : this.pid})
				)
			);
	};

	// Element Actions
	QuizElements.qn_save = function(qid) {
		return $("<a>", {"id" : "save_" + qid,"href" : "#","html" : $("#lbl_save").html()}).button( {
			icons : {primary : "ui-icon-disk"}}
		).bind("click",function() {
			$(this).unbind("click");
			$(this).bind("click", function() {
				return false;
			});
			if((typeof tinyMCE != 'undefined') && tinyMCE.getInstanceById('description_'+qid)){
            	tinyMCE.triggerSave();
            }
			$("#form_" + qid).ajaxSubmit({
				url : $("#url_saveqn").text(),
				type : "post",
				dataType : "json",
				success : function(result, status, xhr, form) {
					QuizElements.update_response(qid, result,status, xhr, form, false);
				}
			});
			return false;
		});
	};

	QuizElements.qn_add_choice = function(qid) {
		var button = $("<button>");
		button.append($("#lbl_add_option").html());
		button.button();
		button.bind("click", function() {
			var len = $("#choices_" + qid).find(".choice").length;
			$("#choices_" + qid).append(
				$("<div>").attr( {"class" : "choice"}).append(
					$("<input>", {"style" : "margin-right: 5px;"}).attr( {
						id : "choice_" + len + "_" + qid,
						name : "choices[]",
						type : "text",
						size : "40",
						"class" : "input-text"
					}), 
					$("#drag_handle").html()
				)
			);
			return false;
		});
		return button;
	};

	QuizElements.qn_add_row = function(qid) {
		var button = $("<button>");
		button.append($("#lbl_add_row").html());
		button.button();
		button.bind("click", function() {
			var rowtd = $("#form_" + qid).find(".grid").find("tbody").find("tr:first").find("td:first");
			var len = rowtd.find("input").length;
			rowtd.append(
				$("<div>").append(
					$("<input>", {"style" : "margin-right: 5px;"}).attr( {
						id : "grid_0_" + len + "_" + qid,
						name : "grid_rows[]",
						type : "text",
						size : "28",
						"class" : "input-text"
					}), 
					$("#drag_handle").html()
				)
			);
			return false;
		});
		return button;
	};

	QuizElements.qn_add_column = function(qid) {
		var button = $("<button>");
		button.append($("#lbl_add_column").html());
		button.button();
		button.bind("click", function() {
			var rowtd = $("#form_" + qid).find(".grid").find("tbody").find("tr:first").find("td:last");
			var len = rowtd.find("input").length;
			rowtd.append(
				$("<div>").append(
					$("<input>", {"style" : "margin-right: 5px;"}).attr( {
						id : "grid_1_" + len + "_" + qid,
						name : "grid_columns[]",
						type : "text",
						size : "28",
						"class" : "input-text"
					}), 
					$("#drag_handle").html()
				)
			);
			return false;
		});
		return button;
	};
	
	QuizElements.btn_delete_qn = function(qid){
		return $("<a>", {"class":"btn_delete_qn", "title":$("#lbl_delete_qn").html(), "href":"#"}).click(function(){
			var buttonOpts = {};
			var lbl_cancel = $("#lbl_cancel").html();
			var lbl_ok = $("#lbl_ok").html();
			buttonOpts[lbl_ok] = function() {
				$(this).dialog("close");
				var dialog1 = $("<div>", {"title" : $("#lbl_processing").html()}).html($("#msg_please_wait").html()).dialog({modal : true});
				if($("#qid_"+qid).length > 0){
					$.ajax({
						url : $("#url_delete_qn").html(),
						type : "post",
						dataType : "json",
						data: "id="+$("#id").text()+"&qid="+$("#question_"+qid).find("input[name='qid']").val()+"&pid="+$("#pid").text(),
						success : function(data) {
							if (typeof data.error != "undefined" && data.error.length > 0) {
								dialog1.dialog("close");
								$("<div>", {"title" : $("#txt_error").html()}).html(data.error).dialog( {
									modal : true,
									buttons : {
										Ok : function() {
											$(this).dialog("close");
										}
									}
								});
							}else{
								$("#question_" + qid).effect("highlight", 2000).remove();
								dialog1.dialog("close");
							}
						}
					});				
				}else{
					$("#question_" + qid).effect("highlight", 2000).remove();
					dialog1.dialog("close");
				} 
			};
			buttonOpts[lbl_cancel] = function() {
				$(this).dialog("close");
			};
			var dialog = $("<div>", {"title" : $("#lbl_alert").html()}).html($("#msg_confirm").html()).dialog( { 
				modal : true, 
				buttons : buttonOpts
			});			
			return false;
		}).html($("#img_delete_qn").html());
	};
	
	QuizElements.btn_move_up = function(qid){
		return $("<a>", {"class":"btn_move_up", "title":$("#lbl_move_up").html(), "href":"#"}).click(function(){
			if($("#question_"+qid).prev().find("input[name='qtype']").val() == "1"){return false;}
			var dialog = $("<div>", {"title" : $("#lbl_processing").html()}).html($("#msg_please_wait").html()).dialog( { modal : true });
			if($("#qid_"+qid).length > 0 && ($("#question_"+qid).prev().length > 0) && ($("#question_"+qid).prev().find("input[name='qid']").length > 0)){
				$.ajax({
					url : $("#url_move_up").html(),
					type : "post",
					dataType : "json",
					data: "id="+$("#id").text()+"&qid="+$("#question_"+qid).find("input[name='qid']").val(),
					success : function(data) {
						if (typeof data.error != "undefined" && data.error.length > 0) {
							dialog.dialog("close");
							$("<div>", {"title" : $("#txt_error").html()}).html(data.error).dialog( {
								modal : true,
								buttons : {
									Ok : function() {
										$(this).dialog("close");
									}
								}
							});
						}else{
							if($("#question_"+qid).prev().length > 0){
								$("#question_"+qid).prev().find("input[name='order']").val($("#question_"+qid).find("input[name='order']").val());
								$("#question_"+qid).find("input[name='order']").val(data.order);
								$("#question_"+qid).insertBefore($("#question_"+qid).prev()).effect("highlight", 2000);
							}
							dialog.dialog("close");
						}
					}
				});
			}else{
				dialog.dialog("close");
				if(($("#question_"+qid).prev().length > 0) && ($("#question_"+qid).prev().find("input[name='qid']").length > 0)){
					$("#msg_save_questions").dialog({ modal : true, buttons : { Ok : function() { $(this).dialog("close"); } } });
				}else{
					if($("#question_"+qid).prev().length > 0){
						$("#question_"+qid).insertBefore($("#question_"+qid).prev()).effect("highlight", 2000);
					}
				}
			}
			return false;
		}).html($("#img_move_up").html());
	};
	
	QuizElements.btn_move_down = function(qid){
		return $("<a>", {"class":"btn_move_down", "title":$("#lbl_move_down").html(), "href":"#"}).click(function(){
			var dialog = $("<div>", {"title" : $("#lbl_processing").html()}).html($("#msg_please_wait").html()).dialog( { modal : true });
			if($("#qid_"+qid).length > 0 && ($("#question_"+qid).next().length > 0) && ($("#question_"+qid).next().find("input[name='qid']").length > 0)){
				$.ajax({
					url : $("#url_move_down").html(),
					type : "post",
					dataType : "json",
					data: "id="+$("#id").text()+"&qid="+$("#question_"+qid).find("input[name='qid']").val(),
					success : function(data) {
						if (typeof data.error != "undefined" && data.error.length > 0) {
							dialog.dialog("close");
							$("<div>", {"title" : $("#txt_error").html()}).html(data.error).dialog( {
								modal : true,
								buttons : {
									Ok : function() {
										$(this).dialog("close");
									}
								}
							});
						}else{
							if($("#question_"+qid).next().length > 0){
								$("#question_"+qid).next().find("input[name='order']").val($("#question_"+qid).find("input[name='order']").val());
								$("#question_"+qid).find("input[name='order']").val(data.order);
								$("#question_"+qid).insertAfter($("#question_"+qid).next()).effect("highlight", 2000);
							}
							dialog.dialog("close");
						}
					}
				});
			}else{
				dialog.dialog("close");
				if(($("#question_"+qid).next().length > 0) && ($("#question_"+qid).next().find("input[name='qid']").length > 0)){
					$("#msg_save_questions").dialog({ modal : true, buttons : { Ok : function() { $(this).dialog("close"); } } });
				}else{
					if($("#question_"+qid).next().length > 0){
						$("#question_"+qid).insertAfter($("#question_"+qid).next()).effect("highlight", 2000);
					}
				}
			}
			return false;
		}).html($("#img_move_down").html());
	};

	QuizElements.update_response = function(qid, result, status, xhr, form,skip_checks) {
		var type = parseInt($("#qtype_" + qid).val(), 10);
		var option = QuizElements.option;
		// If is is loading questions from edit operation, no need to
		// check for save errors, else check and display errors of save
		// operation. If it is new question created, there will be no errors from
		// backend and hence this can be skipped.
		if (skip_checks == false) {
			if (result != null && typeof result.error != "undefined") {
				$("<div>", {"title" : $("#txt_error").html()}).html(result.error).dialog( {
					modal : true,
					width : 400,
					buttons : {
						Ok : function() {
							$(this).dialog("close");
						}
					}
				});
				$("#save_" + qid).unbind("click");
				$("#save_" + qid).bind("click", function() {
					$(this).unbind("click");
					$(this).bind("click", function() {
						return false;
					});
					$("#form_" + qid).ajaxSubmit({
						url : $("#url_saveqn").text(),
						type : "post",
						dataType : "json",
						success : function(result, status, xhr,form) {
							QuizElements.update_response(qid,result, status, xhr, form,false);
						}
					});
					return false;
				});
				return false;
			} else {
				$("#form_" + qid).append($("<input>", {"type" : "hidden","id" : "qid_" + qid,"name" : "qid"}).val(result.data));
			}
		}
		
		$("#form_" + qid).find(".question_title").hide();
		$("#title_" + qid).replaceWith($("<div>", {"id" : "title_" + qid,"class" : "question_title"}).html($("#title_" + qid).val()));
		$("#form_" + qid).find(".qn_title").after($("<div>",{"class":"question_desc"}).html($("#description_" + qid).val()));
		$("#form_" + qid).find(".rich_editor").hide();
		$("#form_" + qid).find(".qn_options").hide();
		$("#action_" + qid).hide();
		$("#ruler_" + qid).hide();
		
		switch (type) {
		case 1: // Page header
			$("#question_" + qid).bind("click",function() {
				QuizElements.toggle_form_elements(qid);
			});
			break;
		case 2: // Multiple choice - radio
		case 3: // Multiple choice - checkbox
		case 4: // Multiple choice - select
			type = parseInt($("#optchoice_" + qid).val(), 10);
			$("#optionshelp_" + qid).hide();
			$("#question_" + qid).find(".choice").hide();
			$("#choice_empty_" + qid).hide();
			$("#btn_add_choice_" + qid).hide();
			switch (type) {
			case 2:
			case 3:
				$("#question_" + qid).find("input[name='choices[]']").each(function(index, element) {
					if (element.value != null && element.value != "") {
						$("#choices_" + qid).append(
							$("<div>",{"class" : "choicediv"}).append(
								$("<input>",{"type" : (type == 2) ? "radio" : "checkbox" }),
								$("<label>").html(element.value)));
							}
						});
				break;
			case 4:
				var select = $("<select>").append($("<option>").html($("#lbl_select_option").text()));
				$("#question_" + qid).find("input[name='choices[]']").each(function(index, element) {
					if (element.value != null && element.value != "") {
						select.append($("<option>").html(element.value));
					}
				});
				$("#choices_" + qid).append($("<div>", {"class" : "choicediv"}).append(select));
				break;
			}
			$("#question_" + qid).bind("click", function() {
				$("#optionshelp_" + qid).show();
				$("#choices_" + qid).show();
				$("#choice_empty_" + qid).show();
				$("#btn_add_choice_" + qid).show();
				$("#question_" + qid).find(".choicediv").remove();
				$("#question_" + qid).find(".choice").show();
				$("#form_" + qid).find(".choice_options").sortable({revert : true,opacity : 0.6,cursor : "move",placeholder : "ui-state-highlight"});
				QuizElements.toggle_form_elements(qid);
			});
			break;
		case 5: // Grid Radio
		case 6: // Grid Checkbox
			$("#optionshelp_" + qid).hide();
			$("#form_" + qid).find(".grid").hide();
			$("#choice_empty_" + qid).hide();
			var rows = $("#form_" + qid).find(".grid").find("tbody").find("tr:first").find("td:first").find("input");
			var cols = $("#form_" + qid).find(".grid").find("tbody").find("tr:first").find("td:last").find("input");
			var thead = $("<tr>").append($("<th>"));
			cols.each(function(index) {
				thead.append($("<th>").append(cols[index].value));
			});
			var tbody = $("<tbody>");
			for ( var i = 0; i < rows.length; i++) {
				tr = $("<tr>").append($("<td>").append(rows[i].value));
				for ( var j = 0; j < cols.length; j++) {
					tr.append($("<td>").append($("<input>", {"type" : "radio"	})));
				}
				tbody.append(tr);
			}
			$("<table>", {"class" : "preview_grid"}).append($("<thead>").append(thead), tbody).insertAfter($("#form_" + qid).find(".qn_title"));

			$("#question_" + qid).bind("click", function() {
				$("#optionshelp_" + qid).show();
				$("#form_" + qid).find(".grid").show();
				$("#choice_empty_" + qid).show();
				$("#form" + qid).find(".choicediv").remove();
				$("#question_" + qid).find(".choice").show();
				$("#question_" + qid).find(".preview_grid").remove();
				$("#form_" + qid).find(".sortable").sortable( {revert : true, opacity : 0.6, cursor : "move", placeholder : "ui-state-highlight"});
				QuizElements.toggle_form_elements(qid);
			});
			break;
		case 7: // Free text - Single line
		case 8: // Free text - Multiline
		case 9: // Free text - Password
		case 10: // Free text - Rich Editor
			type = parseInt($("#opttexttype_" + qid).val(), 10);
			switch (type) {
			case 7: // Single line
				$("<input>", { "id" : "temp_free_text_" + qid,"type" : "text","class" : "input-text" }).insertAfter($("#title_" + qid));
				break;
			case 8: // Multi line
				$("<textarea>", {"id" : "temp_free_text_" + qid,"cols" : "30","rows" : "3","class" : "input-text" }).insertAfter($("#title_" + qid));
				break;
			case 9: // Password
				$("<input>", {"id" : "temp_free_text_" + qid,"type" : "password","class" : "input-text" }).insertAfter($("#title_" + qid));
				break;
			}

			$("#question_" + qid).bind( "click", function() {
				$("#temp_free_text_" + qid).remove();
				QuizElements.toggle_form_elements(qid);
			});
			break;
		}
	};
	
	QuizElements.toggle_form_elements = function(qid){
		$("#question_" + qid).unbind("click");
		$("#form_" + qid).find(".question_title").show();
		$("#title_" + qid).replaceWith($("<input>", {"type" : "text","id" : "title_" + qid,"name" : "question_title","class" : "input-text" }).val($("#title_" + qid).html()));
		$("#form_" + qid).find(".rich_editor").show();
		$("#form_" + qid).find(".question_desc").remove();
		$("#ruler_" + qid).show();
		$("#form_" + qid).find(".qn_options").show();
		$("#action_" + qid).show();		
		$("#save_" + qid).unbind("click");
		$("#save_" + qid).bind("click", function() {
			$(this).unbind("click");
			$(this).bind("click", function() {return false;});
			if((typeof tinyMCE != 'undefined') && tinyMCE.getInstanceById('description_'+qid)){
            	tinyMCE.triggerSave();
            }
			$("#form_" + qid).ajaxSubmit({
				url : $("#url_saveqn").text(),
				type : "post",
				dataType : "json",
				success : function(result, status, xhr, form) {
					QuizElements.update_response(qid, result, status, xhr, form, false);
				}
			});
			return false;
		});
	};

	QuizElements.build_question_list = function(questions) {
		var qid = 0;
		for (question in questions) {
			var qtype = parseInt(questions[question].question_type, 10);
			var mandatory = parseInt(questions[question].mandatory, 10) > 0 ? true : false;
			switch (qtype) {
			case 1: // Page header
				$("#quiz-content").find("ul:first").append(QuizElements.page_header(qid));
				break;
			case 2: // Multiple Choice - Radio
			case 3: // Multiple Choice - Checkbox
			case 4: // Multiple Choice - Select
				$("#quiz-content").find("ul:first").append(QuizElements.multiple_choice(qid));
				if (typeof questions[question].answers != "undefined") {
					var i = 0;
					for (var answer in questions[question].answers) {
						if (typeof questions[question].answers[i] != "undefined") {
							if (i > 4) {
								$("#choices_"+qid).append(
									$("<div>").attr({"class":"choice"}).append(
										$("<input>", {
											"style" : "margin-right: 5px;", 
											"id" : "choice_" + i + "_" + qid,
											"name" : "choices[]",
											"type" : "text",
											"size" : "40",
											"class" : "input-text"}),
										$("#drag_handle").html()
									)
								);
							}
							$("#choice_" + i + "_" + qid).val(questions[question].answers[i].title);
							i++;
						}
					}
				}
				$("#optchoice_" + qid).children("option[value=" + questions[question].question_type+ "]").attr("selected", "selected");
				var optcustomchoice = parseInt(questions[question].include_custom, 10) > 0 ? true : false;
				$("#optcustomchoice_" + qid).attr("checked",optcustomchoice);
				break;
			case 5: // Grid - Radio
			case 6: // Grid - Checkbox
				$("#quiz-content").find("ul:first").append(QuizElements.grid(qid));
				if (typeof questions[question].answers != "undefined") {
					var i = 0;
					var j = 0;
					for ( var answer = 0; answer < questions[question].answers.length; answer++) {
						if (typeof questions[question].answers[i] != "undefined") {
							if (questions[question].answers[i].answer_type == "x") {
								if (i > 4) {
									$("#form_" + qid).find(".grid").find("tbody").find("tr:first").find("td:first").append(
										$("<div>").append(
											$("<input>",{"style" : "margin-right: 5px;"}).attr({
												id : "grid_0_" + i + "_" + qid,
												name : "grid_rows[]", 
												type : "text",
												size : "28",
												"class" : "input-text" }
											),
											$("#drag_handle").html()
										)
									);
								}
								$("#grid_0_" + i + "_" + qid).val(questions[question].answers[answer].title);
								i++;
							} else if (questions[question].answers[i].answer_type == "y") {
								if (j > 4) {
									$("#form_" + qid).find(".grid").find("tbody").find("tr:first").find("td:last").append(
										$("<div>").append(
											$("<input>",{"style" : "margin-right: 5px;"}).attr({
												id : "grid_1_" + j + "_" + qid,
												name : "grid_columns[]",
												type : "text",
												size : "28",
												"class" : "input-text"}
											),
											$("#drag_handle").html()
										)
									);
								}
								$("#grid_1_" + j + "_" + qid).val(questions[question].answers[answer].title);
								j++;
							}
						}
					}
				}
				var optcustomchoice = parseInt(questions[question].include_custom, 10) > 0 ? true : false;
				$("#optcustomchoice_" + qid).attr("checked",optcustomchoice);
				break;
			case 7: // Free Text - Single line
			case 8: // Free Text - Multi line
			case 9: // Free Text - Password
			case 10: // Free text - Rich Editor
				$("#quiz-content").find("ul:first").append(QuizElements.free_text(qid));
				$("#opttexttype_" + qid).children("option[value=" + questions[question].question_type+ "]").attr("selected", "selected");
				break;
			}
			$("#title_" + qid).val(questions[question].title);
			$("#description_" + qid).val(questions[question].description);
			QuizElements.attach_editor($("#form_"+qid).find("#description_"+qid));
			$("#optmandatory_" + qid).attr("checked", mandatory);
			$("#form_" + qid).append($("<input>", {"type" : "hidden","id" : "qid_" + qid,"name" : "qid"}).val(questions[question].id));
			this.update_response(qid, null, null, null, null, true);
			qid++;
		}
		this.init_content_area();
	};

	QuizElements.attach_handlers = function() {
		/** Page Header */
		$("#pageheader").unbind("click");
		$("#pageheader").bind("click", function() {
			if ($("input[name='qtype'][value='1']").length > 0) {
				$("#msg_page_header_exists").dialog( {
					modal : true,
					buttons : {
						Ok : function() {
							$(this).dialog("close");
						}
					}
				});
				return false;
			} else {
				var qid = $("#quiz-content").find("ul:first").children("li").length + 1;
				$("#quiz-content").find("ul:first").prepend(QuizElements.page_header(qid));
				$.scrollTo($("#question_" + qid), {duration : 1000,offset : -100});
				$("#question_" + qid).effect("highlight", "",2000, "");
				QuizElements.attach_editor($("#form_"+qid).find("#description_"+qid));
			}
		});

		/** Multiple Choice */
		$("#choice").unbind("click");
		$("#choice").bind("click", function() {
			var qid = $("#quiz-content").find("ul:first").children("li").length + 1;
			$("#quiz-content").find("ul:first").append(QuizElements.multiple_choice(qid));
			$(".choice_options").sortable( {revert : true, opacity : 0.6, cursor : "move", placeholder : "ui-state-highlight"});
			$.scrollTo($("#question_" + qid), {duration : 1000, offset : -100 });
			$("#question_" + qid).effect("highlight", "", 2000, "");
			QuizElements.attach_editor($("#form_"+qid).find("#description_"+qid));
		});

		/** Grid */
		$("#grid").unbind("click");
		$("#grid").bind("click", function() {
			var qid = $("#quiz-content").find("ul:first").children("li").length + 1;
			$("#quiz-content").find("ul:first").append(QuizElements.grid(qid));
			$(".grid").find(".sortable").sortable( {revert : true, opacity : 0.6, cursor : "move", placeholder : "ui-state-highlight"});
			$.scrollTo($("#question_" + qid), {duration : 1000, offset : -100 });
			$("#question_" + qid).effect("highlight", "", 2000, "");
			QuizElements.attach_editor($("#form_"+qid).find("#description_"+qid));
		});

		/** Free Text */
		$("#textbox").unbind("click");
		$("#textbox").bind("click", function() {
			var qid = $("#quiz-content").find("ul:first").children("li").length + 1;
			$("#quiz-content").find("ul:first").append(QuizElements.free_text(qid));
			$.scrollTo($("#question_" + qid), {duration : 1000, offset : -100});
			$("#question_" + qid).effect("highlight", "", 2000, "");
			QuizElements.attach_editor($("#form_"+qid).find("#description_"+qid));
		});
	};

	QuizElements.attach_editor = function(element){
		if(typeof myBBCodeSettings != "undefined"){
			element.markItUp(myBBCodeSettings);
		}else if(typeof tinyMCE != "undefined"){
			tinyMCE.execCommand("mceAddControl", false, element.attr("id"));
		}
	};
	
	QuizElements.init_content_area = function() {
		$("#quiz-toolbox").find("ul:first").children("li").draggable( {
			revert : false,
			accept : "#quiz-content",
			connectToSortable : "#quiz-content > ul",
			helper : "clone"
		});
		$("#quiz-content").droppable({
			accept : "#quiz-toolbox > ul > li",
			drop : function(event, ui) {
				var qid = $("#quiz-content").find("ul:first").children("li").length;
				var id = $(ui.draggable).attr("id");

				/** Page Header */
				if (id.indexOf("pageheader") != -1) {
					if ($("input[name='qtype'][value='1']").length > 0) {
						$("#msg_page_header_exists").dialog({
							modal : true,
							buttons : {
								Ok : function() {
									$(this).dialog("close");
								}
							}
						});
						return false;
					} else {
						qid = $("#quiz-content").find("ul:first").children("li").length + 1;
						$("#quiz-content").find("ul:first").prepend(QuizElements.page_header(qid));
						$.scrollTo($("#question_"+ qid), {duration : 1000,offset : -100});
						$("#question_" + qid).effect("highlight", "", 2000, "");
						QuizElements.attach_editor($("#form_"+qid).find("#description_"+qid));
					}
				}

				/** Multiple Choice */
				if (id.indexOf("choice") != -1) {
					qid = $("#quiz-content").find("ul:first").children("li").length + 1;
					$("#quiz-content").find("ul:first").append(QuizElements.multiple_choice(qid));
					$(".choice_options").sortable( {revert : true, opacity : 0.6, cursor : "move", placeholder : "ui-state-highlight"});
					$.scrollTo($("#question_" + qid),{duration : 1000, offset : -100});
					$("#question_" + qid).effect("highlight", "", 2000, "");
					QuizElements.attach_editor($("#form_"+qid).find("#description_"+qid));
				}

				/** Grid */
				if (id.indexOf("grid") != -1) {
					qid = $("#quiz-content").find("ul:first").children("li").length + 1;
					$("#quiz-content").find("ul:first").append(QuizElements.grid(qid));
					$(".grid").find(".sortable").sortable({revert : true, opacity : 0.6, cursor : "move", placeholder : "ui-state-highlight"});
					$.scrollTo($("#question_" + qid),{duration : 1000, offset : -100});
					$("#question_" + qid).effect("highlight", "", 2000, "");
					QuizElements.attach_editor($("#form_"+qid).find("#description_"+qid));
				}

				/** Free Text */
				if (id.indexOf("textbox") != -1) {
					qid = $("#quiz-content").find("ul:first").children("li").length + 1;
					$("#quiz-content").find("ul:first").append(QuizElements.free_text(qid));
					$.scrollTo($("#question_" + qid),{duration : 1000, offset : -100 });
					$("#question_" + qid).effect("highlight", "", 2000, "");
					QuizElements.attach_editor($("#form_"+qid).find("#description_"+qid));
				}
				$("#" + qid + "_title").focus();
			}
		});

		$("#new_page ").unbind("click");
		$("#new_page").bind("click", function() {
			$("#progress-confirm").appendTo($(".cs- ")).show();
			$.ajax( {
				url : $("#url_newpage").text(),
				data : "id=" + QuizElements.id,
				type : "POST",
				dataType : "json",
				success : function(data) {
					if (typeof data.error != "undefined" && data.error.length > 0) {
						$("<div>",{"title" : $("#txt_error").html()}).html(data.error).dialog({
							modal : true,
							buttons : {
								Ok : function() {
									$(this).dialog("close");
								}
							}
						});
					} else {
						if (typeof data.page != "undefined") {
							var id = parseInt($(".cs-pagination").find("input[name='current_page']:last").next().find("span").text(), 10);
							if (isNaN(id)) {
								id = $(".cs-pagination").find("input[name='current_page']").length + 1;
							} else {
								id = id + 1;
							}
							$("<input>", { "name" : "current_page", "type" : "radio", "id" : "page_"+ id}).val(data.page).insertBefore(
								$(".cs-pagination").find("#new_page")
							);
							$("<label>", {"for" : "page_" + id}).html(id).insertBefore(
								$(".cs-pagination").find("#new_page")
							);
							QuizElements.init_page_elements();
						}
					}
					$("#progress-confirm").hide();
				}
			});
			$(this).attr("checked", false);
		});

		$("#remove_page").unbind("click");
		$("#remove_page").bind("click", function() {
			if ($(".cs-pagination").find("input[type='radio']:first").attr("checked") == true) {
				$("#msg_remove_first_page").dialog( {
					modal : true,
					buttons : {
						Ok : function() {
							$(this).dialog("close");
						}
					}
				});
			} else {
				var pageid = $(".cs-pagination").find("input[name='current_page']:checked").val();
				$("#msg_remove_current_page").dialog({
					modal : true,
					buttons : {
						Ok : function() {
							var dialog = $(this);
							$.ajax( {
								url : $("#url_removepage").text(),
								data : "id=" + QuizElements.id + "&pid=" + pageid,
								type : "POST",
								dataType : "json",
								success : function(data) {
									if (typeof data.error != "undefined" && data.error.length > 0) {
										$("<div>",{"title" : $("#txt_error").html()}).html(data.error).dialog({
											modal : true,
											buttons : {
												Ok : function() {
													$(this).dialog("close");
												}
											}
										});
									} else {
										QuizElements.load_questions($(".cs-pagination").find("input[type='radio']:first").val());
										$(".cs-pagination").find("input[name='current_page']:checked").next().remove();
										$(".cs-pagination").find("input[name='current_page']:checked").remove();
										$(".cs-pagination").find("input[name='current_page']").first().attr("checked",true);
										QuizElements.init_page_elements();
										dialog.dialog("close");
									}
								}
							});
						},
						Cancel : function() {
							$(this).dialog("close");
						}
					}
				});
			}
			$(this).attr("checked", false);
		});

		$("#finish_quiz").unbind("click");
		$("#finish_quiz").bind("click", function() {
			var pageid = $(".cs-pagination").find("input[name='current_page']:checked").val();
			$("#msg_unsaved_questions").dialog( {
				modal : true,
				buttons : {
					Ok : function() {
						var dialog = $(this).dialog('close');
						location.replace($("#url_finish").text());
					},
					Cancel : function() {
						$(this).dialog('close');
					}
				}
			});
			$(this).attr("checked", false);
		});
		$("#msg_drag_drop").show();
		$("#init_quiz_help").remove();
		this.attach_handlers();
	};

	QuizElements.load_questions = function(pageid) {
		if (null != pageid) {
			this.pid = pageid;
		}
		$('ul[class="content"]').empty();
		$("<div>", {"id" : "init_quiz_help"}).append(
			$("#progress-confirm").clone().show(),
			$("#txt_loading_wait").html()).insertBefore($("#quiz-content").find("ul:first")
		);
		$.ajax( {
			url : $("#url_loadqn").text(),
			data : "id=" + QuizElements.id + "&pid=" + QuizElements.pid,
			type : 'POST',
			dataType : 'json',
			success : function(data) {
				if (typeof data.error != 'undefined' && data.error.length > 0) {
					$("<div>", {"title" : $("#txt_error").html()}).html(data.error).dialog( {
						modal : true,
						buttons : {
							Ok : function() {
								$(this).dialog('close');
							}
						}
					});
				} else {
					if (typeof data.questions != 'undefined') {
						QuizElements.build_question_list(data.questions);
					}
				}
			}
		});
	};

	QuizElements.load_page = function(pageid) {
		if (isNaN(pageid)) {
			$("#msg_error_processing").dialog( {
				modal : true,
				buttons : {
					Ok : function() {
						$(this).dialog('close');
					}
				}
			});
			return false;
		}
		$("#msg_unsaved_questions").dialog( {
			modal : true,
			buttons : {
				Ok : function() {
					$("#quiz-content").find("ul:first").empty();
					$("#msg_drag_drop").hide();
					$("#pid").html(pageid);
					$(this).dialog('close');
					QuizElements.load_questions(pageid);
				},
				Cancel : function() {
					$(this).dialog('close');
				}
			}
		});
	};

	QuizElements.init_page_elements = function() {
		$(".cs-pagination").buttonset();
		$(".cs-pagination").find("input[name='current_page']").unbind("click");
		$(".cs-pagination").find("input[name='current_page']").bind("click", function() {QuizElements.load_page($(this).val());});
	};
})(jQuery);