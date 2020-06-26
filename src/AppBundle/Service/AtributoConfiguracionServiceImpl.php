<?php
namespace AppBundle\Service;

use AppBundle\Entity\AtributoConfiguracion;
use AppBundle\Repository\AtributoConfiguracionRepository;
use Psr\Log\LoggerInterface;

class AtributoConfiguracionServiceImpl {

    private $atributoConfiguracionRepository;
    private $logger;
    

    public function __construct(LoggerInterface $logger, AtributoConfiguracionRepository $atributoConfiguracionRepository)
    {
        $this->atributoConfiguracionRepository = $atributoConfiguracionRepository;
        $this->logger = $logger;        
    }    

     /**
     * @param AppBundle\Entity\AtributoConfiguracion     
     * @return void
     */  
    public function save(AtributoConfiguracion $atributoConfiguracion){
        return $this->atributoConfiguracionRepository->save($atributoConfiguracion);
    }

    public function findById($id){
        $this->logger->info("Buscando AtributoConfiguracion por id ".$id);
        return $this->atributoConfiguracionRepository->findById($id);       
    }

    public function getAtributoConfiguracion($clave){
        $this->logger->info("Buscando AtributoConfiguracion por clave ".$clave);
        $atributoConfiguracion = null;

        $atributosConfiguracion = 
            $this->atributoConfiguracionRepository->findAtributoConfiguracionByClave($clave);       
        
        if($atributosConfiguracion!=null &&
            is_array($atributosConfiguracion) &&
             count($atributosConfiguracion)>0){
                $atributoConfiguracion = $atributosConfiguracion[0]; 
        }
                
        return $atributoConfiguracion;       
    }
}



?>