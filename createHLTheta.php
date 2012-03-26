<?php 

//function createDataPointsFromForm(){
//	global $PanelProfile;
//	global $PanelProfileHeight;
//	global $PanelProfileLength;
//	global $PanelProfileAngle;
//	global $_POST;
//	print('<br>'.sizeOf(array_keys($_POST, $PanelProfile)).'<br>');
//	if(sizeof($_POST)>0) {
//		for($i = 0; $i < (sizeOf(array_keys($_POST, $PanelProfile) )/3)  ; $i+=1 ){
//			$profileKeys[]= createDataPoint(
//				$_POST[$PanelProfile.$PanelWordSeperator.$PanelProfileHeight.$PanelWordSeperator.$i],
//				$_POST[$PanelProfile.$PanelWordSeperator.$PanelProfileLength.$PanelWordSeperator.$i],
//				$_POST[$PanelProfile.$PanelWordSeperator.$PanelProfileAngle.$PanelWordSeperator.$i]);
//		}
//	}
//	return $profileKeys;
//}
function createDataPoint($formHeight,$formLength,$formAngle){
	return array( 'h' => $formHeight, 'l'=>$formLength, 'theta'=>$formAngle);
}

?>
