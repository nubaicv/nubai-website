<?php

declare (strict_types=1);

namespace App\Helper;

use App\Entity\Customer;
use Doctrine\ORM\EntityManagerInterface;

class CustomerPasswordResetHelper {

    private const PASSWORD_RESET_ERRORS = [
        'resetTokenLifetimeExpired' => 'reset.token.lifetime.expired',
        'generateResetTokenError' => 'error.generate.reset.token',
        'validateResetTokenError' => 'error.validate.reset.token'
    ];

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em) {

        $this->em = $em;
    }

    public function generateResetToken(Customer $user): string {

        $resetToken = \bin2hex(\random_bytes(32));

        try {

            $user->setResetToken($resetToken);
            $user->setResetTokenExpiresAt(new \DateTime('3 min'));
            $this->em->persist($user);
            $this->em->flush();
        } catch (\Exception $ex) {

            throw new \Exception(self::PASSWORD_RESET_ERRORS['generateResetTokenError']);
        }

        return $resetToken;
    }

    public function validateTokenAndFetchUser(string $fullToken): ?Customer {

        try {

            $user = $this->em->getRepository(Customer::class)->findOneBy([
                'resetToken' => $fullToken,
            ]);

            if (!$user) {
                
                return null;
            }
            
            if ($this->getTokenLifetime($user) < 0) {
                
                $this->removeResetRequest($fullToken);
                return null;
            }
        } catch (\Exception $ex) {
            
            throw new \Exception(self::PASSWORD_RESET_ERRORS['validateResetTokenError']);
        }

        return $user;
    }

    public function removeResetRequest(string $fullToken): void {

        $user = $this->em->getRepository(Customer::class)->findOneBy([
            'resetToken' => $fullToken,
        ]);

        $user->setResetToken(null);
        $user->setResetTokenExpiresAt(null);
        $this->em->persist($user);
        $this->em->flush();
    }

    private function getTokenLifetime(Customer $user): int {
        
        $expiresAt = $user->getResetTokenExpiresAt()->getTimestamp();
        
        return ($expiresAt - \time());
    }
}
