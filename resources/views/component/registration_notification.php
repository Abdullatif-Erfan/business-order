<script>
	var placementFrom = 'top';//top,bottom
	var placementAlign = 'center'; // right,center
	var state = '<?php echo $type; ?>'; //Default,Primary,Secondary,Info,Success,Warning,Danger
	var style = 'withicon'; //plain, withicon
	var content = {};

	content.message = '<span style="font-size:16px;"><?php echo $msg; ?></span>';            
	content.title = '&nbsp;&nbsp;&nbsp;'+' <span style="font-size:16px;"> پیام </span> ';
	if (style == "withicon") {
		content.icon = 'fa fa-bell';
	} else {
		content.icon = 'none';
	}
	content.url = '#';
	content.target = '_blank';

	$.notify(content,{
		type: state,
		placement: {
			from: placementFrom,
			align: placementAlign
		},
		time: 5000,
	});
</script>