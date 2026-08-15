<?php

namespace App\Security;

use App\Entity\Phone;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

/**
 * PhoneVoter class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class PhoneVoter extends Voter
{
    /**
     * Stores Security
     * @var Security
     */
    private $security;

    public const PHONE_CREATE = 'phoneCreate';

    public const PHONE_DELETE = 'phoneDelete';

    public const PHONE_DISPLAY = 'phoneDisplay';

    public const PHONE_MODIFY = 'phoneModify';

    private const ATTRIBUTES = array(
        self::PHONE_CREATE,
        self::PHONE_DELETE,
        self::PHONE_DISPLAY,
        self::PHONE_MODIFY,
    );

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (null !== $subject) {
            return $subject instanceof Phone && in_array($attribute, self::ATTRIBUTES);
        }

        return in_array($attribute, self::ATTRIBUTES);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        //Checks current user
        if (null === $token->getUser() || is_string($token->getUser())) {
            return false;
        }

        //Defines access rights
        switch ($attribute) {
            case self::PHONE_CREATE:
                return $this->canCreate();
                break;
            case self::PHONE_DELETE:
                return $this->canDelete($token, $subject);
                break;
            case self::PHONE_DISPLAY:
                return $this->canDisplay($token, $subject);
                break;
            case self::PHONE_MODIFY:
                return $this->canModify($token, $subject);
                break;
        }

        throw new LogicException('Invalid attribute: ' . $attribute);
    }

    /**
     * Checks if is allowed to create
     */
    private function canCreate()
    {
        //Checks roles allowed
        $roles = array(
            'ROLE_MANAGER',
            'ROLE_ADMIN',
        );

        foreach ($roles as $role) {
            if ($this->security->isGranted($role)) {
                return true;
            }
        }

        //Checks roles UNallowed
        $roles = array(
            'ROLE_TRAINEE',
            'ROLE_COACH',
            'ROLE_DRIVER',
            'ROLE_ASSISTANT',
        );

        foreach ($roles as $role) {
            if ($this->security->isGranted($role)) {
                return false;
            }
        }

        return $this->security->isGranted('ROLE_USER');
    }

    /**
     * Checks if is allowed to delete
     */
    private function canDelete($token, $subject)
    {
        //Checks roles allowed
        $roles = array(
            'ROLE_MANAGER',
            'ROLE_ADMIN',
        );

        foreach ($roles as $role) {
            if ($this->security->isGranted($role)) {
                return true;
            }
        }

        return $this->isLinked($token, $subject);
    }

    /**
     * Checks if is allowed to display
     */
    private function canDisplay($token, $subject)
    {
        //Checks roles allowed
        $roles = array(
            'ROLE_DRIVER',
            'ROLE_ASSISTANT',
            'ROLE_MANAGER',
            'ROLE_ADMIN',
        );

        foreach ($roles as $role) {
            if ($this->security->isGranted($role)) {
                return true;
            }
        }

        return $this->isLinked($token, $subject);
    }

    /**
     * Checks if is allowed to modify
     */
    private function canModify($token, $subject)
    {
        //Checks roles allowed
        $roles = array(
            'ROLE_MANAGER',
            'ROLE_ADMIN',
        );

        foreach ($roles as $role) {
            if ($this->security->isGranted($role)) {
                return true;
            }
        }

        return $this->isLinked($token, $subject);
    }

    /**
     * Checks if child is linked to the user
     */
    public function isLinked($token, $subject)
    {
        if (null !== $token->getUser()->getUserPersonLink()) {
            $personId = $token->getUser()->getUserPersonLink()->getPerson()->getPersonId();
            foreach ($subject->getPersons() as $person) {
                if ($person->getPerson()->getPersonId() === $personId) {
                    return true;
                }
            }

            return false;
        }
    }
}
