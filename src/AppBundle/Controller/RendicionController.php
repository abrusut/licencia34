<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\View\TwitterBootstrap3View;

use AppBundle\Entity\Rendicion;

/**
 * Rendicion controller.
 *
 * @Route("/rendicion")
 */
class RendicionController extends Controller
{
    
    /**
     * Lists all Rendicion entities.
     *
     * @Route("/", name="rendicion")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('AppBundle:Rendicion')->createQueryBuilder('e');

        list($filterForm, $queryBuilder) = $this->filter($queryBuilder, $request);
        list($rendicions, $pagerHtml) = $this->paginator($queryBuilder, $request);
        
        $totalOfRecordsString = $this->getTotalOfRecordsString($queryBuilder, $request);

        return $this->render('AppBundle:Rendicion:index.html.twig', array(
            'rendicions' => $rendicions,
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
        $filterForm = $this->createForm('AppBundle\Form\RendicionFilterType');

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
            return $me->generateUrl('rendicion', $requestParams);
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
     * Displays a form to create a new Rendicion entity.
     *
     * @Route("/new", name="rendicion_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
    
        $rendicion = new Rendicion();
        $form   = $this->createForm('AppBundle\Form\RendicionType', $rendicion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $rendicion->setCreatedAt(new \DateTime());
            $em->persist($rendicion);
            $em->flush();
            
            $editLink = $this->generateUrl('rendicion_edit', array('id' => $rendicion->getId()));
            $this->get('session')->getFlashBag()->add('success', "<a href='$editLink'>Registro Creado</a>" );
            
            $nextAction=  $request->get('submit') == 'save' ? 'rendicion' : 'rendicion_new';
            return $this->redirectToRoute($nextAction);
        }
        return $this->render('AppBundle:Rendicion:new.html.twig', array(
            'rendicion' => $rendicion,
            'form'   => $form->createView(),
        ));
    }
    

    /**
     * Finds and displays a Rendicion entity.
     *
     * @Route("/{id}", name="rendicion_show")
     * @Method("GET")
     */
    public function showAction(Rendicion $rendicion)
    {
        $deleteForm = $this->createDeleteForm($rendicion);
        return $this->render('AppBundle:Rendicion:show.html.twig', array(
            'rendicion' => $rendicion,
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    

    /**
     * Displays a form to edit an existing Rendicion entity.
     *
     * @Route("/{id}/edit", name="rendicion_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Rendicion $rendicion)
    {
        $deleteForm = $this->createDeleteForm($rendicion);
        $editForm = $this->createForm('AppBundle\Form\RendicionType', $rendicion);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $rendicion->setUpdatedAt(new \DateTime());            
            $em->persist($rendicion);
            $em->flush();
            
            $this->get('session')->getFlashBag()->add('success', 'Registro Actualizado!');
            return $this->redirectToRoute('rendicion_edit', array('id' => $rendicion->getId()));
        }
        return $this->render('AppBundle:Rendicion:edit.html.twig', array(
            'rendicion' => $rendicion,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    

    /**
     * Deletes a Rendicion entity.
     *
     * @Route("/{id}", name="rendicion_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Rendicion $rendicion)
    {
    
        $form = $this->createDeleteForm($rendicion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $rendicion->setFechaBaja(new \DateTime());
            $em->persist($rendicion);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'Registro Borrado');
        } else {
            $this->get('session')->getFlashBag()->add('error', 'Problemas Borrando Registro');
        }
        
        return $this->redirectToRoute('rendicion');
    }
    
    /**
     * Creates a form to delete a Rendicion entity.
     *
     * @param Rendicion $rendicion The Rendicion entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Rendicion $rendicion)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('rendicion_delete', array('id' => $rendicion->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    /**
     * Delete Rendicion by id
     *
     * @Route("/delete/{id}", name="rendicion_by_id_delete")
     * @Method("GET")
     */
    public function deleteByIdAction(Rendicion $rendicion){
        $em = $this->getDoctrine()->getManager();
        
        try {
            $rendicion->setFechaBaja(new \DateTime());
            $em->persist($rendicion);            
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'Registro Borrado');
        } catch (Exception $ex) {
            $this->get('session')->getFlashBag()->add('error', 'Problemas Borrando Registro');
        }

        return $this->redirect($this->generateUrl('rendicion'));

    }
    

    /**
    * Bulk Action
    * @Route("/bulk-action/", name="rendicion_bulk_action")
    * @Method("POST")
    */
    public function bulkAction(Request $request)
    {
        $ids = $request->get("ids", array());
        $action = $request->get("bulk_action", "delete");

        if ($action == "delete") {
            try {
                $em = $this->getDoctrine()->getManager();
                $repository = $em->getRepository('AppBundle:Rendicion');

                foreach ($ids as $id) {
                    $rendicion = $repository->find($id);
                    $rendicion->setFechaBaja(new \DateTime());
                    $em->persist($rendicion);
                    $em->flush();
                }

                $this->get('session')->getFlashBag()->add('success', 'Registros Borrados!');

            } catch (Exception $ex) {
                $this->get('session')->getFlashBag()->add('error', 'Problemas Borrando Registros ');
            }
        }

        return $this->redirect($this->generateUrl('rendicion'));
    }
    

}
