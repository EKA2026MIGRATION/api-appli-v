<?php

namespace App\Security;

use App\Entity\Invoice;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

/**
 * InvoiceVoter class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class InvoiceVoter extends Voter
{
    /**
     * Stores Security
     * @var Security
     */
    private $security;

    public const INVOICE_CREATE = 'invoiceCreate';

    public const INVOICE_DELETE = 'invoiceDelete';

    public const INVOICE_DISPLAY = 'invoiceDisplay';

    public const INVOICE_LIST = 'invoiceList';

    public const INVOICE_MODIFY = 'invoiceModify';

    private const ATTRIBUTES = array(
        self::INVOICE_CREATE,
        self::INVOICE_DELETE,
        self::INVOICE_DISPLAY,
        self::INVOICE_LIST,
        self::INVOICE_MODIFY,
    );

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (null !== $subject) {
            return $subject instanceof Invoice && in_array($attribute, self::ATTRIBUTES);
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
            case self::INVOICE_CREATE:
                return $this->canCreate();
                break;
            case self::INVOICE_DELETE:
                return $this->canDelete();
                break;
            case self::INVOICE_DISPLAY:
                return $this->canDisplay();
                break;
            case self::INVOICE_LIST:
                return $this->canList();
                break;
            case self::INVOICE_MODIFY:
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
