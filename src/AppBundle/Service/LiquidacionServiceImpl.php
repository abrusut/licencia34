<?php
namespace AppBundle\Service;

use AppBundle\Entity\Liquidacion;
use AppBundle\Repository\LiquidacionRepository;
use Psr\Log\LoggerInterface;

class LiquidacionServiceImpl {

    private $liquidacionRepository;
    private $logger;
    

    public function __construct(LoggerInterface $logger, LiquidacionRepository $liquidacionRepository)
    {
        $this->liquidacionRepository = $liquidacionRepository;
        $this->logger = $logger;        
    }    

     /**
     * @param AppBundle\Entity\Liquidacion     
     * @return void
     */  
    public function save(Liquidacion $liquidacion){
        return $this->liquidacionRepository->save($liquidacion);
    }

    public function findById($id){
        $this->logger->info("Buscando liquidacion por id ".$id);
        return $this->liquidacionRepository->findById($id);       
    }
}



?>