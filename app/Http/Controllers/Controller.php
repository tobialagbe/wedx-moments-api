<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\JsonResponse;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function exceptionResponse(\Exception $exception)
    {
        $payload = [
            'status' => 'error',
            'message' => $exception->getMessage()
        ];
        $code = $exception->getCode();
        if($code == 0 || $code > 20000){
            $code = 500;
        }
        return new JsonResponse($payload, $code);
    }

    public function successResponse($message, $status = 200)
    {

        $payload = [
            'status' => 'success',
            'data' => $message
        ];
        return new JsonResponse($payload, $status);
    }
}
