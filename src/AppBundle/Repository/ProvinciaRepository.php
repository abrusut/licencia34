<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Provincia;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

/**
 * ProvinciaRepository
 *
 */
final class ProvinciaRepository
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
        $this->repository = $entityManager->getRepository(Provincia::class);
        $this->entityManager = $entityManager;
    }
    public function save(Provincia $provincia)
    {
        $this->entityManager->persist($provincia);
        $this->entityManager->flush();
    }

    public function findById($id){
        return $this->repository->find($id);
    }
    public function findByProvinciaIdAndProvinciaNombre($provinciaId,$provinciaNombre)
    {
        return $this->repository
            ->findOneBy(array(
                'id' => $provinciaId,
                'nombre' => $provinciaNombre                
            ));
    }
}


?>