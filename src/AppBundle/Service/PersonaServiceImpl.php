<?php

namespace AppBundle\Service;

use AppBundle\Entity\Persona;
use AppBundle\Repository\PersonaRepository;
use Psr\Log\LoggerInterface;


class PersonaServiceImpl {

    private $logger;
    private $personaRepository;

    public function __construct(LoggerInterface $logger, 
        PersonaRepository $personaRepository )
    {        
        $this->logger =  $logger;
        $this->personaRepository = $personaRepository;        
    }

     /**
     * @param AppBundle\Entity\Persona     
     * @return void
     */  
    public function save(Persona $persona){
        return $this->personaRepository->save($persona);
    }

    public function findById($id){
        $this->logger->info("Buscando Persona por id ".$id);
        return $this->personaRepository->findById($id);       
    }

    public function getTotalPersonas(){
        $this->logger->info("Buscando Total de Personas");
        return $this->personaRepository->getTotalPersonas();  
    }

    public function findBySexoAndTipoDocumentoAndNumeroDocumento($sexo,$tipoDocumento,
        $numeroDocumento){
        
        $this->logger->info("Buscando Persona por findBySexoAndTipoDocumentoAndNumeroDocumento "
            .$sexo." ".$tipoDocumento." ".$numeroDocumento);

        return $this->personaRepository
                        ->findBySexoAndTipoDocumentoAndNumeroDocumento(
                            $sexo,
                            $tipoDocumento,
                            $numeroDocumento);
      
    }
}

?>