<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Licencia;
use AppBundle\Exception\SimpleMessageException;
use AppBundle\Service\AtributoConfiguracionServiceImpl;
use AppBundle\Service\BoletaServiceImpl;
use AppBundle\Service\ComprobanteServiceImpl;
use AppBundle\Service\EncryptImpl;
use AppBundle\Service\LicenciaServiceImpl;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\GestionarLicenciaType;

class GestionarLicenciaController extends Controller
{
    /**
     * @var AtributoConfiguracionServiceImpl
     */
    private $atributoConfiguracionService;
    /**
     * @var LicenciaServiceImpl
     */
    private $licenciaService;
    /**
     * @var BoletaServiceImpl
     */
    private $boletaService;
    /**
     * @var ComprobanteServiceImpl
     */
    private $comprobanteService;
    
    
    /**
     * GestionarLicenciaController constructor.
     */
    public function __construct(AtributoConfiguracionServiceImpl $atributoConfiguracionService,
                                LicenciaServiceImpl $licenciaService,
                                BoletaServiceImpl $boletaService,
                                ComprobanteServiceImpl $comprobanteService
            )
    {
        $this->atributoConfiguracionService = $atributoConfiguracionService;
        $this->licenciaService = $licenciaService;
        $this->boletaService = $boletaService;
        $this->comprobanteService = $comprobanteService;
    }
    
