<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\View\TwitterBootstrap3View;

use AppBundle\Entity\TipoLicencia;

/**
 * TipoLicencia controller.
 *
 * @Route("/tipolicencia")
 */
class TipoLicenciaController extends Controller
{
    /**
     * Lists all TipoLicencia entities.
     *
     * @Route("/", name="tipolicencia")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('AppBundle:TipoLicencia')->createQueryBuilder('e');

        list($filterForm, $queryBuilder) = $this->filter($queryBuilder, $request);
        list($tipoLicencias, $pagerHtml) = $this->paginator($queryBuilder, $request);
        
        $totalOfRecordsString = $this->getTotalOfRecordsString($queryBuilder, $request);
       
        return $this->render('AppBundle:tipolicencia:index.html.twig', array(
            'tipoLicencias' => $tipoLicencias,
            'pagerHtml' => $pagerHtml,
            'filterForm' => $filterForm->createView(),
            'totalOfRecordsString' => $totalOfRecordsString,

        ));
    }

    /**
    * Create filter form and process filter request.
    *
    */
    protected function filter($queryBuilder, Request $request)
    {
        $session = $request->getSession();
        $filterForm = $this->createForm('AppBundle\Form\TipoLicenciaFilterType');

        // Reset filter
        if ($request->get('filter_action') == 'reset') {
            $session->remove('TipoLicenciaControllerFilter');
        }

        // Filter action
        if ($request->get('filter_action') == 'filter') {
            // Bind values from the request
            $filterForm->handleRequest($request);

            if ($filterForm->isValid()) {
                // Build the query from the given form object
                $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($filterForm, $queryBuilder);
                // Save filter to session
                $filterData = $filterForm->getData();
                $session->set('TipoLicenciaControllerFilter', $filterData);
            }
        } else {
            // Get filter from session
            if ($session->has('TipoLicenciaControllerFilter')) {
                $filterData = $session->get('TipoLicenciaControllerFilter');
                
                foreach ($filterData as $key => $filter) { //fix for entityFilterType that is loaded from session
                    if (is_object($filter)) {
                        $filterData[$key] = $queryBuilder->getEntityManager()->merge($filter);
                    }
                }
                
                $filterForm = $this->createForm('AppBundle\Form\TipoLicenciaFilterType', $filterData);
                $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($filterForm, $queryBuilder);
            }
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
            return $me->generateUrl('tipolicencia', $requestParams);
        };

        // Paginator - view
        $view = new TwitterBootstrap3View();
        $pagerHtml = $view->render($pagerfanta, $routeGenerator, array(
            'proximity' => 3,
            'prev_message' => 'Anterior',
            'next_message' => 'Siguiente',
        ));

        return array($entities, $pagerHtml);
    }
    
    
    
    /*
     * Calculates the total of records string
     */
    protected function getTotalOfRecordsString($queryBuilder, $request) {
        $totalOfRecords = $queryBuilder->select('COUNT(e.id)')->getQuery()->getSingleScalarResult();
        $show = $request->get('pcg_show', 10);
        $page = $request->get('pcg_page', 1);

        $startRecord = ($show * ($page - 1)) + 1;
        $endRecord = $show * $page;

        if ($endRecord > $totalOfRecords) {
            $endRecord = $totalOfRecords;
        }
        return "Mostrando $startRecord - $endRecord de $totalOfRecords Registros.";
    }
    
    

