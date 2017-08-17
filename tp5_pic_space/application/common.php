<?php


 // 获取文件夹大小
function getDirSize($dir){

    $handle = opendir($dir);

    $sizeResult = 0;
    
    while (false!==($FolderOrFile = readdir($handle)))
    {
        if($FolderOrFile != "." && $FolderOrFile != "..")
        {
            if(is_dir("$dir/$FolderOrFile"))
            {
                $sizeResult += getDirSize("$dir/$FolderOrFile");
            }
            else
            {
                $sizeResult += filesize("$dir/$FolderOrFile");
            }
        }   
    }
    closedir($handle);
    
    return $sizeResult;
}









?>