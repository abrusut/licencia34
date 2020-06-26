<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * TipoDocumento
 *
 * @ORM\Entity()
 */
class TipoDocumento
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /*============================Variables ===============================*/

    /**
     *@var string
     *
     *@ORM\Column(name="tipo", type="string", length=100)
     */
    private $tipo;



     /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_baja", type="datetime", nullable=true)     
     */
    private $fechaBaja;

    /*============================Setter y getters ===============================
     *
     */
    /**
     * Set tipo
     *
     * @param string $tipo
     * @return TipoDocumento
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Get tipo
     *
     * @return string
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /*============================Constructor   ===============================
     */

    public function __toString() {
        return $this->getTipo();

    }


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set fechaBaja
     *
     * @param \DateTime $fechaBaja
     * @return TipoDocumento
     */
    public function setFechaBaja($fechaBaja)
    {
        $this->fechaBaja = $fechaBaja;

        return $this;
    }

    /**
     * Get fechaBaja
     *
     * @return \DateTime 
     */
    public function getFechaBaja()
    {
        return $this->fechaBaja;
    }
}