    public function addAction(Request $request)
    {
        $this->get('logger')->info("GestionarLicenciaController->addAction");

        // Usuarios logueado no pueden cargar licencias
        $user = $this->get('security.token_storage')->getToken()->getUser();
        if(!is_null($user) && is_object($user)){
            $this->get('logger')->info("GestionarLicenciaController->addAction, Usuario Logueado No puede cargar Licencia");
            $this->container->get('session')->getFlashBag()->clear();
            $this->addFlash('default_message_error', 'Usuario Logueado No puede Cargar Una Licencia');
           return $this->redirectToRoute('login_success_home');
        }
        
         $terminosYCondiciones
            = $this->atributoConfiguracionService
                ->getAtributoConfiguracion('licencia_texto_terminoycondiciones');
        
        $ayudaGeneral
                = $this->atributoConfiguracionService
                    ->getAtributoConfiguracion('licencia_texto_ayudageneral');
        
        $licencia = new Licencia();
        $form = $this
            ->container
            ->get('form.factory')
            ->create('AppBundle\Form\GestionarLicenciaType', $licencia);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->get('logger')->info("GestionarLicenciaController, formulario enviado y validado OK..");
            $this->get('logger')->info(
                "GestionarLicenciaController, Proceso Licencia para Sexo;"
                    . $licencia->getPersona()->getSexo()
                    . " TipoDocumento: " . $licencia->getPersona()->getTipoDocumento()->getId()
                    . " NumeroDocumento: " . $licencia->getPersona()->getNumeroDocumento()
            );
            
            /** @var EncryptImpl $encryptService */
            // $encryptService = $this->get('encrypt_service');

            try {
                $this->licenciaService->generarLicencia($licencia);
                $this->licenciaService->save($licencia);
                // Arama el numero de la licencia
                $licencia->setearNumeroCompleto();
                $this->licenciaService->persist($licencia);
            } catch (\Doctrine\DBAL\DBALException $e) {
                $exceptionNumber = $e->getPrevious()->getCode();
                $exceptionMessage = $e->getMessage();
                $this->get('logger')->error("GestionarLicenciaController,ERROR " . $exceptionNumber . " message " . $exceptionMessage);
                return $this->render('Exception/errorDB.html.twig', array('errorCode' => $exceptionNumber, 'errorMessage' => $exceptionMessage));
            } catch (SimpleMessageException $sme) {
                $exceptionNumber = $sme->getCode();
                $exceptionMessage = $sme->getMessage();
                $this->get('logger')->error("GestionarLicenciaController,ERROR " . $exceptionNumber . " message " . $exceptionMessage);
                $this->addFlash('licenciaForm_message_error', 'La Licencia no pudo ser generada .' . $sme->getMessage());
                return $this->render(
                    'Licencia/alta.licencia.html.twig',
                    array(
                        'form' => $form->createView(),
                        'licencia' => $licencia,
                        'terminosYCondiciones' => $terminosYCondiciones,
                        'ayudaGeneral' => $ayudaGeneral
                    )
                );
            } catch (\RuntimeException $re) {
                $exceptionNumber = $re->getCode();
                $exceptionMessage = $re->getMessage();
                $this->get('logger')->error("GestionarLicenciaController,ERROR " . $exceptionNumber . " message " . $exceptionMessage);
                $this->addFlash('licenciaForm_message_error', 'La Licencia no pudo ser generada .' . $re->getMessage());
                return $this->render(
                    'Licencia/alta.licencia.html.twig',
                    array(
                        'form' => $form->createView(),
                        'licencia' => $licencia,
                        'terminosYCondiciones' => $terminosYCondiciones,
                        'ayudaGeneral' => $ayudaGeneral
                    )
                );
            }

            try {
                //para generar la boleta tiene que estar persistido el comprobante y la licencia
                $numeroCodigoBarra = $this->boletaService->generarCodigoBarras($licencia);

                $licencia->getComprobante()->setNumeroCodigoBarra($numeroCodigoBarra);

                // Actualizo el comprobante con el codigo de barras
                $this->comprobanteService->save($licencia->getComprobante());

                $this->get('logger')->info("GestionarLicenciaController, formulario PROCESADO OK.." . 'La Licencia ' . $licencia . ' ha sido creada correctamente.');
                $this->container->get('session')->getFlashBag()->clear();
                $this->addFlash('licenciaForm_message', 'La Licencia ' . $licencia . ' ha sido creada correctamente.');
            } catch (\Doctrine\DBAL\DBALException $e) {
                $exceptionNumber = $e->getPrevious()->getCode();
                $exceptionMessage = $e->getMessage();
                $this->get('logger')->error("GestionarLicenciaController,ERROR " . $exceptionNumber . " message " . $exceptionMessage);
                return $this->render('AppBundle:Exception:errorDB.html.twig', array('errorCode' => $exceptionNumber, 'errorMessage' => $exceptionMessage));
            } catch (SimpleMessageException $sme) {
                $exceptionNumber = $sme->getCode();
                $exceptionMessage = $sme->getMessage();
                $this->get('logger')->error("GestionarLicenciaController,ERROR " . $exceptionNumber . " message " . $exceptionMessage);
                $this->addFlash('licenciaForm_message_error', 'La Licencia no pudo ser generada .' . $sme->getMessage());
                return $this->render(
                    'Licencia/alta.licencia.html.twig',
                    array(
                        'form' => $form->createView(),
                        'licencia' => $licencia,
                        'terminosYCondiciones' => $terminosYCondiciones,
                        'ayudaGeneral' => $ayudaGeneral
                    )
                );
            } catch (\RuntimeException $re) {
                $exceptionNumber = $re->getCode();
                $exceptionMessage = $re->getMessage();
                $this->get('logger')->error("GestionarLicenciaController,ERROR " . $exceptionNumber . " message " . $exceptionMessage);
                $this->addFlash('licenciaForm_message_error', 'La Licencia no pudo ser generada .' . $re->getMessage());
                return $this->render(
                    'Licencia/alta.licencia.html.twig',
                    array(
                        'form' => $form->createView(),
                        'licencia' => $licencia,
                        'terminosYCondiciones' => $terminosYCondiciones,
                        'ayudaGeneral' => $ayudaGeneral
                    )
                );
            }

            $this->get('logger')->info("GestionarLicenciaController, Se Guardan todos los datos OK, redirijo a la impresion de la boleta");

            return $this->redirectToRoute(
                'licencia_generada_detalle',
                array(
                    'licenciaId' => $licencia->getId()                    
                )
            );
        }

