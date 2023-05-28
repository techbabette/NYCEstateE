<?php
function saveAdjustedPhotoToDisk($image, $targetFile, $maxW, $maxH){
    //Get information from about provided
    $file_name = $image['tmp_name'];
    list($width, $height, $type, $attr) = getimagesize( $file_name );

    //Set the initial values of new width and new height to the current ones in case no resizing is to be done.
    $new_width = $width;
    $new_height = $height;

    //New solution
    $ratio = 1;
    $ratioW = -1;
    $ratioH = -1;

    if($new_width > $maxW){
        $ratioW = $maxW / $new_width;
    }

    if($new_height > $maxH){
        $ratioH = $maxH / $new_height;
    }

    if($ratioH < $ratioW){
        $ratio = $ratioH;
    }

    if($ratioW < $ratioH){
        $ratio = $ratioW;
    }

    //Old solution
    // if($new_width > $height){
    //     $ratio = $maxW /  $new_width;
    // }
    // else{
    //     $ratio = $maxH / $new_height;
    // }

    //If image would get smaller by multiplying with the calculated ratio, multiply.
    if($ratio < 1){
        $new_width = $new_width * $ratio;
        $new_height = $new_height * $ratio;
    }

    $target_filename = $file_name;
    $src = imagecreatefromstring((file_get_contents($file_name)));
    $dst = imagecreatetruecolor($new_width, $new_height);
    //Copy image onto image of rescaled size, ie make the image resized
    imagecopyresampled($dst, $src, 0, 0, 0, 0,  $new_width, $new_height, $width, $height);
    //Make a new image of desired maximum size (Background image)
    $newDst = imagecreatetruecolor($maxW, $maxH);
    //Change the color of the background image
    $bg = imagecolorallocate ($newDst, 31, 39, 27);
    imagefilledrectangle($newDst, 0, 0, $maxW, $maxH, $bg);
    //Merge the resized image onto the background image, positioning it to the center in case of smaller width
    imagecopymerge($newDst, $dst, ($maxW - $new_width) / 2, $maxH - $new_height, 0, 0, $new_width, $new_height, 100);
    //Save the new image as a jpeg
    $result = imagejpeg($newDst, $targetFile);
    //Destory the source image
    imagedestroy($src);
    return $result;
}
?>
