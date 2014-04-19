$(function(){

	$("#tabs").tabs();
	$("form.login").login();
	$("form.slope").formInfo();
	var svg = d3.select("svg");
	var databaseLayers = $.getJSON("http://localhost:5984/profiles/_design/layers/_view/layers");
	databaseLayers.done(function(data){
		data.rows.sort(compare);
		var profile = svg.selectAll("circle.profile").data(data.rows);
		//delete if not present
		profile.exit().transition().attr("r",0).remove();
		//add base circle if new
		profile.enter().append("circle")
			.attr("r", 0)
			.attr("class","profile").transition().attr("r",12);
		//apply to all circles
		profile.attr("cx",function(d){return d.value.x})
		.attr("cy",function(d){return d.value.y});
		var pathData = [];
		$.each(data.rows, function(index,elem){
			var pathPoint = {};
			pathPoint.source={};
			pathPoint.target = {};
			pathPoint.source.x = elem.value.x;
			pathPoint.source.y = elem.value.y;
			if(data.rows[index+1]){
				pathPoint.target.x = data.rows[index+1].value.x;
				pathPoint.target.y = data.rows[index+1].value.y;
				pathData.push(pathPoint);
			}
			
		});
	
		var path = svg.selectAll("path.profile").data(pathData);
		path.exit().remove();
		path.enter().append("path").attr("d",function(d){
			var dx = d.target.x - d.source.x,
				dy = d.target.y - d.source.y,
				dr = Math.sqrt(dx*dx+dy*dy);
			return "M"+d.source.x +","+d.source.y+"," + d.target.x+","+d.target.y;
		}).attr({"class":"profile","stroke":"blue","stroke-width":"3"});
	});
	
	function compare(a,b){
		if(a.value.x < b.value.x)
			return -1;
		if(a.value.x > b.value.x)
			return 1;
		return 0;
	}
	
});