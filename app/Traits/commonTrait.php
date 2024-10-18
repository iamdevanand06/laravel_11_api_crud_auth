<?php

namespace App\Traits;

use Storage;

trait commonTrait
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($result, $message)
    {
    	$response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];

        return response()->json($response, 200);
    }

    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($error, $errorMessages = [], $code = 404)
    {
    	$response = [
            'success' => false,
            'message' => $error,
        ];

        if(!empty($errorMessages)){
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $code);
    }

    public function fileExists($filePath){
        if (Storage::exists($filePath)) {
            $metaData = Storage::getMetaData($filePath);
            if($metaData == false) {
                return false;  // It is a directory, not a file
            }
            return true; // It is a file
        }
    }
}
