$(function(){
	// Socket IO
	var app = io.connect($conf['websocket']);
	function app_on(channel, event, callback){
		app.on(channel, function (data){
	    	if(data['event'] == event){
	    		return callback(data['data']);
	    	}
	    });
	}
	file_uploaded = [];
	app_on('gitFtp', 'ftp_push', function(data){
		file_uploaded.push(data);

		$(".result").html(data.percentage);
		var html = "";
		$.each(file_uploaded.reverse(), function(i, row){
			html += row.file+"<br>";
		});
		$(".result_content").html(html);
	});

	// Update git-ftp
	$("#update_git_ftp").click(function(e){
		e.preventDefault();
		if(typeof nds != "undefined") return false;
		nds = true;

		$btn = $(this);
		$(this).html('<div class="spinner light"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div>');

		$.ajax({
			url : 'action.php?update=true',
			success : function(a){
				delete nds;
				$btn.html("Update GIT-FTP");
				alert(a);
			}
		});

		return false;
	});

	// Save Project
	$("#save_project").click(function(e){
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
		$(this).html('<div class="spinner light"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div>');

		$.ajax({
			url : 'action.php',
			type : 'post',
			data : 'action=save_project&project='+$project+"&id="+$id+"&ftp_server="+$ftp_server+"&ftp_user="+$ftp_user+"&ftp_pass="+$ftp_pass+"&ftp_dir="+$ftp_dir,
			success : function(a){
				delete nds;
				$btn.html("Save");
				alert(a);
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
		$(this).html('<div class="spinner light"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div>');

		$.ajax({
			url : 'action.php',
			type : 'post',
			data : 'action=ftp_check&project='+$project+"&id="+$id+"&ftp_server="+$ftp_server+"&ftp_user="+$ftp_user+"&ftp_pass="+$ftp_pass+"&ftp_dir="+$ftp_dir,
			success : function(a){
				delete nds;
				$btn.html("FTP Check");
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
		$(this).html('<div class="spinner light"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div>');

		$.ajax({
			url : 'action.php',
			type : 'post',
			data : 'action=list_commit&project='+$project,
			success : function(a){
				delete nds;
				$btn.html("Graph Commit");

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
		$btn = $(this);
		$(this).html('<div class="spinner light"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div>');

		$.ajax({
			url : 'action.php',
			type : 'post',
			data : 'action=checkout&project='+$project+"&commit_from="+$commit_from+"&commit_to="+$commit_to,
			success : function(resp){
				delete nds;
				$btn.html("Checkout");

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
		$ftp_server = $("#ftp_server").val();
		$ftp_user = $("#ftp_user").val();
		$ftp_pass = $("#ftp_pass").val();
		$ftp_dir = $("#ftp_dir").val();
		$btn = $(this);
		$(this).html('<div class="spinner light"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div>');

		$.ajax({
			url : 'action.php',
			type : 'post',
			data : 'action=ftp_push&project='+$project+"&id="+$id+"&commit_from="+$commit_from+"&commit_to="+$commit_to+"&ftp_server="+$ftp_server+"&ftp_user="+$ftp_user+"&ftp_pass="+$ftp_pass+"&ftp_dir="+$ftp_dir,

			success : function(resp){
				delete nds;
				$btn.html("Deploy");
			}
		});

		return false;
	});
});