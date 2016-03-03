<?php

rename_pagseguro_files('source');
function rename_pagseguro_files($directory){
    $files = scandir($directory);
    $ignores = ['.','..'];

    foreach($files as $file){
        if(!in_array($file,$ignores)){
            $old_path = $directory.DIRECTORY_SEPARATOR.$file;
            if(is_file($old_path)){
                $ext = pathinfo($old_path,PATHINFO_EXTENSION);
                $filename = pathinfo($old_path,PATHINFO_FILENAME);
                $filename = mb_ereg_replace('/PagSeguro/i','',$filename);
                $filename = mb_ereg_replace('/\.class/i','',$filename);
                $new_path =  $directory.DIRECTORY_SEPARATOR.$filename.'.'.$ext;
                rename($old_path,$new_path);
                rename_pagseguro_usages($new_path);
            }
            else{
                $path = $directory.DIRECTORY_SEPARATOR.$file;
                if(is_dir($path)){
                    rename_pagseguro_files($path);
                }
            }
        }
    }
}

function rename_pagseguro_usages($file){
    $source = file($file);
    foreach($source as $key => $line){
        $source[$key] = mb_ereg_replace('/\<\?php/','',$source[$key]);
        $source[$key] = mb_ereg_replace('/PagSeguro/','',$source[$key]);
        $source[$key] = mb_ereg_replace('/namespace\s+.*;\n/','',$source[$key]);
    }
    $dir = dirname($file);
    $dir = basename($dir);
    $dir = ucfirst($dir);
    $namespace = 'PagSeguro\\'.$dir;
    $namespace_line = 'namespace '.$namespace.';';
    array_unshift($source,$namespace_line);
    array_unshift($source,'<?php ');
    file_put_contents($file,implode('', $source));
}
?>