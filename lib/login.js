
/*
 * reference http://docs.couchdb.org/en/latest/intro/security.html
 */

$.widget("rhok.login",{
	_create:function(){
		var self = this;
		$("input.submit-login").on({"click":function(ev){
			ev.preventDefault();
			$.post("http://localhost:5984/_session",self.element.serialize()).success(
					function(data){
						console.log(data);
					});
		}})
	}
});
$.widget("rhok.slopes",$.ui.autocomplete,{
	mapAutocomplete:function(request,response){
		$.getJSON("http://localhost:5984/slopes/_design/slopes/_view/slopes",{
			startkey:"\""+request.term+"\"",
			endkey:"\""+request.term+"\ufff0\""}).done(
					function(data){
						if(data && data.rows){
							var names = $.map(data.rows,
									function(elem,index){
									return {"label":elem.key,"value":elem.id,"doc":elem.value};
								});
							response(names);
						}
					});
	},
	processSelect : function(event,ui){
		if(ui.item){
			$(event.target).val(ui.item.value);
			$(event.target).data("document",ui.item.doc);
			$("input.docId",event.target.form).val(ui.item.value);
			$("input.revision",event.target.form).val(ui.item.doc._rev);
			$("input.latitude",event.target.form).val(ui.item.doc.latitude);
			$("input.longitude",event.target.form).val(ui.item.doc.longitude);
		}
	}
	,
	_create:function(){
		this.options.source=this.mapAutocomplete;
		//this.options.response = this.processResponse;
		this.options.select = this.processSelect;
		
		
		this._super();
	}
});
$.widget("rhok.formInfo",{
	_create:function(){
		var self = this;
		$("input.slopes",this.element).slopes();
	}
});