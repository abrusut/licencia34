<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\PrePersist;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\FileBag;

/**
 * FileRendicionLiquidacion
 *  
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class FileRendicionLiquidacion
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $nombreArchivo;

    /**
     * @ORM\Column(type="string", nullable=true)    
     */
    private $archivo;
   
    /**
     * @ORM\Column(type="string", nullable=true)    
     */
    private $extension;

    /**
     * @ORM\Column(type="string", nullable=true)    
     */
    private $size;

     /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $nombreOriginalArchivo;

     /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $fullPath;

     /**
     * The mime type provided by the uploader.
     * @ORM\Column(type="string", length=100,nullable=true)
     *
     * @var string
     */
    private $mimeType;

    /**     
     * @ORM\Column(type="string", length=100,nullable=true)    
     * @var string
     */
    private $guessClientExtension;    

     /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)     
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
     * @var boolean
     * 
     * @ORM\Column(name="procesado", type="boolean")
     * 
     */
    protected $procesado;

     /**    
    * @var \AppBundle\Entity\Rendicion
    * @ORM\OneToMany(targetEntity="AppBundle\Entity\Rendicion", mappedBy="fileRendicionLiquidacion")
    */
    private $rendiciones;
    
     /**    
    * @var \AppBundle\Entity\Rendicion
    * @ORM\OneToMany(targetEntity="AppBundle\Entity\Rendicion", mappedBy="fileRendicionLiquidacion")
    */
    private $liquidaciones;

     /**    
    * @var \AppBundle\Entity\Comprobante
    * @ORM\OneToMany(targetEntity="AppBundle\Entity\Comprobante", mappedBy="fileRendicionLiquidacion")
    */
    private $comprobantes;

    public function __construct(){
        $this->procesado = false;
    }
    /**
     * bind values from archivo
     *
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile 
     * @return void
     */
    public function bindValueFromFile(UploadedFile $file, $fileName,$pathForUpload ){
        $this->setNombreOriginalArchivo($file->getClientOriginalName());
        $this->setExtension($file->getClientOriginalExtension());
        $this->setSize($file->getClientSize());
        $this->setNombreArchivo($file->getFilename());
        $this->setArchivo($fileName);
        $this->setGuessClientExtension($file->guessClientExtension());
        $this->setMimeType($file->getClientMimeType());
        $this->setFullPath($pathForUpload.$fileName);
        
        //$this->setCreatedAt(new \DateTime());        
    }
   
    public function __toString() {
        return $this->getNombreArchivo() ." - ".$this->getArchivo();
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
     * Set nombreArchivo
     *
     * @param string $nombreArchivo
     * @return FileRendicionLiquidacion
     */
    public function setNombreArchivo($nombreArchivo)
    {
        $this->nombreArchivo = $nombreArchivo;

        return $this;
    }

    /**
     * Get nombreArchivo
     *
     * @return string 
     */
    public function getNombreArchivo()
    {
        return $this->nombreArchivo;
    }

    /**
     * Get archivo
     *
     * @return \File 
     */
    public function getArchivo()
    {
        return $this->archivo;
    }

      /**
     * Set archivo
     *
     * @param string $archivo
     * @return FileRendicionLiquidacion
     */
    public function setArchivo($archivo)
    {
        $this->archivo = $archivo;

        return $this;
    }

    
    /**
     * Set extension
     *
     * @param string $extension
     * @return FileRendicionLiquidacion
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;

        return $this;
    }

    /**
     * Get extension
     *
     * @return string 
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * Set size
     *
     * @param string $size
     * @return FileRendicionLiquidacion
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return string 
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set nombreOriginalArchivo
     *
     * @param string $nombreOriginalArchivo
     * @return FileRendicionLiquidacion
     */
    public function setNombreOriginalArchivo($nombreOriginalArchivo)
    {
        $this->nombreOriginalArchivo = $nombreOriginalArchivo;

        return $this;
    }

    /**
     * Get nombreOriginalArchivo
     *
     * @return string 
     */
    public function getNombreOriginalArchivo()
    {
        return $this->nombreOriginalArchivo;
    }

    /**
     * Set mimeType
     *
     * @param string $mimeType
     * @return FileRendicionLiquidacion
     */
    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    /**
     * Get mimeType
     *
     * @return string 
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * Set guessClientExtension
     *
     * @param string $guessClientExtension
     * @return FileRendicionLiquidacion
     */
    public function setGuessClientExtension($guessClientExtension)
    {
        $this->guessClientExtension = $guessClientExtension;

        return $this;
    }

    /**
     * Get guessClientExtension
     *
     * @return string 
     */
    public function getGuessClientExtension()
    {
        return $this->guessClientExtension;
    }

    /**
     * Set guessExtension
     *
     * @param string $guessExtension
     * @return FileRendicionLiquidacion
     */
    public function setGuessExtension($guessExtension)
    {
        $this->guessExtension = $guessExtension;

        return $this;
    }

    /**
     * Get guessExtension
     *
     * @return string 
     */
    public function getGuessExtension()
    {
        return $this->guessExtension;
    }

    /**
     * Set procesado
     *
     * @param boolean $procesado
     * @return FileRendicionLiquidacion
     */
    public function setProcesado($procesado)
    {
        $this->procesado = $procesado;

        return $this;
    }

    /**
     * Get procesado
     *
     * @return boolean 
     */
    public function getProcesado()
    {
        return $this->procesado;
    }

    /**
     * Set fullPath
     *
     * @param string $fullPath
     * @return FileRendicionLiquidacion
     */
    public function setFullPath($fullPath)
    {
        $this->fullPath = $fullPath;

        return $this;
    }

    /**
     * Get fullPath
     *
     * @return string 
     */
    public function getFullPath()
    {
        return $this->fullPath;
    }

    /**
     * Add rendiciones
     *
     * @param \AppBundle\Entity\Rendicion $rendiciones
     * @return FileRendicionLiquidacion
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
     * Add liquidaciones
     *
     * @param \AppBundle\Entity\Rendicion $liquidaciones
     * @return FileRendicionLiquidacion
     */
    public function addLiquidacione(\AppBundle\Entity\Rendicion $liquidaciones)
    {
        $this->liquidaciones[] = $liquidaciones;

        return $this;
    }

    /**
     * Remove liquidaciones
     *
     * @param \AppBundle\Entity\Rendicion $liquidaciones
     */
    public function removeLiquidacione(\AppBundle\Entity\Rendicion $liquidaciones)
    {
        $this->liquidaciones->removeElement($liquidaciones);
    }

    /**
     * Get liquidaciones
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLiquidaciones()
    {
        return $this->liquidaciones;
    }

    /**
     * Add comprobantes
     *
     * @param \AppBundle\Entity\Comprobante $comprobantes
     * @return FileRendicionLiquidacion
     */
    public function addComprobante(\AppBundle\Entity\Comprobante $comprobantes)
    {
        $this->comprobantes[] = $comprobantes;

        return $this;
    }

    /**
     * Remove comprobantes
     *
     * @param \AppBundle\Entity\Comprobante $comprobantes
     */
    public function removeComprobante(\AppBundle\Entity\Comprobante $comprobantes)
    {
        $this->comprobantes->removeElement($comprobantes);
    }

    /**
     * Get comprobantes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getComprobantes()
    {
        return $this->comprobantes;
    }
}
