<?php
namespace AppBundle\Exception;

class SimpleMessageException extends \RuntimeException{
    
    public function __construct($messageForUser = 'Error en la aplicacion', $code=400){
        parent::__construct();
        $this->message=$messageForUser;
        $this->code=$code;
    }
    
}


?>