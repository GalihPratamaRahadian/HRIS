<?php 

namespace App\MyClass;

use App\Device;
use App\FTLog;
use App\FTEventLog;
use App\GateAuth;
use App\MyClass\MyImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

class FaceTerminal
{
    public static function urlRequest()
    {
        return appconfig('relay_face_terminal_url');
    }

    public static function createUser($userData)
    {
        $url = FaceTerminal::urlRequest().'create';

        $dataDevice = Device::all();
        foreach($dataDevice as $device) {
            $dataPost = http_build_query($userData).'&device='.$device->device_name.'&ip='.$device->ip_address.'&port='.$device->port.'&username='.$device->username.'&password='.$device->password;
            File::put('datauser.txt', $dataPost);
            $curl = curl_init(); _CONNECTTIMEOUT, 1);
            curl_setopt($curl, CURLOPT_DNS_CACHE_TIMEOUT, 10); 

            curl_setopt($curl, CURLOPT
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt ($curl, CURLOPT_POST, TRUE);
            curl_setopt ($curl, CURLOPT_POSTFIELDS, $dataPost); 

            curl_setopt($curl, CURLOPT_USERAGENT, 'api');

            curl_setopt($curl, CURLOPT_TIMEOUT, 1); 
            curl_setopt($curl, CURLOPT_HEADER, 0);
            curl_setopt($curl,  CURLOPT_RETURNTRANSFER, false);
            curl_setopt($curl, CURLOPT_FORBID_REUSE, true);
            curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);

            $data = curl_exec($curl);   

            curl_close($curl);
        }
        return true;
    }

    public static function createLog(Request $request)
    {
        if(!empty($request->authId))
        {
            if(FTEventLog::exists(['auth_id' => $request->authId]))
            {
                $lastLog    = FTEventLog::where(['auth_id' => $request->authId])
                            ->first();
                $date       = strtotime(date('Y-m-d H:i:s', strtotime($request->date)));
                $lastDate   = strtotime($lastLog);

                if(($date - $lastDate) >= FaceTerminal::logIntervalSameUser())
                {
                    $log = FaceTerminal::writeLog($request);
                }
            }
            else
            {
                $log = FaceTerminal::writeLog($request);
            }
        }
        else
        {
            $log = FaceTerminal::writeLog($request);
        }


        return $log;
    }

    // private static function userCheck()

    private static function writeLog(Request $request)
    {
        // Full
        $filename = FaceTerminal::createPhotoLog($request->picture);
        // Face
        FaceTerminal::createFaceLog($filename);

        $authId = null;
        $name   = '-----';
        $from   = '-----';
        if(!empty($request->authId))
        {
            if(GateAuth::exists($request->authId))
            {
                $gateAuth   = GateAuth::find($request->authId);
                $authId     = $request->authId;
                if($gateAuth->ref_type == 'karyawan')
                {
                    if(!empty($gateAuth->karyawan))
                    {
                        $name   = $gateAuth->karyawan->nama;
                        if(!empty($gateAuth->karyawan->departemen))
                        {
                            $from = $gateAuth->karyawan->departemen->nama_departemen;
                        }
                    }
                }
                elseif($gateAuth->ref_type == 'visitor')
                {
                    if(!empty($gateAuth->visitor))
                    {
                        $name   = $gateAuth->visitor->nama_visitor;
                        $from   = $gateAuth->visitor->perusahaan;
                    }
                }
            }
        }

        $mask = $request->mask == true? 'Y' : 'N';

        $log = FTEventLog::create([
            'date'          => date('Y-m-d H:i:s', strtotime($request->date)),
            'device_name'   => $request->device,
            'auth_id'       => $authId,
            'name'          => $name,
            'from'          => $from,
            'temperature'   => $request->temperature,
            'mask'          => $mask,
            'filename'      => $filename,
        ]);

        FaceTerminal::createWatermark($log);

        return $log;
    }

    private static function logIntervalSameUser()
    {
        return 5;
    }

    private static function createPhotoLog($base64Data)
    {
        $photo = base64_decode($base64Data);
        $filename = date('YmdHis');
        $img = imagecreatefromstring($photo);
        if($img !== false)
        {
            header('Content-Type: image/jpeg');
            imagejpeg($img, "log/".$filename.".jpeg");
        }

        return $filename.".jpeg";
    }

    public static function createFaceLog($filename)
    {
        $face = Image::make('log/'.$filename);
        if($face->width() > $face->height())
        {
            $face->resize(null, 432, function($const){
                $const->aspectRatio();
            });
        }
        else
        {
            $face->resize(352, null, function($const){
                $const->aspectRatio();
            });
        }
        $face->crop(352, 432);
        $face->save('log/face/'.$filename);
        return $face->width();
    }


    public static function createWatermark($log)
    {
        $img = Image::make('log/'.$log->filename);  
        $img->text($log->name, $img->width() - 25, $img->height() - 85, function($font) {  
            $font->file('fonts/Calibri.ttf');  
            $font->size(15);  
            $font->color('#ffffff');  
            $font->align('right');  
            $font->valign('top');
        });
        $img->text("Suhu ".$log->temperature."&deg;C", $img->width() - 25, $img->height() - 65, function($font) {  
            $font->file('fonts/Calibri.ttf');  
            $font->size(15);  
            $font->color('#ffffff');  
            $font->align('right');  
            $font->valign('top');
        });
        $maskText = "Tidak menggunakan masker";
        if($log->mask == 'Y')
        {
            $maskText = "Menggunakan masker";
        }
        $img->text($maskText, $img->width() - 25, $img->height() - 45, function($font) {  
            $font->file('fonts/Calibri.ttf');  
            $font->size(15);  
            $font->color('#ffffff');  
            $font->align('right');  
            $font->valign('top');
        });
        $img->text(date('d/m/Y H:i:s'), $img->width() - 25, $img->height() - 25, function($font) {  
            $font->file('fonts/Calibri.ttf');  
            $font->size(15);  
            $font->color('#ffffff');  
            $font->align('right');  
            $font->valign('top');
        });
        $img->save('log/'.$log->filename);
    }

}