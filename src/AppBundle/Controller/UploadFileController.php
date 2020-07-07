<?php

namespace AppBundle\Controller;

use AppBundle\Entity\FileRendicionLiquidacion;
use AppBundle\Entity\Rendicion;
use AppBundle\Exception\SimpleMessageException;
use AppBundle\Service\ComprobanteServiceImpl;
use AppBundle\Service\FileCsvReaderServiceImpl;
use AppBundle\Service\FileUploaderServiceImpl;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\FileBag;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Rendicion controller.
 *
 * @Route("/upload")
 */
class UploadFileController extends Controller
{   

    /**     
     * Muestra el form para upload de archivos
     * @Route("/file", name="index_file_upload")
     * @Method("GET")
     */
    public function indexUploadFileAction(Request $request)
    {                
        return $this->render('AppBundle:Upload:nostg.upload.file.html.twig', array());
        //return $this->render('AppBundle:Upload:stg.upload.file.html.twig', array());
    }

    /**
     * Procesa el Upload de Rendiciones y Liquidaciones
     *
     * @Route("/file/process", name="process_file_upload")
     * @Method("POST")
     */
    public function rendicionLiquidacionProcessFileAction(Request $request)        
    {
        $this->get('logger')->info("UploadFileController->rendicionLiquidacionProcessFileAction, entro a procesar CSV ");

        if (!$request->isXmlHttpRequest()) {
            $this->get('logger')->info("UploadFileController->rendicionLiquidacionProcessFileAction, llamada no ajax ");
            return new JsonResponse(array('message' => 'No es posible enviar el formulario'), 400);
        }

        $this->get('logger')->info("UploadFileController->rendicionLiquidacionProcessFileAction, leo el archivo");
         
        //Lee el POST con el archivo enviado
         /** @var UploadedFile  $fileUploadedFile */
        $fileUploadedFile = $this
                                ->getUploadedFile($request);
        
        //Arma el nombre del arhivo para guarar
        $fileName = $this
                    ->generateUniqueFileName($fileUploadedFile->getClientOriginalName()).
                        "." .
                        $fileUploadedFile->getClientOriginalExtension();

        $this->get('logger')->info("UploadFileController->rendicionLiquidacionProcessFileAction, fileName ".$fileName);      
         
        $pathForUpload = $this->getParameter('app.path.upload.file.rendicion.liquidacion');

        $this->get('logger')->info("UploadFileController->rendicionLiquidacionProcessFileAction, pathForUpload ".$pathForUpload);      

        //Servicio que se encarga de subir el archivo al server
        /** @var FileUploaderServiceImpl $fileUploadService */
        $fileUploadService = $this->get('file_upload_service');     
        try {
            $em = $this->getDoctrine()->getManager();

            //Creo registro en file_rendicion_liquidacion con los datos del archivo leido
            $fileRendicionLiquidacion = $this->
                                        createFileRendicionLiquidacion($fileUploadedFile, $fileName,$pathForUpload );

            //Subo Archivo
            $achivoEnDisco = $fileUploadService
                                        ->upload( $pathForUpload,
                                            $fileUploadedFile,
                                            $fileName);            

            $this->get('logger')->info("UploadFileController->rendicionLiquidacionProcessFileAction, Archivo Subido: ".$achivoEnDisco);                                                  

            //Leo el Archivo y Guardo Rendiciones o Liquidaciones
            /** @var FileCsvReaderServiceImpl $fileCsvReaderServiceImpl */
            $fileCsvReaderServiceImpl = $this->get('file_csv_reader');
            $rendicionesOLiquidacionesGuardadas = 
                    $fileCsvReaderServiceImpl->readCsvFile($achivoEnDisco, $fileRendicionLiquidacion);
                        
             //Proceso levanto el Archivo Persistido, y marco pagado los comprobantes
             if(!is_null($rendicionesOLiquidacionesGuardadas) 
                && count($rendicionesOLiquidacionesGuardadas)>0
                && $rendicionesOLiquidacionesGuardadas[0] instanceof Rendicion)
                {
                    /** @var ComprobanteServiceImpl $comprobanteService */
                    $comprobanteService = $this->get('comprobante_service');
                    $cantidadPagosProcesados =
                            $comprobanteService
                                        ->procesarPagos($rendicionesOLiquidacionesGuardadas,
                                                        $fileRendicionLiquidacion);

                    if($cantidadPagosProcesados!=count($rendicionesOLiquidacionesGuardadas)){
                        $mensaje="La cantidad de Pagos procesados (rendiciones pagadas) ".$cantidadPagosProcesados .
                                ", no es igual a la cantidad de rendiciones subidas en el archivo ".count($rendicionesOLiquidacionesGuardadas);
                        throw new SimpleMessageException($mensaje, 500);                
                    }
                }

            //commit ALL transaction
            $fileRendicionLiquidacion->setProcesado(true);            
            $this->get('logger')->info("UploadFileController->rendicionLiquidacionProcessFileAction, EJECUTO FLUSH()");                                                  
            $em->flush();
        } catch(SimpleMessageException $sm){
            $this->get('logger')->error("UploadFileController->rendicionLiquidacionProcessFileAction, SimpleMessageException: ". $sm->getMessage());                                                  
            return new JsonResponse(array('message' => $sm->getMessage()), 500);
        } catch (FileException $e) {
            $this->get('logger')->error("UploadFileController->rendicionLiquidacionProcessFileAction, FileException: ". $e->getMessage());                                                  
            return new JsonResponse(array('message' => $e->getMessage()), 400);
        }catch (Exception $ex) {
            $this->get('logger')->error("UploadFileController->rendicionLiquidacionProcessFileAction, Exception : ". $ex->getMessage());                                                  
            return new JsonResponse(array('message' => $ex->getMessage()), 500);
        }catch (\Exception $ex) {
            $this->get('logger')->error("UploadFileController->rendicionLiquidacionProcessFileAction, \Exception : ". $ex->getMessage());                                                  
            return new JsonResponse(array('message' => $ex->getMessage()), 500);
        }
        
        $this->get('logger')->info("UploadFileController->rendicionLiquidacionProcessFileAction, Devuelvo Success 200 : cantidadRegistrosGuardados ".$cantidadPagosProcesados);
        
        return new JsonResponse(array('message' => 'Success!','cantidadRegistrosGuardados'=> $cantidadPagosProcesados), 200);        
               
    }

