<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\PrePersist;
use TangoMan\CSVReaderBundle\Service\CSVLine;

/**
 * Rendicion
 *
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class Rendicion
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
     * @ORM\Column(name="numero_rendicion", type="string", nullable=true)     
     */
    private $numeroRendicion;

     /**
     * @var string
     *
     * @ORM\Column(name="nis", type="string", nullable=true)      
     */
    private $nis;

    /**
     * Many rendiciones tienen 1 liquidacion
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Liquidacion", inversedBy="rendiciones")
     * @ORM\JoinColumn(name="liquidacion_id", referencedColumnName="id")
     */
    private $liquidacion;

     /**
     * Many rendiciones tienen 1 FileRendicionLiquidacion
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\FileRendicionLiquidacion", inversedBy="rendiciones")
     * @ORM\JoinColumn(name="file_rendicion_liquidacion_id", referencedColumnName="id")
     */
    private $fileRendicionLiquidacion;

    /**
     * @var \Date
     *
     * @ORM\Column(name="fecha_cobro", type="date",nullable=false)
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
     * @var float
     *
     * @ORM\Column(name="importe_cobrado", type="float", nullable=false)
     * @Assert\NotNull()
     * )
     */
    private $importeCobrado;


     /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true, options={"comment"="Tabla liquidacion"})
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
    }


     /**
    * @ORM\PrePersist
    */
    public function setCreatedAtValue()
    {
        $this->createdAt = new \DateTime();
    }

     /** @ORM\PreUpdate */
     public function setUpdatedAtValue()
     {
         $this->updatedAt = new \DateTime();
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
     * Set numeroRendicion
     *
     * @param string $numeroRendicion
     * @return Rendicion
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
     * Set nis
     *
     * @param string $nis
     * @return Rendicion
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
     * Set codigoBarraCliente
     *
     * @param string $codigoBarraCliente
     * @return Rendicion
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
     * Set importeCobrado
     *
     * @param float $importeCobrado
     * @return Rendicion
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
     * Set liquidacion
     *
     * @param \AppBundle\Entity\Liquidacion $liquidacion
     * @return Rendicion
     */
    public function setLiquidacion(\AppBundle\Entity\Liquidacion $liquidacion = null)
    {
        $this->liquidacion = $liquidacion;

        return $this;
    }

    /**
     * Get liquidacion
     *
     * @return \AppBundle\Entity\Liquidacion 
     */
    public function getLiquidacion()
    {
        return $this->liquidacion;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Rendicion
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
     * @return Rendicion
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

    /**
     * Set fechaCobro
     *
     * @param \DateTime $fechaCobro
     * @return Rendicion
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
     * Set fileRendicionLiquidacion
     *
     * @param \AppBundle\Entity\FileRendicionLiquidacion $fileRendicionLiquidacion
     * @return Rendicion
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
}
