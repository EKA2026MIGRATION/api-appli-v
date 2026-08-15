<?php

namespace App\Security;

use App\Entity\Registration;
use App\Entity\User;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

/**
 * RegistrationVoter class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class RegistrationVoter extends Voter
{
    /**
     * Stores Security
     * @var Security
     */
    private $security;

    public const REGISTRATION_CREATE = 'registrationCreate';

    public const REGISTRATION_DELETE = 'registrationDelete';

    public const REGISTRATION_DISPLAY = 'registrationDisplay';

    public const REGISTRATION_LIST = 'registrationList';

    public const REGISTRATION_LIST_WITHOUT_CART = 'registrationListWithoutCart';

    public const REGISTRATION_MODIFY = 'registrationModify';

    private const ATTRIBUTES = array(
        self::REGISTRATION_CREATE,
        self::REGISTRATION_DELETE,
        self::REGISTRATION_DISPLAY,
        self::REGISTRATION_LIST,
        self::REGISTRATION_LIST_WITHOUT_CART,
        self::REGISTRATION_MODIFY,
    );

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (null !== $subject) {
            return (is_int($subject) || $subject instanceof Registration) && in_array($attribute, self::ATTRIBUTES);
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
            case self::REGISTRATION_CREATE:
                return $this->canCreate();
                break;
            case self::REGISTRATION_DELETE:
                return $this->canDelete($token, $subject);
                break;
            case self::REGISTRATION_DISPLAY:
                return $this->canDisplay($token, $subject);
                break;
            case self::REGISTRATION_LIST:
                return $this->canList($token, $subject);
                break;
            case self::REGISTRATION_LIST_WITHOUT_CART:
                return $this->canListWithoutCart();
                break;
            case self::REGISTRATION_MODIFY:
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
        return $this->security->isGranted('ROLE_USER');
    }

    /**
     * Checks if is allowed to delete
     */
    private function canDelete($token, $subject)
    {
        //Checks roles allowed
        $roles = array(
            'ROLE_ASSISTANT',
            'ROLE_MANAGER',
            'ROLE_ADMIN',
        );

        foreach ($roles as $role) {
            if ($this->security->isGranted($role)) {
                return true;
            }
        }

        return 'cart' === $subject->getStatus() && $this->isLinked($token, $subject);
    }

    /**
     * Checks if is allowed to display
     */
    private function canDisplay($token, $subject)
    {
        //Checks roles allowed
        $roles = array(
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
     * Checks if is allowed to list
     */
    private function canList($token, $subject)
    {
        //Checks roles allowed
        $roles = array(
            'ROLE_ASSISTANT',
            'ROLE_MANAGER',
            'ROLE_ADMIN',
        );

        foreach ($roles as $role) {
            if ($this->security->isGranted($role)) {
                return true;
            }
        }

        return ($subject === $token->getUser()->getUserPersonLink()->getPerson()->getPersonId());
    }

    /**
     * Checks if is allowed to list without the cart status
     */
    private function canListWithoutCart()
    {
        //Checks roles allowed
        $roles = array(
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
    private function canModify($token, $subject)
    {
        //Checks roles allowed
        $roles = array(
            'ROLE_ASSISTANT',
            'ROLE_MANAGER',
            'ROLE_ADMIN',
        );

        foreach ($roles as $role) {
            if ($this->security->isGranted($role)) {
                return true;
            }
        }

        return 'cart' === $subject->getStatus() && $this->isLinked($token, $subject);
    }

    /**
     * Checks if registration is linked to the user
     */
    public function isLinked($token, $subject)
    {
        if (null !== $token->getUser()->getUserPersonLink() && null !== $subject->getPerson()->getPersonId()) {
            return($token->getUser()->getUserPersonLink()->getPerson()->getPersonId() === $subject->getPerson()->getPersonId());
        }

        return false;
    }
}
