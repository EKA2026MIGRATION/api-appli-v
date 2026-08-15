<?php

namespace App\Security;

use App\Entity\Staff;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

/**
 * StaffVoter class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class StaffVoter extends Voter
{
    /**
     * Stores Security
     * @var Security
     */
    private $security;

    public const STAFF_CREATE = 'staffCreate';

    public const STAFF_DELETE = 'staffDelete';

    public const STAFF_DISPLAY = 'staffDisplay';

    public const STAFF_LIST = 'staffList';

    public const STAFF_MODIFY = 'staffModify';

    private const ATTRIBUTES = array(
        self::STAFF_CREATE,
        self::STAFF_DELETE,
        self::STAFF_DISPLAY,
        self::STAFF_LIST,
        self::STAFF_MODIFY,
    );

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (null !== $subject) {
            return $subject instanceof Staff && in_array($attribute, self::ATTRIBUTES);
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
            case self::STAFF_CREATE:
                return $this->canCreate();
                break;
            case self::STAFF_DELETE:
                return $this->canDelete();
                break;
            case self::STAFF_DISPLAY:
                return $this->canDisplay();
                break;
            case self::STAFF_LIST:
                return $this->canList();
                break;
            case self::STAFF_MODIFY:
                return $this->canModify();
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
        );

        foreach ($roles as $role) {
            if ($this->security->isGranted($role)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks if is allowed to modify
     */
    private function canModify()
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
}
