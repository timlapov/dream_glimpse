<?php

namespace App\Security\Voter;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class UserVoter extends Voter
{
    public const EDIT = 'USER_EDIT';
    public const DELETE = 'USER_DELETE';

    public function __construct(
        private Security $security
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::DELETE])
            && $subject instanceof \App\Entity\User;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
//        if (!$user instanceof UserInterface) {
//            return false;
//        }
//
//        if ($this->security->isGranted('ROLE_ADMIN') || $token->getUser() === $user) {
//            return true;
//        }
//
//        return false;

        if (!$user instanceof UserInterface) {
            return false;
        }

        // Check if the current user is an admin
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        // Check if the current user is viewing their own profile
        if ($attribute === self::EDIT || $attribute === self::DELETE) {
            return $subject === $user;
        }

        return false;
    }
}
