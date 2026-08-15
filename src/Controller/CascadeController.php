<?php

namespace App\Controller;

use App\Service\CascadeService;
use App\Entity\Registration;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * CascadeController class
 */
class CascadeController extends AbstractController
{
    private $cascadeService;

    public function __construct(CascadeService $cascadeService)
    {
        $this->cascadeService = $cascadeService;
    }

//Cascade from registration
    /**
     * Cascde from registration 
     *
     * @Route("/cascade/registration/{registrationId}",
     *    name="cascade_from_registration",
     *    methods={"HEAD", "PUT"})
     * @Entity("registration", expr="repository.find(registrationId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Registration::class))
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Tag(name="Registration")
     */
    public function cascadeFromRegistration(Registration $registration)
    {
        $this->denyAccessUnlessGranted('childList');

        $result = $this->cascadeService->cascadeFromRegistration($registration);
      
        return new JsonResponse($result);
    }
}
