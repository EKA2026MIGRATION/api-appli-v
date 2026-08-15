<?php

namespace App\Security;

use App\Entity\StaffPresence;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

/**
 * StaffPresenceVoter class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class StaffPresenceVoter extends Voter
{
    /**
     * Stores Security
     * @var Security
     */
    private $security;

    public const STAFF_PRESENCE_CREATE = 'staffPresenceCreate';

    public const STAFF_PRESENCE_DELETE = 'staffPresenceDelete';

    public const STAFF_PRESENCE_DISPLAY = 'staffPresenceDisplay';

    public const STAFF_PRESENCE_LIST = 'staffPresenceList';

    private const ATTRIBUTES = array(
        self::STAFF_PRESENCE_CREATE,
        self::STAFF_PRESENCE_DELETE,
        self::STAFF_PRESENCE_DISPLAY,
        self::STAFF_PRESENCE_LIST,
    );

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (null !== $subject) {
            return $subject instanceof StaffPresence && in_array($attribute, self::ATTRIBUTES);
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
            case self::STAFF_PRESENCE_CREATE:
                return $this->canCreate();
                break;
            case self::STAFF_PRESENCE_DELETE:
                return $this->canDelete();
                break;
            case self::STAFF_PRESENCE_DISPLAY:
                return $this->canDisplay();
                break;
            case self::STAFF_PRESENCE_LIST:
                return $this->canList();
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

        return false;
    }

    /**
     * Checks if is allowed to delete
     */
    private function canDelete()
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

        return false;
    }

    /**
     * Checks if is allowed to display
     */
    private function canDisplay()
    {
        //Checks roles allowed
        $roles = array(
            'ROLE_DRIVER',
            'ROLE_COACH',
            'ROLE_MANAGER',
            'ROLE_ADMIN',
            'ROLE_ASSISTANT'
        );

        foreach ($roles as $role) {
            if ($this->security->isGranted($role)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks if is allowed to list
     */
    private function canList()
    {
        //Checks roles allowed
        $roles = array(
            'ROLE_DRIVER',
            'ROLE_COACH',
            'ROLE_MANAGER',
            'ROLE_ADMIN',
            'ROLE_ASSISTANT'
        );

        foreach ($roles as $role) {
            if ($this->security->isGranted($role)) {
                return true;
            }
        }

        return false;
    }
}
