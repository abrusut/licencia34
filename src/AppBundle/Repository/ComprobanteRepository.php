<?php

namespace AppBundle\Repository;

use AppBundle\Entity\AtributoConfiguracion;
use AppBundle\Entity\Comprobante;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;


/**
 * ComprobanteRepository
 *
 */
final class ComprobanteRepository
{
    
    /**
     * @var EntityRepository
     */
    private $repository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->repository = $entityManager->getRepository(Comprobante::class);
        $this->entityManager = $entityManager;
    }
    
     /**
     * @param AppBundle\Entity\Comprobante     
     * @return void
     */  
    public function save(Comprobante $comprobante)
    {
        $this->entityManager->persist($comprobante);
        $this->entityManager->flush();
    }    

    public function persist(Comprobante $comprobante)
    {
        $this->entityManager->persist($comprobante);
    }

    public function flush()
    {
        $this->entityManager->flush();
    }

    public function findById($id){
        return $this->find($id);
    }

}


?>