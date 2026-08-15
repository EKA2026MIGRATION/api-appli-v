<?php

namespace App\Security;

use App\Entity\ProductCancelledDate;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

/**
 * ProductCancelledDateVoter class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class ProductCancelledDateVoter extends Voter
{
    /**
     * Stores Security
     * @var Security
     */
    private $security;

    public const PRODUCT_CANCELLED_DATE_CREATE = 'productCancelledDateCreate';

    public const PRODUCT_CANCELLED_DATE_DELETE = 'productCancelledDateDelete';

    public const PRODUCT_CANCELLED_DATE_DISPLAY = 'productCancelledDateDisplay';

    public const PRODUCT_CANCELLED_DATE_LIST = 'productCancelledDateList';

    public const PRODUCT_CANCELLED_DATE_MODIFY = 'productCancelledDateModify';

    private const ATTRIBUTES = array(
        self::PRODUCT_CANCELLED_DATE_CREATE,
        self::PRODUCT_CANCELLED_DATE_DELETE,
        self::PRODUCT_CANCELLED_DATE_DISPLAY,
        self::PRODUCT_CANCELLED_DATE_LIST,
        self::PRODUCT_CANCELLED_DATE_MODIFY,
    );

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (null !== $subject) {
            return $subject instanceof ProductCancelledDate && in_array($attribute, self::ATTRIBUTES);
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
            case self::PRODUCT_CANCELLED_DATE_CREATE:
                return $this->canCreate();
                break;
            case self::PRODUCT_CANCELLED_DATE_DELETE:
                return $this->canDelete();
                break;
            case self::PRODUCT_CANCELLED_DATE_DISPLAY:
                return $this->canDisplay();
                break;
            case self::PRODUCT_CANCELLED_DATE_LIST:
                return $this->canList();
                break;
            case self::PRODUCT_CANCELLED_DATE_MODIFY:
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
