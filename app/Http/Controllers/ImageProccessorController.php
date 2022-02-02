<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class ImageProccessorController extends Controller
{
    public function index()
    {
        return view('welcome');
    }

    public function checkAndConvertImage(Request $request)
    {
        if($request->modifiedHeight || $request->modifiedWidth){

            if($mimeType = $request->file->getClientmimeType() == 'image/jpeg'){
                if($request->changeTypeTo){
                    Log::info("Convert karana h");
                    $convertedImageResource = $this->convertImageType($request);
                    $uniquesavename = "wz-image_proccessor";
                    imagepng($convertedImageResource, public_path('images/convertedResizedImages/'.$uniquesavename.'.png'));
                    $imageFromStorage = public_path('images/convertedResizedImages/'.$uniquesavename.'.png');
                    $originalName = $uniquesavename.'.png';
                    list($width, $height, $type, $attr) = getimagesize($imageFromStorage);
                    return response()->json([
                        'status' => 'CRImgage',
                        'NewWidth' => $width,
                        'NewHeight' => $height,
                        'NewMimeType' =>'image/.png',
                        'NewImagePath'=>$originalName,
                        
                    ]);
                }
              $imageFromStorage =  $this->changeSizeOfJpegImage($request);
                list($width, $height, $type, $attr) = getimagesize($imageFromStorage);
                $file = $request->file;
                $mimeType = $file->getClientmimeType();
                $originalName = $request->file->getClientOriginalName();
                return response()->json([
                    'status' => 'newJpg',
                    'NewWidth' => $width,
                    'NewHeight' => $height,
                    'NewMimeType' =>$mimeType,
                    'NewImage'=>$originalName,
                ]);
            }else{
                if($request->changeTypeTo){
                    Log::info("Convert karana h");
                    $originalName = $request->file->getClientOriginalName();
                    $convertedImageResource = $this->convertImageType($request);
                    $uniquesavename = "wz-image_proccessor";
                    imagepng($convertedImageResource, public_path('images/convertedResizedImages/'.$uniquesavename.'.jpeg'));
                    $imageFromStorage = public_path('images/convertedResizedImages/'.$uniquesavename.'.jpeg');
                    $originalName = $uniquesavename.'.jpeg';
                    list($width, $height, $type, $attr) = getimagesize($imageFromStorage);
                    return response()->json([
                        'status' => 'CRImgage',
                        'NewWidth' => $width,
                        'NewHeight' => $height,
                        'NewMimeType' =>'image/.jpeg',
                        'NewImagePath'=>$originalName,
                        
                    ]);
                }
                $imageFromStorage =  $this->changeSizeOfPngImage($request);
                list($width, $height, $type, $attr) = getimagesize($imageFromStorage);
                $file = $request->file;
                $mimeType = $file->getClientmimeType();
                $originalName = $request->file->getClientOriginalName();
                return response()->json([
                    'status' => 'newPng',
                    'NewWidth' => $width,
                    'NewHeight' => $height,
                    'NewMimeType' =>$mimeType,
                    'NewImage'=>$originalName,
                ]);
            }
        }else{
            list($width, $height, $type, $attr) = getimagesize($request->file);
            $file = $request->file;
            $mimeType = $file->getClientmimeType();
            return response()->json([
                'status' => 'checking',
                'width' => $width,
                'height' => $height,
                'mimeType' =>$mimeType,
            ]);
        }
    }

    function changeSizeOfJpegImage($request)
    {
        Log::info("Got inside changeSizeOFJpegImage");
        $image_name = $request->file;
        $originalName = $request->file->getClientOriginalName();
        Log::info($originalName);
        header('Content-Type: image/jpg');
        $image = imagecreatefromjpeg($image_name);
        $imgResized = imagescale($image , $request->modifiedHeight, $request->modifiedWidth);
        imagejpeg($imgResized, public_path('images/processedImages/'.$originalName));
        $imageFromStorage = public_path('images/processedImages/'.$originalName);
        Log::info($imageFromStorage);
        return $imageFromStorage;
    }

    function changeSizeOfPngImage($request)
    {
        Log::info("Got inside changeSizeOfPngImage");
        $image_name = $request->file;
        $originalName = $request->file->getClientOriginalName();
        header('Content-Type: image/png');
        $image = imagecreatefrompng($image_name);
        $imgResized = imagescale($image , $request->modifiedHeight, $request->modifiedWidth);
        imagepng($imgResized, public_path('/images/processedImages/'.$originalName));
        $imageFromStorage = public_path('images/processedImages/'.$originalName);
        Log::info($imageFromStorage);
        return $imageFromStorage;
    }

    function convertImageType($request){
        switch (exif_imagetype($request->file)) {
            case IMAGETYPE_PNG :
                Log::info("PNG image aaya convert hone k liye");
                $img = imagecreatefrompng($request->file);
                break;
            case IMAGETYPE_JPEG :
                $img = imagecreatefromjpeg($request->file);
                Log::info("JPG image aaya convert hone k liye");
                break;
            default :
                throw new InvalidArgumentException('Invalid image type');
        }
        $imgResized = imagescale($img , $request->modifiedHeight, $request->modifiedWidth);
        return $imgResized;
    }

}