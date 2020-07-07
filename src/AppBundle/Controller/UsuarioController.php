<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Routing\Annotation\Route;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\View\TwitterBootstrap3View;

use AppBundle\Entity\Usuario;

/**
 * Usuario controller.
 *
 * @Route("/usuario")
 */
class UsuarioController extends Controller
{
    /**
     * Lists all Usuario entities.
     *
     * @Route("/", name="usuario")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('AppBundle:Usuario')->createQueryBuilder('e');

        list($filterForm, $queryBuilder) = $this->filter($queryBuilder, $request);
        list($usuarios, $pagerHtml) = $this->paginator($queryBuilder, $request);
        
        $totalOfRecordsString = $this->getTotalOfRecordsString($queryBuilder, $request);

        return $this->render('AppBundle:Usuario:index.html.twig', array(
            'usuarios' => $usuarios,
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
        $filterForm = $this->createForm('AppBundle\Form\UsuarioFilterType');

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
            return $me->generateUrl('usuario', $requestParams);
        };

        // Paginator - view
        $view = new TwitterBootstrap3View();
        $pagerHtml = $view->render($pagerfanta, $routeGenerator, array(
            'proximity' => 3,
            'prev_message' => 'previous',
            'next_message' => 'next',
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
        return "Showing $startRecord - $endRecord of $totalOfRecords Records.";
    }
    
    

    /**
     * Displays a form to create a new Usuario entity.
     *
     * @Route("/new", name="usuario_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
    
        $usuario = new Usuario();
        $form   = $this->createForm('AppBundle\Form\UsuarioType', $usuario);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($usuario);
            $em->flush();
            
            $editLink = $this->generateUrl('usuario_edit', array('id' => $usuario->getId()));
            $this->get('session')->getFlashBag()->add('success', "<a href='$editLink'>New usuario was created successfully.</a>" );
            
            $nextAction=  $request->get('submit') == 'save' ? 'usuario' : 'usuario_new';
            return $this->redirectToRoute($nextAction);
        }
        return $this->render('AppBundle:Usuario:new.html.twig', array(
            'usuario' => $usuario,
            'form'   => $form->createView(),
        ));
    }
    

    /**
     * Finds and displays a Usuario entity.
     *
     * @Route("/{id}", name="usuario_show")
     * @Method("GET")
     */
    public function showAction(Usuario $usuario)
    {
        $deleteForm = $this->createDeleteForm($usuario);
        return $this->render('AppBundle:Usuario:show.html.twig', array(
            'usuario' => $usuario,
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    

    /**
     * Displays a form to edit an existing Usuario entity.
     *
     * @Route("/{id}/edit", name="usuario_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Usuario $usuario)
    {
        $deleteForm = $this->createDeleteForm($usuario);
        $editForm = $this->createForm('AppBundle\Form\UsuarioType', $usuario);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($usuario);
            $em->flush();
            
            $this->get('session')->getFlashBag()->add('success', 'Edited Successfully!');
            return $this->redirectToRoute('usuario_edit', array('id' => $usuario->getId()));
        }
        return $this->render('AppBundle:Usuario:edit.html.twig', array(
            'usuario' => $usuario,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    

    /**
     * Deletes a Usuario entity.
     *
     * @Route("/{id}", name="usuario_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Usuario $usuario)
    {
    
        $form = $this->createDeleteForm($usuario);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($usuario);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'The Usuario was deleted successfully');
        } else {
            $this->get('session')->getFlashBag()->add('error', 'Problem with deletion of the Usuario');
        }
        
        return $this->redirectToRoute('usuario');
    }
    
    /**
     * Creates a form to delete a Usuario entity.
     *
     * @param Usuario $usuario The Usuario entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Usuario $usuario)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('usuario_delete', array('id' => $usuario->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    /**
     * Delete Usuario by id
     *
     * @Route("/delete/{id}", name="usuario_by_id_delete")
     * @Method("GET")
     */
    public function deleteByIdAction(Usuario $usuario){
        $em = $this->getDoctrine()->getManager();
        
        try {
            $em->remove($usuario);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'The Usuario was deleted successfully');
        } catch (Exception $ex) {
            $this->get('session')->getFlashBag()->add('error', 'Problem with deletion of the Usuario');
        }

        return $this->redirect($this->generateUrl('usuario'));

    }
    

    /**
    * Bulk Action
    * @Route("/bulk-action/", name="usuario_bulk_action")
    * @Method("POST")
    */
    public function bulkAction(Request $request)
    {
        $ids = $request->get("ids", array());
        $action = $request->get("bulk_action", "delete");

        if ($action == "delete") {
            try {
                $em = $this->getDoctrine()->getManager();
                $repository = $em->getRepository('AppBundle:Usuario');

                foreach ($ids as $id) {
                    $usuario = $repository->find($id);
                    $em->remove($usuario);
                    $em->flush();
                }

                $this->get('session')->getFlashBag()->add('success', 'usuarios was deleted successfully!');

            } catch (Exception $ex) {
                $this->get('session')->getFlashBag()->add('error', 'Problem with deletion of the usuarios ');
            }
        }

        return $this->redirect($this->generateUrl('usuario'));
    }



    /** Mantenimiento De Usuarios Con FOS USER */
    /**
     * @Route("/fosusuario", name="fosusuario")
     */
    public function indexFosUsuarioAction()
    {
        $em = $this->getDoctrine()->getManager();
        $usuarios = $em->getRepository(Usuario::class)->findAll();
        return $this->render('MProdTramitesEmerAgBundle:Usuario:index.html.twig', array(
            "breadcrumbs" => $this->breadcrumbs(["Listado"]),
            "entities" => $usuarios
        ));
    }

    /**
     * @Route("/fosusuario/{id}/roles", name="fosusuario_roles")
     */
    public function nuevoFosUsuarioAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $usuario = $em->getRepository(Usuario::class)->findOneById($id);
        $password = $usuario->getPassword();
        $rolesAll = $em->getRepository(Rol::class)->findAll();
        $form = $this->createForm(UsuarioType::class, $usuario, ["submit" => true]);
        if ($request->getMethod() == "POST"){
            $form->handleRequest($request);
            $rolesUsuario = $usuario->getRolesArray();

            foreach ($rolesAll as $rol) {
                $usuario->removeRole($rol->getNombreSTD());
            }

            foreach ($rolesUsuario as $rol) {
                $usuario->addRole($rol->getNombreSTD());
            }

            $em->persist($usuario);
            $em->flush();
            return $this->redirectToRoute('usuario');
        }
        return $this->render('MProdTramitesEmerAgBundle:Usuario:rolesUsuario.html.twig', array(
            "breadcrumbs" => $this->breadcrumbs(["Asignar roles al usuario " . $usuario->getUsername()]),
            "form" => $form->createView()
        ));
    }

    private function breadcrumbs($arr=[]){
        return array_merge(
            [ "Usuarios", ],
            $arr
        );
    }
    

}
