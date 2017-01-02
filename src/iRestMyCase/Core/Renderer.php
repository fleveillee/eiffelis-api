<?php

namespace iRestMyCase\Core;

use iRestMyCase\Core\Models\HttpResponse;

class Renderer{

     public static function renderHttpResponse(HttpResponse $httpResponse)
     {
          if($httpResponse->headers() !== null){
               for($i=0; $i<count($httpResponse->headers()); $i++){
                    if($i=0){
                         header($value[$i]);
                    }else{
                         header($value[$i], false);
                    }
               }
          }

          if($httpResponse->statusCode() !== null){
               http_response_code($httpResponse->statusCode());
          }

          if($httpResponse->messageBody() !== null){
               echo $httpResponse->messageBody();
          }
          ob_flush();
          flush();
     }

     public static function renderHttpErrorResponse($errorCode = 500, $errorMessage){
          $httpResponse = new HttpResponse();
          $httpResponse->statusCode($errorCode);
          $httpResponse->messageBody("Http Error $errorCode: $errorMessage");
          self::renderHttpResponse($httpResponse);
     }

}