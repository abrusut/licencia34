<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\View\TwitterBootstrap3View;

use AppBundle\Entity\Localidad;

/**
 * Localidad controller.
 *
 * @Route("/localidad")
 */
class LocalidadController extends Controller
{
    /**
     * Lists all Localidad entities.
     *
     * @Route("/", name="localidad")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('AppBundle:Localidad')->createQueryBuilder('e');

        list($filterForm, $queryBuilder) = $this->filter($queryBuilder, $request);
        list($localidads, $pagerHtml) = $this->paginator($queryBuilder, $request);
        
        $totalOfRecordsString = $this->getTotalOfRecordsString($queryBuilder, $request);

        return $this->render('AppBundle:localidad:index.html.twig', array(
            'localidads' => $localidads,
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
        $filterForm = $this->createForm('AppBundle\Form\LocalidadFilterType');

        // Reset filter
        if ($request->get('filter_action') == 'reset') {
            $session->remove('LocalidadControllerFilter');
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
                $session->set('LocalidadControllerFilter', $filterData);
            }
        } else {
            // Get filter from session
            if ($session->has('LocalidadControllerFilter')) {
                $filterData = $session->get('LocalidadControllerFilter');
                
                foreach ($filterData as $key => $filter) { //fix for entityFilterType that is loaded from session
                    if (is_object($filter)) {
                        $filterData[$key] = $queryBuilder->getEntityManager()->merge($filter);
                    }
                }
                
                $filterForm = $this->createForm('AppBundle\Form\LocalidadFilterType', $filterData);
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
            return $me->generateUrl('localidad', $requestParams);
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
     * Displays a form to create a new Localidad entity.
     *
     * @Route("/new", name="localidad_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
    
        $localidad = new Localidad();
        $form   = $this->createForm('AppBundle\Form\LocalidadType', $localidad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($localidad);
            $em->flush();
            
            $editLink = $this->generateUrl('localidad_edit', array('id' => $localidad->getId()));
            $this->get('session')->getFlashBag()->add('success', "<a href='$editLink'>Registro Creado.</a>" );
            
            $nextAction=  $request->get('submit') == 'save' ? 'localidad' : 'localidad_new';
            return $this->redirectToRoute($nextAction);
        }
        return $this->render('AppBundle:localidad:new.html.twig', array(
            'localidad' => $localidad,
            'form'   => $form->createView(),
        ));
    }
    

    /**
     * Finds and displays a Localidad entity.
     *
     * @Route("/{id}", name="localidad_show")
     * @Method("GET")
     */
    public function showAction(Localidad $localidad)
    {
        $deleteForm = $this->createDeleteForm($localidad);
        return $this->render('AppBundle:localidad:show.html.twig', array(
            'localidad' => $localidad,
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    

    /**
     * Displays a form to edit an existing Localidad entity.
     *
     * @Route("/{id}/edit", name="localidad_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Localidad $localidad)
    {
        $deleteForm = $this->createDeleteForm($localidad);
        $editForm = $this->createForm('AppBundle\Form\LocalidadType', $localidad);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($localidad);
            $em->flush();
            
            $this->get('session')->getFlashBag()->add('success', 'Registro Actualizado!');
            return $this->redirectToRoute('localidad_edit', array('id' => $localidad->getId()));
        }
        return $this->render('AppBundle:localidad:edit.html.twig', array(
            'localidad' => $localidad,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    

    /**
     * Deletes a Localidad entity.
     *
     * @Route("/{id}", name="localidad_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Localidad $localidad)
    {
    
        $form = $this->createDeleteForm($localidad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($localidad);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'Registro Borrado');
        } else {
            $this->get('session')->getFlashBag()->add('error', 'Problemas Borrando Registro');
        }
        
        return $this->redirectToRoute('localidad');
    }
    
    /**
     * Creates a form to delete a Localidad entity.
     *
     * @param Localidad $localidad The Localidad entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Localidad $localidad)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('localidad_delete', array('id' => $localidad->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    /**
     * Delete Localidad by id
     *
     * @Route("/delete/{id}", name="localidad_by_id_delete")
     * @Method("GET")
     */
    public function deleteByIdAction(Localidad $localidad){
        $em = $this->getDoctrine()->getManager();
        
        try {
            $em->remove($localidad);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'Registro Borrado');
        } catch (Exception $ex) {
            $this->get('session')->getFlashBag()->add('error', 'Problemas borrando registro');
        }

        return $this->redirect($this->generateUrl('localidad'));

    }
    

    /**
    * Bulk Action
    * @Route("/bulk-action/", name="localidad_bulk_action")
    * @Method("POST")
    */
    public function bulkAction(Request $request)
    {
        $ids = $request->get("ids", array());
        $action = $request->get("bulk_action", "delete");

        if ($action == "delete") {
            try {
                $em = $this->getDoctrine()->getManager();
                $repository = $em->getRepository('AppBundle:Localidad');

                foreach ($ids as $id) {
                    $localidad = $repository->find($id);
                    $em->remove($localidad);
                    $em->flush();
                }

                $this->get('session')->getFlashBag()->add('success', 'Registros Borrados!');

            } catch (Exception $ex) {
                $this->get('session')->getFlashBag()->add('error', 'Problemas Borrando Registros');
            }
        }

        return $this->redirect($this->generateUrl('localidad'));
    }
    

}
