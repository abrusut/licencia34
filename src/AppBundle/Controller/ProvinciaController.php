<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Persona;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Form\PersonaType;
use AppBundle\Service\JsonServiceImpl;
use AppBundle\Service\ProvinciaServiceImpl;
use AppBundle\Service\TipoLicenciaServiceImpl;
use AppBundle\Entity\Provincia;

class ProvinciaController extends Controller
{    
    public function findByAction(Request $request) {
        $this->get('logger')->info("Entro en ProvinciaController findByAction ");

        $data = json_decode($request->getContent());
        
        /** @var ProvinciaServiceImpl $provinciaService */
        $provinciaService = $this->get('provincia_service');
        $provincia = $provinciaService
                            ->findByProvinciaIdAndProvinciaNombre($data->provinciaId,
                                                                            $data->provinciaNombre);

        /** @var JsonServiceImpl $jsonService */
        $jsonService = $this->get('json_service');          
        $jsonService->setArrayIgnoredAttributes(array('licencias'));

        $provinciaJson = $jsonService->transformToJson($provincia);
                     

        $this->get('logger')->info("Provincia: ".$provinciaJson);  

        return new Response($provinciaJson,Response::HTTP_OK, array('content-type'=> 'application/json'));
    }

    public function findTiposLicenciaForProvinciaAction(Request $request) {
        $this->get('logger')->info("Entro en ProvinciaController findTipoLicenciaForProvinciaAction ");

        $data = json_decode($request->getContent());

        $provincia = new Provincia();
        $provincia = $provincia->copyValues(json_decode($data->provincia));

        /** @var TipoLicenciaServiceImpl $tipoLicenciaService */
        $tipoLicenciaService = $this->get('tipo_licencia_service');
        $tiposLicencia = $tipoLicenciaService
                            ->findTiposLicenciaForProvincia($provincia);

        /** @var JsonServiceImpl $jsonService */
        $jsonService = $this->get('json_service');          
        //$jsonService->setArrayIgnoredAttributes(array('licencias'));

        $tiposLicenciaJson = $jsonService->transformToJson($tiposLicencia);
                     

        $this->get('logger')->info("Tipos Licencia Encontrados  : ".$tiposLicenciaJson);  

        return new Response($tiposLicenciaJson,Response::HTTP_OK, array('content-type'=> 'application/json'));
    }

    
}
