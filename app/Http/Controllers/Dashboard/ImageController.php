<?php

namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdminLoginRequest;
//use Request;
use Illuminate\Support\Facades\Session;
class ImageController extends Controller
{
	 /* public function mergeImg()
    {
       $image1 = asset('assets/images/merg_images/frame1.webp');
        $image2 = asset('assets/images/merg_images/frame2.webp');
		
      //  $image1 = public_path('/assets/images/merg_images/frame1.jpg');
       // $image2 = public_path('/images/frame2.jpg');
 //getimagesize($image2);
         list($width,$height) = getimagesize($image2);

         $image1 = imagecreatefromstring(file_get_contents($image1));
        $image2 = imagecreatefromstring(file_get_contents($image2));

        imagecopymerge($image1,$image2,80,45,30,0,185,140,100);
        header('Content-Type:image/png');
        imagepng($image1);

       return $masterImg = imagepng($image1,'merged.png');

       // dd($masterImg);
	   
	   
	   
	}*/
	
/*	public function mergeImg()
    {
		$x=200;
		$y=200;
	$final_img = imagecreate($x, $y); // where x and y are the dimensions of the final image
 $image1 = asset('assets/images/merg_images/img1.png');
        $image2 = asset('assets/images/merg_images/img1.png');
        $image3 = asset('assets/images/merg_images/img3.png');
		
$image_1 = imagecreatefrompng($image1 );
$image_2 = imagecreatefrompng($image2);
$image_3 = imagecreatefrompng($image2);
imagecopy($image_1, $final_img, 0, 0, 0, 0, $x, $y);
imagecopy($image_2, $final_img, 0, 0, 0, 0, $x, $y);
imagecopy($image_3, $final_img, 0, 0, 0, 0, $x, $y);

imagealphablending($final_img, false);
imagesavealpha($final_img, true);
/*if($output_to_browser){

header('Content-Type: image/png');
imagepng($final_img);

}else{
// output to file

imagepng($final_img, 'final_img.png');

//}

	}*/
	
	
public function mergeImg()
    {
 $image1 = asset('assets/images/merg_images/img1.png');

$src = imagecreatefrompng($image1);
//$src = imagecreatefromgif('php.gif');
$dest = imagecreatetruecolor(80, 40);

// Copy
imagecopy($dest, $src, 0, 0, 20, 13, 80, 40);

// Output and free from memory
 header('Content-Type: image/png');
imagepng($dest);
//imagegif($dest);

 imagedestroy($dest);
 imagedestroy($src);	
	
	
	}
	
	
	
	
	
	
	
	
	
}