<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\View\TwitterBootstrap3View;

use AppBundle\Entity\Comprobante;

/**
 * Comprobante controller.
 *
 * @Route("/comprobante")
 */
class ComprobanteController extends Controller
{
    /**
     * Lists all Comprobante entities.
     *
     * @Route("/", name="comprobante")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('AppBundle:Comprobante')->createQueryBuilder('e');

        list($filterForm, $queryBuilder) = $this->filter($queryBuilder, $request);
        list($comprobantes, $pagerHtml) = $this->paginator($queryBuilder, $request);
        
        $totalOfRecordsString = $this->getTotalOfRecordsString($queryBuilder, $request);

        return $this->render('AppBundle:comprobante:index.html.twig', array(
            'comprobantes' => $comprobantes,
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
        $filterForm = $this->createForm('AppBundle\Form\ComprobanteFilterType');

        // Reset filter
        if ($request->get('filter_action') == 'reset') {
            $session->remove('ComprobanteControllerFilter');
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
                $session->set('ComprobanteControllerFilter', $filterData);
            }
        } else {
            // Get filter from session
            if ($session->has('ComprobanteControllerFilter')) {
                $filterData = $session->get('ComprobanteControllerFilter');
                
                foreach ($filterData as $key => $filter) { //fix for entityFilterType that is loaded from session
                    if (is_object($filter)) {
                        $filterData[$key] = $queryBuilder->getEntityManager()->merge($filter);
                    }
                }
                
                $filterForm = $this->createForm('AppBundle\Form\ComprobanteFilterType', $filterData);
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
            return $me->generateUrl('comprobante', $requestParams);
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
     * Displays a form to create a new Comprobante entity.
     *
     * @Route("/new", name="comprobante_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
    
        $comprobante = new Comprobante();
        $form   = $this->createForm('AppBundle\Form\ComprobanteType', $comprobante);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($comprobante);
            $em->flush();
            
            $editLink = $this->generateUrl('comprobante_edit', array('id' => $comprobante->getId()));
            $this->get('session')->getFlashBag()->add('success', "<a href='$editLink'>Registro Creado.</a>" );
            
            $nextAction=  $request->get('submit') == 'save' ? 'comprobante' : 'comprobante_new';
            return $this->redirectToRoute($nextAction);
        }
        return $this->render('AppBundle:comprobante:new.html.twig', array(
            'comprobante' => $comprobante,
            'form'   => $form->createView(),
        ));
    }
    

    /**
     * Finds and displays a Comprobante entity.
     *
     * @Route("/{id}", name="comprobante_show")
     * @Method("GET")
     */
    public function showAction(Comprobante $comprobante)
    {
        $deleteForm = $this->createDeleteForm($comprobante);
        return $this->render('AppBundle:comprobante:show.html.twig', array(
            'comprobante' => $comprobante,
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    

    /**
     * Displays a form to edit an existing Comprobante entity.
     *
     * @Route("/{id}/edit", name="comprobante_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Comprobante $comprobante)
    {
        $deleteForm = $this->createDeleteForm($comprobante);
        $editForm = $this->createForm('AppBundle\Form\ComprobanteType', $comprobante);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($comprobante);
            $em->flush();
            
            $this->get('session')->getFlashBag()->add('success', 'Registro Actualizado!');
            return $this->redirectToRoute('comprobante_edit', array('id' => $comprobante->getId()));
        }
        return $this->render('AppBundle:comprobante:edit.html.twig', array(
            'comprobante' => $comprobante,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    

    /**
     * Deletes a Comprobante entity.
     *
     * @Route("/{id}", name="comprobante_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Comprobante $comprobante)
    {
    
        $form = $this->createDeleteForm($comprobante);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($comprobante);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'Comprobante Actualizado');
        } else {
            $this->get('session')->getFlashBag()->add('error', 'Error Comprobante');
        }
        
        return $this->redirectToRoute('comprobante');
    }
    
    /**
     * Creates a form to delete a Comprobante entity.
     *
     * @param Comprobante $comprobante The Comprobante entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Comprobante $comprobante)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('comprobante_delete', array('id' => $comprobante->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    /**
     * Delete Comprobante by id
     *
     * @Route("/delete/{id}", name="comprobante_by_id_delete")
     * @Method("GET")
     */
    public function deleteByIdAction(Comprobante $comprobante){
        $em = $this->getDoctrine()->getManager();
        
        try {
            $em->remove($comprobante);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'Borrado Correcto');
        } catch (Exception $ex) {
            $this->get('session')->getFlashBag()->add('error', 'Error Borrando');
        }

        return $this->redirect($this->generateUrl('comprobante'));

    }
    

    /**
    * Bulk Action
    * @Route("/bulk-action/", name="comprobante_bulk_action")
    * @Method("POST")
    */
    public function bulkAction(Request $request)
    {
        $ids = $request->get("ids", array());
        $action = $request->get("bulk_action", "delete");

        if ($action == "delete") {
            try {
                $em = $this->getDoctrine()->getManager();
                $repository = $em->getRepository('AppBundle:Comprobante');

                foreach ($ids as $id) {
                    $comprobante = $repository->find($id);
                    $em->remove($comprobante);
                    $em->flush();
                }

                $this->get('session')->getFlashBag()->add('success', 'Comprobantes Borrados!');

            } catch (Exception $ex) {
                $this->get('session')->getFlashBag()->add('error', 'Error Borrando ');
            }
        }

        return $this->redirect($this->generateUrl('comprobante'));
    }
    

}
