function buildSoilStrataArray()
{
    var data = new Array();
    var num_strata = $(":rhok-chasm","body").chasm("getStrataCount");
    var num_profile_segments = $(":rhok-chasm","body").chasm("getProfileSegmentsCount");
    for (var strataIdx = 0; strataIdx < /*INDEX.*/num_strata; strataIdx++)
    {
        var strata = new Array();
        for (var idx = 0; idx < /*INDEX.*/num_profile_segments + 4; idx++)
        {
            strata.push(parseFloat($("#soilDepth" + idx + "\\.strata" + strataIdx + "\\:depth").val()));
        }
        if ( strata.length === /*INDEX.*/num_profile_segments + 4 )
        {
            data[strataIdx] = strata;
        }
    }
    
    return data;
}

function buildProfileArray()
{
	var num_strata = $(":rhok-chasm","body").chasm("getStrataCount");
    var num_profile_segments = $(":rhok-chasm","body").chasm("getProfileSegmentsCount");
	var totalHeight = 0;
	var totalWidth = 0;
	
    var data = new Array(/*INDEX.*/num_profile_segments);
    for (var idx = 1; idx <= /*INDEX.*/num_profile_segments; idx++)
    {
        var row = new Array(3);
        row[ CHASM.H ] = parseFloat($("#profile" + idx + "\\:height").val());
        row[ CHASM.L ] = parseFloat($("#profile" + idx + "\\:length").val());
        row[ CHASM.THETA ] = parseFloat($("#profile" + idx + "\\:angle").val());
        data[ idx - 1 ] = row;
        
        totalHeight += row[ CHASM.H ];
        if ( row[ CHASM.THETA ] != 90 && row[ CHASM.THETA ] != -90 )
        {
        	totalWidth += row[ CHASM.H ] / Math.tan( TRIG.degreesToRadians( row[ CHASM.THETA ] ) );
        }
    }
    
    // add virtual segments
	var dataRow = [ 0, 0.15 * totalWidth, 0 ];
	
	data.unshift( dataRow );
	data.push( dataRow );
	
	dataRow = [ 0.25 * totalHeight, 0, 90 ];
	
	data.push( dataRow );
    
    return data;
}

function buildWaterArray()
{
	var num_strata = $(":rhok-chasm","body").chasm("getStrataCount");
    var num_profile_segments = $(":rhok-chasm","body").chasm("getProfileSegmentsCount");
    var data =  new Array(/*INDEX.*/num_profile_segments + 4);
    for (var idx = 0; idx < /*INDEX.*/num_profile_segments + 4; idx++)
    {
        data[idx] = parseFloat($("#waterDepth" + idx +"\\:depth").val());
    }
    
    return data;
}