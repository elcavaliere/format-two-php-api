<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Swagger\Annotations as SWG;

/**
 * @SWG\Swagger(
 *   schemes={"http","https"},
 *   host=SWAGGER_LUME_CONST_HOST,
 *   basePath="/api",
 *   @SWG\SecurityScheme(
 *      securityDefinition="Bearer",
 *      type="apiKey",
 *      name="Authorization",
 *      in="header"
 *   ),
 *   @SWG\Info(
 *     title="Factor Two PHP REST API",
 *     description="Factor Two PHP REST API description",
 *     version="1.0.0",
 *     @SWG\Contact(
 *        name="Kitadi Elie",
 *        email="kitadi.elie.ik.2623@gmail.com"
 *     ),
 *     @SWG\License(
 *         name="Developed by Elie",
 *         url="https://github.com/elcavaliere"
 *     )
 *   )
 * )
 */

class Controller extends BaseController
{
    //
}
