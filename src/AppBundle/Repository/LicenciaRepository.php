<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Comprobante;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Licencia;

/**
 * LicenciaRepository
 *
 */
final class LicenciaRepository
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
        $this->repository = $entityManager->getRepository(Licencia::class);
        $this->entityManager = $entityManager;
    }
    public function persist(Licencia $licencia)
    {
        $this->entityManager->persist($licencia);
    }    

    public function save(Licencia $licencia)
    {
        $this->entityManager->persist($licencia);
        $this->entityManager->flush();
    }    

    public function findById($id){
        return $this->repository->find($id);
    }

    public function getQueryByPersonaAndTipoLicencia($persona, $tipoLicencia){
        $parameters = array(         
            'numeroDocumento' => $persona->getNumeroDocumento(),
            'sexo' => $persona->getSexo(),
            'tipoDocumento'  => $persona->getTipoDocumento(),
            'tipoLicencia' => $tipoLicencia
        );

        $dql = "SELECT l 
                FROM AppBundle\Entity\Licencia l
                JOIN l.persona p
                WHERE p.numeroDocumento = :numeroDocumento
                and   p.sexo = :sexo
                and   p.tipoDocumento = :tipoDocumento
                and   l.tipoLicencia = :tipoLicencia
                order by l.id desc";

        return $this
                    ->entityManager
                    ->createQuery($dql)
                    ->setParameters($parameters);                    

    }
    public function findByPersonaAndTipoLicencia($persona, $tipoLicencia){
        $parameters = array(         
            'numeroDocumento' => $persona->getNumeroDocumento(),
            'sexo' => $persona->getSexo(),
            'tipoDocumento'  => $persona->getTipoDocumento(),
            'tipoLicencia' => $tipoLicencia
        );

        $dql = "SELECT l 
                FROM AppBundle\Entity\Licencia l
                JOIN l.persona p
                WHERE p.numeroDocumento = :numeroDocumento
                and   p.sexo = :sexo
                and   p.tipoDocumento = :tipoDocumento
                and   l.tipoLicencia = :tipoLicencia";

        return $this
                    ->entityManager
                    ->createQuery($dql)
                    ->setParameters($parameters)
                    ->getResult();
    }
    

    public function findByComprobanteId($idComprobante){
        return $this
            ->findOneByComprobante($idComprobante);
    }

    public function getTotalLicenciasImpagas(){
        $parameters = array(            
        );

        $dql = "SELECT count(l) 
                FROM AppBundle\Entity\Licencia l
                JOIN l.comprobante c
                WHERE c.fechaCobro is null and c.monto != 0";

        return $this
                    ->entityManager
                    ->createQuery($dql)
                    ->setParameters($parameters)
                    ->getSingleScalarResult();

           
    }

    public function getTotalLicenciasGratuitas(){
        $parameters = array(            
        );

        $dql = "SELECT count(l) 
                FROM AppBundle\Entity\Licencia l
                JOIN l.comprobante c
                WHERE c.monto = 0";

        return $this
                    ->entityManager
                    ->createQuery($dql)
                    ->setParameters($parameters)
                    ->getSingleScalarResult();
    }

    public function getTotalLicenciasPagas(){
        $parameters = array(            
        );

        $dql = "SELECT count(l) 
                FROM AppBundle\Entity\Licencia l
                JOIN l.comprobante c
                WHERE c.fechaCobro is not null and c.monto != 0";

        return $this
                    ->entityManager
                    ->createQuery($dql)
                    ->setParameters($parameters)
                    ->getSingleScalarResult();

           
    }

    public function getTotalArancelesCobrados(){
        $parameters = array(            
        );

        $dql = "SELECT sum(c.monto) 
                FROM AppBundle\Entity\Licencia l                
                JOIN l.comprobante c";

        return $this
                    ->entityManager
                    ->createQuery($dql)
                    ->setParameters($parameters)
                    ->getSingleScalarResult();

           
    }

}


?>