<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity\Rol;
use AppBundle\Form\RolType;

class RolController extends Controller
{
    /**
     * @Route("/rol", name="rol")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $roles = $em->getRepository(Rol::class)->findAll();
        return $this->render('MProdTramitesEmerAgBundle:Usuario:indexRol.html.twig', array(
            "breadcrumbs" => $this->breadcrumbs(["Listado"]),
            "entities" => $roles
        ));
    }

    /**
     * @Route("/rol/nuevo", name="rol_new")
     */
    public function nuevoAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $rol = new Rol();
        $form = $this->createForm(RolType::class, $rol, ["submit" => true]);
        if ($request->getMethod() == "POST"){
            $form->handleRequest($request);
            $em->persist($rol);
            $em->flush();
            return $this->redirectToRoute('rol');
        }
        return $this->render('MProdTramitesEmerAgBundle:Usuario:newRol.html.twig', array(
            "breadcrumbs" => $this->breadcrumbs(["Nuevo"]),
            "form" => $form->createView()
        ));
    }

    /**
     * @Route("/rol/{id}", name="rol_edit")
     */
    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $rol = $em->getRepository(Rol::class)->findOneById($id);
        $form = $this->createForm(RolType::class, $rol, ["submit" => true]);
        if ($request->getMethod() == "POST"){
            $form->handleRequest($request);
            $em->persist($rol);
            $em->flush();
            return $this->redirectToRoute('rol');
        }
        return $this->render('MProdTramitesEmerAgBundle:Usuario:newRol.html.twig', array(
            "breadcrumbs" => $this->breadcrumbs(["Nuevo"]),
            "form" => $form->createView()
        ));
    }

    /**
     * @Route("/rol/{id}/delete", name="rol_delete")
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $rol = $em->getRepository(Rol::class)->findOneById($id);
        if ($request->getMethod() == "POST"){
            $em->remove($id);
            $em->flush();
            return $this->redirectToRoute('rol');
        }
        $form = $this->createForm(RolType::class, $rol, ["submit" => true]);
        return $this->render('MProdTramitesEmerAgBundle:Usuario:deleteRol.html.twig', array(
            "breadcrumbs" => $this->breadcrumbs(["Eliminar Rol " . $id->getNombre()]),
            "entity" => $id,
            "form" => $form->createView()
        ));
    }

    private function breadcrumbs($arr=[]){
        return array_merge(
            [ "Roles", ],
            $arr
        );
    }
}
