<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Provincia;
use AppBundle\Service\JsonServiceImpl;
use AppBundle\Service\ProvinciaServiceImpl;
use AppBundle\Service\TipoLicenciaServiceImpl;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProvinciaController extends Controller
{
    /**
     * @var ProvinciaServiceImpl
     */
    private $provinciaServiceImpl;
    /**
     * @var JsonServiceImpl
     */
    private $jsonServiceImpl;
    /**
     * @var TipoLicenciaServiceImpl
     */
    private $tipoLicenciaServiceImpl;
    
    public function __construct(ProvinciaServiceImpl $provinciaServiceImpl,
                                TipoLicenciaServiceImpl $tipoLicenciaServiceImpl,
                                JsonServiceImpl $jsonServiceImpl)
    {
        $this->provinciaServiceImpl = $provinciaServiceImpl;
        $this->jsonServiceImpl = $jsonServiceImpl;
        $this->tipoLicenciaServiceImpl = $tipoLicenciaServiceImpl;
    }
    
    public function findByAction(Request $request) {
        $this->get('logger')->info("Entro en ProvinciaController findByAction ");

        $data = json_decode($request->getContent());
        
        /** @var ProvinciaServiceImpl $provinciaService */
        $provincia = $this->provinciaServiceImpl
                            ->findByProvinciaIdAndProvinciaNombre($data->provinciaId,
                                                                            $data->provinciaNombre);

        /** @var JsonServiceImpl $jsonService */
        $this->jsonServiceImpl->setArrayIgnoredAttributes(array('licencias'));

        $provinciaJson = $this->jsonServiceImpl->transformToJson($provincia);
                     

        $this->get('logger')->info("Provincia: ".$provinciaJson);  

        return new Response($provinciaJson,Response::HTTP_OK, array('content-type'=> 'application/json'));
    }

    public function findTiposLicenciaForProvinciaAction(Request $request) {
        $this->get('logger')->info("Entro en ProvinciaController findTipoLicenciaForProvinciaAction ");

        $data = json_decode($request->getContent());

        $provincia = new Provincia();
        $provincia = $provincia->copyValues(json_decode($data->provincia));

        /** @var TipoLicenciaServiceImpl $tipoLicenciaService */
        $tiposLicencia = $this->tipoLicenciaServiceImpl
                            ->findTiposLicenciaForProvincia($provincia);

        /** @var JsonServiceImpl $jsonService */

        $tiposLicenciaJson = $this->jsonServiceImpl->transformToJson($tiposLicencia);
                     

        $this->get('logger')->info("Tipos Licencia Encontrados  : ".$tiposLicenciaJson);  

        return new Response($tiposLicenciaJson,Response::HTTP_OK, array('content-type'=> 'application/json'));
    }

    
}
