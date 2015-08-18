$(function(){
	// Socket IO
	app = EIO.app($conf['websocket']);

	// Update GIT FTP
	$("#update_git_ftp").click(function(e){
		e.preventDefault();
		if(typeof nds != "undefined") return false;
		nds = true;
		$btn = $(this);
		$btn_name = $btn.html();
		$(this).html('<div class="spinner light"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div>');

		$.ajax({
			url : 'action.php?update',
			success : function(a){
				delete nds;
				$btn.html($btn_name);
				gAlert(a);
			}
		});

		return false;
	});

	// Save Project
	$(".save_project").click(function(e){
		e.preventDefault();
		if(typeof nds != "undefined") return false;
		nds = true;
		$id = $("#project-id").val();
		$project = $("#project").val();
		$git_repo = $("#git_repo").val();
		$git_user = $("#git_user").val();
		$git_pass = $("#git_pass").val();
		$git_ignore_dir = $("#git_ignore_dir").val();
		$ftp_server = $("#ftp_server").val();
		$ftp_user = $("#ftp_user").val();
		$ftp_pass = $("#ftp_pass").val();
		$ftp_dir = $("#ftp_dir").val();
		$btn = $(this);
		$btn_name = $btn.html();
		$(this).html('<div class="spinner light"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div>');

		$.ajax({
			url : 'action.php',
			type : 'post',
			data : 'action=save_project&project='+$project+"&id="+$id+"&git_repo="+$git_repo+"&git_user="+$git_user+"&git_pass="+$git_pass+"&git_ignore_dir="+$git_ignore_dir+"&ftp_server="+$ftp_server+"&ftp_user="+$ftp_user+"&ftp_pass="+$ftp_pass+"&ftp_dir="+$ftp_dir,
			success : function(a){
				delete nds;
				$btn.html($btn_name);
				gAlert("Save project succesfully,.");
			}
		});

		return false;
	});

	// FTP check connection
	$("#submit_ftp_check").click(function(e){
		e.preventDefault();
		if(typeof nds != "undefined") return false;
		nds = true;
		$id = $("#project-id").val();
		$project = $("#project").val();
		$ftp_server = $("#ftp_server").val();
		$ftp_user = $("#ftp_user").val();
		$ftp_pass = $("#ftp_pass").val();
		$ftp_dir = $("#ftp_dir").val();
		$btn = $(this);
		$btn_name = $btn.html();
		$(this).html('<div class="spinner light"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div>');

		$.ajax({
			url : 'action.php',
			type : 'post',
			data : 'action=ftp_check&project='+$project+"&id="+$id+"&ftp_server="+$ftp_server+"&ftp_user="+$ftp_user+"&ftp_pass="+$ftp_pass+"&ftp_dir="+$ftp_dir,
			success : function(a){
				delete nds;
				$btn.html($btn_name);
				alert(a);
			}
		});

		return false;
	});
	
	// Graph Commit
	$("#submit_project").click(function(e){
		e.preventDefault();
		if(typeof nds != "undefined") return false;
		nds = true;
		$project = $("#project").val();
		$btn = $(this);
		$btn_name = $btn.html();
		$(this).html('<div class="spinner light"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div>');

		$.ajax({
			url : 'action.php',
			type : 'post',
			data : 'action=list_commit&project='+$project,
			success : function(a){
				delete nds;
				$btn.html($btn_name);

				if(a == "error"){
					alert("Oops, directory project have not git!");
					return false;
				}
				$("#commit_from").html(a);
				$("#commit_to").html(a);
			}
		});

		return false;
	});

	// Checkout Commit
	$("#submit_check").click(function(e){
		e.preventDefault();
		if(typeof nds != "undefined") return false;
		nds = true;
		$project = $("#project").val();
		$commit_from = $("#commit_from").val();
		$commit_to = $("#commit_to").val();
		$git_ignore_dir = $("#git_ignore_dir").val();
		$btn = $(this);
		$btn_name = $btn.html();
		$(this).html('<div class="spinner light"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div>');

		$.ajax({
			url : 'action.php',
			type : 'post',
			data : 'action=checkout&project='+$project+"&commit_from="+$commit_from+"&commit_to="+$commit_to+"&git_ignore_dir="+$git_ignore_dir,
			success : function(resp){
				delete nds;
				$btn.html($btn_name);

				try{
					var data = $.parseJSON(resp);
				}catch(e){
					alert(resp);
					return false;
				}
				var html = "";
				$.each(data, function(file, status){
					html += status+": "+file+"<br>";
				});
				$(".result_content").html(html);
			}
		});

		return false;
	});

	// Upload file with git repository
	$("#submit_transfer").click(function(e){
		e.preventDefault();
		if(typeof nds != "undefined") return false;
		nds = true;
		file_uploaded = [];
		$id = $("#project-id").val();
		$project = $("#project").val();
		$commit_from = $("#commit_from").val();
		$commit_to = $("#commit_to").val();
		$git_ignore_dir = $("#git_ignore_dir").val();
		$ftp_server = $("#ftp_server").val();
		$ftp_user = $("#ftp_user").val();
		$ftp_pass = $("#ftp_pass").val();
		$ftp_dir = $("#ftp_dir").val();
		$btn = $(this);
		$btn_name = $btn.html();
		$(this).html('<div class="spinner light"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div>');

		$.ajax({
			url : 'action.php',
			type : 'post',
			data : 'action=ftp_push&project='+$project+"&id="+$id+"&commit_from="+$commit_from+"&commit_to="+$commit_to+"&git_ignore_dir="+$git_ignore_dir+"&ftp_server="+$ftp_server+"&ftp_user="+$ftp_user+"&ftp_pass="+$ftp_pass+"&ftp_dir="+$ftp_dir,
			success : function(resp){
				delete nds;
				$btn.html($btn_name);
			}
		});

		return false;
	});

	// Git push repository
	$("#commit_subject").keyup(function(e){
		e.preventDefault();
		if(!e.shiftKey && e.which == 13){
			if(typeof nds != "undefined") return false;
			nds = true;
			$project = $("#project").val();
			$git_repo = $("#git_repo").val();
			$git_user = $("#git_user").val();
			$git_pass = $("#git_pass").val();
			$commit_subject = $(this).val();
			$("#coll_git_push").collapse('hide');
			$(this).val("");

			$.ajax({
				url : 'action.php',
				type : 'post',
				data : 'action=git_push&project='+$project+"&commit_subject="+$commit_subject+"&git_repo="+$git_repo+"&git_user="+$git_user+"&git_pass="+$git_pass,
				success : function(resp){
					delete nds;
				}
			});
			return false;
		}
	});

	/* Window keyboard event*/
	$(window).keyup(function(e){
    	e.preventDefault();
    	// console.log(e);
		// To List
		if(e.altKey && e.which == 76){
			window.location = "index.php"
		}
		// Save Project
		if(e.altKey && e.which == 83){
			$(".save_project").trigger("click");
		}
		// Graph Commit modal
		if(e.altKey && e.which == 71){
			$("#graph_modal").modal("show");
		}
		// Checkout
		if(e.altKey && e.which == 67){
			$("#submit_check").trigger("click");
		}
		// Deploy
		if(e.altKey && e.which == 79){
			$("#submit_transfer").trigger("click");
		}
		// Open modal push
		if(e.altKey && e.which == 80){
			$("#coll_git_push").collapse('toggle');
			$(".content_file").height(280);
			$("#commit_subject").focus();
		}
		// Open modal push
		if(e.altKey && e.which == 82){
			$.ajax({url:"action.php",type:'post',
				data:'action=posix_kill&pid='+$("#posix_id").val()+"&project="+$("#project").val(),
				success : function(e){
					gAlert(e);
				}
			})
		}
		// Open localhost project
		if(e.altKey && e.which == 78){
			window.open('http://localhost/'+$("#project").val()+"/"+$("#git_ignore_dir").val(),'_blank');
		}
		// Escape Event
		if(e.keyCode == 27){
			$("#coll_git_push").collapse('hide');
		}
	});

	$('#coll_git_push').on('hidden.bs.collapse', function () {
		$(".content_file").height(360);
	})
});

/* Helpers */
function gAlert($msg){
	$.gritter.add({
		title: 'Notification',
		text: $msg,
		time: 2000
	});
}