<?php

namespace App\Security\Voter;

use App\Entity\Comment;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class CommentVoter extends Voter
{

    public const DELETE = 'COMMENT_DELETE';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject)
    {
        return ($attribute === self::DELETE) && ($subject instanceof Comment);
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

        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        return $this->canDelete($subject, $user);

        throw new \LogicException("This shouldn't be reached.");
    }

    private function canDelete(Comment $comment, UserInterface $user): bool
    {
        return $comment->getAuthor() === $user;
    }
}
