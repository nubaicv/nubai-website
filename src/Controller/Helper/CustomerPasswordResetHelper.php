<?php

declare (strict_types=1);

namespace App\Controller\Helper;

use App\Entity\Customer;
use Doctrine\ORM\EntityManagerInterface;

class CustomerPasswordResetHelper {

    private const PASSWORD_RESET_ERRORS = [
        'resetTokenLifetimeExpired' => 'reset.token.lifetime.expired',
        'generateResetTokenError' => 'error.generate.reset.token'
    ];

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em) {

        $this->em = $em;
    }

    public function generateResetToken(Customer $user): string {

        $resetToken = \bin2hex(\random_bytes(32));

        try {

            $user->setResetToken($resetToken);
            $this->em->persist($user);
            $this->em->flush();
        } catch (\Exception $ex) {
            
            throw new \Exception(self::PASSWORD_RESET_ERRORS['generateResetTokenError']);
        }

        return $resetToken;
    }

    public function validateTokenAndFetchUser(string $fullToken): ?Customer {
        
        return null;
    }

    public function removeResetRequest(string $fullToken): void {
        
    }

    private function getTokenLifetime(): int {
        //para usar em validateTokenAndFetchUser
    }
}
