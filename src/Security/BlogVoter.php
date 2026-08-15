<?php

namespace App\Security;

use App\Entity\Blog;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

/**
 * BlogVoter class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class BlogVoter extends Voter
{
    /**
     * Stores Security
     * @var Security
     */
    private $security;

    public const BLOG_CREATE = 'blogCreate';

    public const BLOG_DELETE = 'blogDelete';

    public const BLOG_DISPLAY = 'blogDisplay';

    public const BLOG_LIST = 'blogList';

    public const BLOG_MODIFY = 'blogModify';

    private const ATTRIBUTES = array(
        self::BLOG_CREATE,
        self::BLOG_DELETE,
        self::BLOG_DISPLAY,
        self::BLOG_LIST,
        self::BLOG_MODIFY,
    );

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (null !== $subject) {
            return $subject instanceof Blog && in_array($attribute, self::ATTRIBUTES);
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
            case self::BLOG_CREATE:
                return $this->canCreate();
                break;
            case self::BLOG_DELETE:
                return $this->canDelete();
                break;
            case self::BLOG_DISPLAY:
                return $this->canDisplay();
                break;
            case self::BLOG_LIST:
                return $this->canList();
                break;
            case self::BLOG_MODIFY:
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
     * Checks if is allowed to delete
     */
    private function canDelete()
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
     * Checks if is allowed to display
     */
    private function canDisplay()
    {
        return true;
    }

    /**
     * Checks if is allowed to list
     */
    private function canList()
    {
        return true;
    }

    /**
     * Checks if is allowed to modify
     */
    private function canModify()
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
}
