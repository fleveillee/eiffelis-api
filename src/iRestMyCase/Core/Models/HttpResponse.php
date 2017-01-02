<?php

namespace iRestMyCase\Core\Models;

class HttpResponse
{
     /** @var int Http Status Code **/
     private $statusCode;

     /** @var array Headers **/
     private $headers;


     /** @var string Message Body **/
     private $messageBody;

     /**
      * Headers
      * @param array|string $value
      * @return array
      **/
     public function headers($value = null)
     {
          if(isset($value)){
               if(is_array($value)){
                    $this->headers = $value;
               }
               else{
                    $this->headers = [$value];
               }
          }
          return $this->headers;
     }

     public function statusCode($value = null)
     {
          if(isset($value) && is_numeric($value)){
               $value = (int)$value;
               if($value >= 100 && $value < 600){
                    $this->statusCode = $value;
               }
          }

          return $this->statusCode;
     }


     public function messageBody($body = null)
     {
          if(isset($body)){
               $this->messageBody = $body;
          }
          return $this->messageBody;
     }


}


