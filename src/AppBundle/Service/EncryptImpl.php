<?php
namespace AppBundle\Service;
use Psr\Log\LoggerInterface;


class EncryptImpl
{
    private $logger;
    private $keySalt;

    public function __construct(LoggerInterface $logger,
                                $keySalt = ''){
        $this->logger = $logger;
        $this->keySalt = $keySalt;
    }

    public function encrypt($param){              
        return base64_encode(urlencode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, 
                                                        md5($this->keySalt), 
                                                        $param, 
                                                        MCRYPT_MODE_CBC, 
                                                        md5(md5($this->keySalt)))));   

   
   

    }
    public function decrypt($param){
       return rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, 
                    md5($this->keySalt), 
                    urldecode(base64_decode($param)), 
                    MCRYPT_MODE_CBC, md5(md5($this->keySalt))), "\0");
    }
}



?>