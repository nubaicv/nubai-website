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
        
        $maxFileSize = 5 * 1024 * 1024;
        
        if (!$file && !$file->isValid()) {
            
            return false;
        }
        
        if ($file->getSize() > $maxFileSize) {
            
            return false;
        }
        
        return true;
    }

    public function saveImage(UploadedFile $file, Customer $user) {

        try {

            $filename = md5(uniqid()) . '.' . $file->getClientOriginalExtension();
            $file->move($this->uploadDirectory, $filename);
        } catch (\Exception $ex) {
            
            throw new \Exception(self::PROFILE_ERRORS['saveImageError']);
        }
    }
    
    private function removePreviousImage(Customer $user): void {
        
    }
    
    private function createFileName(Customer $user): string {
        
    }
}
