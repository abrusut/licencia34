<?php

namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\PrePersist;
/**
 * Comprobante
 *
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class Comprobante
{
     /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
   
    
    /**
     * @var float
     *
     * @ORM\Column(name="monto", type="float", nullable=false)
     * @Assert\Range(
     *              min = 0,
     *              minMessage = "El monto no pueden ser menor que cero"
     * )
     */
    private $monto;

    /**
     * @var float
     *
     * @ORM\Column(name="recargo_segundo_vencimiento", type="float", nullable=true)     
     */
    private $recargoSegundoVencimiento;

    /**
     * @var float
     *
     * @ORM\Column(name="recargo_primer_vencimiento", type="float", nullable=true)
     * 
     */
    private $recargoPrimerVencimiento;

     /**
     *@var string
     *
     *@ORM\Column(name="cliente_sap", type="string", nullable=false)
     */
    private $clienteSap; 

     /**
     *@var string
     *
     *@ORM\Column(name="letra_servicio", type="string", nullable=false)
     */
    private $letraServicio; 

    /**
     *@var string
     *
     *@ORM\Column(name="rendicion_numero", type="string", nullable=true)
     */
    private $rendicionNumero; 

     /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_cobro", type="date", nullable=true)     
     */
    private $fechaCobro;

     /**
     * Many comprobantes son pagados con 1 FileRendicionLiquidacion
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\FileRendicionLiquidacion", inversedBy="comprobantes")
     * @ORM\JoinColumn(name="file_rendicion_liquidacion_id", referencedColumnName="id")
     */
    private $fileRendicionLiquidacion;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="primer_vencimiento", type="datetime", nullable=true)     
     */
    private $primerVencimiento;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="segundo_vencimiento", type="datetime", nullable=true)     
     */
    private $segundoVencimiento;    

    /**
     *@var string
     *
     *@ORM\Column(name="numero_codigo_barra", type="string", nullable=true)
     */
    private $numeroCodigoBarra;

     /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     * @Assert\NotNull()
     */
    private $createdAt;    
   

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
     * Set monto
     *
     * @param float $monto
     * @return Comprobante
     */
    public function setMonto($monto)
    {
        $this->monto = $monto;

        return $this;
    }

    /**
     * Get monto
     *
     * @return float 
     */
    public function getMonto()
    {
        return $this->monto;
    }

    /**
     * Set rendicionNumero
     *
     * @param string $rendicionNumero
     * @return Comprobante
     */
    public function setRendicionNumero($rendicionNumero)
    {
        $this->rendicionNumero = $rendicionNumero;

        return $this;
    }

    /**
     * Get rendicionNumero
     *
     * @return string 
     */
    public function getRendicionNumero()
    {
        return $this->rendicionNumero;
    }   

    /**
     * Set fechaVencimiento
     *
     * @param \DateTime $fechaVencimiento
     * @return Comprobante
     */
    public function setFechaVencimiento($fechaVencimiento)
    {
        $this->fechaVencimiento = $fechaVencimiento;

        return $this;
    }

    /**
     * Get fechaVencimiento
     *
     * @return \DateTime 
     */
    public function getFechaVencimiento()
    {
        return $this->fechaVencimiento;
    }


    /**
     *
     * @return string
     *
     */
    public function __toString() {
        return " ".$this->getId() ;
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
     * @return Comprobante
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
     * Set clienteSap
     *
     * @param string $clienteSap
     * @return Comprobante
     */
    public function setClienteSap($clienteSap)
    {
        $this->clienteSap = $clienteSap;

        return $this;
    }

    /**
     * Get clienteSap
     *
     * @return string 
     */
    public function getClienteSap()
    {
        return $this->clienteSap;
    }

    /**
     * Set letraServicio
     *
     * @param string $letraServicio
     * @return Comprobante
     */
    public function setLetraServicio($letraServicio)
    {
        $this->letraServicio = $letraServicio;

        return $this;
    }

    /**
     * Get letraServicio
     *
     * @return string 
     */
    public function getLetraServicio()
    {
        return $this->letraServicio;
    }

    /**
     * Set primerVencimiento
     *
     * @param \DateTime $primerVencimiento
     * @return Comprobante
     */
    public function setPrimerVencimiento($primerVencimiento)
    {
        $this->primerVencimiento = $primerVencimiento;

        return $this;
    }

    /**
     * Get primerVencimiento
     *
     * @return \DateTime 
     */
    public function getPrimerVencimiento()
    {
        return $this->primerVencimiento;
    }

    /**
     * Set segundoVencimiento
     *
     * @param \DateTime $segundoVencimiento
     * @return Comprobante
     */
    public function setSegundoVencimiento($segundoVencimiento)
    {
        $this->segundoVencimiento = $segundoVencimiento;

        return $this;
    }

    /**
     * Get segundoVencimiento
     *
     * @return \DateTime 
     */
    public function getSegundoVencimiento()
    {
        return $this->segundoVencimiento;
    }

    /**
     * Set numeroCodigoBarra
     *
     * @param string $numeroCodigoBarra
     * @return Comprobante
     */
    public function setNumeroCodigoBarra($numeroCodigoBarra)
    {
        $this->numeroCodigoBarra = $numeroCodigoBarra;

        return $this;
    }

    /**
     * Get numeroCodigoBarra
     *
     * @return string 
     */
    public function getNumeroCodigoBarra()
    {
        return $this->numeroCodigoBarra;
    }

    /**
     * Set recargoSegundoVencimiento
     *
     * @param float $recargoSegundoVencimiento
     * @return Comprobante
     */
    public function setRecargoSegundoVencimiento($recargoSegundoVencimiento)
    {
        $this->recargoSegundoVencimiento = $recargoSegundoVencimiento;

        return $this;
    }

    /**
     * Get recargoSegundoVencimiento
     *
     * @return float 
     */
    public function getRecargoSegundoVencimiento()
    {
        return $this->recargoSegundoVencimiento;
    }

    /**
     * Set recargoPrimerVencimiento
     *
     * @param float $recargoPrimerVencimiento
     * @return Comprobante
     */
    public function setRecargoPrimerVencimiento($recargoPrimerVencimiento)
    {
        $this->recargoPrimerVencimiento = $recargoPrimerVencimiento;

        return $this;
    }

    /**
     * Get recargoPrimerVencimiento
     *
     * @return float 
     */
    public function getRecargoPrimerVencimiento()
    {
        return $this->recargoPrimerVencimiento;
    }
  
    /**
     * Set fileRendicionLiquidacion
     *
     * @param \AppBundle\Entity\FileRendicionLiquidacion $fileRendicionLiquidacion
     * @return Comprobante
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

    /**
     * Set fechaCobro
     *
     * @param \DateTime $fechaCobro
     * @return Comprobante
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
}
