<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\View\TwitterBootstrap3View;

use AppBundle\Entity\Licencia;
use AppBundle\Service\PersonaServiceImpl;
use AppBundle\Service\LicenciaServiceImpl;

/**
 * Licencia controller.
 *
 * @Route("/licencia")
 */
class LicenciaController extends Controller
{
    /**
     * Lists all Licencia entities.
     *
     * @Route("/", name="licencia")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('AppBundle:Licencia')->createQueryBuilder('e');

        list($filterForm, $queryBuilder) = $this->filter($queryBuilder, $request);
        list($licencias, $pagerHtml) = $this->paginator($queryBuilder, $request);
        
        $arrayTotalRecords = $this->getTotalOfRecords($queryBuilder, $request);
        $totalOfRecordsString = $arrayTotalRecords['msg'];
        $cantidadLicencias = $arrayTotalRecords['cantidad'];

        $cantidaLicenciasImpagas = $this->getTotalLicenciasImpagas();
        $cantidadDePersonasRegistradas = $this->getTotalPersonas();
        $totalAranceles = $this->getTotalArancelesCobrados();
        $cantidaLicenciasPagas = $this->getTotalLicenciasPagas();
        $cantidaLicenciasGratuitas = $this->getTotalLicenciasGratuitas();

        return $this->render('AppBundle:Licencia:index.html.twig', array(
            'licencias' => $licencias,
            'pagerHtml' => $pagerHtml,
            'filterForm' => $filterForm->createView(),
            'totalOfRecordsString' => $totalOfRecordsString,
            'cantidadLicencias' => $cantidadLicencias,
            'cantidaLicenciasImpagas' => $cantidaLicenciasImpagas,
            'cantidadDePersonasRegistradas' => $cantidadDePersonasRegistradas,
            'totalAranceles'=>$totalAranceles,
            'cantidaLicenciasPagas' => $cantidaLicenciasPagas,
            'cantidaLicenciasGratuitas' => $cantidaLicenciasGratuitas
        ));
    }


    /**
    * Create filter form and process filter request.
    *
    */
    protected function filter($queryBuilder, $request)
    {
        $filterForm = $this->createForm('AppBundle\Form\LicenciaFilterType');

        // Bind values from the request
        $filterForm->handleRequest($request);

        if ($filterForm->isValid()) {
            // Build the query from the given form object
            $this->get('petkopara_multi_search.builder')->searchForm( $queryBuilder, $filterForm->get('search'));
        }

        return array($filterForm, $queryBuilder);
    }

