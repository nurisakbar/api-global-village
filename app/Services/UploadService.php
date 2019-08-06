<?php

namespace App\Services;
use Intervention\Image\ImageManagerStatic as Image;
use File;

class UploadService
{
    public function image($request,$name,$folderDestination)
    {
        // ====================== START VALIDATION ========================
        $validator          = \Validator::make($request->all(), [
            $name    =>  'required|mimes:jpeg,jpg,png'
        ]);
        
        if ($validator->fails()) {    
            return response()->json($validator->messages(), 200);
        }

        // ===================== END VALIDATION ==========================

        
        if ($request->hasFile($name)) {
            $file       = $request->file($name);

            $resolusi   = [780,1024];

            $fileName   = date('Y_m_d_H_i_s').'_'.str_replace([" ",'-','-'],"_",$file->getClientOriginalName());
            
            foreach($resolusi as $row)
            {
                $canvas = Image::canvas($row, $row);

                $resizeImage  = Image::make($file)->resize($row, $row, function($constraint) {
                    $constraint->aspectRatio();
                });

                if (!File::isDirectory($folderDestination . '/' . $row)) {
                    File::makeDirectory($folderDestination . '/' . $row);
                }

                $canvas->insert($resizeImage, 'center');
                $canvas->save($folderDestination . '/' . $row . '/' . $fileName);
            }
            
            $file->move($folderDestination,$fileName);

            // =================== SET RESULT ============================
            $response['status'] = "success";
            $response['data']   = [
                                    'file_name'=>$fileName,
                                    'url'=>asset($folderDestination.'/'.$fileName)
                                ];
        }else
        {
            // =================== SET RESULT ============================
            $response['status'] = "failed";
            $response['data']   = null;
        }

        return $response;
    }

    public function video($request,$folderDestination)
    {
        // ====================== START VALIDATION ========================
        $validator          = \Validator::make($request->all(), [
            'video'    =>  'required'
        ]);
        
        if ($validator->fails()) {    
            return response()->json($validator->messages(), 200);
        }

        // ===================== END VALIDATION ==========================

        if ($request->hasFile('video')) {
            $file       = $request->file('video');
            $fileName   = $file->getClientOriginalName();
            $fileName   = str_replace(" ","-",$fileName);
            $file->move($folderDestination,$fileName);

            // =================== SET RESULT ============================
            $response['status'] = "success";
            $response['data']   = [
                                    'file_name' =>   $fileName,
                                    'url'       =>  asset($folderDestination.'/'.$fileName)
                                ];
        }else
        {
            // =================== SET RESULT ============================
            $response['status'] = "failed";
            $response['data']   = null;
        }

        return $response;
    }
}
