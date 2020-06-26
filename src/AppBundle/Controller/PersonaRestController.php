<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Persona;
use AppBundle\Exception\SimpleMessageException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Form\PersonaType;
use AppBundle\Service\JsonServiceImpl;

class PersonaRestController extends Controller
{

    public function findByAction(Request $request) {
        
        $this->get('logger')->info("Entro en PersonaRestController findByAction ");
    
        $data = json_decode($request->getContent());
        $personaService = $this->get('persona_service');
        $persona = $personaService
                            ->findBySexoAndTipoDocumentoAndNumeroDocumento($data->sexo,
                                $data->tipoDocumento,
                                $data->numeroDocumento);

        /** @var JsonServiceImpl $jsonService */
        $jsonService = $this->get('json_service');
        $jsonService->setArrayIgnoredAttributes(array('licencias'));

        $personaJson = $jsonService->transformToJson($persona);


        $this->get('logger')->info("Devuelvo PersonaJson");

        return new Response($personaJson,Response::HTTP_OK, array('content-type'=> 'application/json'));
    }
}
