<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\PrePersist;
/**
 * Licencia
 *
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class Licencia
{

    public static $LEY_PESCA = '12212';
    public static $LEY_CAZA = '4830';

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
   
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_emitida", type="datetime")
     * @Assert\NotNull()
     */
    private $fechaEmitida;

     /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_desde", type="datetime", nullable=true)     
     */
    private $fechaDesde;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_vencimiento", type="datetime")
     * @Assert\NotNull()
     */
    private $fechaVencimiento;

    /**
     * @var \AppBundle\Entity\TipoLicencia     
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\TipoLicencia")
     */
    private $tipoLicencia;

    /**
     * @var \AppBundle\Entity\Persona
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Persona", 
     *                  inversedBy="licencias",cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="persona_id", referencedColumnName="id")
     * })
     */
    private $persona;

    
     /**     
     * @var \AppBundle\Entity\Comprobante
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Comprobante",cascade={"persist"})
     * @ORM\JoinColumn(name="comprobante_id", referencedColumnName="id")
     */
    private $comprobante;


    /**
     *@var string
     *
     *@ORM\Column(name="numero", type="string", nullable=true)
     */
    private $numero; 


    public function __construct()
    {        
        $this->setFechaVencimiento(new \DateTime('last day of December this year'));        
        $this->setFechaEmitida(new \DateTime());        
    }

    public function __toString()
    {
        return 'Licencia Numero '.$this->getId();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }



    /**
     * @return \DateTime
     */
    public function getFechaEmitida()
    {
        return $this->fechaEmitida;
    }

    /**
     * @param \DateTime $fechaEmitida
     */
    public function setFechaEmitida($fechaEmitida)
    {
        $this->fechaEmitida = $fechaEmitida;
    }

    /**
     * @return \DateTime
     */
    public function getFechaVencimiento()
    {
        return $this->fechaVencimiento;
    }

    /**
     * @param \DateTime $fechaVencimiento
     */
    public function setFechaVencimiento($fechaVencimiento)
    {
        $this->fechaVencimiento = $fechaVencimiento;
    }   

    /**
     * @return mixed
     */
    public function getTipoLicencia()
    {
        return $this->tipoLicencia;
    }

    /**
     * @param mixed $tipoLicencia
     */
    public function setTipoLicencia(TipoLicencia $tipoLicencia)
    {
        $this->tipoLicencia = $tipoLicencia;
    }

    /**
     * @return mixed
     */
    public function getPersona()
    {
        return $this->persona;
    }

   
    /**
     * Set persona
     *
     * @param \AppBundle\Entity\Persona $persona
     * @return Licencia
     */
    public function setPersona(Persona $persona)
    {
        $this->persona = $persona;

        return $this;
    }

    /**
    * @ORM\PrePersist
    */
    public function setFechaEmitidaValue()
    {
        $this->fechaEmitida = new \DateTime();
    }

 
    /**
     * Set fechaDesde
     *
     * @param \DateTime $fechaDesde
     * @return Licencia
     */
    public function setFechaDesde($fechaDesde)
    {
        $this->fechaDesde = $fechaDesde;

        return $this;
    }

    /**
     * Get fechaDesde
     *
     * @return \DateTime 
     */
    public function getFechaDesde()
    {
        return $this->fechaDesde;
    }

    /**
     * Set comprobante
     *
     * @param \AppBundle\Entity\Comprobante $comprobante
     * @return Licencia
     */
    public function setComprobante(\AppBundle\Entity\Comprobante $comprobante = null)
    {
        $this->comprobante = $comprobante;

        return $this;
    }

    /**
     * Get comprobante
     *
     * @return \AppBundle\Entity\Comprobante 
     */
    public function getComprobante()
    {
        return $this->comprobante;
    }

    public function getStringTipoLicenciaCazaOPesca(){

        $pos = stripos($this->getTipoLicencia()->getDescripcion(), 'caza');
        if( $pos !== false){
            return 'caza';
        }
        $pos = stripos($this->getTipoLicencia()->getDescripcion(), 'pesca');
        if( $pos !== false){
            return 'pesca';
        }
        return "";
    }

    /**
     * Set setearNumeroCompleto
     *
     * @return string
     */
    public function setearNumeroCompleto()
    {
        if(!is_null($this->getPersona()) && 
            !is_null($this->getPersona()->getTipoDocumento()) &&
            !is_null($this->getPersona()->getTipoDocumento()->getId()) &&
            !is_null($this->getPersona()->getNumeroDocumento()) &&
            !is_null($this->getTipoLicencia()) &&
            !is_null($this->getTipoLicencia()->getId()) &&
            !is_null($this->getId())
            ){

            $this->setNumero($this->getPersona()->getTipoDocumento()->getId(). 
                    $this->getPersona()->getNumeroDocumento() .
                    $this->getTipoLicencia()->getId() .
                    $this->getId());
        }
            
        return "";
    }

     /**
     * Get getNumeroCompleto
     *
     * @return string
     */
    public function getNumeroCompleto()
    {
        if(!is_null($this->getPersona()) && 
            !is_null($this->getPersona()->getTipoDocumento()) &&
            !is_null($this->getPersona()->getTipoDocumento()->getId()) &&
            !is_null($this->getPersona()->getNumeroDocumento()) &&
            !is_null($this->getTipoLicencia()) &&
            !is_null($this->getTipoLicencia()->getId()) &&
            !is_null($this->getId())
            ){

            return $this->getPersona()->getTipoDocumento()->getId(). 
                    $this->getPersona()->getNumeroDocumento() .
                    $this->getTipoLicencia()->getId() .
                    $this->getId();
        }
            
        return "";
    }

    public function configurarVigencia(){
         // Vencimiento
         $fechaVencimiento = new \DateTime();         

         if($this->tipoLicencia->getDiasVigencia() == 365){
            // Vence el ultimo dia del año corriente
            $fechaVencimiento = new \DateTime('last day of December this year');
         }else{            
            // Si la fecha desde es distinta al hoy, calculo el vencimiento
            $fechaDesde = $this->getFechaDesde();
            $today = new \DateTime();
                    
            $diff  = $today->diff($fechaDesde);
            if(!is_null($fechaDesde) && $diff->days > 0){
                // Vence segun los dias de vigencia configurados
                $sumatoriaDeDias = ($diff->days + 1) + $this->tipoLicencia->getDiasVigencia();                
                $fechaVencimiento->add(new \DateInterval('P'.$sumatoriaDeDias .'D'));
            }else{
                $fechaVencimiento->add(new \DateInterval('P'.$this->tipoLicencia->getDiasVigencia().'D'));
            }
            // No debe superar la fecha tope
            // Si el vencimiento calculado supera la Fecha Tope que configura el tipo de licencia
            // lo piso con la fecha tope
            if(!is_null($this->tipoLicencia->getFechaTope()) &&
                $fechaVencimiento > $this->tipoLicencia->getFechaTope()){
                    $fechaVencimiento = $this->tipoLicencia->getFechaTope();
            }
         }

        $this->setFechaVencimiento($fechaVencimiento);
    }

    /**
     * Set numero
     *
     * @param string $numero
     * @return Licencia
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero
     *
     * @return string 
     */
    public function getNumero()
    {
        return $this->numero;
    }
}
