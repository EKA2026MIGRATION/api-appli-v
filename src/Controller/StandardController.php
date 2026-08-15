<?php

namespace App\Controller;

use App\Entity\Standard;
use Knp\Component\Pager\PaginatorInterface;
use App\Service\StandardServiceInterface;
use Doctrine\ORM\EntityManagerInterface;

use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * StandardController class
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
class StandardController extends AbstractController
{
    private $standard_service;
    private $em;

    public function __construct(EntityManagerInterface $em,StandardServiceInterface $standard_service)
    {
        $this->em = $em;
        $this->standard_service = $standard_service;
    }

//TEL DISPLAY
    /**
     * Display tel rediction number
     *
     * @Route("/standard/tel/display",
     *    name="standard_tel_display",
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Standard::class))
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Not Found",
     * )
     * @SWG\Tag(name="Standard")
     */
    public function displayTel()
    {

        $standard = $this->em->getRepository('App\Entity\Standard')->findOneBy(['param' => 'TEL_REDIRECTION']);

        return new JsonResponse($standard->getValue());
    }

    //STANDARD PARAM
        /**
         * return a param
         *
         * @Route("/standard/param/{constant}",
         *    name="standard_display_param",
         *    methods={"HEAD", "GET"})
         *
         * @SWG\Response(
         *     response=200,
         *     description="Success",
         *     @SWG\Schema(
         *         type="array",
         *         @SWG\Items(ref=@Model(type=Standard::class))
         *     )
         * )
         * @SWG\Response(
         *     response=403,
         *     description="Access denied",
         * )
         * @SWG\Response(
         *     response=404,
         *     description="Not Found",
         * )
         * @SWG\Tag(name="Standard")
         */
        public function displayParam($constant)
        {

            $standard = $this->em->getRepository('App\Entity\Standard')->findOneBy(['param' => $constant]);
            $standardArray = $standard->toArray();

            return new JsonResponse($standardArray);
        }


//MODIFY
    /**
     * Modifies ride
     *
     * @Route("/standard/tel/modify/{number}",
     *    name="standard_tel_modify",
     *    methods={"HEAD", "PUT"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="standard", ref=@Model(type=Standard::class)),
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Not Found",
     * )
     * @SWG\Tag(name="Standard")
     */
    public function modifyTel($number)
    {

        $standard = $this->em->getRepository('App\Entity\Standard')->findOneBy(['param' => 'TEL_REDIRECTION']);
        $standard->setValue($number);
        $this->em->persist($standard);
        $this->em->flush();

        return new JsonResponse(['téléphone modifié']);
    }






}
