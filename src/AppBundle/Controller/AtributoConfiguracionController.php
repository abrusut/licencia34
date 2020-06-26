<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\View\TwitterBootstrap3View;

use AppBundle\Entity\AtributoConfiguracion;

/**
 * AtributoConfiguracion controller.
 *
 * @Route("/atributoconfiguracion")
 */
class AtributoConfiguracionController extends Controller
{
    /**
     * Lists all AtributoConfiguracion entities.
     *
     * @Route("/", name="atributoconfiguracion")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('AppBundle:AtributoConfiguracion')->createQueryBuilder('e');

        list($filterForm, $queryBuilder) = $this->filter($queryBuilder, $request);
        list($atributoConfiguracions, $pagerHtml) = $this->paginator($queryBuilder, $request);
        
        $totalOfRecordsString = $this->getTotalOfRecordsString($queryBuilder, $request);

        return $this->render('AppBundle:AtributoConfiguracion:index.html.twig', array(
            'atributoConfiguracions' => $atributoConfiguracions,
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
        $filterForm = $this->createForm('AppBundle\Form\AtributoConfiguracionFilterType');

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
            return $me->generateUrl('atributoconfiguracion', $requestParams);
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
     * Displays a form to create a new AtributoConfiguracion entity.
     *
     * @Route("/new", name="atributoconfiguracion_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
    
        $atributoConfiguracion = new AtributoConfiguracion();
        $form   = $this->createForm('AppBundle\Form\AtributoConfiguracionType', $atributoConfiguracion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $atributoConfiguracion->setCreatedAt(new \DateTime());
            $em->persist($atributoConfiguracion);
            $em->flush();
            
            $editLink = $this->generateUrl('atributoconfiguracion_edit', array('id' => $atributoConfiguracion->getId()));
            $this->get('session')->getFlashBag()->add('success', "<a href='$editLink'>Registro Creado.</a>" );
            
            $nextAction=  $request->get('submit') == 'save' ? 'atributoconfiguracion' : 'atributoconfiguracion_new';
            return $this->redirectToRoute($nextAction);
        }
        return $this->render('AppBundle:AtributoConfiguracion:new.html.twig', array(
            'atributoConfiguracion' => $atributoConfiguracion,
            'form'   => $form->createView(),
        ));
    }
    

    /**
     * Finds and displays a AtributoConfiguracion entity.
     *
     * @Route("/{id}", name="atributoconfiguracion_show")
     * @Method("GET")
     */
    public function showAction(AtributoConfiguracion $atributoConfiguracion)
    {
        $deleteForm = $this->createDeleteForm($atributoConfiguracion);
        return $this->render('AppBundle:AtributoConfiguracion:show.html.twig', array(
            'atributoConfiguracion' => $atributoConfiguracion,
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    

    /**
     * Displays a form to edit an existing AtributoConfiguracion entity.
     *
     * @Route("/{id}/edit", name="atributoconfiguracion_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, AtributoConfiguracion $atributoConfiguracion)
    {
        $deleteForm = $this->createDeleteForm($atributoConfiguracion);
        $editForm = $this->createForm('AppBundle\Form\AtributoConfiguracionType', $atributoConfiguracion);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $atributoConfiguracion->setUpdatedAt(new \DateTime());
            $em->persist($atributoConfiguracion);
            $em->flush();
            
            $this->get('session')->getFlashBag()->add('success', 'Registro Actualizado!');
            return $this->redirectToRoute('atributoconfiguracion_edit', array('id' => $atributoConfiguracion->getId()));
        }
        return $this->render('AppBundle:AtributoConfiguracion:edit.html.twig', array(
            'atributoConfiguracion' => $atributoConfiguracion,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    

    /**
     * Deletes a AtributoConfiguracion entity.
     *
     * @Route("/{id}", name="atributoconfiguracion_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, AtributoConfiguracion $atributoConfiguracion)
    {
    
        $form = $this->createDeleteForm($atributoConfiguracion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $atributoConfiguracion->setFechaBaja(new \DateTime());
            $em->persist($atributoConfiguracion);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'Registro Dado de Baja');
        } else {
            $this->get('session')->getFlashBag()->add('error', 'Problemas dando de Baja Registro');
        }
        
        return $this->redirectToRoute('atributoconfiguracion');
    }
    
    /**
     * Creates a form to delete a AtributoConfiguracion entity.
     *
     * @param AtributoConfiguracion $atributoConfiguracion The AtributoConfiguracion entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(AtributoConfiguracion $atributoConfiguracion)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('atributoconfiguracion_delete', array('id' => $atributoConfiguracion->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    /**
     * Delete AtributoConfiguracion by id
     *
     * @Route("/delete/{id}", name="atributoconfiguracion_by_id_delete")
     * @Method("GET")
     */
    public function deleteByIdAction(AtributoConfiguracion $atributoConfiguracion){
        $em = $this->getDoctrine()->getManager();
        
        try {
            $atributoConfiguracion->setFechaBaja(new \DateTime());
            $em->persist($atributoConfiguracion);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'Registro Dado de Baja');
        } catch (Exception $ex) {
            $this->get('session')->getFlashBag()->add('error', 'Problemas dando de Baja Registro');
        }

        return $this->redirect($this->generateUrl('atributoconfiguracion'));

    }
    

    /**
    * Bulk Action
    * @Route("/bulk-action/", name="atributoconfiguracion_bulk_action")
    * @Method("POST")
    */
    public function bulkAction(Request $request)
    {
        $ids = $request->get("ids", array());
        $action = $request->get("bulk_action", "delete");

        if ($action == "delete") {
            try {
                $em = $this->getDoctrine()->getManager();
                $repository = $em->getRepository('AppBundle:AtributoConfiguracion');

                foreach ($ids as $id) {
                    $atributoConfiguracion = $repository->find($id);
                    $atributoConfiguracion->setFechaBaja(new \DateTime());
                    $em->persist($atributoConfiguracion);
                    $em->flush();
                }

                $this->get('session')->getFlashBag()->add('success', 'Registros Dados de Baja!');

            } catch (Exception $ex) {
                $this->get('session')->getFlashBag()->add('error', 'Problemas dando de baja Registros ');
            }
        }

        return $this->redirect($this->generateUrl('atributoconfiguracion'));
    }
    

}
