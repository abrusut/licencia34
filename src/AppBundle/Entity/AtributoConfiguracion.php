<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreUpdate;

/**
 * AtributoConfiguracion
 *
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class AtributoConfiguracion
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

       
    /**
     * @var string
     *
     * @ORM\Column(name="clave", type="string", nullable=false)
     * @Assert\NotNull()
     */
    private $clave;

    /**
     * @var string
     *     
     * @ORM\Column(type="text", length=65535, nullable=false))
     * @Assert\NotNull()
     */
    private $valor;

     /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     * @Assert\NotNull()
     */
    private $createdAt;


     /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)     
     */
    private $updatedAt;

     /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="fecha_baja", type="datetime", nullable=true)     
     */
    private $fechaBaja;


    /** 
     * @ORM\PreUpdate 
    */
    public function setUpdatedAtValue()
    {
        $this->updatedAt = new \DateTime();
    }

    /**
    * @ORM\PrePersist
    */
    public function setCreatedAtValue()
    {
        $this->createdAt = new \DateTime();
    }


    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Persona
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
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
     * Set numeroCorrelativo
     *
     * @param string $numeroCorrelativo
     * @return Numerador
     */
    public function setNumeroCorrelativo($numeroCorrelativo)
    {
        $this->numeroCorrelativo = $numeroCorrelativo;

        return $this;
    }

    /**
     * Get numeroCorrelativo
     *
     * @return string 
     */
    public function getNumeroCorrelativo()
    {
        return $this->numeroCorrelativo;
    }

    /**
     * Set tipoNumerador
     *
     * @param string $tipoNumerador
     * @return Numerador
     */
    public function setTipoNumerador($tipoNumerador)
    {
        $this->tipoNumerador = $tipoNumerador;

        return $this;
    }

    /**
     * Get tipoNumerador
     *
     * @return string 
     */
    public function getTipoNumerador()
    {
        return $this->tipoNumerador;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Numerador
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set fechaBaja
     *
     * @param \DateTime $fechaBaja
     * @return Numerador
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

    /**
     * Set clave
     *
     * @param string $clave
     * @return AtributoConfiguracion
     */
    public function setClave($clave)
    {
        $this->clave = $clave;

        return $this;
    }

    /**
     * Get clave
     *
     * @return string 
     */
    public function getClave()
    {
        return $this->clave;
    }

    /**
     * Set valor
     *
     * @param string $valor
     * @return AtributoConfiguracion
     */
    public function setValor($valor)
    {
        $this->valor = $valor;

        return $this;
    }

    /**
     * Get valor
     *
     * @return string 
     */
    public function getValor()
    {
        return $this->valor;
    }
}
