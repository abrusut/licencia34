<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\Common\Collections\ArrayCollection;
use TangoMan\CSVReaderBundle\Service\CSVLine;

/**
 * Liquidacion
 *
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class Liquidacion
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
     * @ORM\Column(name="nis", type="string", nullable=true)      
     */
    private $nis;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_cobro", type="datetime",nullable=false)
     * @Assert\NotNull()
     */
    private $fechaCobro;

     /**
     * @var string
     *
     * @ORM\Column(name="codigo_barra_cliente", type="string", nullable=false)
     * @Assert\NotNull()
     */
    private $codigoBarraCliente;

     /**
     * @var string
     *
     * @ORM\Column(name="numero_rendicion", type="string", nullable=true)     
     */
    private $numeroRendicion;

    /**
     * @var float
     *
     * @ORM\Column(name="importe_cobrado", type="float", nullable=false)
     * @Assert\NotNull()
     * )
     */
    private $importeCobrado;

      /**
     * @var float
     *
     * @ORM\Column(name="comision", type="float", nullable=true)
     * )
     */
    private $comision;

     /**    
    * @var AppBundle\Entity\Rendicion
    * @ORM\OneToMany(targetEntity="AppBundle\Entity\Rendicion", mappedBy="liquidacion")
    */
    private $rendiciones;
    
      /**
     * Many rendiciones tienen 1 FileRendicionLiquidacion
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\FileRendicionLiquidacion", inversedBy="liquidaciones")
     * @ORM\JoinColumn(name="file_rendicion_liquidacion_id", referencedColumnName="id")
     */
    private $fileRendicionLiquidacion;
       

     /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true, 
     *          options={"comment"="Tabla liquidacion"})
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

   
    public function __construct()
    {       
        $this->rendiciones = new ArrayCollection();
    }

     /** @ORM\PreUpdate */
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
     * Set nis
     *
     * @param string $nis
     * @return Liquidacion
     */
    public function setNis($nis)
    {
        $this->nis = $nis;

        return $this;
    }

    /**
     * Get nis
     *
     * @return string 
     */
    public function getNis()
    {
        return $this->nis;
    }

    /**
     * Set fechaCobro
     *
     * @param \DateTime $fechaCobro
     * @return Liquidacion
     */
    public function setFechaCobro($fechaCobro)
    {
        $this->fechaCobro = $fechaCobro;

        return $this;
    }

    /**
     * Get fechaCobro
     *
     * @return \DateTime 
     */
    public function getFechaCobro()
    {
        return $this->fechaCobro;
    }

    /**
     * Set codigoBarraCliente
     *
     * @param string $codigoBarraCliente
     * @return Liquidacion
     */
    public function setCodigoBarraCliente($codigoBarraCliente)
    {
        $this->codigoBarraCliente = $codigoBarraCliente;

        return $this;
    }

    /**
     * Get codigoBarraCliente
     *
     * @return string 
     */
    public function getCodigoBarraCliente()
    {
        return $this->codigoBarraCliente;
    }

    /**
     * Set numeroRendicion
     *
     * @param string $numeroRendicion
     * @return Liquidacion
     */
    public function setNumeroRendicion($numeroRendicion)
    {
        $this->numeroRendicion = $numeroRendicion;

        return $this;
    }

    /**
     * Get numeroRendicion
     *
     * @return string 
     */
    public function getNumeroRendicion()
    {
        return $this->numeroRendicion;
    }

    /**
     * Set importeCobrado
     *
     * @param float $importeCobrado
     * @return Liquidacion
     */
    public function setImporteCobrado($importeCobrado)
    {
        $this->importeCobrado = $importeCobrado;

        return $this;
    }

    /**
     * Get importeCobrado
     *
     * @return float 
     */
    public function getImporteCobrado()
    {
        return $this->importeCobrado;
    }

    /**
     * Set comision
     *
     * @param float $comision
     * @return Liquidacion
     */
    public function setComision($comision)
    {
        $this->comision = $comision;

        return $this;
    }

    /**
     * Get comision
     *
     * @return float 
     */
    public function getComision()
    {
        return $this->comision;
    }  

    /**
     * Add rendiciones
     *
     * @param \AppBundle\Entity\Rendicion $rendiciones
     * @return Liquidacion
     */
    public function addRendicione(\AppBundle\Entity\Rendicion $rendiciones)
    {
        $this->rendiciones[] = $rendiciones;

        return $this;
    }

    /**
     * Remove rendiciones
     *
     * @param \AppBundle\Entity\Rendicion $rendiciones
     */
    public function removeRendicione(\AppBundle\Entity\Rendicion $rendiciones)
    {
        $this->rendiciones->removeElement($rendiciones);
    }

    /**
     * Get rendiciones
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRendiciones()
    {
        return $this->rendiciones;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Liquidacion
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
     * @return Liquidacion
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
     * Set fileRendicionLiquidacion
     *
     * @param \AppBundle\Entity\FileRendicionLiquidacion $fileRendicionLiquidacion
     * @return Liquidacion
     */
    public function setFileRendicionLiquidacion(\AppBundle\Entity\FileRendicionLiquidacion $fileRendicionLiquidacion = null)
    {
        $this->fileRendicionLiquidacion = $fileRendicionLiquidacion;

        return $this;
    }

    /**
     * Get fileRendicionLiquidacion
     *
     * @return \AppBundle\Entity\FileRendicionLiquidacion 
     */
    public function getFileRendicionLiquidacion()
    {
        return $this->fileRendicionLiquidacion;
    }

    public function bind(CSVLine $linea, $head){        
        foreach ($head as $key => $value) {  
            if(!is_null($value) && !empty($value)){              

                if($value == 'fechaCobro')
                {
                    $formato = 'd/m/Y';
                    $this->$value = new \DateTime(\DateTime::createFromFormat($formato, $linea->get($value))->format('Y-m-d'));                     
                }else{
                    $this->$value = $linea->get($value);
                }
               
            }
        }        
    }
}
