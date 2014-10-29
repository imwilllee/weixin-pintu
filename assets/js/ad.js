$(document).ready(function(){
	var images = [
		{title: '广告一', image: 'assets/images/ad1.jpg'},
		{title: '广告二', image: 'assets/images/ad2.jpg'}
	];

	var html = '';
	$.each(images, function(i, v){
		if (html == '') {
			html += '<div class="slide-item slide-active">';
		} else {
			html += '<div class="slide-item">';
		}
		html += '<img src="' + v.image +'" style="width:100%;height:60px;"> ';
		html += '</div>';
	});
	var slide = function () {
		$('.slide-item').toggleClass('slide-active');
		setTimeout(slide, 3000);
	};
	$('.ad').html(html);
	slide();

});
