<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\DataFixtures\ORM;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
class TipoLicencias implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {

        $tiposLicencia = array(
            array(
                'descripcion'=>'Caza Deportiva Particular',
                'generoJubilado'=>null,
                'arancel'=> 300.00,
                'diasVigencia'=>365,
                'fechaTope'=>new \DateTime('last day of December this year'),
                'isActive'=>1,
                'clienteSap'=>'15964',
                'letraServicio'=>'A',
                'porcentajeRecargoPrimerVencimiento'=>0,
                'porcentajeRecargoSegundoVencimiento'=>0,
                'diasPrimerVencimiento'=>0,
                'diasSegundoVencimiento'=>0,
                'aplicaEnProvincia' => 'SF'
                ),
            array(
                'descripcion'=>'Pesca Deportiva Particular',
                'generoJubilado'=>null,
                'arancel'=> 500.00,
                'diasVigencia'=>365,
                'fechaTope'=>new \DateTime('last day of December this year'),
                'isActive'=>1,
                'clienteSap'=>'15964',
                'letraServicio'=>'A',
                'porcentajeRecargoPrimerVencimiento'=>0,
                'porcentajeRecargoSegundoVencimiento'=>0,
                'diasPrimerVencimiento'=>0,
                'diasSegundoVencimiento'=>0,
                'aplicaEnProvincia' => 'SF'
                ),
            array(
                'descripcion'=>'Caza Deportiva Turista Nacional',
                'generoJubilado'=>null,
                'arancel'=> 400.00,
                'diasVigencia'=>7,
                'fechaTope'=>new \DateTime('last day of December this year'),
                'isActive'=>1,
                'clienteSap'=>'15964',
                'letraServicio'=>'A',
                'porcentajeRecargoPrimerVencimiento'=>0,
                'porcentajeRecargoSegundoVencimiento'=>0,
                'diasPrimerVencimiento'=>0,
                'diasSegundoVencimiento'=>0,
                'aplicaEnProvincia' => 'T'
                ),
            array(
                'descripcion'=>'Pesca Deportiva Turista Nacional',
                'generoJubilado'=>null,
                'arancel'=> 200,
                'diasVigencia'=>7,
                'fechaTope'=>new \DateTime('last day of December this year'),
                'isActive'=>1,
                'clienteSap'=>'15964',
                'letraServicio'=>'A',
                'porcentajeRecargoPrimerVencimiento'=>0,
                'porcentajeRecargoSegundoVencimiento'=>0,
                'diasPrimerVencimiento'=>0,
                'diasSegundoVencimiento'=>0,
                'aplicaEnProvincia' => 'T'
                ),
            array(
                'descripcion'=>'Caza deportiva Dama',
                'generoJubilado'=>'f',
                'arancel'=> 0,
                'diasVigencia'=>365,
                'fechaTope'=>new \DateTime('last day of December this year'),
                'isActive'=>1,
                'clienteSap'=>'15964',
                'letraServicio'=>'A',
                'porcentajeRecargoPrimerVencimiento'=>0,
                'porcentajeRecargoSegundoVencimiento'=>0,
                'diasPrimerVencimiento'=>0,
                'diasSegundoVencimiento'=>0,
                'aplicaEnProvincia' => 'SF'
            ),
            array(
                'descripcion'=>'Pesca deportiva Dama',
                'generoJubilado'=>'f',
                'arancel'=> 0,
                'diasVigencia'=>365,
                'fechaTope'=>new \DateTime('last day of December this year'),
                'isActive'=>1,
                'clienteSap'=>'15964',
                'letraServicio'=>'A',
                'porcentajeRecargoPrimerVencimiento'=>0,
                'porcentajeRecargoSegundoVencimiento'=>0,
                'diasPrimerVencimiento'=>0,
                'diasSegundoVencimiento'=>0,
                'aplicaEnProvincia' => 'SF'
            ),
            array(
                'descripcion'=>'Caza deportiva Jubilado',
                'generoJubilado'=>'j',
                'arancel'=> 0,
                'diasVigencia'=>365,
                'fechaTope'=>new \DateTime('last day of December this year'),
                'isActive'=>1,
                'clienteSap'=>'15964',
                'letraServicio'=>'A',
                'porcentajeRecargoPrimerVencimiento'=>0,
                'porcentajeRecargoSegundoVencimiento'=>0,
                'diasPrimerVencimiento'=>0,
                'diasSegundoVencimiento'=>0,
                'aplicaEnProvincia' => 'SF'
            ),
            array(
                'descripcion'=>'Pesca deportiva Jubilado',
                'generoJubilado'=>'j',
                'arancel'=> 0,
                'diasVigencia'=>365,
                'fechaTope'=>new \DateTime('last day of December this year'),
                'isActive'=>1,
                'clienteSap'=>'15964',
                'letraServicio'=>'A',
                'porcentajeRecargoPrimerVencimiento'=>0,
                'porcentajeRecargoSegundoVencimiento'=>0,
                'diasPrimerVencimiento'=>0,
                'diasSegundoVencimiento'=>0,
                'aplicaEnProvincia' => 'SF'
            )
        );
        foreach ($tiposLicencia as $tipoLicencia) {
            $entidad = new \AppBundle\Entity\TipoLicencia();
            $entidad = $entidad->copyValues($tipoLicencia);
            $manager->persist($entidad);
        }
        $manager->flush();
    }

}
