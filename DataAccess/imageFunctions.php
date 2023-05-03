<?php
function saveAdjustedPhotoToDisk($image, $targetFile, $maxW, $maxH){
    $file_name = image['tmp_name'];
    list($width, $height, $type, $attr) = getimagesize( $file_name );
    if ( $width > $maxW || $height > $maxH ) {
        $target_filename = $file_name;
        $ratio = $width/$height;
        if( $ratio > 1) {
            $new_width = $maxW;
            $new_height = $maxH/$ratio;
        } else {
            $new_width = $maxW*$ratio;
            $new_height = $maxH;
        }
        $src = imagecreatefromstring( file_get_contents( $file_name ) );
        $dst = imagecreatetruecolor( $new_width, $new_height );
        imagecopyresampled( $dst, $src, 0, 0, 0, 0, $new_width, $new_height, $width, $height );
        imagedestroy( $src );
        imagejpeg($dst, $targetFile);
        return true;
    }
    else{
        $src = imagecreatefromstring( file_get_contents( $file_name ) );
        $dst = imagecreatetruecolor( $new_width, $new_height );
        imagecopyresampled( $dst, $src, 0, 0, 0, 0, $new_width, $new_height, $width, $height );
        move_uploaded_file($image["tmp_name"], $target_file);
    }
}
?>