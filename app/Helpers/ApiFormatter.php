<?php

namespace App\Helpers;

Class ApiFormatter {
    protected static $response = [
        "status" => NULL,
        "message" => NULL,
        "data" => NULL,
    ];

    public static function sendResponse($status = NULL, $mesage = NULL, $data = [])
    {
        self::$response['status'] = $status;
        self::$response['message'] = $mesage;
        self::$response['data'] = $data;
        return response()->json(self::$response, self::$response['status']);
        //status : http status code (200,400,500)
        //message : desc http status code ('succes', 'bad request', 'server error')
        //data : hasil yg diambil dari db
    }
}
