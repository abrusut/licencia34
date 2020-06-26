<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\DataFixtures\ORM;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class Provincias implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {

        $provincias = array(
            array('nombre'=>'Buenos Aires'),
            array('nombre'=>'Catamarca'),
            array('nombre'=>'Chaco'),
            array('nombre'=>'Chubut'),
            array('nombre'=>'Córdoba'),
            array('nombre'=>'Corrientes'),
            array('nombre'=>'Entre Ríos'),
            array('nombre'=>'Formosa'),
            array('nombre'=>'Jujuy'),
            array('nombre'=>'La Pampa'),
            array('nombre'=>'La Rioja'),
            array('nombre'=>'Mendoza'),
            array('nombre'=>'Misiones'),
            array('nombre'=>'Neuquén'),
            array('nombre'=>'Río Negro'),
            array('nombre'=>'Salta'),
            array('nombre'=>'San Juan'),
            array('nombre'=>'San Luis'),
            array('nombre'=>'Santa Cruz'),
            array('nombre'=>'Santa Fe'),
            array('nombre'=>'Santiago del Estero'),
            array('nombre'=>'Tierra del Fuego'),
            array('nombre'=>'Tucumán')
        );
        foreach ($provincias as $provincia) {
            $entidad = new \AppBundle\Entity\Provincia();
            $entidad->setNombre($provincia['nombre']);            
            $manager->persist($entidad);
           
            if($provincia['nombre'] === 'Santa Fe')
                $this->setReference('provinciaSantaFe', $entidad);
        }
        $manager->flush();
        
        
    }

    public function getOrder()
    {
        return 2;
    }
}
