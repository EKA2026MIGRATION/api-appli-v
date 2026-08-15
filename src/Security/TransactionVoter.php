<?php

namespace App\Security;

use App\Entity\Transaction;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

/**
 * TransactionVoter class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class TransactionVoter extends Voter
{
    /**
     * Stores Security
     * @var Security
     */
    private $security;

    public const TRANSACTION_CREATE = 'transactionCreate';

    public const TRANSACTION_DELETE = 'transactionDelete';

    public const TRANSACTION_DISPLAY = 'transactionDisplay';

    public const TRANSACTION_LIST = 'transactionList';

    public const TRANSACTION_MODIFY = 'transactionModify';

    private const ATTRIBUTES = array(
        self::TRANSACTION_CREATE,
        self::TRANSACTION_DELETE,
        self::TRANSACTION_DISPLAY,
        self::TRANSACTION_LIST,
        self::TRANSACTION_MODIFY,
    );

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (null !== $subject) {
            return (is_int($subject) || $subject instanceof Transaction) && in_array($attribute, self::ATTRIBUTES);
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
            case self::TRANSACTION_CREATE:
                return $this->canCreate();
                break;
            case self::TRANSACTION_DELETE:
                return $this->canDelete();
                break;
            case self::TRANSACTION_DISPLAY:
                return $this->canDisplay($token, $subject);
                break;
            case self::TRANSACTION_LIST:
                return $this->canList($token, $subject);
                break;
            case self::TRANSACTION_MODIFY:
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
            'ROLE_USER',
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
    private function canDisplay($token, $subject)
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
