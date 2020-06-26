<?php
namespace AppBundle\Service;

use AppBundle\Entity\Rendicion;
use AppBundle\Repository\RendicionRepository;
use Psr\Log\LoggerInterface;

class RendicionServiceImpl {

    private $rendicionRepository;
    private $logger;
    

    public function __construct(LoggerInterface $logger, RendicionRepository $rendicionRepository)
    {
        $this->rendicionRepository = $rendicionRepository;
        $this->logger = $logger;        
    }    

     /**
     * @param AppBundle\Entity\Rendicion     
     * @return void
     */  
    public function save(Rendicion $rendicion){
        $this->logger->info("Guardando Rendicion ".$rendicion);
        return $this->rendicionRepository->save($rendicion);
    }

    public function findById($id){
        $this->logger->info("Buscando Rendicion por id ".$id);
        return $this->rendicionRepository->findById($id);       
    }


    public function getRendicionesWhereFileProcesado($procesado){
        $this->logger->info("Buscando Rendicion por File procesado ".$procesado);
        return $this->rendicionRepository->getRendicionesWhereFileProcesado($procesado);        
    }
}



?>