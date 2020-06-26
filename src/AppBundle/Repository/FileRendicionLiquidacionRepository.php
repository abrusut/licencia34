<?php

namespace AppBundle\Repository;

use AppBundle\Entity\FileRendicionLiquidacion;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

/**
 * FileRendicionLiquidacionRepository
 *
 */
final class FileRendicionLiquidacionRepository
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
        $this->repository = $entityManager->getRepository(FileRendicionLiquidacion::class);
        $this->entityManager = $entityManager;
    }
    public function save(FileRendicionLiquidacion $fileRendicionLiquidacion)
    {
        $this->entityManager->persist($fileRendicionLiquidacion);
        $this->entityManager->flush();
    }    

    public function findById($id){
        return $this->repository->find($id);
    }

    public function findFileRendicionLiquidacionByNombreOriginalArchivo($nombreOriginalArchivo){
        return $this->repository->findBy(array(
            'nombreOriginalArchivo' => $nombreOriginalArchivo            
            )
        );
    }
}


?>