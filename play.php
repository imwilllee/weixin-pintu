<?php
	if (empty($_GET['id']) || !preg_match('/^-?\d+$/', $_GET['id'])) {
		header('location: index.html');
		exit();
	}
	require 'bootstrap.php';
	$result = dibi::query('SELECT title,original_file_path,small_file_path FROM items where [id] = %i %lmt', $_GET['id'], 1);
	$row = $result->fetch();
	if (empty($row)) {
		header('location: index.html');
		exit();
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, minimal-ui">
		<meta content="yes" name="apple-mobile-web-app-capable"/>
		<meta content="yes" name="apple-touch-fullscreen"/>
		<meta content="telephone=no" name="format-detection"/>
		<meta content="black" name="apple-mobile-web-app-status-bar-style">
		<title>私属小游戏</title>
		<link rel="stylesheet" type="text/css" href="assets/css/index.css">
		<style>
			.sbg {
				display: none;
				position: fixed;
				top: 0;
				left: 0;
				width: 100%;
				height: 100%;
				text-align: center;
				color: #fff;
				font-size: 22px;
				line-height: 1.7em;
				background: rgba(0,0,0,0.85);
				z-index: 200;
			}
			.sbg .arron {
				position: absolute;
				top: 8px;
				right: 8px;
				width: 100px;
				height: 100px;
				background: url(assets/images/arron.gif) no-repeat;
				background-size: 100px 100px;
			}
			.sbg p {
				padding-top: 78px;
			}
			.moregame, .replay{
				width: 60px;
				height: 30px;
				background-color: #35ff5e;
				text-shadow: 1px;
			}
			.makegame{
				width: 198px;
				height: 42px;
				margin: 10px auto;
				background-image:url(assets/images/make.png);
				text-align: center;
				overflow: hidden;
			}
		</style>
	</head>
	<body>
		<!--页面集合-->
		<div id="pageWrapper">
			<div id="pages">
				<div id="page_default" class="pagemodel">
					<div class="initloading" ><span class="normal-loading"></span></div>
				</div>
			</div>
		</div>
		<script>
			var $host = location.protocol + '//' + location.host;
			var people_img = $host + '<?php echo $row['small_file_path']; ?>';
		</script>
		<script type="text/template" style="display:none;" id="pageTemplate">
			<div id="<%= id%>" class="pagemodel">
				<div class="initloading" >
					<span class="normal-loading"></span>
				</div>
			</div>
		</script>
		<script type="text/template" style="display:none;" id="jigsawTemplate">
			<div class="virturl-body"></div>
			<h1 id="game-title" class="game-title"><?php echo htmlspecialchars($row['title']); ?></h1>
			<div class="drag-content" id="drag-content">
				<div class="play-container">
					<div class="drag-box"></div>
					<div class="masker">
						<div class="load">
							<div class="first-layer"></div>
							<div class="second-layer"></div>
							<div class="third-layer"></div>
							<div class="count-down" >
								<div class="play-button play-button-ready playbtn" ></div>
								<ul>
									<li>3</li>
									<li>2</li>
									<li>1</li>
								</ul>
							</div>
						</div>
					</div>
					<span class="done">done</span>
					<div class="menu-box" id="menuBox">
						<div class="overcast" id="overcast">
							<div class="menu">
								<div class="menu-body" id="menuBody">
									<div class="h1" id="h1">哇，你好厉害啊</div>
									<div class="h2" id="h2">你只用了N步就完成了！</div>
									<a href="javascript:" id="reload" class="overBtn madeGame">再来一次</a>
									<a href="javascript:" id="shareBtn" class="overBtn share">炫耀一下</a>
									<a href="javascript:" id="madeBtn" class="overBtn madeGame">我也要定制</a>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="timer">
					<div class="timer-con">
						<div class="step-con">
							<img class="step-con" src="assets/images/step.png" width="20" height="20">
						</div>
						<span id="counter" class="t counter">0&nbsp;步</span>
					</div>
					<div class="makegame" id="makegame"></div>
				</div>
			</div>
		</script>

		<div id="sbg" class="sbg">
			<div class="arron"></div>
			<p id="msg">请点击右上角按钮<br />再点击【分享到朋友圈】<br />  喊他们来挑战吧！</p>
		</div>
		<div class="ad"></div>
		<script type="text/template" style="display:none;" id="jigsawLayoutTemplate">
			<% for(var i = 0 ; i < list.length;i++){%>
				<div class="item" sort="<%=list[i].sort%>" dragitem='1' style="width:<%=list[i].w%>px;height:<%=list[i].h%>px;background:url(<%=img%>) no-repeat;background-position:<%=list[i].x%>px <%=list[i].y%>px;background-size:<%=width%>px <%=height%>px;"></div>
			<%}%>
		</script>
		<script src="assets/js/jquery.min.js"></script>
		<script src="assets/js/common.js"></script>
		<script src="assets/js/index.min.js"></script>
		<script src="assets/js/wxtools.js"></script>

		<script type="text/javascript">
			var gameTitle = "<?php echo htmlspecialchars($row['title']); ?>";
			var dataForWeixin = {
				appId : "",
				MsgImg : people_img,
				TLImg:people_img,
				shareurl : 'http://<?php echo $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>',
				title : '我定制了“' + gameTitle + '”看你用几步能得到完整的TA',
				desc : '定制好友拼图\n-来自《私属小游戏》',
				before_share : function() {
				},
				callback : function() {
					$('#sbg').hide();
				}
			};

			$('#shareBtn').on('click', function(){
				setTimeout(
					function(){
						$('#sbg').show();
					},
					500
				);
			});

			$('#sbg').on('click', function(){
				$('#sbg').hide();
			});

			$('#madeBtn').on('click', function(){
				alert('关注后，您可以定制只属于你自己的好友拼图，点击确认立即关注');
				location.href='http://mp.weixin.qq.com/s?__biz=MzA4ODUwMzYzMw==&mid=212923715&idx=1&sn=652d50922e2d630366e46a8cf3c9b72a#rd';
			});

			function gameover(){
				var over1 = '', over2 = '';
				if(step < 10){
					over1 = '哇，骚年，你好厉害啊';
					over2 = '你只用了' + step + '步就完成了';
				} else{
					over1 = '哎，你弱爆了';
					over2 = '你用了' + step + '步才完成拼图';
				}
				$('#h1').html(over1);
				$('#h2').html(over2);
				$('#menuBox').show();
			}

			$('#makegame').on('click', function(){
				location.href="index.html";
			});

			$('#reload').on('click', function(){
				location.reload();
			});
			$(document).ready(function(){
				$.getScript('assets/js/ad.js');
			});
		</script>
	</body>
</html>
