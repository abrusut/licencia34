<?php

namespace AppBundle\Service;

use AppBundle\Repository\ProvinciaRepository;
use Psr\Log\LoggerInterface;
use AppBundle\Entity\Provincia;

class ProvinciaServiceImpl{

    private $logger;
    private $provinciaRepository;

    public function __construct(LoggerInterface $logger, 
        ProvinciaRepository $provinciaRepository )
    {        
        $this->logger =  $logger;
        $this->provinciaRepository = $provinciaRepository;        
    }

     /**
     * @param AppBundle\Entity\Provincia     
     * @return void
     */  
    public function save(Provincia $provincia){
        return $this->provinciaRepository->save($provincia);
    }

    public function findById($id){
        $this->logger->info("Buscando Provincia por id ".$id);
        return $this->provinciaRepository->findById($id);       
    }

    public function findByProvinciaIdAndProvinciaNombre($provinciaId,$provinciaNombre){
        
        $this->logger->info("Buscando Persona por findByProvinciaIdAndProvinciaNombre "
            .$provinciaId." ".$provinciaNombre);

        return $this->provinciaRepository
                        ->findByProvinciaIdAndProvinciaNombre(
                            $provinciaId,
                            $provinciaNombre);
      
    }
}

?>