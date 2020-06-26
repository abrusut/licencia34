<?php
namespace AppBundle\Service;

use AppBundle\Entity\FileRendicionLiquidacion;
use AppBundle\Entity\Liquidacion;
use AppBundle\Entity\Rendicion;
use AppBundle\Repository\FileRendicionLiquidacionRepository;
use AppBundle\Repository\LiquidacionRepository;
use AppBundle\Repository\RendicionRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\Exception\Exception;
use TangoMan\CSVReaderBundle\Service\CSVLine;
use TangoMan\CSVReaderBundle\Service\CSVReaderService;


class FileCsvReaderServiceImpl
{

    private $logger;
    private $fileRendicionLiquidacionRepository;
    private $rendicionRepository;
    private $liquidacionRepository;
    private $csvReaderService;
    private $container;

    public function __construct(
        LoggerInterface $logger,
        FileRendicionLiquidacionRepository $fileRendicionLiquidacionRepository,
        RendicionRepository $rendicionRepository,
        LiquidacionRepository $liquidacionRepository,
        CSVReaderService $csvReaderService,
        ContainerInterface $container
    ) {
        $this->logger = $logger;
        $this->rendicionRepository = $rendicionRepository;
        $this->liquidacionRepository = $liquidacionRepository;
        $this->csvReaderService = $csvReaderService;
        $this->container = $container;
    }


    public function readCsvFile($file, FileRendicionLiquidacion $fileRendicionLiquidacion)
    {        
        $objectToSave = null;
        $nombreOriginal = substr($fileRendicionLiquidacion->getNombreOriginalArchivo(), 0, 2);
        $procesoRendiciones=false;
        $procesoLiquidaciones=false;
        $arrayLineasPersist=array();
        switch ($nombreOriginal) {
            case 'RE':
                $this->logger->info("FileCsvReaderServiceImpl->readCsvFile,Se detectan RENDICIONES ");
                $objectToSave = new Rendicion();
                $procesoRendiciones=true;                
                break;

            case 'LI':
                $this->logger->info("FileCsvReaderServiceImpl->readCsvFile,Se detectan LIQUIDACIONES ");
                $objectToSave = new Liquidacion();       
                $procesoLiquidaciones = true;
                break;
            default:
                # code...
                break;
        }        
        // File check
        $count = 0;
        $rendicionesOLiquidacionesGuardadas = array();
        if (is_file($file)) {

            $delimiter = $this->container->getParameter('app.read.csv.delimiter');
            if(is_null($delimiter)){
                $delimiter = ',';
            }

            $addExtraEmptyLine = $this->container->getParameter('app.read.csv.add.empty.line');
            $head = $this->createHeader($objectToSave);
            if($addExtraEmptyLine){
                array_push($head,'');
            }

            // Init reader service            
            $this->csvReaderService->init($file, $head, $delimiter);

            $this->logger->info("FileCsvReaderServiceImpl->readCsvFile, Voy a leer linea a linea");
            try{
                while (false !== ($linea = $this->csvReaderService->readLine())) {
                    if (!is_null($linea)) {
                        $count++;                    
                        $this->loguearLinea($linea,$head);    
                        array_push($arrayLineasPersist,$linea);                                    
                    }
                }
            }catch(Exception $e){
                $this->logger->error("FileCsvReaderServiceImpl->readCsvFile,Error Leyendo registros ".$e->getMessage());
                throw $e;                
            }catch (\Exception $e) {
                $this->logger->error("FileCsvReaderServiceImpl->readCsvFile,Error Leyendo registros ".$e->getMessage());                
                throw $e;
            }

            try{
                if($procesoLiquidaciones){       
                    $this->logger->info("FileCsvReaderServiceImpl->readCsvFile, Voy a Generar y Persistir Liquidaciones" );          
                    foreach ($arrayLineasPersist as $key => $value) {                    
                        $objectToSave = new Liquidacion(); 
                        $objectToSave->bind($value, $head);
                        $objectToSave->setFileRendicionLiquidacion($fileRendicionLiquidacion);
                        $this->liquidacionRepository->persist($objectToSave);
                        array_push($rendicionesOLiquidacionesGuardadas, $objectToSave);
                    }  
                    $this->logger->info("FileCsvReaderServiceImpl->readCsvFile, Cantidad de Liquidaciones a Grabar ".$registrosGuardados );                              
                }
            }catch(Exception $e){
                $this->logger->error("FileCsvReaderServiceImpl->readCsvFile,Error Generando y Guardando registros de Liquidaciones ".$e->getMessage());
                throw $e;
            }catch (\Exception $e) {
                $this->logger->error("FileCsvReaderServiceImpl->readCsvFile,Error Generando y Guardando registros de Liquidaciones ".$e->getMessage());
                throw $e;
            }

            try{
                if($procesoRendiciones){   
                    $this->logger->info("FileCsvReaderServiceImpl->readCsvFile, Voy a Generar y Persistir Rendiciones" );          
                    foreach ($arrayLineasPersist as $key => $value) {                       
                        $objectToSave = new Rendicion();
                        $objectToSave->bind($value, $head);    
                        $objectToSave->setFileRendicionLiquidacion($fileRendicionLiquidacion);     
                        $this->rendicionRepository->persist($objectToSave);
                        array_push($rendicionesOLiquidacionesGuardadas, $objectToSave);
                    }
                    $this->logger->info("FileCsvReaderServiceImpl->readCsvFile, Cantidad de Rendiciones a Grabar ".count($rendicionesOLiquidacionesGuardadas) );                           
                }
            }catch(Exception $e){
                $this->logger->error("FileCsvReaderServiceImpl->readCsvFile,Error Generando y Guardando registros de Rendiciones ".$e->getMessage());
                throw $e;
            }catch (\Exception $e) {
                $this->logger->error("FileCsvReaderServiceImpl->readCsvFile,Error Generando y Guardando registros de Rendiciones ".$e->getMessage());
                throw $e;
            }
           
        }else{
            $this->logger->info("FileCsvReaderServiceImpl->readCsvFile->readCsvFile, el archivo no tiene el formato esperado ");
        }

        $this->logger->info("FileCsvReaderServiceImpl->readCsvFile,Cantidad de Registros leidos " . $count. " cant Persistidos: ".count($rendicionesOLiquidacionesGuardadas));            
        return $rendicionesOLiquidacionesGuardadas;
    }

    private function createHeader($objectToSave){
        $this->logger->info("FileCsvReaderServiceImpl->readCsvFile,Armo la Cabecera");
        $head = array();
        if($objectToSave instanceof Rendicion){            
            $head = $this->container->getParameter('app.read.csv.head.rendicion');
        }

        if($objectToSave instanceof Liquidacion){            
            $head = $this->container->getParameter('app.read.csv.head.liquidacion');
        }

        $this->loguearCabecera($head);
        return $head;
    }

    private function loguearCabecera($head)
    {        
        foreach ($head as $key => $value) {
            $this->logger->info("FileCsvReaderServiceImpl->loguearCabecera, key:" . $value  . " value: " . $value);
        }
    }

    private function loguearLinea(CSVLine $linea, $head)
    {
        $count = count($linea);
        foreach ($head as $key => $value) {
            $this->logger->info("FileCsvReaderServiceImpl->loguearLinea, key:" . $value  . " value: " . $linea->get($value));
        }
    }
}
