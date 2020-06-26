<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Numerador;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Persona;

/**
 * PersonaRepository
 *
 */
final class PersonaRepository
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
        $this->repository = $entityManager->getRepository(Persona::class);
        $this->entityManager = $entityManager;
    }
    public function save(Persona $persona)
    {
        $this->entityManager->persist($persona);
        $this->entityManager->flush();
    }

    public function findById($id){
        return $this->repository->find($id);
    }
    public function findBySexoAndTipoDocumentoAndNumeroDocumento($sexo,$tipoDocumento,
            $numeroDocumento)
    {
        return $this->repository
            ->findOneBy(array(
                'sexo' => $sexo,
                'tipoDocumento' => $tipoDocumento,
                'numeroDocumento' => $numeroDocumento,
            ));
    }

    public function getTotalPersonas(){        
        return $this->entityManager->createQueryBuilder('p')
            ->select('count(p.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}


?>