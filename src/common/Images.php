<?
  function createNewImage($path, $maxWidthOrHeight)
  {
    $retString = "";
    $size = GetImageSize($path);
    $width = $size[0];
    $height = $size[1];
    $newHeight = $maxWidthOrHeight;
    $newWidth = $maxWidthOrHeight;
    $newPath = $path;
    $newPath = preg_replace('/\.jpg/i', '_' . $maxWidthOrHeight . '.jpg', $newPath);
    $retString .= "Width: $width<br />Height: $height<br />Path: $path<br />New Path: $newPath\n";
    $origRatio = $height / $width;
    // Create a Thumbnail
    if($width > $height)
    {
      $retString .= "width > height<br />";
      $newHeight = round(($newWidth / $width) * $height);
    }
    else
    {
      $retString .= "width <= height<br />";
      $newWidth = round(($newHeight / $height) * $width);
    }
    $retString .= "New Image Size:  $newWidth x $newHeight<br />";
    $orig = imagecreatefromjpeg($path);
    if( ($newHeight > $height ) || ($newWidth > $width) )
    {
      $retString .= "ERROR, can't create image of size $newHeight x $newWidth <br />\n";
      $retString .= "Original: $height x $width<br />\n";
      return 0;
    }
    $newPic = ImageCreateTrueColor($newWidth, $newHeight);
    $retString .= "Created new jpeg<br />";
    ImageCopyResampled($newPic, $orig, 0,0,0,0, $newWidth, $newHeight, $width, $height);
    imagejpeg($newPic, $newPath, 90);
    system("chmod a+r $newPath");
    return $retString;
  }
?>
