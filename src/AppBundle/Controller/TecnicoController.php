<?php
/**
 * Created by PhpStorm.
 * User: administrador
 * Date: 16/07/18
 * Time: 08:18
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Tecnico;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\TecnicoType;

class TecnicoController extends Controller {

    public function resetPassTecnicoAction($id) {

        $em = $this->container->get('doctrine')->getManager();
        $tecnico = $em->getRepository('AppBundle:Tecnico')->find($id);

        $passwordplano = 'TEcnico1';
        //obtengo el entorno de seguridad
        $encoder = $this->container->get('security.password_encoder');
        //codifico el password
        $encoded = $encoder->encodePassword($tecnico, $passwordplano);
        //seteo el password
        $tecnico->setPassword($encoded);

        try {
            $em->persist($tecnico);
            $em->flush();
            $this->addFlash('resetPass', 'Se ha reseteado el password del técnico  ' . $tecnico->getApellido() . ','. $tecnico->getNombre() . '.');

            // realiza alguna acción, como enviar un correo electrónico
            $message = \Swift_Message::newInstance()->setSubject('Contacto del sistema de caza y pesca.')
                ->setFrom('agespinosa@santafe.gov.ar')
                ->setTo($tecnico->getEmail())
                ->setBody($this->renderView('AppBundle:Tecnico:ResetPassTecnico.txt.twig'));

            $this->get('mailer')->send($message);
        } catch (\Doctrine\DBAL\DBALException $e) {
            $exception_number = $e->getPrevious()->getCode();
            $exception_message = $e->getMessage();

            return $this->render('AppBundle:Exception:errorDB.html.twig',
                array('errorCode' => $exception_number, 'errorMessage' => $exception_message));
        }
        return $this->redirect($this->generateUrl("tecnico_list"));
    }

        public function editAction($id, Request $request) {

        $em = $this->container->get('doctrine')->getManager();
        $tecnico = $em
            ->getRepository('AppBundle:Tecnico')
            ->find($id);
        $form = $this
            ->container
            ->get('form.factory')
            ->create(new TecnicoType(), $tecnico);

        $form->handleRequest($request);

        if ($form->isValid() and $form->isSubmitted()) {

            try {
                $em->persist($tecnico);
                $em->flush();
                $this->addFlash('tecnico_list_mensaje', 'El Técnico ' . $tecnico . '  ha sido editado correctamente.');
            } catch (\Doctrine\DBAL\DBALException $e) {
                $exception_number = $e->getPrevious()->getCode();
                $exception_message = $e->getMessage();

                return $this->render('AppBundle:Exception:errorDB.html.twig', array('errorCode' => $exception_number, 'errorMessage' => $exception_message));
            }
            return $this->redirect($this->generateUrl("tecnico_list"));
        }

        return $this->render('AppBundle:Tecnico:edit.html.twig', array('form' => $form->createView()));
    }


    public function viewAction($id)
    {

        $em = $this->container->get('doctrine')->getManager();
        $tecnico = $em->getRepository('AppBundle:Tecnico')->find($id);
        $deleteForm = $this->createDeleteForm($tecnico->getId());

        return $this->render('AppBundle:Tecnico:view.html.twig', array('tecnico' => $tecnico,
            'delete_form' => $deleteForm->createView()));

    }

        private function createDeleteForm($id) {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl("tecnico_delete", array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm();
    }

    public function deleteAction($id, Request $request)
    {
        $em = $this->container->get('doctrine')->getManager();
        $tecnico = $em->getRepository('AppBundle:Tecnico')->find($id);

        $form = $this->createDeleteForm($tecnico->getId());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {

                $em->remove($tecnico);
                $em->flush();
                $this->addFlash('tecnico_list', 'El Técnico ' . $tecnico . ' ha sido borrado correctamente.');
            } catch (\Doctrine\DBAL\DBALException $e) {
                $exception_number = $e->getPrevious()->getCode();
                $exception_message = $e->getMessage();

                return $this->render('AppBundle:Exception:errorDB.html.twig', array('errorCode' => $exception_number, 'errorMessage' => $exception_message));
            }
            return $this->redirect($this->generateUrl("tecnico_list"));
        }
    }

    public function listAction(Request $request) {
        $em = $this->container->get('doctrine')->getManager();
        $tecnico= $em
            ->getRepository('AppBundle:Tecnico')
            ->findAll();

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate($tecnico, $request->query->get('page', 1), 5
        );


        return $this->render('AppBundle:Tecnico:list.html.twig',
            array('pagination' => $pagination));
    }

    public function addAction(Request $request) {
        $em = $this->container->get('doctrine')->getManager();
        $tecnico = new Tecnico();
        $form = $this
            ->container
            ->get('form.factory')
            ->create(new TecnicoType(), $tecnico);
        $tecnico->setRole("ROLE_TECNICO");

        $form->handleRequest($request);

        if ($form->isValid() and $form->isSubmitted()) {

            $passwordplano = 'TEcnico1';
            $encoder = $this->container->get('security.password_encoder');
            $encoded = $encoder->encodePassword($tecnico, $passwordplano);
            $tecnico->setPassword($encoded);
           // $tecnico->setFechaAlta(new \DateTime());
            $tecnico->setIsActive(true);

            try {
                $em->persist($tecnico);
                $em->flush();
                $this->addFlash('tecnico_list_mensaje', 'El tecnico ' . $tecnico . ' ha sido creado correctamente.');
            } catch (\Doctrine\DBAL\DBALException $e) {
                $exception_number = $e->getPrevious()->getCode();
                $exception_message = $e->getMessage();

                return $this->render('AppBundle:Exception:errorDB.html.twig', array('errorCode' => $exception_number, 'errorMessage' => $exception_message));
            }
            return $this->redirect($this->generateUrl("tecnico_list"));
        }

        return $this->render('AppBundle:Tecnico:add.html.twig', array('form' => $form->createView()));
    }
}