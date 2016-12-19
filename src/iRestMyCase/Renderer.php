<?php

namespace iRestMyCase;

use iRestMyCase\Models\HttpResponse;

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

}