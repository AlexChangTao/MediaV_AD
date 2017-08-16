<?php

// 检查角色权限
if (!function_exists('check_auth')){
	function check_auth($rule){
		$ci = &get_instance();
		if($ci->session->userdata('aid')==1) return true;
		$ci->load->library('auth');
		return $ci->auth->check($rule,$ci->session->userdata('aid'))?true:false;
	}
}

if (!function_exists('formatBytes')){
	function formatBytes($size) { 
		$units = array(' B', ' KB', ' MB', ' GB', ' TB'); 
		for ($i = 0; $size >= 1024 && $i < 4; $i++) $size /= 1024; 
		return round($size, 2).$units[$i]; 
	}
}

if(!function_exists('dump')){
	function dump($r){
		echo('<pre>');
		var_dump($r);
		echo('</pre>');
	}
}

if (!function_exists('listDir')){
	function listDir($dir){
		if(is_dir($dir)){
			if ($dh = opendir($dir)){
				while (($file = readdir($dh)) !== false){
					if((is_dir($dir."/".$file)) && $file!="." && $file!=".."){
						echo "<b><font color='red'>文件名：</font></b>",$file,"<br><hr>";
						listDir($dir."/".$file."/");
					}else{
						if($file!="." && $file!=".."){
							echo $file."<br>";
						}
					}
				}
			closedir($dh);
			}
		}
	}
}

if (!function_exists('deepScanDir')){
	/****************************
	* 获取目录下的所有文件
	* [$dir] 文件夹路径
	****************************/
	function deepScanDir($dir) {
	    $fileArr = array ();
	    $dirArr = array ();
	    $dir = rtrim($dir, '//');
	    if (is_dir($dir)) {
	        $dirHandle = opendir($dir);
	        while (false !== ($fileName = readdir($dirHandle))) {
	            $subFile = $dir . DIRECTORY_SEPARATOR . $fileName;
	            if (is_file($subFile)) {
	                $fileArr[] = $subFile;
	            }
	            elseif (is_dir($subFile) && str_replace('.', '', $fileName) != '') {
	                $dirArr[] = $subFile;
	                $arr = deepScanDir($subFile);
	                $dirArr = array_merge($dirArr, $arr['dir']);
	                $fileArr = array_merge($fileArr, $arr['file']);
	            }
	        }
	        closedir($dirHandle);
	    }
	    return array (
	        'dir' => $dirArr,
	        'file' => $fileArr
	    );
	}
}

if (!function_exists('writeFileContents')){
	/****************************
	* 将内容写入文件
	* [$filename] 文件路径
	* [$contents]  文件内容
	* [$type]     读写类型
	****************************/
	function writeFileContents($filename, $contents, $type='a') {
	    if (!($fd = fopen($filename, $type)))
	        return FALSE;
	 
	    if (!fwrite($fd, $contents."\n")) {
	        fclose($fd);
	        return FALSE;
	    }
	 
	    fclose($fd);
	    return true;
	}
}