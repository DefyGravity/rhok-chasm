/**
 * 
 */
function load(options){
	options = options ||{};
	$.extend(options,{
		
	});
	
}
infoFileList.bind("refresh", function(event){
    	var select = $(this).clone();
    	select.empty();
		var json =new Array();
		
		json.unshift({name:""});
		$.tmpl("infoDataOption",json).appendTo(select);
		if(json.length > 1 && select.is(":disabled")){
			select.removeAttr("disabled");	
		}
		$(this).replaceWith(select);
    });