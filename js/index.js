$(function(){
	var index = {
		username:null,//学号
		name:null,//姓名
		type:null,//院系选修课,通选课
		input:null,//提交表单附带隐藏输入框
		typeFlag:true,
		timer:null,
		count:0,
		speed:200,//抢课速度，

		init:function(){
			var self = this;
			self.username=$("#user").attr('data-uid');
			self.bindEvent();
		},

		bindEvent:function(){
			var self = this;
			$("#btn-grab").on("click",function(){
				var checkbox = $("#course-list input[type='checkbox']");
				var flag = false;
				var course = "";
				for(var i=0;i<checkbox.length;i++){
					if(checkbox.eq(i).attr("checked")){
						flag = true;
						course += checkbox.eq(i).attr("value")+",";
					}
				}
				if(flag){
					var tips = $("<p>start grabing course</p>");
					$("#info").append(tips);
					course = course.substr(0,course.length-1);
					self.timer = setInterval(function(){
						self.grab(self.count++,course);
					},self.speed);
				}
				else{
					var tips = $("<p>please choose a class</p>");
					$("#info").append(tips);
					setTimeout(function(){
						tips.css({"opacity":"0","height":"0px"});
						setTimeout(function(){
							tips.remove();
						},self.speed)
					},self.speed)
				}
			});
			$("#btn-stop").on("click",function(){
				window.clearInterval(self.timer);
			})
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
					'username':self.username,
					'course':course
				},
				success:function(data){
					var len=data.length;
					for(var i=0;i<len;i++){
						if(data[i].success){
							$("#grab-"+id).append("<p>成功</p>");
							$.ajax({
								"url":'grab.php?action=myCourse',
								dataType:'string',
								type:'post',
								data:{
									'username':self.username
								},
								success: function (data) {
									$("#course-my").html(data);
								}
							});
						}
						else{
							$("#grab-"+id).append("<p>"+data[i].message+"</p>");
						}
					}
				},
				error:function(XMLHttpRequest, textStatus, errorThrown){
					$("#grab-"+id).html("grabing("+id+") submit error");
					console.log(XMLHttpRequest, textStatus, errorThrown);
				}
			})
		},
		del: function (id) {
			$.ajax({
				url:'grab.php?del',
				type:'post',
				dataType:'json',
				data:{
					'username':self.username,
					'course':id
				},
				success: function (data) {
					if(data.success){
						alert("删除成功");
						$.ajax({
							"url":'grab.php?action=myCourse',
							dataType:'string',
							type:'post',
							data:{
								'username':self.username
							},
							success: function (data) {
								$("#course-my").html(data);
							}
						});
					}
					else{
						alert("删除失败");
					}
				}
			});
		}
	}
	index.init();
});

function xstkOper(id){
	if(confirm("确认退选？")){
		$.ajax({
			url:'grab.php?action=del',
			type:'post',
			dataType:'json',
			data:{
				'username':$("#user").attr('data-uid'),
				'course':id
			},
			success: function (data) {
				if(data.success){
					alert("删除成功");
					$.ajax({
						"url":'grab.php?action=myCourse',
						type:'post',
						dataType:'string',
						data:{
							'username':$("#user").attr('data-uid')
						},
						success: function (data) {
							$("#course-my").html(data);
						}
					});
				}
				else{
					alert("删除失败");
				}
			},
			error:function(XMLHttpRequest, textStatus, errorThrown){
				console.log(XMLHttpRequest, textStatus, errorThrown);
			}
		});
	}
}
