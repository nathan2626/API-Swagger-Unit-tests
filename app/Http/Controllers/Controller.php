<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    /**
     * @OA\Info(
     * title="To Dont List API",
     *  version="0.1",)
     * @OA\SecurityScheme(
    *      securityScheme="bearerAuth",
    *      in="header",
    *      name="bearerAuth",
    *      type="http",
    *      scheme="bearer",
    *      bearerFormat="JWT",
    * )
     * 
     */

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}