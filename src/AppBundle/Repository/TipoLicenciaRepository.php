<?php

namespace AppBundle\Repository;

use AppBundle\Entity\TipoLicencia;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

/**
 * TipoLicenciaRepository
 *
 */
final class TipoLicenciaRepository extends ServiceEntityRepository
{
    /**
     * @var EntityRepository
     */
    private $repository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, TipoLicencia::class);
    }
    

    public function findById($id){
        return $this->repository->find($id);
    }
}


?>