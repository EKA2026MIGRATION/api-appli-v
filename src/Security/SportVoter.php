<?php

namespace App\Security;

use App\Entity\Sport;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

/**
 * SportVoter class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class SportVoter extends Voter
{
    /**
     * Stores Security
     * @var Security
     */
    private $security;

    public const SPORT_CREATE = 'sportCreate';

    public const SPORT_DELETE = 'sportDelete';

    public const SPORT_DISPLAY = 'sportDisplay';

    public const SPORT_LIST = 'sportList';

    public const SPORT_MODIFY = 'sportModify';

    private const ATTRIBUTES = array(
        self::SPORT_CREATE,
        self::SPORT_DELETE,
        self::SPORT_DISPLAY,
        self::SPORT_LIST,
        self::SPORT_MODIFY,
    );

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (null !== $subject) {
            return $subject instanceof Sport && in_array($attribute, self::ATTRIBUTES);
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
            case self::SPORT_CREATE:
                return $this->canCreate();
                break;
            case self::SPORT_DELETE:
                return $this->canDelete();
                break;
            case self::SPORT_DISPLAY:
                return $this->canDisplay();
                break;
            case self::SPORT_LIST:
                return $this->canList();
                break;
            case self::SPORT_MODIFY:
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
