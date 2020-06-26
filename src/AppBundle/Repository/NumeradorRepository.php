<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Liquidacion;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Numerador;

/**
 * NumeradorRepository
 *
 */
final class NumeradorRepository
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
        $this->repository = $entityManager->getRepository(Numerador::class);
        $this->entityManager = $entityManager;
    }
    
    public function save(Numerador $numerador)
    {
        $this->entityManager->persist($numerador);
        $this->entityManager->flush();
    }    

    public function findById($id){
        return $this->entityManager->find($id);
    }
}


?>