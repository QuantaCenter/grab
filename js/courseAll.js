$(function(){
	var courseAll = {
		username:null,//学号
		name:null,//姓名
		typeFlag:true,
		timer:null,
		count:0,
		speed:200,//抢课速度
		course:[],

		init:function(){
			var self = this;
			self.username=$("#user").attr('data-uid');
			self.bindEvent();
		},

		bindEvent:function(){
			var self = this;
			$("#query").on("click", function () {
				$("#bg").show();
				var q={
					szjylb:$("#szjylb").val(),
					szkclb:$("#szkclb").val(),
					kcxx:$("#kcxx").val(),
					skls:$("#skls").val(),
					skxq:$("#skxq").val(),
					skjc:$("#skjc").val(),
					sfym:$("#sfym").is(":checked")?'true':'false',
					sfct:$("#sfct").is(":checked")?'true':'false',
					username:self.username
				};
				self.getCourse(q);
			});
			$(document).on("click","#btn-into",function(){
				var checkbox = $("#course-list input[type='checkbox']");
				for(var i=0;i<checkbox.length;i++){
					if(checkbox.eq(i).is(":checked")){
						var c={
							id:checkbox.eq(i).attr("value"),
							name:checkbox.eq(i).attr("data-name")
						};
						self.into(c);
					}
				}
			});
			$("#btn-grab").on("click", function () {
				var flag = false;
				var course = "";
				for(var i=0;i<self.course.length;i++){
					flag = true;
					course += self.course[i].id+",";
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
			});
			$(document).on("click",".delcon", function () {
				var id=$(this).attr("data-id");
				var li=$(this).parent();
				$.each(self.course, function (i, v) {
					if(v.id==id){
						self.course.splice(i,1);
						li.remove();
						return false;
					}
				});
			});
		},

		getCourse:function(query){
			$.ajax({
				url:"grab.php?action=showTX",
				//url:"json.json",
				type:"get",
				dataType:"json",
				data:query,
				success: function (data) {
					if(data){
						var dt=data.aaData;
						var len=dt.length;
						var cl=$("#course-list");
						var trs='<tbody><tr><th colspan="8" class="text-center">通选课</th></tr>' +
							'<tr align="center">' +
							'<td></td>' +
							'<td>课程名称</td>' +
							'<td>老师</td>' +
							'<td>时间</td>' +
							'<td>地点</td>' +
							'<td>学分</td>' +
							'<td>人数</td>' +
							'<td>余量</td>' +
							'</tr>';
						for(var i=0;i<len;i++){
							trs+="<tr>"
							+"<td><input type='checkbox' value='"+ dt[i].jx0404id +"' data-name='"+i+dt[i].kcmc+"'> "+i+" </td>"
							+"<td>"+ dt[i].kcmc + "</td>"
							+"<td>"+ dt[i].skls + "</td>"
							+"<td>"+ dt[i].sksj + "</td>"
							+"<td>"+ dt[i].skdd + "</td>"
							+"<td>"+ dt[i].xf + "</td>"
							+"<td>"+ dt[i].pkrs + "</td>"
							+"<td>"+ dt[i].syrs + "</td>"
							+"</tr>";
						}
						trs+='<tr align="center">' +
							'<td colspan="8">' +
							'<a class=" btn btn-sm btn-default" id="btn-into">添加到抢课列表</a>' +
							'</td>' +
							'</tr></tbody>';
						cl.html(trs);
						console.log(data);
					}
				},
				error:function(XMLHttpRequest, textStatus, errorThrown){
					console.log(XMLHttpRequest, textStatus, errorThrown);
				},
				complete: function () {
					$("#bg").hide();
				}
			});
		},
		grab:function(id, course){
			var self = this;
			var tips = $("<p id='grab-"+id+"'>grabing("+id+")...</p>");
			$("#info")[0].scrollTop = $("#info")[0].scrollHeight;
			$("#info").append(tips);
			$.ajax({
				url:'grab.php?action=grabTX',
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
								type:'post',
								dataType:"json",
								data:{
									'username':self.username
								},
								success: function (data) {
									$("#course-my").html(data.course);
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
		//del: function (id) {
		//	$.ajax({
		//		url:'grab.php?del',
		//		type:'post',
		//		dataType:'json',
		//		data:{
		//			'username':self.username,
		//			'course':id
		//		},
		//		success: function (data) {
		//			if(data.success){
		//				alert("删除成功");
		//				$.ajax({
		//					"url":'grab.php?action=myCourse',
		//					dataType:'string',
		//					type:'post',
		//					data:{
		//						'username':self.username
		//					},
		//					success: function (data) {
		//						$("#course-my").html(data);
		//					}
		//				});
		//			}
		//			else{
		//				alert("删除失败");
		//			}
		//		},
		//		error:function(XMLHttpRequest, textStatus, errorThrown){
		//			console.log(XMLHttpRequest, textStatus, errorThrown);
		//		}
		//	});
		//},
		into: function (obj) {
			var self=this;
			var flag=true;
			$.each(self.course, function (i, v) {
				if(v.id==obj.id){
					flag=false;
				}
			});
			if(flag){
				self.course.push(obj);
				var li='<li class="list-group-item">' +
					obj.name +
					' <span class="glyphicon glyphicon-remove right delcon" data-id="'+obj.id+'"></span>' +
					'</li>';
				$(".list-group").append(li);
			}
			console.log(self.course);
		},
	};
	courseAll.init();
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
					$.ajax({
						url:'grab.php?action=myCourse',
						type:'post',
						dataType:"json",
						data:{
							'username':$("#user").attr('data-uid')
						},
						success: function (data) {
							console.log(data);
							$("#course-my").html(data.course);
						},
						error:function(XMLHttpRequest, textStatus, errorThrown){
							console.log(XMLHttpRequest, textStatus, errorThrown);
						}
					});
					alert("删除成功");
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
