<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Routing\Annotation\Route;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\View\TwitterBootstrap3View;

use AppBundle\Entity\Liquidacion;

/**
 * Liquidacion controller.
 *
 * @Route("/liquidacion")
 */
class LiquidacionController extends Controller
{
    /**
     * Lists all Liquidacion entities.
     *
     * @Route("/", name="liquidacion")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('AppBundle:Liquidacion')->createQueryBuilder('e');

        list($filterForm, $queryBuilder) = $this->filter($queryBuilder, $request);
        list($liquidacions, $pagerHtml) = $this->paginator($queryBuilder, $request);
        
        $totalOfRecordsString = $this->getTotalOfRecordsString($queryBuilder, $request);

        return $this->render('AppBundle:Liquidacion:index.html.twig', array(
            'liquidacions' => $liquidacions,
            'pagerHtml' => $pagerHtml,
            'filterForm' => $filterForm->createView(),
            'totalOfRecordsString' => $totalOfRecordsString,

        ));
    }


    /**
    * Create filter form and process filter request.
    *
    */
    protected function filter($queryBuilder, $request)
    {
        $filterForm = $this->createForm('AppBundle\Form\LiquidacionFilterType');

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
            return $me->generateUrl('liquidacion', $requestParams);
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
     * Displays a form to create a new Liquidacion entity.
     *
     * @Route("/new", name="liquidacion_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
    
        $liquidacion = new Liquidacion();
        $form   = $this->createForm('AppBundle\Form\LiquidacionType', $liquidacion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $liquidacion->setCreatedAt(new \DateTime());
            $em->persist($liquidacion);
            $em->flush();
            
            $editLink = $this->generateUrl('liquidacion_edit', array('id' => $liquidacion->getId()));
            $this->get('session')->getFlashBag()->add('success', "<a href='$editLink'>Registro Creado</a>" );
            
            $nextAction=  $request->get('submit') == 'save' ? 'liquidacion' : 'liquidacion_new';
            return $this->redirectToRoute($nextAction);
        }
        return $this->render('AppBundle:Liquidacion:new.html.twig', array(
            'liquidacion' => $liquidacion,
            'form'   => $form->createView(),
        ));
    }
    

    /**
     * Finds and displays a Liquidacion entity.
     *
     * @Route("/{id}", name="liquidacion_show")
     * @Method("GET")
     */
    public function showAction(Liquidacion $liquidacion)
    {
        $deleteForm = $this->createDeleteForm($liquidacion);
        return $this->render('AppBundle:Liquidacion:show.html.twig', array(
            'liquidacion' => $liquidacion,
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    

    /**
     * Displays a form to edit an existing Liquidacion entity.
     *
     * @Route("/{id}/edit", name="liquidacion_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Liquidacion $liquidacion)
    {
        $deleteForm = $this->createDeleteForm($liquidacion);
        $editForm = $this->createForm('AppBundle\Form\LiquidacionType', $liquidacion);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $liquidacion->setUpdatedAt(new \DateTime());
            $em->persist($liquidacion);
            $em->flush();
            
            $this->get('session')->getFlashBag()->add('success', 'Registro Actualizado!');
            return $this->redirectToRoute('liquidacion_edit', array('id' => $liquidacion->getId()));
        }
        return $this->render('AppBundle:Liquidacion:edit.html.twig', array(
            'liquidacion' => $liquidacion,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    

    /**
     * Deletes a Liquidacion entity.
     *
     * @Route("/{id}", name="liquidacion_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Liquidacion $liquidacion)
    {
    
        $form = $this->createDeleteForm($liquidacion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $liquidacion->setFechaBaja(new \DateTime());
            $em->persist($liquidacion);  
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'Registro Borrado');
        } else {
            $this->get('session')->getFlashBag()->add('error', 'Problemas  Borrando Registro');
        }
        
        return $this->redirectToRoute('liquidacion');
    }
    
    /**
     * Creates a form to delete a Liquidacion entity.
     *
     * @param Liquidacion $liquidacion The Liquidacion entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Liquidacion $liquidacion)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('liquidacion_delete', array('id' => $liquidacion->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    /**
     * Delete Liquidacion by id
     *
     * @Route("/delete/{id}", name="liquidacion_by_id_delete")
     * @Method("GET")
     */
    public function deleteByIdAction(Liquidacion $liquidacion){
        $em = $this->getDoctrine()->getManager();
        
        try {
            $liquidacion->setFechaBaja(new \DateTime());
            $em->persist($liquidacion);  
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'Registro Borrado');
        } catch (Exception $ex) {
            $this->get('session')->getFlashBag()->add('error', 'Problemas  Borrando Registro');
        }

        return $this->redirect($this->generateUrl('liquidacion'));

    }
    

    /**
    * Bulk Action
    * @Route("/bulk-action/", name="liquidacion_bulk_action")
    * @Method("POST")
    */
    public function bulkAction(Request $request)
    {
        $ids = $request->get("ids", array());
        $action = $request->get("bulk_action", "delete");

        if ($action == "delete") {
            try {
                $em = $this->getDoctrine()->getManager();
                $repository = $em->getRepository('AppBundle:Liquidacion');

                foreach ($ids as $id) {
                    $liquidacion = $repository->find($id);
                    $liquidacion->setFechaBaja(new \DateTime());
                    $em->persist($liquidacion);                    
                    $em->flush();
                }

                $this->get('session')->getFlashBag()->add('success', 'Registros Borrados!');

            } catch (Exception $ex) {
                $this->get('session')->getFlashBag()->add('error', 'Problemas Borrando Registros ');
            }
        }

        return $this->redirect($this->generateUrl('liquidacion'));
    }
    

}
