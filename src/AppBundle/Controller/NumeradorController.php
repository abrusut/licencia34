<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\View\TwitterBootstrap3View;

use AppBundle\Entity\Numerador;

/**
 * Numerador controller.
 *
 * @Route("/numerador")
 */
class NumeradorController extends Controller
{
    /**
     * Lists all Numerador entities.
     *
     * @Route("/", name="numerador")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('AppBundle:Numerador')->createQueryBuilder('e');

        list($filterForm, $queryBuilder) = $this->filter($queryBuilder, $request);
        list($numeradors, $pagerHtml) = $this->paginator($queryBuilder, $request);
        
        $totalOfRecordsString = $this->getTotalOfRecordsString($queryBuilder, $request);

        return $this->render('AppBundle:Numerador:index.html.twig', array(
            'numeradors' => $numeradors,
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
        $filterForm = $this->createForm('AppBundle\Form\NumeradorFilterType');

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
            return $me->generateUrl('numerador', $requestParams);
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
     * Displays a form to create a new Numerador entity.
     *
     * @Route("/new", name="numerador_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
    
        $numerador = new Numerador();
        $form   = $this->createForm('AppBundle\Form\NumeradorType', $numerador);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $numerador->setCreatedAt(new \DateTime());
            $em->persist($numerador);
            $em->flush();
            
            $editLink = $this->generateUrl('numerador_edit', array('id' => $numerador->getId()));
            $this->get('session')->getFlashBag()->add('success', "<a href='$editLink'>Registro Creado.</a>" );
            
            $nextAction=  $request->get('submit') == 'save' ? 'numerador' : 'numerador_new';
            return $this->redirectToRoute($nextAction);
        }
        return $this->render('AppBundle:Numerador:new.html.twig', array(
            'numerador' => $numerador,
            'form'   => $form->createView(),
        ));
    }
    

    /**
     * Finds and displays a Numerador entity.
     *
     * @Route("/{id}", name="numerador_show")
     * @Method("GET")
     */
    public function showAction(Numerador $numerador)
    {
        $deleteForm = $this->createDeleteForm($numerador);
        return $this->render('AppBundle:Numerador:show.html.twig', array(
            'numerador' => $numerador,
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    

    /**
     * Displays a form to edit an existing Numerador entity.
     *
     * @Route("/{id}/edit", name="numerador_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Numerador $numerador)
    {
        $deleteForm = $this->createDeleteForm($numerador);
        $editForm = $this->createForm('AppBundle\Form\NumeradorType', $numerador);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $numerador->setUpdatedAt(new \DateTime());
            $em->persist($numerador);
            $em->flush();
            
            $this->get('session')->getFlashBag()->add('success', 'Registro Actualizado!');
            return $this->redirectToRoute('numerador_edit', array('id' => $numerador->getId()));
        }
        return $this->render('AppBundle:Numerador:edit.html.twig', array(
            'numerador' => $numerador,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    

    /**
     * Deletes a Numerador entity.
     *
     * @Route("/{id}", name="numerador_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Numerador $numerador)
    {
    
        $form = $this->createDeleteForm($numerador);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $numerador->setFechaBaja(new \DateTime());
            $em->persist($numerador);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'Registro Borrado');
        } else {
            $this->get('session')->getFlashBag()->add('error', 'Problemas Borrando Registro');
        }
        
        return $this->redirectToRoute('numerador');
    }
    
    /**
     * Creates a form to delete a Numerador entity.
     *
     * @param Numerador $numerador The Numerador entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Numerador $numerador)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('numerador_delete', array('id' => $numerador->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    /**
     * Delete Numerador by id
     *
     * @Route("/delete/{id}", name="numerador_by_id_delete")
     * @Method("GET")
     */
    public function deleteByIdAction(Numerador $numerador){
        $em = $this->getDoctrine()->getManager();
        
        try {
            $numerador->setFechaBaja(new \DateTime());
            $em->persist($numerador);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'Registro Borrado');
        } catch (Exception $ex) {
            $this->get('session')->getFlashBag()->add('error', 'Problemas Borrando Registro');
        }

        return $this->redirect($this->generateUrl('numerador'));

    }
    

    /**
    * Bulk Action
    * @Route("/bulk-action/", name="numerador_bulk_action")
    * @Method("POST")
    */
    public function bulkAction(Request $request)
    {
        $ids = $request->get("ids", array());
        $action = $request->get("bulk_action", "delete");

        if ($action == "delete") {
            try {
                $em = $this->getDoctrine()->getManager();
                $repository = $em->getRepository('AppBundle:Numerador');

                foreach ($ids as $id) {
                    $numerador = $repository->find($id);
                    $numerador->setFechaBaja(new \DateTime());
                    $em->persist($numerador);
                    $em->flush();
                }

                $this->get('session')->getFlashBag()->add('success', 'Registro Borrado!');

            } catch (Exception $ex) {
                $this->get('session')->getFlashBag()->add('error', 'Problemas Borrando Registros ');
            }
        }

        return $this->redirect($this->generateUrl('numerador'));
    }
    

}
