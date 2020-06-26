<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Licencia;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\LicenciaType;
use AppBundle\Service\LicenciaServiceImpl;
use AppBundle\Exception\SimpleMessageException;
use AppBundle\Service\BoletaServiceImpl;
use AppBundle\Service\ComprobanteServiceImpl;
use AppBundle\Service\Barcode\Barcode;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Twig\BarcodeTwigExtension;

class BoletaPagoController extends Controller
{
    public function imprimirHtmlAction(Request $request, $licenciaId)
    {
        $this->get('logger')->info("BoletaPagoController, imprimirHtmlAction, licencia " . $licenciaId);

        $impresion = $request->query->get('impresion');

        $idLicencia = urldecode($licenciaId);
        /** @var LicenciaServiceImpl $licenciaService */
        $licenciaService = $this->get('licencia_service');

        $licencia = $licenciaService->findById($idLicencia);

        /** @var Barcode $barcodeService */
        $barcodeService = $this->get('barcode_service');

        return $this->render(
            'AppBundle:Licencia:boleta.pago.pdf.html.twig',
            array('licencia' => $licencia,
                    'impresion' => $impresion)
        );
    }

    public function imprimirByComprobanteAction(Request $request, $id)
    {
        error_reporting(E_ERROR); 
        $this->get('logger')->info("BoletaPagoController, imprimirByComprobante, comprobante " . $id);

        /** @var LicenciaServiceImpl $licenciaService */
        $licenciaService = $this->get('licencia_service');

        $licencia = $licenciaService->findByComprobanteId($id);
        return $this->imprimirAction($request,$licencia->getId());
    }

    public function imprimirAction(Request $request, $licenciaId)
    {      
        error_reporting(E_ERROR); 
        $this->get('logger')->info("BoletaPagoController, imprimirAction, licencia " . $licenciaId);

        $idLicencia = urldecode($licenciaId);
        /** @var LicenciaServiceImpl $licenciaService */
        $licenciaService = $this->get('licencia_service');

        /** @var Licencia $licencia */
        $licencia = $licenciaService->findById($idLicencia);            
        
        switch ($licencia->getStringTipoLicenciaCazaOPesca()) {
            case 'caza':
                $leyNumero = Licencia::$LEY_CAZA;
                break;
            
            case 'pesca':
                $leyNumero = Licencia::$LEY_PESCA;
                break;

            default:
                $leyNumero = "";     
                break;
        }
        


        $pdf = $this->container->get("white_october.tcpdf")->create('', 
                                    PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetAuthor('Ministerio de la Producción');
        $pdf->SetTitle('Emisión de Boletas Licencias Caza y Pesca');
        $pdf->SetSubject($licencia->getTipoLicencia()->getDescripcion());
        $pdf->SetKeywords($licencia->getTipoLicencia()->getDescripcion());
        $pdf->setFontSubsetting(true);
        $pdf->SetFont('helvetica', '', 11, '', true);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);        
        $pdf->AddPage();       


        $options = array(
            'code'   => $licencia->getComprobante()->getNumeroCodigoBarra(),
            'type'   => 'c128',
            'format' => 'png',
            'width'  => 10,
            'height' => 10,
            'color'  => array(0, 0,0)
        );
        
        $barcode =
            $this->get('sgk_barcode.generator')->generate($options);

        $savePath = $this->container->getParameter('barcode_directory');
        $fileName = 'barcode.png';
    
        file_put_contents($savePath.$fileName, base64_decode($barcode));    


        // El HTML Tiene los datos de la licencia
        $html = $this->renderView('AppBundle:Licencia:boleta.pago.pdf.html.twig',
                                    array('licencia' => $licencia,
                                            'leyNumero' => $leyNumero));        
        $pdf->writeHTML($html, true, false, true, false, 'J');
        
                       
        $pdf->Output($licencia->getTipoLicencia()->getDescripcion().'.pdf', 'I');        
    }

   
}
