<?php 
function printSession(){
	if(isset($_SESSION) && sizeof($_SESSION)>0){
		foreach($_SESSION as $name=>$value) {
		print('http session vars - Var Name/value:'.$name.'/'.$value.'<br>');
		}
	}
	print('Hello Session World!!');
	return 'hello';
}

?>
