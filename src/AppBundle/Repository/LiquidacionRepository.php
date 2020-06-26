<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Liquidacion;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

/**
 * LiquidacionRepository
 *
 */
final class LiquidacionRepository
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
        $this->repository = $entityManager->getRepository(Liquidacion::class);
        $this->entityManager = $entityManager;
    }
    
    public function save(Liquidacion $liquidacion)
    {
        $this->entityManager->persist($liquidacion);
        $this->entityManager->flush();
    } 
    
    public function persist(Liquidacion $liquidacion)
    {
        $this->entityManager->persist($liquidacion);
    }

    public function flush()
    {
        $this->entityManager->flush();
    }

    public function findById($id){
        return $this->repository->find($id);
    }
}


?>