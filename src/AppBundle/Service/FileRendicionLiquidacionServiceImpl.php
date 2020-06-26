<?php
namespace AppBundle\Service;

use AppBundle\Entity\FileRendicionLiquidacion;
use AppBundle\Repository\FileRendicionLiquidacionRepository;

use Psr\Log\LoggerInterface;

class FileRendicionLiquidacionServiceImpl {

    private $fileRendicionLiquidacionRepository;
    private $logger;
    

    public function __construct(LoggerInterface $logger, FileRendicionLiquidacionRepository $fileRendicionLiquidacionRepository)
    {
        $this->fileRendicionLiquidacionRepository = $fileRendicionLiquidacionRepository;
        $this->logger = $logger;        
    }    

     /**
     * @param AppBundle\Entity\Rendicion     
     * @return void
     */  
    public function save(FileRendicionLiquidacion $fileRendicionLiquidacion){
        $this->logger->info("Guardando FileRendicionLiquidacion ".$fileRendicionLiquidacion);
        return $this->fileRendicionLiquidacionRepository->save($fileRendicionLiquidacion);
    }

    public function findById($id){
        $this->logger->info("Buscando FileRendicionLiquidacion por id ".$id);
        return $this->fileRendicionLiquidacionRepository->findById($id);       
    }
}



?>