$(function(){
	//matches .htaccess in %apacheDir%/db/.htaccess
	//RewriteRule ^(.*)$ http://localhost:5984/$1 [P]
	$.couch.urlPrefix= 'db'; 	
	$("#page").chasm();

	var infoFileList =$("#infoData\\:fileList");
	var loginLogout = $("#infoData\\:loginLogout");
	loginLogout.toggle(function(event){
		login({
			buttonSelector:"#infoData\\:loginLogout",
			userSelector:"#infoData\\:user",
			passwordSelector:"#infoData\\:password",
			containerSelector:"fieldset",
			containerEffect:"highlight"
		});
		$("#infoData\\:fileList").trigger("refresh");
		},function(event){
		logout({
			buttonSelector:"#infoData\\:loginLogout",
			userSelector:"#infoData\\:user",
			passwordSelector:"#infoData\\:password",
			containerSelector:"fieldset",
			containerEffect:"highlight"
		});
	});
	
	infoFileList.bind("refresh", function(event, data){
		var dfd = $.Deferred();
		var select = $(this);
		$(this).empty();
		/*$.tmpl("infoDataOption", {form:{}}).appendTo(select);
		$.couch.db("chasmforms").allDocs({
			success:function(data){
				for(var i=0; i < data.rows.length; i++){
					$.couch.db("chasmforms").openDoc( data.rows[i].id, {attachPrevRev:data.rows[i].value.rev},
							{success:function(data){
								if(data.name && data.name!==''){
									$.tmpl("infoDataOption", data).appendTo(select);
									if(select.prop("disabled")){
										select.prop("disabled", false);
									}
								}
							}
					});
				}
			}
		});*/
		dfd.resolve();
		return dfd.promise();
    });
    GRAPH.ui = JXG.JSXGraph.initBoard('box', {boundingbox: [-5,100,150,-5], 
    	showNavigation: 1, snapToGrid: true, snapSizeX: 2, snapSizeY: 2, 
    	originX: 0, originY: 500, unitX: 150, unitY: 100, axis:true 
    	// , grid:true /* NEED TO DEBUG JSX GRAPH - having a grid breaks
		// function */
    	});
    CHASM.addListener( GRAPH.handleUpdate );
	

	infoFileList.trigger("refresh");
	var nameSelector = "#infoData\\:name";
	var latSelector ="#infoData\\:latitude";
	var longSelector="#infoData\\:longitude";
	var formSelector = "#form";
	//some info fields
	var nameFieldRegEx=/^(\w+)\[(\w+)\]$/;
	//some water fields
	var waterRegEx=/^water(\w+)/;
	//some soilStrata fields
	var nameIdFieldIdRegEx=/^(\w+)\[(\d+)\]\[(\w+)\]\[(\d+)\]$/;
	// profile, and most other fields.
	var nameRowFieldRegEx=/^(\w+)\[(\d+)\]\[(\w+)\]$/;
	
	$("#form").bind("build", function(event,data){
		var json = JSON.parse(localStorage.chasm)[data.selectedIndex];
		if(json && json.form){
			
			var nameValues = {};
			var profileColumnOrder ={size:3, order:{height:0, length:1,angle:2}};
			for(var i =0 ;i < json.form.length; i++){
				if(nameIdRegEx.test(json.form[i].name)){
					var matches = json.form[i].name.match(nameIdRegEx);
					var tab = matches[1];
					var row = matches[2];
					var column = profileColumnOrder.order[matches[3]];
					if(!nameValues.hasOwnProperty(tab)){
						nameValues[tab]=new Array();
					}
					for(var j = nameValues[tab].length; row >= nameValues[tab].length; j++){
						nameValues[tab].push({ID: j, columns : new Array(profileColumnOrder.size)});
					}
					nameValues[tab][row].ID = row; //extra, but to be clear
					nameValues[tab][row].columns[column] ={ID:row, name:matches[3], value:json.form[i].value}
				}
			}
			if(nameValues.profile ){
				var profileHeader =$("#profile-header");
				var profileFooter = $("#profile-footer");
				var profileBody = $("#profile-data");
				profileHeader.find("#profileInitialCliff").remove();
				$.tmpl("profileHeaderFooterTr",{row:nameValues.profile.shift(), prefix:'profileInitialCliff',"class":'computed'}).appendTo(profileHeader);
				profileFooter.empty();
				$.tmpl("profileHeaderFooterTr",{row:nameValues.profile.pop(), prefix:'profileVerticalBoundary',"class":'computed'}).appendTo(profileFooter);
				$.tmpl("profileHeaderFooterTr",{row:nameValues.profile.pop(), prefix:'profileHorizontalBoundary',"class":'computed'}).prependTo(profileFooter);
				
				profileBody.empty();
				$.tmpl("profileBodyTr", nameValues.profile).appendTo(profileBody);
			}
		}
	});
	//localStorage.chasm = '[{"name":"","latitude":"","longitude":"", "form":[]}]';
	$("#form").submit( function(event){
		var chasm = new Array();
		
		var name = $(nameSelector).val();
		var lat = $(latSelector).val();
		var long = $(longSelector).val();
		var form = $(this).serializeArray();
		var select = infoFileList;
		var doc ={"name":name,"latitude":lat,"longitude":long,"form":form};
		if(select.val() !== ','){
			var valueArray = select.val().split(",");
			$.extend(doc, {"_id":valueArray[0],"_rev":valueArray[1]});
		}
		$.couch.db('chasmforms').saveDoc(doc,
				{success:function (data){
					//$.tmpl("infoDataOption", data).appendTo(select);
					//select.prop("selectedIndex",select.find("option").length);
		}});		
	});
});