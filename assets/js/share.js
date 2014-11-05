$(document).ready(function () {
	$.getScript('assets/js/wechat.js', function(){
		var data = {
			'app': '',
			'img': 'http://182.92.192.169/logo.jpg',
			'link': 'http://182.92.192.169/index.html',
			'desc': '嗨。我在玩定制拼图游戏，小伙伴们，快来加入吧！',
			'title': '我在玩定制拼图游戏，快来加入吧！'
		};
		var callback = function() {
		}
		wechat('timeline', data, callback);
		wechat('friend', data, callback);
	});
});