        $this->get('logger')->info("GestionarLicenciaController, devuelvo formulario a la vista");
        return $this->render(
            'Licencia/alta.licencia.html.twig',
            array(
                'form' => $form->createView(),
                'licencia' => $licencia,
                'terminosYCondiciones' => $terminosYCondiciones,
                'ayudaGeneral' => $ayudaGeneral              
            )
        );
    }

    public function regenerarBoletaPagoYCodigoBarraAction(Request $request, $licenciaId, $readOnly)
    {
        $this->get('logger')->info("GestionarLicenciaController, regenerarBoletaPagoYCodigoBarraAction, licenciaId " . $licenciaId);

        $idLicencia = urldecode($licenciaId);

        /** @var Licencia $licencia */
        $licencia =  $this->licenciaService->findById($idLicencia);
               

        try {
            //para generar la boleta tiene que estar persistido el comprobante y la licencia
            $numeroCodigoBarra = $this->boletaService->generarCodigoBarras($licencia);

            $licencia->getComprobante()->setNumeroCodigoBarra($numeroCodigoBarra);

            // Actualizo el comprobante con el codigo de barras
            if(!$readOnly){
                $this->comprobanteService->save($licencia->getComprobante());
            }

            $this->get('logger')->info("GestionarLicenciaController, formulario PROCESADO OK.." . 'La Licencia ' . $licencia . ' ha sido creada correctamente.');
            $this->container->get('session')->getFlashBag()->clear();
            $this->addFlash('licenciaForm_message', 'La Licencia ' . $licencia . ' ha sido creada correctamente.');
        } catch (\Doctrine\DBAL\DBALException $e) {
            $exceptionNumber = $e->getPrevious()->getCode();
            $exceptionMessage = $e->getMessage();
            $this->get('logger')->error("GestionarLicenciaController,ERROR " . $exceptionNumber . " message " . $exceptionMessage);
            return $this->render('Exception/errorDB.html.twig', array('errorCode' => $exceptionNumber, 'errorMessage' => $exceptionMessage));
        } catch (SimpleMessageException $sme) {
            $exceptionNumber = $sme->getCode();
            $exceptionMessage = $sme->getMessage();
            $this->get('logger')->error("GestionarLicenciaController,ERROR " . $exceptionNumber . " message " . $exceptionMessage);
            return $this->render('Exception/errorDB.html.twig', array('errorCode' => $exceptionNumber, 'errorMessage' => $exceptionMessage));
        } catch (\RuntimeException $re) {
            $exceptionNumber = $re->getCode();
            $exceptionMessage = $re->getMessage();
            $this->get('logger')->error("GestionarLicenciaController,ERROR " . $exceptionNumber . " message " . $exceptionMessage);
            $this->addFlash('licenciaForm_message_error', 'La Licencia no pudo ser generada .' . $re->getMessage());
            $this->get('logger')->error("GestionarLicenciaController,ERROR " . $exceptionNumber . " message " . $exceptionMessage);
            return $this->render('Exception/errorDB.html.twig', array('errorCode' => $exceptionNumber, 'errorMessage' => $exceptionMessage));
        }

        $this->get('logger')->info("GestionarLicenciaController, regenerarBoletaPagoYCodigoBarraAction devuelvo formulario READONLY a la vista");
        return $this->render(
            'Licencia/licencia.generada.detail.html.twig',
            array(               
                'licencia' => $licencia,
                'urlBoletaPago' => $this->generateUrl(
                    'boleta_pago_imprimir',
                    array('licenciaId' => $licencia->getId())
                ),
                'readOnly' => $readOnly,
                'comprobante' => $licencia->getComprobante()
            )
        );
    }

    public function verLicenciaAction(Request $request, $licenciaId)
    {
        $this->get('logger')->info("GestionarLicenciaController, imprimirAction, licenciaId " . $licenciaId);

        $idLicencia = urldecode($licenciaId);

        $licencia = $this->licenciaService->findById($idLicencia);
        $persona = $licencia->getPersona();
        

        $this->get('logger')->info("GestionarLicenciaController, verLicenciaAction devuelvo formulario READONLY a la vista");
        return $this->render(
            'Licencia/licencia.generada.detail.html.twig',
            array(               
                'licencia' => $licencia,
                'urlBoletaPago' => $this->generateUrl(
                    'boleta_pago_imprimir',
                    array('licenciaId' => $licencia->getId())
                )
            )
        );
    }
}