    /**
     * Displays a form to create a new TipoLicencia entity.
     *
     * @Route("/new", name="tipolicencia_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
    
        $tipoLicencium = new TipoLicencia();
        $form   = $this->createForm('AppBundle\Form\TipoLicenciaType', $tipoLicencium);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($tipoLicencium);
            $em->flush();
            
            $editLink = $this->generateUrl('tipolicencia_edit', array('id' => $tipoLicencium->getId()));
            $this->get('session')->getFlashBag()->add('success', "<a href='$editLink'>Registro Creado.</a>" );
            
            $nextAction=  $request->get('submit') == 'save' ? 'tipolicencia' : 'tipolicencia_new';
            return $this->redirectToRoute($nextAction);
        }
        return $this->render('AppBundle:tipolicencia:new.html.twig', array(
            'tipoLicencium' => $tipoLicencium,
            'form'   => $form->createView(),
        ));
    }
    

    /**
     * Finds and displays a TipoLicencia entity.
     *
     * @Route("/{id}", name="tipolicencia_show")
     * @Method("GET")
     */
    public function showAction(TipoLicencia $tipoLicencium)
    {
        $deleteForm = $this->createDeleteForm($tipoLicencium);
        return $this->render('AppBundle:tipolicencia:show.html.twig', array(
            'tipoLicencium' => $tipoLicencium,
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    

    /**
     * Displays a form to edit an existing TipoLicencia entity.
     *
     * @Route("/{id}/edit", name="tipolicencia_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, TipoLicencia $tipoLicencium)
    {
        $deleteForm = $this->createDeleteForm($tipoLicencium);
        $editForm = $this->createForm('AppBundle\Form\TipoLicenciaType', $tipoLicencium);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($tipoLicencium);
            $em->flush();
            
            $this->get('session')->getFlashBag()->add('success', 'Registro Actualizado!');
            return $this->redirectToRoute('tipolicencia_edit', array('id' => $tipoLicencium->getId()));
        }
        return $this->render('AppBundle:tipolicencia:edit.html.twig', array(
            'tipoLicencium' => $tipoLicencium,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    

    /**
     * Deletes a TipoLicencia entity.
     *
     * @Route("/{id}", name="tipolicencia_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, TipoLicencia $tipoLicencium)
    {
    
        $form = $this->createDeleteForm($tipoLicencium);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $tipoLicencium->setIsActive(false);
            $em->persist($tipoLicencium);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'Registro Deshabilitado');
        } else {
            $this->get('session')->getFlashBag()->add('error', 'Error al Deshabilitando Registro');
        }
        
        return $this->redirectToRoute('tipolicencia');
    }
    
    /**
     * Creates a form to delete a TipoLicencia entity.
     *
     * @param TipoLicencia $tipoLicencium The TipoLicencia entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(TipoLicencia $tipoLicencium)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('tipolicencia_delete', array('id' => $tipoLicencium->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    /**
     * Delete TipoLicencia by id
     *
     * @Route("/delete/{id}", name="tipolicencia_by_id_delete")
     * @Method("GET")
     */
    public function deleteByIdAction(TipoLicencia $tipoLicencium){
        $em = $this->getDoctrine()->getManager();
        
        try {
            $tipoLicencium->setIsActive(false);
            $em->persist($tipoLicencium);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'Registro Deshabilitado');
        } catch (Exception $ex) {
            $this->get('session')->getFlashBag()->add('error', 'Error al Deshabilitando registro');
        }

        return $this->redirect($this->generateUrl('tipolicencia'));

    }
    

    /**
    * Bulk Action
    * @Route("/bulk-action/", name="tipolicencia_bulk_action")
    * @Method("POST")
    */
    public function bulkAction(Request $request)
    {
        $ids = $request->get("ids", array());
        $action = $request->get("bulk_action", "delete");

        if ($action == "delete") {
            try {
                $em = $this->getDoctrine()->getManager();
                $repository = $em->getRepository('AppBundle:TipoLicencia');

                foreach ($ids as $id) {
                    $tipoLicencium = $repository->find($id);
                    $tipoLicencium->setIsActive(false);
                    $em->persist($tipoLicencium);
            
                    $em->flush();
                }

                $this->get('session')->getFlashBag()->add('success', 'Registro Deshabilitado');

            } catch (Exception $ex) {
                $this->get('session')->getFlashBag()->add('error', 'Error Deshabilitando Registro ');
            }
        }

        return $this->redirect($this->generateUrl('tipolicencia'));
    }
    

}
