<?php

namespace App\Service;

use App\Entity\Credential;
use App\Entity\CredentialRole;
use App\Entity\CredentialStaff;
use App\Service\StaffServiceInterface;

use c975L\UserBundle\Service\UserServiceInterface;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * CredentialService class
 * @author Sandy Razafitrimo <sandyrazafitrimo@gmail.com>
 */
class CredentialService implements CredentialServiceInterface
{
    private $em;

    private $mainService;

    public function __construct(
        EntityManagerInterface $em,
        MainServiceInterface $mainService,
        UserServiceInterface $userService,
        StaffServiceInterface $staffService
    )
    {
        $this->em = $em;
        $this->mainService = $mainService;
        $this->userService = $userService;
        $this->staffService = $staffService;
    }


      /**
     * {@inheritdoc}
     */
    public function list()
    {
        $credentials = $this->em->getRepository('App\Entity\Credential')->list();
        return $credentials;
     ;
    }

    /**
     * {@inheritdoc}
     */
    public function listByRole($role)
    {
        $role = str_replace('ROLE_', '', $role);
        $credentials = $this->em->getRepository('App\Entity\CredentialRole')->listByRole($role);
        return $credentials;
     ;
    }

        /**
     * {@inheritdoc}
     */
    public function updateRole($role, $credentialId, $checked)
    {

        $credential = $this->em->getRepository('App\Entity\Credential')->find($credentialId);

        if($checked == "checked") {
            $join = new CredentialRole();
            $join->setCredential($credential);
            $join->setRole(strtoupper($role));
            $this->em->persist($join);
            $this->em->flush($join);

        } else {
            $join = $this->em->getRepository('App\Entity\CredentialRole')->findOneBy(['credential' => $credential, 'role' => $role]);
            $this->em->remove($join);
            $this->em->flush();
        }

        
        //return $credentials;
     ;
    }


        /**
     * {@inheritdoc}
     */
    public function updateStaff($staffId, $credentialId, $checked)
    {
        $staff = $this->em->getRepository('App\Entity\Staff')->find($staffId);
        $credential = $this->em->getRepository('App\Entity\Credential')->find($credentialId);

        if($checked == "checked") {
            $join = new CredentialStaff();
            $join->setCredential($credential);
            $join->setStaff($staff);
            $this->em->persist($join);
            $this->em->flush($join);

        } else {
            $join = $this->em->getRepository('App\Entity\CredentialStaff')->findOneBy(['credential' => $credential, 'staff' => $staff]);
            $this->em->remove($join);
            $this->em->flush();
        }

        
        //return $credentials;
     ;
    }

    /**
     * {@inheritdoc}
     */
    public function listCredentialUser($identifier)
    {

        if(!$user = $this->userService->findUserByIdentifier($identifier)) return ['message' => 'no user founded'];

        if(!$roles = $user->getRoles()) return ['message' => 'no roles founded'];

        $credentials = [];
        foreach($roles as $role) {
            $credentials = array_merge($credentials, $this->listByRole(str_replace('ROLE_', '', $role)));
        }

        $person = $user->getUserPersonLink()->getPerson();

        $staff = $this->em->getRepository('App\Entity\Staff')->findOneBy(['person' => $person]);

        $credentials = array_merge($credentials, $this->em->getRepository('App\Entity\CredentialStaff')->listByStaff($staff));

        foreach($credentials as $credential) {
            $credentialsArray[] = ['name' => $credential['name'], 'role' => $credential['role'], 'description' => $credential['description']  ];
        }

        sort($credentialsArray);

        return $credentialsArray;
     ;
    }


     /**
     * {@inheritdoc}
     */
    public function addCredentialRole()
    {
      
     ;
    }

    /**
     * {@inheritdoc}
     */
    public function removeCredentialRole()
    {
      
      ;
    }

    /**
     * {@inheritdoc}
     */
    public function addCredentialStaff()
    {
      
     ;
    }

    /**
     * {@inheritdoc}
     */
    public function removeCredentialStaff()
    {
      
      ;
    }

    /**
     * Returns the list of all credentials
     * @return array
     */
    public function findAll()
    {
      
        ;
    }

  


}
