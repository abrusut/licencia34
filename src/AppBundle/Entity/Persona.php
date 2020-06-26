<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * Persona
 * @ORM\Entity()
 * @ORM\Table(name="persona")
 * @ORM\HasLifecycleCallbacks()
 */
class Persona
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="nombre", type="string", length=60,nullable=false)
     * @Assert\NotBlank()
     * @Assert\Length(min = 3)
     * @Assert\Length( max = 60)
     * @Assert\NotNull()
     */
    private $nombre;

    /**
     * @var string
     * @ORM\Column(name="apellido", type="string", length=60,nullable=false)
     * @Assert\NotBlank()
     * @Assert\Length(min = 3)
     * @Assert\Length( max = 60)
     * @Assert\NotNull()
     */
    private $apellido;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_nacimiento", type="date", nullable=false)
     * @Assert\NotNull()
     */
    private $fechaNacimiento;

    /**
     * @var string
     *
     * @ORM\Column(name="domicilio_calle", type="string", nullable=true,length=60)
     * @Assert\NotBlank()
     * @Assert\Length(min = 3)
     * @Assert\Length( max = 60)
     * @Assert\NotNull()
     */
    private $domicilioCalle;

    /**
     * @var integer
     *
     * @ORM\Column(name="domicilio_numero", type="integer",nullable=true)
     */
    private $domicilioNumero;

    /**     
     * @var \AppBundle\Entity\Localidad
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Localidad")
     */
    private $localidad;

    /**
     * @var string
     *
     * @ORM\Column(name="localidad_otra_provincia", type="string", nullable=true)
     */
    private $localidadOtraProvincia;


   /**
     * @var AppBundle\Entity\TipoDocumento
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\TipoDocumento")
     */
    public $tipoDocumento;


    /**
     * @var integer
     *
     * @ORM\Column(name="numero_documento", type="string", length=9)
     * @Assert\NotBlank()
     * @Assert\Length(min = 8)
     * @Assert\Length( max = 8)
     * @Assert\NotNull()     
     */
    private $numeroDocumento;


    /**
     * @var String
     *
     * @ORM\Column(name="sexo", type="string", length=1, nullable=false)
     * @Assert\Choice(choices={"m", "f"}, message="Seleccione un Sexo Valido")
     *
     */
    private $sexo;

    /**
     * @var Boolean
     *
     * @ORM\Column(name="jubilado", type="boolean", nullable=false)     
     *
     */
    private $jubilado;

    /**
     * @var integer
     *
     * @ORM\Column(name="telefono", type="string", length=15, nullable=true)
     */
    private $telefono;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=50, nullable=false)
     * @Assert\NotNull()
     * @Assert\Email()
     */
    private $email;
    
     /**     
     * @var \AppBundle\Entity\Provincia
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Provincia")
     * @ORM\JoinColumn(name="provincia_id", referencedColumnName="id")
     */
    private $provincia;

    /**    
    * @var \AppBundle\Entity\Licencia
    * @ORM\OneToMany(targetEntity="AppBundle\Entity\Licencia", mappedBy="persona")
    */
    private $licencias;

    
     /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true, options={"comment"="Tabla persona"})
     */
    private $createdAt;

    public function __construct()
    {
        $this->licencias = new ArrayCollection();
    }



    /**
     * @return mixed
     */

    public function getProvincia()
    {
        return $this->provincia;
    }

    /**
     * @param mixed $provincia
     */
    public function setProvincia(Provincia $provincia)
    {
        $this->provincia = $provincia;
    }


   
    /**
     *  Retorna el nombre y el apellido del Usuario
     *
     * @return string
     *
     */
    public function __toString() {
        return $this->id." ".$this->getNombre() . " " . $this->getApellido();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * @param string $nombre
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    /**
     * @return string
     */
    public function getApellido()
    {
        return $this->apellido;
    }

    /**
     * @param string $apellido
     */
    public function setApellido($apellido)
    {
        $this->apellido = $apellido;
    }

    /**
     * @return DateTime
     */
    public function getFechaNacimiento()
    {
        return $this->fechaNacimiento;
    }

    /**
     * @param DateTime $fechaNacimiento
     */
    public function setFechaNacimiento($fechaNacimiento)
    {
        $this->fechaNacimiento = $fechaNacimiento;
    }

    /**
     * @return mixed
     */
    public function getLocalidad()
    {
        return $this->localidad;
    }

    /**
     * @param mixed $localidad
     */
    public function setLocalidad($localidad)
    {
        $this->localidad = $localidad;
    }

    /**
     * @return string
     */
    public function getLocalidadOtraProvincia()
    {
        return $this->localidadOtraProvincia;
    }

    /**
     * @param string $localidadOtraProvincia
     */
    public function setLocalidadOtraProvincia($localidadOtraProvincia)
    {
        $this->localidadOtraProvincia = $localidadOtraProvincia;
    }

    /**
     * @return string
     */
    public function getNumeroDocumento()
    {
        return $this->numeroDocumento;
    }

    /**
     * @param string $numeroDocumento
     */
    public function setNumeroDocumento($numeroDocumento)
    {
        $this->numeroDocumento = $numeroDocumento;
    }

    /**
     * @return String
     */
    public function getSexo()
    {
        return $this->sexo;
    }

    /**
     * @param String $sexo
     */
    public function setSexo($sexo)
    {
        $this->sexo = $sexo;
    }
    

    /**
     * @return string
     */
    public function getTelefono()
    {
        return $this->telefono;
    }

    /**
     * @param string $telefono
     */
    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

  
    /**
     * @return mixed
     */
    public function gettipoDocumento()
    {
        return $this->tipoDocumento;
    }

    /**
     * @param mixed $tipoDocumento
     */
    public function setTipoDocumento($tipoDocumento)
    {
        $this->tipoDocumento = $tipoDocumento;
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
     * Add licencias
     *
     * @param \AppBundle\Entity\Licencia $licencias
     * @return Persona
     */
    public function addLicencia(\AppBundle\Entity\Licencia $licencias)
    {
        $this->licencias[] = $licencias;

        return $this;
    }

    /**
     * Remove licencias
     *
     * @param \AppBundle\Entity\Licencia $licencias
     */
    public function removeLicencia(\AppBundle\Entity\Licencia $licencias)
    {
        $this->licencias->removeElement($licencias);
    }

    /**
     * Get licencias
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLicencias()
    {
        return $this->licencias;
    }

    /**
     * Set jubilado
     *
     * @param boolean $jubilado
     * @return Persona
     */
    public function setJubilado($jubilado)
    {
        $this->jubilado = $jubilado;

        return $this;
    }

    /**
     * Get jubilado
     *
     * @return boolean 
     */
    public function getJubilado()
    {
        return $this->jubilado;
    }

    /**
     * Set domicilioCalle
     *
     * @param string $domicilioCalle
     * @return Persona
     */
    public function setDomicilioCalle($domicilioCalle)
    {
        $this->domicilioCalle = $domicilioCalle;

        return $this;
    }

    /**
     * Get domicilioCalle
     *
     * @return string 
     */
    public function getDomicilioCalle()
    {
        return $this->domicilioCalle;
    }

    /**
     * Set domicilioNumero
     *
     * @param integer $domicilioNumero
     * @return Persona
     */
    public function setDomicilioNumero($domicilioNumero)
    {
        $this->domicilioNumero = $domicilioNumero;

        return $this;
    }

    /**
     * Get domicilioNumero
     *
     * @return integer 
     */
    public function getDomicilioNumero()
    {
        return $this->domicilioNumero;
    }
    
    public function copyValues($objectForCopy)
    {   
        // Campos que no se deben actualizar
        $exludeProperties = array("id","numeroDocumento", "sexo", "tipoDocumento");

        $vars=is_object($objectForCopy)?get_object_vars($objectForCopy):$objectForCopy;
        if(!is_array($vars)) throw Exception('Sin propiedades para el objeto persona!');
        foreach ($vars as $key => $value) {
            if(!in_array($key,$exludeProperties))
            {
                $this->$key = $value;
            }
        }
    }

    public function getNombreCompleto()
    {
        return $this->getNombre() . " " . $this->getApellido();
    }
}
