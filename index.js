$(function(){
	var index = {
		username:null,//学号
		name:null,//姓名
		type:null,//院系选修课,通选课
		input:null,//提交表单附带隐藏输入框
		loginFlag:true,//防止多次身份验证
		typeFlag:true,
		timer:null,
		count:0,

		init:function(){
			var self = this;
			self.bindEvent();
		},

		bindEvent:function(){
			var self = this;
			$("#form-login input").on("keydown",function(event){
				if(event.keyCode==13){
					$("#form-login .btn-submit").trigger("click");
				}
			})
			$("#form-login .btn-submit").on("click",function(){
				var username = $("#username").val();
				var password = $("#password").val();
				if(username==""||password==""){
					var msg = $("<p>please complete the form</p>");
					$("#info").append(msg);
					(function(msg){
						setTimeout(function(){
							msg.css({"opacity":"0","height":"0px"});
							setTimeout(function(){
								msg.remove()
							},1000);
						},1000)
					})(msg)
				}
				else{
					self.login(username, password);
				}
			});
			$("#form-login .btn-logout").on("click",function(){
				window.location.reload();
			});
			$("#form-type .nav a").on("click",function(){
				var type = $(this).attr("data-code");
				$("#info").empty();
				self.count = 0;
				self.chooseType(type);
			});
			$("#btn-grab").on("click",function(){
				var checkbox = $("#course-list input[type='checkbox']");
				var flag = false;
				var course = "";
				for(var i=0;i<checkbox.length;i++){
					if(checkbox.eq(i).attr("checked")){
						flag = true;
						course += checkbox.eq(i).attr("name")+",";
					}
				}
				if(flag){
					var tips = $("<p>start grabing course</p>");
					$("#info").append(tips);
					course = course.substr(0,course.length-1);
					self.timer = setInterval(function(){
						self.grab(self.count++,course);
					},200);
				}
				else{
					var tips = $("<p>please choose a class</p>");
					$("#info").append(tips);
					setTimeout(function(){
						tips.css({"opacity":"0","height":"0px"});
						setTimeout(function(){
							tips.remove();
						},1000)
					},1000)
				}
			});
			$("#btn-stop").on("click",function(){
				window.clearInterval(self.timer);
			})
		},

		login:function(username, password){
			var self = this;
			if(self.loginFlag){
				self.loginFlag = false;
				var tips = $("<p>sending ajax request, please wait...</p>");
				$("#info").append(tips);
				var callback = function(){
					tips.css({"opacity":"0","height":"0px"});
					setTimeout(function(){
						tips.remove();
					},1000)
					self.loginFlag = true;
					$("#btn-submit").attr({"disabled":""});
				}
				$.ajax({
					url:'grab.php?action=login',
					type:'post',
					dataType:'json',
					data:{
						'username':username,
						'password':password
					},
					success:function(data){
						if(data!="0"){
							self.name = data.name;
							self.username = data.username;
							var msg = $("<p>login successful</p>");
							$("#username,#password").attr({"disabled":"disabled"});
							$("#form-login .btn-submit").off("click").addClass("btn-on");
							$("#form-type").css({"opacity":"1","height":"auto"});
							$("#form-login .btn-logout").show();
						}
						else{
							var msg = $("<p>login failed, please try again</p>");
							(function(msg){
								setTimeout(function(){
									msg.css({"opacity":"0","height":"0px"});
									setTimeout(function(){
										msg.remove()
									},1000);
								},1000)
							})(msg)
						}
						$("#info").append(msg);
						callback();
					},
					error:function(XMLHttpRequest, textStatus, errorThrown){
						console.log(XMLHttpRequest, textStatus, errorThrown);
						callback();
					}
				})
			}
		},

		chooseType:function(type){
			var self = this;
			self.type = type;
			if(self.typeFlag){
				var tips = $("<p>querying course, please wait...</p>");
				$("#info").append(tips);
				self.typeFlag = false;
				var callback = function(){
					tips.css({"opacity":"0","height":"0px"});
					setTimeout(function(){
						tips.remove();
					},1000)
					self.typeFlag = true;
				}
				$.ajax({
					url:'grab.php?action=choose',
					type:'post',
					dataType:'json',
					data:{
						'type':type,
						'username':self.username,
						'name':self.name
					},
					success:function(data){
						callback();
						if(data!="0"){
							$("#course-list").html(data.list);
							$("#course-my").html(data.my);
							$("#btn-grab,#btn-stop").show();
							self.input = data.input;
						}
					},
					error:function(XMLHttpRequest, textStatus, errorThrown){
						callback();
						console.log(XMLHttpRequest, textStatus, errorThrown);
					}
				});
			}
		},

		grab:function(id, course){
			var self = this;
			var tips = $("<p id='grab-"+id+"'>grabing("+id+")...</p>");
			$("#info")[0].scrollTop = $("#info")[0].scrollHeight;
			$("#info").append(tips);
			$.ajax({
				url:'grab.php?action=grab',
				type:'post',
				dataType:'json',
				data:{
					'type':self.type,
					'username':self.username,
					'name':self.name,
					'input':self.input,
					'course':course
				},
				success:function(data){
					$("#course-my").html(data.my);
					$("#grab-"+id).html("grabing("+id+") success");
				},
				error:function(XMLHttpRequest, textStatus, errorThrown){
					$("#grab-"+id).html("grabing("+id+") error");
					console.log(XMLHttpRequest, textStatus, errorThrown);
				}
			})
		}
	}
	index.init();
})