    /**
    * Get results from paginator and get paginator view.
    *
    */
    protected function paginator($queryBuilder, Request $request)
    {
        //sorting
        $sortCol = $queryBuilder->getRootAlias().'.'.$request->get('pcg_sort_col', 'id');
        $queryBuilder->orderBy($sortCol, $request->get('pcg_sort_order', 'desc'));
        // Paginator
        $adapter = new DoctrineORMAdapter($queryBuilder);
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
            return $me->generateUrl('licencia', $requestParams);
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
    protected function getTotalOfRecords($queryBuilder, $request) {
        $totalOfRecords = $queryBuilder->select('COUNT(e.id)')->getQuery()->getSingleScalarResult();
        $show = $request->get('pcg_show', 10);
        $page = $request->get('pcg_page', 1);

        $startRecord = ($show * ($page - 1)) + 1;
        $endRecord = $show * $page;

        if ($endRecord > $totalOfRecords) {
            $endRecord = $totalOfRecords;
        }
        return array( 'msg' => "Mostrando $startRecord - $endRecord de $totalOfRecords Registros.",
                    'cantidad' => $totalOfRecords );
    }    
    

    /**
     * Finds and displays a Licencia entity.
     *
     * @Route("/{id}", name="licencia_show")
     * @Method("GET")
     */
    public function showAction(Licencia $licencium)
    {
        $deleteForm = $this->createDeleteForm($licencium);
        return $this->render('AppBundle:Licencia:show.html.twig', array(
            'licencium' => $licencium,
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    

    /**
     * Displays a form to edit an existing Licencia entity.
     *
     * @Route("/{id}/edit", name="licencia_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Licencia $licencium)
    {
        $deleteForm = $this->createDeleteForm($licencium);
        $editForm = $this->createForm('AppBundle\Form\LicenciaType', $licencium);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($licencium);
            $em->flush();
            
            $this->get('session')->getFlashBag()->add('success', 'Registro Actualizado!');
            return $this->redirectToRoute('licencia_edit', array('id' => $licencium->getId()));
        }
        return $this->render('AppBundle:Licencia:edit.html.twig', array(
            'licencium' => $licencium,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    

    /**
     * Deletes a Licencia entity.
     *
     * @Route("/{id}", name="licencia_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Licencia $licencium)
    {
    
        $form = $this->createDeleteForm($licencium);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($licencium);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'Licencia Borrada');
        } else {
            $this->get('session')->getFlashBag()->add('error', 'Problemas Borrando Licencia');
        }
        
        return $this->redirectToRoute('licencia');
    }
    
    /**
     * Creates a form to delete a Licencia entity.
     *
     * @param Licencia $licencium The Licencia entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Licencia $licencium)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('licencia_delete', array('id' => $licencium->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    /**
     * Delete Licencia by id
     *
     * @Route("/delete/{id}", name="licencia_by_id_delete")
     * @Method("GET")
     */
    public function deleteByIdAction(Licencia $licencium){
        $em = $this->getDoctrine()->getManager();
        
        try {
            $em->remove($licencium);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'Licencia Borrada');
        } catch (Exception $ex) {
            $this->get('session')->getFlashBag()->add('error', 'Problemas Borrando Licencia');
        }

        return $this->redirect($this->generateUrl('licencia'));

    }
    

    /**
    * Bulk Action
    * @Route("/bulk-action/", name="licencia_bulk_action")
    * @Method("POST")
    */
    public function bulkAction(Request $request)
    {
        $ids = $request->get("ids", array());
        $action = $request->get("bulk_action", "delete");

        if ($action == "delete") {
            try {
                $em = $this->getDoctrine()->getManager();
                $repository = $em->getRepository('AppBundle:Licencia');

                foreach ($ids as $id) {
                    $licencium = $repository->find($id);
                    $em->remove($licencium);
                    $em->flush();
                }

                $this->get('session')->getFlashBag()->add('success', 'Licencias Borradas!');

            } catch (Exception $ex) {
                $this->get('session')->getFlashBag()->add('error', 'Problemas Borrando Licencias ');
            }
        }

        return $this->redirect($this->generateUrl('licencia'));
    }
    
    public function getTotalLicenciasImpagas(){
        /** @var LicenciaServiceImpl $licenciaService */
        $licenciaService = $this->get('licencia_service');
        return $licenciaService->getTotalLicenciasImpagas();
        
    }
    public function getTotalLicenciasPagas(){
        /** @var LicenciaServiceImpl $licenciaService */
        $licenciaService = $this->get('licencia_service');
        return $licenciaService->getTotalLicenciasPagas();
        
    }

    public function getTotalLicenciasGratuitas(){
        /** @var LicenciaServiceImpl $licenciaService */
        $licenciaService = $this->get('licencia_service');
        return $licenciaService->getTotalLicenciasGratuitas();
        
    }
    

    public function getTotalPersonas(){
        /** @var PersonaServiceImpl $personaService */
        $personaService = $this->get('persona_service');
        return $personaService->getTotalPersonas();
    }


    public function getTotalArancelesCobrados(){
        /** @var LicenciaServiceImpl $licenciaService */
        $licenciaService = $this->get('licencia_service');
        return $licenciaService->getTotalArancelesCobrados();
        
    }

    
}
