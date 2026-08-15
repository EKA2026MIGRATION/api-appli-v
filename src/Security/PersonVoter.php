<?php

namespace App\Security;

use App\Entity\Person;
use LogicException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\NullToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

/**
 * PersonVoter class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class PersonVoter extends Voter
{
    /**
     * Stores curent Request
     * @var Request
     */
    private $request;

    /**
     * Stores Security
     * @var Security
     */
    private $security;

    public const PERSON_CREATE = 'personCreate';

    public const PERSON_DELETE = 'personDelete';

    public const PERSON_DISPLAY = 'personDisplay';

    public const PERSON_LIST = 'personList';

    public const PERSON_MODIFY = 'personModify';

    private const ATTRIBUTES = array(
        self::PERSON_CREATE,
        self::PERSON_DELETE,
        self::PERSON_DISPLAY,
        self::PERSON_LIST,
        self::PERSON_MODIFY,
    );

    public function __construct(
        Security $security,
        RequestStack $requestStack
    )
    {
        $this->security = $security;
        $this->request = $requestStack->getCurrentRequest();
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (null !== $subject) {
            return $subject instanceof Person && in_array($attribute, self::ATTRIBUTES);
        }

        return in_array($attribute, self::ATTRIBUTES);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        //Checks current user
        if ('personCreate' !== $attribute && (null === $token->getUser() || is_string($token->getUser()))) {
            return false;
        }

        //Defines access rights
        switch ($attribute) {
            case self::PERSON_CREATE:
                return $this->canCreate($token);
                break;
            case self::PERSON_DELETE:
                return $this->canDelete($token, $subject);
                break;
            case self::PERSON_DISPLAY:
                return $this->canDisplay($token, $subject);
                break;
            case self::PERSON_LIST:
                return $this->canList();
                break;
            case self::PERSON_MODIFY:
                return $this->canModify($token, $subject);
                break;
        }

        throw new LogicException('Invalid attribute: ' . $attribute);
    }

    /**
     * Checks if is allowed to create
     */
    private function canCreate($token)
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

        //Tests if user is anon. (just after having been created) and that sha1 corresponds
        if ($token instanceof NullToken) {
            $data = json_decode($this->request->getContent(), true);
            if (array_key_exists('firstname', $data) &&
                array_key_exists('key', $data) &&
                array_key_exists('identifier', $data) &&
                $data['key'] === sha1($data['firstname'])) {
                return true;
            }
        }

        return false;
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
            'ROLE_TRAINEE',
            'ROLE_COACH',
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
     * Checks if is allowed to list
     */
    private function canList()
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

        return false;
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
            if ($subject->getPersonId() === $personId) {
                return true;
            }

            return false;
        }
    }
}
