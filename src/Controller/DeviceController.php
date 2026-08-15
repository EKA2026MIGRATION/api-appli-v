<?php

namespace App\Controller;

use App\Entity\Device;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Service\DeviceService;


/**
 * DeviceController class
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
class DeviceController extends AbstractController
{

    private $deviceService;

    public function __construct(DeviceService $deviceService)
    {
        $this->deviceService = $deviceService;
    }

//ADD DEVICE TO USER
    /**
     * Add a device to an user
     *
     * @Route("/device/add/user",
     *    name="add_device_to_user",
     *    methods={"HEAD", "POST"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     *    )
     * )
     * @SWG\Tag(name="Device")
     */
    public function addToUser(Request $request)
    {
        $data = $request->getContent();

        $device = $this->deviceService->addToUser($data);

        return new JsonResponse($device) ;
    }




}
