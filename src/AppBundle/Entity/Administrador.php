<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Administrador
 *
 * @ORM\Entity 
 */
class Administrador extends Usuario
{

    /*============================Variables ===============================*/

    /**
     *@var string
     *
     *@ORM\Column(name="cargo", type="string", length=100)
     */
    protected $cargo;



    /*============================Setter y getters ===============================
     *
     */
    /**
     * Set cargo
     *
     * @param string $cargo
     * @return Administrador
     */
    public function setCargo($cargo)
    {
        $this->cargo = $cargo;

        return $this;
    }

    /**
     * Get cargo
     *
     * @return string
     */
    public function getCargo()
    {
        return $this->cargo;
    }

    /*============================Constructor   ===============================
     */
    public function __construct() {
        parent::__construct();
        $this->isActive = true;
        $this->setRole("ROLE_ADMIN");
    }

    public function __toString() {
        return $this->getId()." ".$this->getNombre()." ". $this->getApellido()." ".$this->getCargo();

    }

}
