<?php

namespace AppBundle\Service;

use AppBundle\Entity\Licencia;
use AppBundle\Entity\Persona;
use AppBundle\Entity\TipoLicencia;
use AppBundle\Exception\SimpleMessageException;
use AppBundle\Repository\LicenciaRepository;
use Psr\Log\LoggerInterface;

class LicenciaServiceImpl {

    private $logger;
    private $personaService;
    private $tipoLicenciaService;

    /** @var LicenciaRepository $licenciaRepository */
    private $licenciaRepository;
    private $comprobanteService;
    private $boletaService;

    public function __construct(LoggerInterface $logger,
                                PersonaServiceImpl $personaService,
                                TipoLicenciaServiceImpl $tipoLicenciaService,
                                LicenciaRepository $licenciaRepository,
                                ComprobanteServiceImpl $comprobanteService,
                                BoletaServiceImpl $boletaService )
    {
        $this->logger = $logger;
        $this->personaService = $personaService;    
        $this->licenciaRepository = $licenciaRepository;    
        $this->tipoLicenciaService = $tipoLicenciaService;       
        $this->comprobanteService = $comprobanteService;
        $this->boletaService = $boletaService;
    }   

     /**
     * @param AppBundle\Entity\Licencia     
     * @return void
     */  
    public function persist(Licencia $licencia){
        return $this->licenciaRepository->persist($licencia);
    }

    /**
     * @param AppBundle\Entity\Licencia     
     * @return void
     */  
    public function save(Licencia $licencia){
        return $this->licenciaRepository->save($licencia);
    }

    public function findById($id){
        $this->logger->info("Buscando Licencia por id ".$id);
        return $this->licenciaRepository->findById($id);       
    }

    public function findByPersonaAndTipoLicencia($persona, $tipoLicencia){
        $this->logger->info("Buscando Licencia por Persona y tipo licencia".$persona." tipolicencia ".$tipoLicencia );
        return $this->licenciaRepository->findByPersonaAndTipoLicencia($persona,$tipoLicencia);       
    }

    public function getQueryByPersonaAndTipoLicencia($persona, $tipoLicencia){
        $this->logger->info("GetQuery Licencia por Persona y tipo licencia".$persona." tipolicencia ".$tipoLicencia );
        return $this->licenciaRepository->getQueryByPersonaAndTipoLicencia($persona,$tipoLicencia);       
    }
    public function getTotalLicenciasImpagas(){
        $this->logger->info("Buscando Total Licencias Impagas");
        return $this->licenciaRepository->getTotalLicenciasImpagas();       
    }    

    public function getTotalLicenciasPagas()
    {
        $this->logger->info("Buscando Total Licencias Pagas");
        return $this->licenciaRepository->getTotalLicenciasPagas();       
    }    

    public function getTotalLicenciasGratuitas()
    {
        $this->logger->info("Buscando Total Licencias Gratuitas");
        return $this->licenciaRepository->getTotalLicenciasGratuitas();       
    }  
    

    public function getTotalArancelesCobrados()
    {
        $this->logger->info("Buscando Total Aranceles Cobrados");
        return $this->licenciaRepository->getTotalArancelesCobrados();       
    }    
    

    public function findByComprobanteId($idComprobante){
        $this->logger->info("Buscando Licencia por id Comprobante ".$idComprobante);
        return $this->licenciaRepository->findByComprobanteId($idComprobante);       
    }

    public function generarLicencia(Licencia $licencia){
        $this->validate($licencia);            

        // Actualizo la Persona (si existe previamente) con los datos enviados            
        $this->bindPersonaToLicencia($licencia);        
        
        // Creo el Comprobante
        $comprobante = $this->comprobanteService->generarComprobante($licencia->getTipoLicencia());        

        $licencia->setComprobante($comprobante);

        $licencia->configurarVigencia();                              
    }

    public function validate(Licencia $licencia){
        /** @var Persona $persona */
        $persona = $licencia->getPersona();

        /** @var TipoLicencia $tipoLicencia */
        $tipoLicencia = $licencia->getTipoLicencia();

        $generoJubiladoTipoLicencia = $tipoLicencia->getGeneroJubilado();

        $sexoCliente = $persona->getSexo();
        $isPersonaJubilado = $persona->getJubilado();

        if(!is_null($generoJubiladoTipoLicencia))
        {
            if($isPersonaJubilado && ($generoJubiladoTipoLicencia != 'j') ){
                throw new SimpleMessageException("Selecciona que es Jubilado, pero la licencia no es del mismo tipo");
            }

            if(!$isPersonaJubilado && ($generoJubiladoTipoLicencia == 'j') ){
                throw new SimpleMessageException("Selecciona que NO es Jubilado, pero la licencia que intenta sacar es para Jubilados");
            }

            if(!$isPersonaJubilado &&
                ($sexoCliente == 'f' && $generoJubiladoTipoLicencia != 'f'
                || ($sexoCliente == 'm' && $generoJubiladoTipoLicencia != 'm')) ){
                throw new SimpleMessageException("El tipo de licencia seleccionada no corresponde con el sexo cargado en la persona");
            }
        }
    }
    
    public function bindPersonaToLicencia(Licencia $licencia) {        
        // Saco los datos de la persona que vino en el request
        /** @var Persona $personaRequest */ 
        $personaRequest = $licencia->getPersona();
        
        // Si la licencia tiene ID de Persona, lo levanto para que Doctrine no intente
        // hacer Insert, y haga update
        if(!is_null($personaRequest) &&
            is_object($personaRequest) &&
            !is_null($personaRequest->getId()))
        {           
            // Obtengo la Persona desde la Base      
            /** @var Persona $persona */        
            $persona = $this->personaService->findById($personaRequest->getId());

            // SI existe actualizo los datos con los datos que viajaron en el request
            if(!is_null($persona)){
                $persona->copyValues($personaRequest); 
                if(!is_null($personaRequest->getLocalidad())){
                    $persona->setLocalidadOtraProvincia(null);
                }
                
                $licencia->setPersona($persona);
            }            
        }
    }

     

}

?>