// JavaScript Document
jQuery.ajax({
	type: 'GET',
	url: '<?php echo TEMPLATE_URL; ?>./bangumiAPI.php',
	success: function(res) {
		$('#bangumiBody').empty().append(res);

	},
	error:function(){
		$('#bangumiBody').empty().text('加载失败');
	}
});