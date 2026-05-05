<?php

namespace App\MyClass;

use CURLFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class WhatsappNew
{
    // /**
    //  * Send text message to an individual via MikroTik.
    //  *
    //  * @param string $phoneNumber
    //  * @param string $message
    //  * @return array
    //  */

    //end point wa ip public: http://103.242.105.85:60001
    //end point wa ip private: http://10.1.105.164:60001

    const END_POINT_WA = 'WA Baru';

    public static function sendChatAndImageWhatsapp($phoneNumber, $message, $filePath = null, $caption = null)
    {
        $postFields = [
            'recipientId' => $phoneNumber,
            'message' => $message,
        ];

        if ($caption) {
            $postFields['caption'] = $caption;
        }

        if ($filePath) {
            $mimeType = mime_content_type($filePath);
            $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];

            if (in_array($mimeType, $allowedTypes)) {
                $postFields['gambar'] = new CURLFile($filePath, $mimeType, basename($filePath));
            }
        }


        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://10.1.105.164:60001/kirim-pesan-gambar-caption',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postFields,
            CURLOPT_HTTPHEADER => array('Content-Type: multipart/form-data'),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    public static function sendChatanPdfWhatsapp($phoneNumber, $message, $filePath = null, $caption = null)
    {
        $postFields = [
            'recipient' => $phoneNumber,
            'message' => $message,
        ];

        if ($caption) {
            $postFields['caption'] = $caption;
        }

        if ($filePath) {
            $postFields['pdfFile'] = new CURLFile($filePath, 'application/pdf', basename($filePath));
        }


        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://10.1.105.164:60001/send-pdf',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postFields,
            CURLOPT_HTTPHEADER => array('Content-Type: multipart/form-data'),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }
}

/**

Send Chat Tutorial
example :
Whatsapp::sendChat([
	'phoneNumber'	=> "6282316425264",
	'message'	=> "Text Pesan"
]);



Send Media Tutorial
example :
WhatsappNew::sendImage([
	'phoneNumber'		=> "6282316425264",
	'path'		=> "invoice/INV0001.pdf",
    'caption'		=> "Invoice"
]);
 */
