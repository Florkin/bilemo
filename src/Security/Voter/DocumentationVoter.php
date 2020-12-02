<?php

namespace App\Security\Voter;

use App\Entity\DocSection;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class DocumentationVoter extends Voter
{
    /**
     * @var Security
     */
    private $security;

    /**
     * DocumentationVoter constructor.
     * @param Security $security
     */
    public function __construct(Security $security)
    {

        $this->security = $security;
    }

    protected function supports($attribute, $subject)
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['EDIT_DOC'])
            && $subject instanceof DocSection;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        return false;
    }
}
