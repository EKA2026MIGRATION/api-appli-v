<?php

namespace App\Security;

use App\Entity\PickupActivity;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

/**
 * PickupActivityVoter class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class PickupActivityVoter extends Voter
{
    /**
     * Stores Security
     * @var Security
     */
    private $security;

    public const PICKUP_ACTIVITY_CREATE = 'pickupActivityCreate';

    public const PICKUP_ACTIVITY_DELETE = 'pickupActivityDelete';

    public const PICKUP_ACTIVITY_DISPLAY = 'pickupActivityDisplay';

    public const PICKUP_ACTIVITY_LIST = 'pickupActivityList';

    public const PICKUP_ACTIVITY_MODIFY = 'pickupActivityModify';

    private const ATTRIBUTES = array(
        self::PICKUP_ACTIVITY_CREATE,
        self::PICKUP_ACTIVITY_DELETE,
        self::PICKUP_ACTIVITY_DISPLAY,
        self::PICKUP_ACTIVITY_LIST,
        self::PICKUP_ACTIVITY_MODIFY,
    );

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (null !== $subject) {
            return $subject instanceof PickupActivity && in_array($attribute, self::ATTRIBUTES);
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
            case self::PICKUP_ACTIVITY_CREATE:
                return $this->canCreate();
                break;
            case self::PICKUP_ACTIVITY_DELETE:
                return $this->canDelete();
                break;
            case self::PICKUP_ACTIVITY_DISPLAY:
                return $this->canDisplay();
                break;
            case self::PICKUP_ACTIVITY_LIST:
                return $this->canList();
                break;
            case self::PICKUP_ACTIVITY_MODIFY:
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
            'ROLE_COACH',
            'ROLE_ASSISTANT',
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
            'ROLE_COACH',
            'ROLE_ASSISTANT',
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
            'ROLE_COACH',
            'ROLE_ASSISTANT',
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
