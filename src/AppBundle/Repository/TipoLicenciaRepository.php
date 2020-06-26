<?php

namespace AppBundle\Repository;

use AppBundle\Entity\TipoLicencia;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

/**
 * TipoLicenciaRepository
 *
 */
final class TipoLicenciaRepository
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
        $this->repository = $entityManager->getRepository(TipoLicencia::class);
        $this->entityManager = $entityManager;
    }
    
    public function save(TipoLicencia $tipoLicencia)
    {
        $this->entityManager->persist($tipoLicencia);
        $this->entityManager->flush();
    }    

    public function findById($id){
        return $this->repository->find($id);
    }
}


?>