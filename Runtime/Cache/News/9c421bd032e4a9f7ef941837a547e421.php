<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<title>新闻详情</title>
<link href="/Application/News/Static/css/news.css" rel="stylesheet">
<script src="/Public/js/jquery-2.0.3.min.js"></script>
</head>
<body>
    <div class="p20 mt20 bg-white news">
			<h3><?php echo ($info["title"]); ?></h3>
			<div class="info clearfix gray">
				<div class="l">
					<span class="sour">
						来源：<?php echo ($info["source"]); ?>
					</span>
					<span id="">
						<?php echo ($info["author"]); ?>
					</span>
					<span id="">
						<?php echo (date("Y-m-d",$info["create_time"])); ?>
					</span>
				</div>

				<div class="r">
					<span id="">
						<img src="/Application/News/Static/images/msg.png" />
						<span id="comment_num"><?php echo ($comment["total"]); ?></span>
					</span>
					<span id="">
						<img src="/Application/News/Static/images/star.png"/>
						<span id="collect_num"><?php echo ($collect); ?></span>
					</span>
				</div>
			</div>

			<div class="news-detail gray1 mt20">
				<?php echo ($info["detail"]["content"]); ?>
			</div>
		</div>

		<div class="mt20 bg-white comment">
			<div class="ptlr">
				<h3 class="gray-border-bottom"><span>评论</span></h3>
			</div>
			
			
            <div id="comment">
        	<?php if(is_array($comment["data"])): $i = 0; $__LIST__ = $comment["data"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="gray-border-bottom comment-ceil">
				<div class="pblr clearfix">
					<div class="l">
						<img src="<?php echo ($vo["cover"]); ?>"/>
					</div>
					<div class="r">
						<div class="user-name gray1"><?php if(empty($vo["real_name"])): echo ($vo["user_name"]); else: echo ($vo["real_name"]); endif; ?></div>
						<div class="time gray"><?php echo (date("Y-m-d H:i:s",$vo["create_time"])); ?></div>
						<div class="content"><?php echo ($vo["content"]); ?></div>
					</div>
				</div>
			</div><?php endforeach; endif; else: echo "" ;endif; ?>
            </div>
            
            
            
			
			
		</div>
        <br/><br/><br/>		
       
		<div class="fixed-bottom clearfix">	
			<div class="plr20">
				<div class="l">
					<input type="text" id="text" />
					<img  src="/Application/News/Static/images/fly.png" />
				</div>
				<div class="r" id="collect">
					<img  src="/Application/News/Static/images/<?php if(($is_collect) == "1"): ?>star1.png<?php else: ?>star.png<?php endif; ?>"/>				
				</div>
			</div>
			
		</div>
        
        <script type="text/javascript" src="/Application/News/Static/js/layer/layer.js"></script>
        <script type="text/javascript">
			var uid = '<?php echo ($uid); ?>';
			var user_name = '<?php echo ($user["user_name"]); ?>';
			var cover = '<?php echo ($user["cover"]); ?>';
			var news_id = '<?php echo ($info["id"]); ?>';
			var is_collect = <?php echo ($is_collect); ?>;
			var texts = '';
			var time  = 1.5;
			var styles= 'background-color:#000; color:#fff; border:none;opacity:0.5;';
			$(".fixed-bottom .l img").click(function() {
				if(uid == ""){
					layer.open({
						content: '请您先登录再来评论吧！',
						style: styles,
						shade: false,
						time: time
					});
					return false;
				}
				texts = $(".fixed-bottom input").val();
				if (texts == "") {
					layer.open({
						content: '请赏赐几句话吧！',
						style: styles,
						shade: false,
						time: time
					});
					return false;
				}
				
				$.getJSON("<?php echo U('submitComment');?>",{content:texts,uid:uid,news_id:news_id},function(data){
					msg = '评论失败！';
					if(data.rs == 1){
						
						msg = '评论成功！';
						$(".fixed-bottom input").val("");
						html = '<div class="gray-border-bottom comment-ceil"><div class="pblr clearfix"><div class="l"><img src="'+cover+'"/></div><div class="r"><div class="user-name gray1">'+user_name+'</div><div class="time gray">'+getNowFormatDate()+'</div><div class="content">'+texts+'</div></div></div></div> ';
						$("#comment").append(html);
						
						$("#comment_num").text((parseInt($("#comment_num").text())+1));
					}
					layer.open({
						content: msg,
						style: styles,
						shade: false,
						time: time
					});	
					
					var h = $(document).height()-$(window).height();
  					$(document).scrollTop(h);
				})
				
			})
			
			
			//关心 收藏
			$("#collect").click(function() {
				if(uid == ""){
					layer.open({
						content: '请您先登录！',
						style: styles,
						shade: false,
						time: time
					});
					return false;
				}
				
				msg = '关注失败！';
				var img = $(this).find('img');
				if(is_collect == 1){
					$.getJSON("<?php echo U('cancelCollect');?>",{coll_id:news_id,uid:uid},function(data){
						msg = '取消关注失败！';
						if(data.rs == 1){
							msg = '取消关注成功！';												
							$("#collect_num").text((parseInt($("#collect_num").text())-1));
							is_collect = 0;
						}						
						img.attr('src','/Application/News/Static/images/star.png');	
						layer.open({
							content: msg,
							style: styles,
							shade: false,
							time: time
						});	
					})
				}else{					
					$.getJSON("<?php echo U('submitCollect');?>",{coll_id:news_id,uid:uid},function(data){
						msg = '关注失败！';
						if(data.rs == 1){
							msg = '关注成功！';													
							$("#collect_num").text((parseInt($("#collect_num").text())+1));
							is_collect = 1;
						}						
						img.attr('src','/Application/News/Static/images/star1.png');
						layer.open({
							content: msg,
							style: styles,
							shade: false,
							time: time
						});		
					})
				}
				
				
			})
			
			
			function getNowFormatDate() {
				var date = new Date();
				var seperator1 = "-";
				var seperator2 = ":";
				var month = date.getMonth() + 1;
				var strDate = date.getDate();
				if (month >= 1 && month <= 9) {
					month = "0" + month;
				}
				if (strDate >= 0 && strDate <= 9) {
					strDate = "0" + strDate;
				}
				var currentdate = date.getFullYear() + seperator1 + month + seperator1 + strDate
						+ " " + date.getHours() + seperator2 + date.getMinutes()
						+ seperator2 + date.getSeconds();
				return currentdate;
			}
		</script>         
</body>
</html>