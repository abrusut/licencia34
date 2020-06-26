<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;

class SecurityController extends Controller
{
    public function loginSuccessAction()
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $this->get('logger')->info("SecurityController->loginSuccessAction, Usuario Logueado Correctamente ".$user );
        return $this->render('AppBundle:Default:index.html.twig',
                array());
    }

    public function loginAction()
    {

        $authenticationUtils = $this->get('security.authentication_utils');

        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('AppBundle:Security:login.html.twig',
            array('last_username' => $lastUsername,
            'error' => $error));
    }

    public function logoutAction()
    {
        return $this->render('AppBundle:Security:logout.html.twig', array(
                // ...
            ));    }

    public function loginCheckAction()
    {
        return $this->render('AppBundle:Security:loginCheck.html.twig', array(
                // ...
            ));    }

    public function changePasswordAction(Request $request) {
        $persona = $this->get('security.token_storage')->getToken()->getUser();
        $em = $this->container->get('doctrine')->getManager();

        $form = $this->createFormBuilder()
            //->add('usuario', 'text')
            ->add('password', 'password', array('label' => 'Nueva contraseña'))
            ->add('changePassword', 'submit', array('label' => 'Cambiar password'))
            ->getForm();

        $mensaje = "Recuerde colocar dos mayusculas al comienzo y al menos dos números";

        $form->handleRequest($request);

        if ($form->isValid()  and $form->isSubmitted()) {
            // perform some action,
            // such as encoding with MessageDigestPasswordEncoder and persist
            //obtengo el pass
            $passwordplano = $form->get('password')->getData();

            //obtengo el entorno de seguridad
            $encoder = $this->container->get('security.password_encoder');
            //codifico el password
            $encoded = $encoder->encodePassword($persona, $passwordplano);
            //seteo el password
            $persona->setPassword($encoded);

            try {
                $em->persist($persona);
                $em->flush();
                $mensaje = "Su contraseña ha sido cambiada correctamente.";
                $this->addFlash('contraseña', 'Su contraseña ha sido cambiada satisfactoriamente.');

                // realiza alguna acción, como enviar un correo electrónico
                $message = \Swift_Message::newInstance()->setSubject('Contacto de reporte.')
                    ->setFrom('sectorial-mp@santafe.gov.ar')
                    ->setTo($persona->getEmail())
                    ->setBody($this->renderView('AppBundle:Security:ChangePass.txt.twig'));

                $this->get('mailer')->send($message);


            } catch (\Doctrine\DBAL\DBALException $e) {
                $exception_number = $e->getPrevious()->getCode();
                $exception_message = $e->getMessage();

                return $this->render('AppBundle:Exception:errorDB.html.twig', array('errorCode' => $exception_number, 'errorMessage' => $exception_message));
            }

            return $this->render('AppBundle:Security:changePassword.html.twig', array(
                'form' => $form->createView(), 'mensaje' => $mensaje,));
        }
        return $this->render('AppBundle:Security:changePassword.html.twig', array(
            'form' => $form->createView(), 'mensaje' => $mensaje
        ));
    }

}