    /**
     * @return string
     */
    private function generateUniqueFileName($stringName)
    {        
        return sha1(md5(sha1(time().$stringName).time()));
    }    

    private function createFileRendicionLiquidacion($fileUploadedFile,$fileName,$pathForUpload){
        $this->get('logger')->info("UploadFileController->createFileRendicionLiquidacion,".$fileUploadedFile." fileName ".$fileName);
        $fileRendicionLiquidacion = new FileRendicionLiquidacion();
        $fileRendicionLiquidacion->bindValueFromFile($fileUploadedFile,$fileName,$pathForUpload );        
        $this->get('logger')->info("UploadFileController->createFileRendicionLiquidacion, FileRendicionLiquidacion Creado ".$fileRendicionLiquidacion);
        
        $validator = $this->get('validator');
        $errors = $validator->validate($fileRendicionLiquidacion);

        if (count($errors) > 0) {            
            $errorsString = (string) $errors;
            $this->get('logger')->info("UploadFileController->createFileRendicionLiquidacion, No supera validaciones: ".$errorsString);
            throw new SimpleMessageException("No supera validaciones ".$errorsString);            
        }

        $this->get('logger')->info("UploadFileController->createFileRendicionLiquidacion, Persisto el registro : ".$fileRendicionLiquidacion);
        $em = $this->getDoctrine()->getManager();
        $em->persist($fileRendicionLiquidacion);        

        return $fileRendicionLiquidacion;
    }

    private function getUploadedFile(Request $request){
         /** @var FileBag  $file */        
         $file = $request->files;  
         if(is_null($file)){
             $this->get('logger')->info("UploadFileController->getUploadedFile, file is null ");    
             return new JsonResponse(array('message' => 'No adjunta archivo'), 400);
         }
         
         /** @var UploadedFile  $fileUploadedFile */
         $fileUploadedFile = null;
         if(!is_null($file->all()) && is_array($file->all()) ){
             foreach ($file->all() as $key => $value) {
                 if($value instanceof UploadedFile){
                     $fileUploadedFile = $value;  
                     $this->get('logger')->info("UploadFileController->getUploadedFile, encuentro archivo en el POST enviado ");
                     break;  
                 }
             }
             
         }

         return $fileUploadedFile;
    }
}