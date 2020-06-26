<?php

namespace AppBundle\Repository;

use AppBundle\Entity\AtributoConfiguracion;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

/**
 * AtributoConfiguracionRepository
 *
 */
final class AtributoConfiguracionRepository
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
        $this->repository = $entityManager->getRepository(AtributoConfiguracion::class);
        $this->entityManager = $entityManager;
    }
    
    public function save(AtributoConfiguracion $atributoConfiguracion)
    {
        $this->entityManager->persist($atributoConfiguracion);
        $this->entityManager->flush();
    }
    
    public function findById($id){
        return $this->repository->find($id);
    }
    
    public function findAtributoConfiguracionByClave($clave){
        return $this->repository->findBy(array(
                'clave' => $clave,
                'fechaBaja' => null
            )
        );
    }
}


?>