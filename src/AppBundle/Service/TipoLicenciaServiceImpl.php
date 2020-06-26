<?php

namespace AppBundle\Service;

use AppBundle\Entity\Provincia;
use AppBundle\Entity\TipoLicencia;
use AppBundle\Repository\TipoLicenciaRepository;
use Psr\Log\LoggerInterface;

class TipoLicenciaServiceImpl {
    
    private $logger;
    private $tipoLicenciaRepository;

    public function __construct(LoggerInterface $logger, 
            TipoLicenciaRepository $tipoLicenciaRepository )
    {
        $this->logger = $logger;
        $this->tipoLicenciaRepository = $tipoLicenciaRepository;
    }

    /**
     * @param AppBundle\Entity\TipoLicencia     
     * @return void
     */  
    public function save(TipoLicencia $tipoLicencia){
        return $this->tipoLicenciaRepository->save($tipoLicencia);
    }

    public function findById($id){
        $this->logger->info("Buscando TipoLicencia por id ".$id);
        return $this->tipoLicenciaRepository->findById($id);       
    }

    public function findTiposLicenciaForProvincia(Provincia $provincia){
        $this->logger->info("Buscando Tipos de Licencia para provincia id ".$provincia->getId());
        $tiposLicencia = $this->tipoLicenciaRepository->findAll();

        $tiposLicenciaResult = array();
        /** @var TipoLicencia $tipoLicencia */
        foreach($tiposLicencia as $tipoLicencia){
            if(!is_null($tipoLicencia) && 
                !is_null($tipoLicencia->getAplicaEnProvincia())){

                if($provincia->isSantaFe() && 
                    $tipoLicencia->getAplicaEnProvincia() == TipoLicencia::$SantaFe){
                        array_push($tiposLicenciaResult, $tipoLicencia);
                }

                if(!$provincia->isSantaFe() && 
                    ($tipoLicencia->getAplicaEnProvincia() == TipoLicencia::$Todas 
                        || is_null($tipoLicencia->getAplicaEnProvincia()))){
                        array_push($tiposLicenciaResult, $tipoLicencia);
                }
            }

            // Si el tipo de licencia de de Jubilado o Femenino, va para todas las provincias
            if(!is_null($tipoLicencia->getGeneroJubilado()) 
                && ( ($tipoLicencia->getGeneroJubilado() == 'j' || $tipoLicencia->getGeneroJubilado() == 'J' ) ||
                    ($tipoLicencia->getGeneroJubilado() == 'f' || $tipoLicencia->getGeneroJubilado() == 'f' )
                )){
                    if(!in_array($tipoLicencia,$tiposLicenciaResult)){
                        array_push($tiposLicenciaResult, $tipoLicencia);
                    }
            }
        }
        return $tiposLicenciaResult;
    }
}

?>