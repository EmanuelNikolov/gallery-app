<?php

namespace App\Security\Voter;

use App\Entity\Photo;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class PhotoVoter extends Voter
{

    public const DELETE = 'PHOTO_DELETE';

    protected function supports($attribute, $subject)
    {
        return ($attribute === self::DELETE) && ($subject instanceof Photo);
    }

    protected function voteOnAttribute(
      $attribute,
      $subject,
      TokenInterface $token
    ) {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        return $this->canEdit($subject, $user);

        throw new \LogicException("This shouldn't be reached.");
    }

    private function canEdit(Photo $photo, UserInterface $user): bool
    {
        return $photo->getUser() === $user;
    }
}
