(function($) {
$(document).ready(function() {   
   var doList = function() {
     var currentModel = $('#ModelClassSelector').children('select');
     var currentModelName = $('option:selected', currentModel).val();
     var strFormname = "#Form_SearchForm" + currentModelName.replace('Form','');
     $(strFormname).submit();
     return false;
   }
   
   $('#ModelClassSelector').live("change",doList);
   $('#list_view').live("click",doList);
   if($('#list_view_loading').length) {
     doList();
   }
   $('button[name=action_clearsearch]').click(doList);

   
	$('#right input:submit').unbind('click').live('click', function(){
		var form = $('#right form');
		var formAction = form.attr('action') + '?' + $(this).fieldSerialize();
		if(typeof tinyMCE != 'undefined') tinyMCE.triggerSave();
		$.ajax({
			url : formAction,
			data : form.formToArray(),
			dataType : "json",
			type : form.attr("method") || "POST",
			success : function(json) {
				tinymce_removeAll();
				
				$('#right #ModelAdminPanel').html(json.html);
				if($('#right #ModelAdminPanel form').hasClass('validationerror')) {
					statusMessage(ss.i18n._t('ModelAdmin.VALIDATIONERROR', 'Validation Error'), 'bad');
				} else {
					statusMessage(json.message, 'good');
				}
	
				Behaviour.apply();
				if(window.onresize) window.onresize();
			}
		});
		return true;
	});
	
});
})(jQuery);
