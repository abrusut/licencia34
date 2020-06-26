<?php
namespace AppBundle\Service;

use AppBundle\Repository\NumeradorRepository;
use Psr\Log\LoggerInterface;
use AppBundle\Entity\Numerador;

class NumeradorServiceImpl {

    private $numeradorRepository;
    private $logger;
    

    public function __construct(LoggerInterface $logger, NumeradorRepository $numeradorRepository)
    {
        $this->numeradorRepository = $numeradorRepository;
        $this->logger = $logger;        
    }    

     /**
     * @param AppBundle\Entity\Numerador     
     * @return void
     */  
    public function save(Numerador $numerador){
        return $this->numeradorRepository->save($numerador);
    }

    public function findById($id){
        $this->logger->info("Buscando liquidacion por id ".$id);
        return $this->numeradorRepository->findById($id);       
    }
}



?>