<?php
namespace AppBundle\Service;


use AppBundle\Service\IFileUploaderService;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\Exception\Exception;
use Psr\Log\LoggerInterface;

class FileUploaderServiceImpl {

    private $logger;

    public function __construct(LoggerInterface $logger) 
    {
        $this->logger = $logger;
    }

    public function upload($uploadDir, $file, $filename) 
    {
        $this->logger->info("FileUploaderServiceImpl->upload, Voy a subir el archivo a ".$uploadDir. " fileName ". $filename);
        try {            
          return  $file->move($uploadDir, $filename);            
        } catch (FileException $e){            
            $this->logger->error("FileUploaderServiceImpl->upload, Error Subiendo archivo a ".$uploadDir. " fileName ". $filename);
            $this->logger->error("FileUploaderServiceImpl->upload, FileException: ".$e->getMessage());           
            throw new $e;        
        } catch (Exception $e){
            $this->logger->error("FileUploaderServiceImpl->upload, Error Subiendo archivo a ".$uploadDir. " fileName ". $filename);
            $this->logger->error("FileUploaderServiceImpl->upload, Exception: ".$e->getMessage());            
            throw new $e;
        } catch (\Exception $e){
            $this->logger->error("FileUploaderServiceImpl->upload, Error Subiendo archivo a ".$uploadDir. " fileName ". $filename);
            $this->logger->error("FileUploaderServiceImpl->upload, Exception: ".$e->getMessage());            
            throw new $e;
        }
        
    }


}




?>