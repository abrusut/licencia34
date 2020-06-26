<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * @ORM\Entity()
 * @ORM\Table(name="usuario")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"usuario" = "Usuario", "tecnico" = "Tecnico", "administrador" = "Administrador"})
 * @UniqueEntity("email")
 * AdvancedUserInterface
 * 
 */

class Usuario extends BaseUser implements \Serializable {

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(name="nombre", type="string", length=50, nullable=true)     
      
     */
    protected $nombre;

    /**
     * @var string
     * @ORM\Column(name="apellido", type="string", length=50, nullable=true)     
 
     */
    protected $apellido;

    /**
     * @var string
     *
     * @ORM\Column(name="dni", type="string", length=9, unique=true, nullable=true)     
     * @Assert\Regex(pattern="/^[F|M|f|m|\d]\d{1,7}$/")
     */
    protected $dni;

  

    /**
     * @var string
     *
     * @ORM\Column(name="telefono", type="string", length=50, nullable=true)
     */
    protected $telefono;

     /**
     * @ORM\ManyToMany(targetEntity="Rol")
     * @ORM\JoinTable(name="usuario_rol",
     *          joinColumns={@ORM\JoinColumn(name="usuario_id", referencedColumnName="id")},
     *          inverseJoinColumns={@ORM\JoinColumn(name="rol_id", referencedColumnName="id")}
     * )
     */
    protected $rolesArray;


    public function __construct()
    {
        parent::__construct();
        $this->rolesArray = new ArrayCollection();
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
     * Set nombre
     *
     * @param string $nombre
     * @return Usuario
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string 
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set apellido
     *
     * @param string $apellido
     * @return Usuario
     */
    public function setApellido($apellido)
    {
        $this->apellido = $apellido;

        return $this;
    }

    /**
     * Get apellido
     *
     * @return string 
     */
    public function getApellido()
    {
        return $this->apellido;
    }

    /**
     * Set dni
     *
     * @param string $dni
     * @return Persona
     */
    public function setDni($dni) {
        $this->dni = $dni;

        return $this;
    }

    /**
     * Get dni
     *
     * @return string
     */
    public function getDni() {
        return $this->dni;
    }

   
    /**
     * Set telefonoFijo
     *
     * @param string $telefono
     * @return Usuario
     */
    public function setTelefono($telefono) {
        $this->telefono = $telefono;

        return $this;
    }

    /**
     * Get telefonoFijo
     *
     * @return string
     */
    public function getTelefono() {
        return $this->telefono;
    }

   

    /* ===================================Issers ===================== */

    public function isAccountNonExpired() {
        return true;
    }

    public function isAccountNonLocked() {
        return true;
    }

    public function isCredentialsNonExpired() {
        return true;
    }

   
    /* ===================================Método to String ===================== */

    /**
     *  Retorna el nombre y el apellido del Usuario
     *
     * @return string
     *
     */
    public function __toString() {
        return $this->id." ".$this->getNombre() . " " . $this->getApellido();
    }

    /* ===================================Métodos ===================== */

    //Para la gestión de login y seguridad
    public function eraseCredentials() {

    }
    /**
     * Set rolesArray
     *
     * @param string $rolesArray
     *
     * @return Usuario
     */
    public function setRolesArray($rolesArray)
    {
        $this->rolesArray = $rolesArray;
        unset($this->roles);
        foreach ($rolesArray as $rol) {
            $this->addRole($rol->getNombreSTD());
        }
        return $this;
    }

    /**
     * Add rolesArray
     *
     * @param Rol $rolesArray
     *
     * @return Usuario
     */
    public function addRolesArray($rol)
    {
        $this->rolesArray->add($rol);
        return $this;
    }

    /**
     * Remove rolesArray
     */
    public function removeRolesArray($rol)
    {
        $this->rolesArray->remove($rol);
        return $this;
    }

    /**
     * Get rolesArray
     *
     * @return Rol
     */
    public function getRolesArray()
    {
        return $this->rolesArray;
    }
    

   
}
