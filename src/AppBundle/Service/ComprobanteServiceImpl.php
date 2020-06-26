<?php
namespace AppBundle\Service;

use AppBundle\Entity\Comprobante;
use AppBundle\Entity\Rendicion;
use AppBundle\Entity\TipoLicencia;

use AppBundle\Repository\ComprobanteRepository;
use Psr\Log\LoggerInterface;

class ComprobanteServiceImpl {

    private $comprobanteRepository;
    private $logger;
    private $tipoLicenciaService;
    private $fileRendicionLiquidacionService;

    /** @var RendicionServiceImpl $rendicionService */
    private $rendicionService;

    public function __construct(LoggerInterface $logger, ComprobanteRepository $comprobanteRepository,
                TipoLicenciaServiceImpl $tipoLicenciaService,
                FileRendicionLiquidacionServiceImpl $fileRendicionLiquidacionService,
                RendicionServiceImpl $rendicionService)
    {
        $this->comprobanteRepository = $comprobanteRepository;
        $this->logger = $logger;
        $this->tipoLicenciaService = $tipoLicenciaService;
        $this->fileRendicionLiquidacionService = $fileRendicionLiquidacionService;
        $this->rendicionService = $rendicionService;
    }
    
    public function procesarPagos($rendicionesOLiquidacionesGuardadas,$fileRendicionLiquidacion)
    {         
        $cantidadPagosProcesados = 0;       
        foreach ($rendicionesOLiquidacionesGuardadas as $object) {
            if(!is_null($object) 
                && $object instanceof Rendicion){
                
                /** @var Rendicion $object */
                $this->logger->info("Procesando Rendicion Codigo Barra:".$object->getCodigoBarraCliente());                                

                if(!is_null($object->getCodigoBarraCliente()) && !empty($object->getCodigoBarraCliente()))
                {
                    /** @var Comprobante $comprobante */
                    $comprobante = $this
                                        ->comprobanteRepository
                                        ->findOneByNumeroCodigoBarra($object->getCodigoBarraCliente());
                    if(!is_null($comprobante)){                                                                                                                                                                                                                                 
                        $comprobante->setFechaCobro($object->getFechaCobro());    
                        $comprobante->setFileRendicionLiquidacion($fileRendicionLiquidacion);
                        $this->comprobanteRepository->persist($comprobante);                                   
                        $cantidadPagosProcesados++;
                    }
                }
            }
        }                
        return $cantidadPagosProcesados;
    }

    public function generarComprobante(TipoLicencia $tipoLicencia){
        $comprobante = new Comprobante();       
        $comprobante->setMonto($tipoLicencia->getArancel());
        $comprobante->setClienteSap($tipoLicencia->getClienteSap());        
        $comprobante->setLetraServicio($tipoLicencia->getLetraServicio());                        
        $comprobante->setNumeroCodigoBarra(null);

        //Primer Vencimiento
        if(!is_null($tipoLicencia->getDiasPrimerVencimiento()) && $tipoLicencia->getDiasPrimerVencimiento()>0)
        {
            $fechaPrimerVencimiento = new \DateTime();
            $fechaPrimerVencimiento->add(new \DateInterval('P'.$tipoLicencia->getDiasPrimerVencimiento().'D'));
            $comprobante->setPrimerVencimiento($fechaPrimerVencimiento);
            $recargoPrimerVencimiento = round(($tipoLicencia->getArancel() * $tipoLicencia->getPorcentajeRecargoPrimerVencimiento())/100,2);
            $comprobante->setRecargoPrimerVencimiento($recargoPrimerVencimiento);
        }

        //Segundo Vencimiento
        if(!is_null($tipoLicencia->getDiasSegundoVencimiento()) && $tipoLicencia->getDiasSegundoVencimiento()>0)
        {
            if( !is_null($comprobante->getPrimerVencimiento())){
                $fechaSegundoVencimiento = (clone $comprobante->getPrimerVencimiento());
            }else{
                $fechaSegundoVencimiento = new \DateTime();
            }
            
            $fechaSegundoVencimiento->add(new \DateInterval('P'.$tipoLicencia->getDiasSegundoVencimiento().'D'));
            $comprobante->setSegundoVencimiento($fechaSegundoVencimiento);
            
            /**  No existe lugar para un segundo recargo
             *$recargoSegundoVencimiento = round(($tipoLicencia->getArancel() * $tipoLicencia->getPorcentajeRecargoSegundoVencimiento())/100,2);
             *  $comprobante->setRecargoSegundoVencimiento($recargoSegundoVencimiento);
            */
        }
        

        return $comprobante;
    }

     /**
     * @param AppBundle\Entity\Comprobante     
     * @return void
     */  
    public function save(Comprobante $comprobante){
        return $this->comprobanteRepository->save($comprobante);
    }

    public function findById($id){
        $this->logger->info("Buscando comprobante por id ".$id);
        return $this->comprobanteRepository->findById($id);       
    }
}



?>