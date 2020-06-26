<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Usuario;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Tecnico
 *
 * @ORM\Entity()
 */
class Tecnico extends Usuario
{

    /*============================Variables ===============================*/

    /**
     *@var string
     *
     *@ORM\Column(name="especialidad", type="string", length=100)
     */
    protected $especialidad;



    /*============================Setter y getters ===============================
     *
     */
    /**
     * Set especialidad
     *
     * @param string $especialidad
     * @return Tecnico
     */
    public function setEspecialidad($especialidad)
    {
        $this->especialidad = $especialidad;

        return $this;
    }

    /**
     * Get especialidad
     *
     * @return string
     */
    public function getEspecialidad()
    {
        return $this->especialidad;
    }

    /*============================Constructor   ===============================
     */
    public function __construct() {
        parent::__construct();
        $this->isActive = true;
        //$this->setRole("ROLE_TECNICO");
    }

    /**
     *  Retorna el nombre y apellido de la persona.
     *
     * @return string
     *
     */
    public function __toString() {
        return $this->getApellido(). ", " . $this->getNombre();
    }
    
    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->isActive;
    }
    
    /**
     * @param bool $isActive
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }
    
    


}
