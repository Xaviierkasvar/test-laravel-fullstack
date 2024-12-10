<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use OpenApi\Annotations as OA;

/**
 * @OA\OpenApi(
 *  @OA\Info(
 *      title="API Documentation",
 *      version="1.0.0",
 *      description="API Documentation for Queo Challenge",
 *      @OA\Contact(
 *          email="javier_castillo_15@hotmail.es",
 *          name="Francisco Javier Castillo Barrios"
 *      )
 *  ),
 *  @OA\Server(
 *      url=L5_SWAGGER_CONST_HOST,
 *      description="API Server"
 *  )
 * )
 */
class ApiController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}