<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\View\TwitterBootstrap3View;
use AppBundle\Form\ReimpresionLicenciaType;

/**
 * ReimpresionLicenciasController controller.
 *
 * @Route("/reimpresion")
 */
class ReimpresionLicenciasController extends Controller
{
    /**
     * @Route("/", name="reimpresion")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $session = $request->getSession();
        $filterForm = $this->createForm('AppBundle\Form\ReimpresionLicenciaType');

        // Reset filter
        if ($request->get('filter_action') == 'reset') {
            $session->remove('ReimpresionLicenciaType');
        }

        // Filter action  
        $pagerHtml ="";    
        $licencias = array();       
        if ($request->get('filter_action') == 'filter') {
            // Bind values from the request
            $filterForm->handleRequest($request);

            if ($filterForm->isValid()) {
                // Save filter to session
                $filterData = $filterForm->getData();
                $session->set('ReimpresionLicenciaType', $filterData);
               }

            $tipoLicencia=$filterData['tipoLicencia']; 
            $personaRequest=$filterData['persona'];
            
            
            if(!is_null($personaRequest)){
                    $sexo=$personaRequest->getSexo();
                    $tipoDocumento=$personaRequest->getTipoDocumento();
                    $numeroDocumento=$personaRequest->getNumeroDocumento();

                    $persona = $this->get('persona_service')
                       ->findBySexoAndTipoDocumentoAndNumeroDocumento(
                            $sexo,
                            $tipoDocumento,
                            $numeroDocumento
                        );
                    if(!is_null($persona)){
                            $query = $this->get('licencia_service')
                                ->getQueryByPersonaAndTipoLicencia($persona,$tipoLicencia);
                            list($licencias, $pagerHtml) = $this->paginator($query, $request);
                    }
            }
            
           
        }

               
        return $this->render('AppBundle:ReimpresionLicencias:index.html.twig', array(
            'licencias' => $licencias,
            'pagerHtml' => $pagerHtml,
            'filterForm' => $filterForm->createView()            
        ));
    }  

    /**
    * Get results from paginator and get paginator view.
    *
    */
    protected function paginator($query, Request $request)
    {        
        // Paginator
        $adapter = new DoctrineORMAdapter($query);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage($request->get('pcg_show' , 10));

        try {
            $pagerfanta->setCurrentPage($request->get('pcg_page', 1));
        } catch (\Pagerfanta\Exception\OutOfRangeCurrentPageException $ex) {
            $pagerfanta->setCurrentPage(1);
        }
        
        $entities = $pagerfanta->getCurrentPageResults();

        // Paginator - route generator
        $me = $this;
        $routeGenerator = function($page) use ($me, $request)
        {
            $requestParams = $request->query->all();
            $requestParams['pcg_page'] = $page;
            return $me->generateUrl('reimpresion', $requestParams);
        };

        // Paginator - view
        $view = new TwitterBootstrap3View();
        $pagerHtml = $view->render($pagerfanta, $routeGenerator, array(
            'proximity' => 3,
            'prev_message' => 'anterior',
            'next_message' => 'siguiente',
        ));

        return array($entities, $pagerHtml);
    }
   

    /*
         * Calculates the total of records string
         */
    protected function getTotalOfRecords($queryBuilder, $request)
    {
        $totalOfRecords = $queryBuilder->select('COUNT(e.id)')->getQuery()->getSingleScalarResult();
        $show = $request->get('pcg_show', 10);
        $page = $request->get('pcg_page', 1);

        $startRecord = ($show * ($page - 1)) + 1;
        $endRecord = $show * $page;

        if ($endRecord > $totalOfRecords) {
            $endRecord = $totalOfRecords;
        }
        return array(
            'msg' => "Mostrando $startRecord - $endRecord de $totalOfRecords Registros.",
            'cantidad' => $totalOfRecords
        );
    }
}
