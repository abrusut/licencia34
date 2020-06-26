<?php

namespace AppBundle\DataFixtures\ORM;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\TipoDocumento;

class TiposDocumentos implements FixtureInterface {

    public function load(ObjectManager $manager) {

        $tipos_de_documentos = array(
            array(
                'tipo'   => 'DNI'
            ),
            array(
                'tipo' => 'L.E'
            ),
            array(
                'tipo' => 'L.C'
            ),

            // ...
        );
        foreach ($tipos_de_documentos as $tipos_de_documentos) {
            $entidad = new TipoDocumento();
            $entidad->setTipo($tipos_de_documentos['tipo']);

            $manager->persist($entidad);
        }
        $manager->flush();
    }

}
