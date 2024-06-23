<?php

declare (strict_types=1);

namespace App\Helper;

use App\Entity\Customer;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\EntityManagerInterface;

class ProfileHelper {

    private const PROFILE_ERRORS = [
        'saveImageError' => 'error.saving.profile.image'
    ];

    private EntityManagerInterface $em;
    
    // Injetado no constructor desde services.yaml
    private string $uploadDirectory;

    public function __construct(EntityManagerInterface $em, string $uploadDirectory) {

        $this->em = $em;
        $this->uploadDirectory = $uploadDirectory;
    }
    
    public function isValidImage(UploadedFile $file): bool {
        
        $maxFileSize = 1 * 1024 * 1024;
        
        if (!$file && !$file->isValid()) {
            return false;
        }
        
        if ($file->getClientMimeType() !== 'image/png') {
            return false;
        }
        
        if ($file->getSize() > $maxFileSize) {
            return false;
        }
        
        return true;
    }
    
    // 
    public function saveImage(UploadedFile $file, Customer $user): void {

        try {
            
            // Apagamos a imagem de profile anterior se existir
            $fileNameToRemove = $user->getProfilePhoto();
            if ($fileNameToRemove && \file_exists($this->uploadDirectory . '/' . $fileNameToRemove)) {
                \unlink($this->uploadDirectory . '/' . $fileNameToRemove);
            }

            $filename = md5(uniqid()) . '.' . $file->getClientOriginalExtension();
            $fileNameToSave = $file->move($this->uploadDirectory, $filename)->getFileName();
            $user->setProfilePhoto($fileNameToSave);
            $this->em->persist($user);
            $this->em->flush();
        } catch (\Exception $ex) {
            
            throw new \Exception(self::PROFILE_ERRORS['saveImageError']);
        }
    }
    
    public function getImagePath(Customer $user): string {
        
        return $this->uploadDirectory . '/' . $user->getProfilePhoto();
    }
}
