<?php

namespace AppBundle\Controller;

use AppBundle\Service\JsonServiceImpl;
use AppBundle\Service\PersonaServiceImpl;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PersonaRestController extends Controller
{
    /**
     * @var PersonaServiceImpl
     */
    private $personaServiceImpl;
    /**
     * @var JsonServiceImpl
     */
    private $jsonServiceImpl;
    
    public function __construct(PersonaServiceImpl $personaServiceImpl,
                                JsonServiceImpl $jsonServiceImpl)
    {
        $this->personaServiceImpl = $personaServiceImpl;
        $this->jsonServiceImpl = $jsonServiceImpl;
    }
    
    public function findByAction(Request $request) {
        
        $this->get('logger')->info("Entro en PersonaRestController findByAction ");
    
        $data = json_decode($request->getContent());
        
        $persona = $this->personaServiceImpl
                            ->findBySexoAndTipoDocumentoAndNumeroDocumento($data->sexo,
                                $data->tipoDocumento,
                                $data->numeroDocumento);

        /** @var JsonServiceImpl $jsonService */
       
       $this->jsonServiceImpl->setArrayIgnoredAttributes(array('licencias'));

        $personaJson = $this->jsonServiceImpl->transformToJson($persona);


        $this->get('logger')->info("Devuelvo PersonaJson");

        return new Response($personaJson,Response::HTTP_OK, array('content-type'=> 'application/json'));
    }
}
