<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * TipoLicencia
 *
 * @ORM\Entity()
 */
class TipoLicencia
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
     *@ORM\Column(name="descripcion", type="string", length=255)
     */
    private $descripcion;

    /**
     * @var String
     *
     * @ORM\Column(name="genero_jubilado", type="string", length=1, nullable=true)
     * @Assert\Choice(choices={"m", "f", "j"}, message="Seleccione una opcion Valida")
     *
     */
    private $generoJubilado;

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
     * @var float
     *
     * @ORM\Column(name="arancel", type="float", nullable=true)
     * @Assert\Range(
     *              min = 0,
     *              minMessage = "El arancel no pueden ser menor que cero"
     * )
     */
    private $arancel;

    /**
     * @var float
     *
     * @ORM\Column(name="porcentaje_recargo_primer_vencimiento", type="float", nullable=true)
     * )
     */
    private $porcentajeRecargoPrimerVencimiento;

       /**
     * @var float
     *
     * @ORM\Column(name="porcentaje_recargo_segundo_vencimiento", type="float", nullable=true)
     * )
     */
    private $porcentajeRecargoSegundoVencimiento;

    /**
     * @var integer
     *
     * @ORM\Column(name="dias_vigencia", type="integer", nullable=true)
     * @Assert\Range(
     *              min = 0
     * )
     */
    private $diasVigencia;

     /**
     * @var integer
     *
     * @ORM\Column(name="dias_primer_vencimiento", type="integer", nullable=true)
     * @Assert\Range(
     *              min = 0
     * )
     */
    private $diasPrimerVencimiento;

     /**
     * @var integer
     *
     * @ORM\Column(name="dias_segundo_vencimiento", type="integer", nullable=true)
     * @Assert\Range(
     *              min = 0
     * )
     */
    private $diasSegundoVencimiento;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_tope", type="datetime")
     * @Assert\NotNull()
     */
    private $fechaTope;

    /**
     * @var boolean
     * 
     * @ORM\Column(name="is_active", type="boolean")
     * 
     */
    protected $isActive;


     /**
     *@var string
     * Define si es un tipo de licencia para:
     *       "T" Todas las provincias
     *       "SF" Provincia Santa Fe     
     *
     *@ORM\Column(name="aplica_provincia", type="string", nullable=true)
     */
    private $aplicaEnProvincia;

    public static $SantaFe = 'SF';
    public static $Todas = 'T';

    /*============================Setter y getters ===============================*/

    function __construct()
    {
        $this->setIsActive(true);
    }

    function __toString()
    {
        $descripcionArancel="";
        if(!is_null($this->getArancel()) 
            && $this->getArancel() == 0)
            {
                $descripcionArancel = " Gratuita";
            }else{
                $descripcionArancel = " $".$this->getArancel();
            }

            $fechaTope ="";
            try{
                $fechaTope = $this->tipoLicencia->getFechaTope()->format('Y-m-d H:i:s');
            }catch(\Exception $e){

            }
            return $this->getDescripcion(). $descripcionArancel .$fechaTope;
        
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
     * @return string
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * @param string $descripcion
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
    }

    /**
     * @return float
     */
    public function getArancel()
    {
        return $this->arancel;
    }

    /**
     * @param float $arancel
     */
    public function setArancel($arancel)
    {
        $this->arancel = $arancel;
    }

    /**
     * @return int
     */
    public function getDiasVigencia()
    {
        return $this->diasVigencia;
    }

    /**
     * @param int $diasVigencia
     */
    public function setDiasVigencia($diasVigencia)
    {
        $this->diasVigencia = $diasVigencia;
    }

    /**
     * @return \DateTime
     */
    public function getFechaTope()
    {
        return $this->fechaTope;
    }

    /**
     * @param \DateTime $fechaTope
     */
    public function setFechaTope($fechaTope)
    {
        $this->fechaTope = $fechaTope;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive() {
        return $this->isActive;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return Persona
     */
    public function setIsActive($isActive) {
        $this->isActive = $isActive;

        return $this;
    }

    /*============================Constructor   ===============================
     */
    
    /**
     * Set clienteSap
     *
     * @param string $clienteSap
     * @return TipoLicencia
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
     * @return TipoLicencia
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
     * Set diasPrimerVencimiento
     *
     * @param integer $diasPrimerVencimiento
     * @return TipoLicencia
     */
    public function setDiasPrimerVencimiento($diasPrimerVencimiento)
    {
        $this->diasPrimerVencimiento = $diasPrimerVencimiento;

        return $this;
    }

    /**
     * Get diasPrimerVencimiento
     *
     * @return integer 
     */
    public function getDiasPrimerVencimiento()
    {
        return $this->diasPrimerVencimiento;
    }

    /**
     * Set diasSegundoVencimiento
     *
     * @param integer $diasSegundoVencimiento
     * @return TipoLicencia
     */
    public function setDiasSegundoVencimiento($diasSegundoVencimiento)
    {
        $this->diasSegundoVencimiento = $diasSegundoVencimiento;

        return $this;
    }

    /**
     * Get diasSegundoVencimiento
     *
     * @return integer 
     */
    public function getDiasSegundoVencimiento()
    {
        return $this->diasSegundoVencimiento;
    }

    /**
     * Set porcentajeRecargoPrimerVencimiento
     *
     * @param float $porcentajeRecargoPrimerVencimiento
     * @return TipoLicencia
     */
    public function setPorcentajeRecargoPrimerVencimiento($porcentajeRecargoPrimerVencimiento)
    {
        $this->porcentajeRecargoPrimerVencimiento = $porcentajeRecargoPrimerVencimiento;

        return $this;
    }

    /**
     * Get porcentajeRecargoPrimerVencimiento
     *
     * @return float 
     */
    public function getPorcentajeRecargoPrimerVencimiento()
    {
        return $this->porcentajeRecargoPrimerVencimiento;
    }

    /**
     * Set porcentajeRecargoSegundoVencimiento
     *
     * @param float $porcentajeRecargoSegundoVencimiento
     * @return TipoLicencia
     */
    public function setPorcentajeRecargoSegundoVencimiento($porcentajeRecargoSegundoVencimiento)
    {
        $this->porcentajeRecargoSegundoVencimiento = $porcentajeRecargoSegundoVencimiento;

        return $this;
    }

    /**
     * Get porcentajeRecargoSegundoVencimiento
     *
     * @return float 
     */
    public function getPorcentajeRecargoSegundoVencimiento()
    {
        return $this->porcentajeRecargoSegundoVencimiento;
    }

    public function copyValues($objectForCopy)
    {           
        $vars=is_object($objectForCopy)?get_object_vars($objectForCopy):$objectForCopy;
        if(!is_array($vars)) throw Exception('Sin propiedades para el objeto TipoLicencia!');
        foreach ($vars as $key => $value) {
            $this->$key = $value;
        }
        return $this;
    }

    /**
     * Set generoJubilado
     *
     * @param string $generoJubilado
     * @return TipoLicencia
     */
    public function setGeneroJubilado($generoJubilado)
    {
        $this->generoJubilado = $generoJubilado;

        return $this;
    }

    /**
     * Get generoJubilado
     *
     * @return string 
     */
    public function getGeneroJubilado()
    {
        return $this->generoJubilado;
    }

    /**
     * Set aplicaEnProvincia
     *
     * @param string $aplicaEnProvincia
     * @return TipoLicencia
     */
    public function setAplicaEnProvincia($aplicaEnProvincia)
    {
        $this->aplicaEnProvincia = $aplicaEnProvincia;

        return $this;
    }

    /**
     * Get aplicaEnProvincia
     *
     * @return string 
     */
    public function getAplicaEnProvincia()
    {
        return $this->aplicaEnProvincia;
    }
}
