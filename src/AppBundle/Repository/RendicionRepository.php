<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Provincia;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Rendicion;

/**
 * RendicionRepository
 *
 */
final class RendicionRepository
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
        $this->repository = $entityManager->getRepository(Rendicion::class);
        $this->entityManager = $entityManager;
    }
    
    public function save(Rendicion $rendicion)
    {
        $this->entityManager->persist($rendicion);
        $this->entityManager->flush();
    }    

    public function persist(Rendicion $rendicion)
    {
        $this->entityManager->persist($rendicion);
    }

    public function flush()
    {
        $this->entityManager->flush();
    }


    public function findById($id){
        return $this->repository->find($id);
    }

    public function getRendicionesWhereFileProcesado($procesado){
        $parameters = array(
            'procesado' => $procesado
        );
        $dql = "SELECT r FROM AppBundle\Entity\Rendicion r,
                    AppBundle\Entity\FileRendicionLiquidacion  frl
                WHERE frl.procesado = :procesado";
        
        return $this
                    ->entityManager
                    ->createQuery($dql)
                    ->setParameters($parameters)
                    ->getResult();
    }
}


?>