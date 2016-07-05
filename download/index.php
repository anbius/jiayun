<?php
$filename = "jyjk.apk";
/*$filesize = filesize($filename);
header( "Content-type: application/octet-stream" );    
header( "Accept-Ranges: bytes" );  
header('Content-Disposition: attachment; filename="'.$filename.'"'); //指定下载文件的描述
header('Content-Length:'.$filesize); //指定下载文件的大小 
header("Accept-Length:".$filesize);

readfile($filename);*/

header("Location:$filename");
?> <!--$filename = "jyjk.apk";
$filesize = filesize($filename);
header( "Content-type: application/octet-stream" );    
header( "Accept-Ranges: bytes" );  
header('Content-Disposition: attachment; filename="'.$filename.'"'); //指定下载文件的描述
header('Content-Length:'.$filesize); //指定下载文件的大小 
header("Accept-Length:".$filesize);
/*var_dump($filesize);
die(); */
//将文件内容读取出来并直接输出，以便下载
readfile($filename);-->