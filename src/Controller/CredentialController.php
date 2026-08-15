<?php

namespace App\Controller;

use App\Entity\Credentail;
use App\Service\CredentialServiceInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * CredentialController class
 * @author Sandy Razafitrimo <sandyrazafitrimo@gmail.com>
 */
class CredentialController extends AbstractController
{
    private $credentialService;

    public function __construct(CredentialServiceInterface $credentialService)
    {
        $this->credentialService = $credentialService;
    }

//LIST
    /**
     * Lists all the credentials
     *
     * @Route("/credential/list",
     *    name="credential_list",
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Credential::class))
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Tag(name="Credential")
     */
    public function list(Request $request)
    {

        $result = $this->credentialService->list();
        return new JsonResponse($result);
    }

    //LIST BY ROLE
    /**
     * Lists all the credentials
     *
     * @Route("/credential/list/{role}",
     *    name="credential_list_role",
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Credential::class))
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Tag(name="Credential")
     */
    public function listRole($role)
    {
        $result = $this->credentialService->listByRole($role);
        return new JsonResponse($result);
    }

    //UPDATE CREDENITAL ROLE
    /**
     * Update the credential role
     *
     * @Route("/credential/updateRole/{role}/{credentialId}/{checked}",
     *    name="credential_update_role",
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Credential::class))
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Tag(name="Credential")
     */
    public function updateRole($role, $credentialId, $checked)
    {
        $result = $this->credentialService->updateRole($role, $credentialId, $checked);
        return new JsonResponse($result);
    }


    //UPDATE CREDENITAL STAFF
    /**
     * Update the credential role
     *
     * @Route("/credential/updateStaff/{staffId}/{credentialId}/{checked}",
     *    name="credential_update_staff",
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Credential::class))
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Tag(name="Credential")
     */
    public function updateStaff($staffId, $credentialId, $checked)
    {
        $result = $this->credentialService->updateStaff($staffId, $credentialId, $checked);
        return new JsonResponse($result);
    }


    //LIST BY ROLE
    /**
     * Lists all the credentials by staff
     *
     * @Route("/credential/user/{identifier}",
     *    name="credential_by_user",
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Credential::class))
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Tag(name="Credential")
     */
    public function credentialByUser($identifier) {
        $result = $this->credentialService->listCredentialUser($identifier);
        return new JsonResponse($result);
    }


}
