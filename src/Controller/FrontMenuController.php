<?php

namespace App\Controller;

use App\Service\LocationService;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * FrontMenuController class
 * @author Sandy Razafitrimo
 */
class FrontMenuController extends AbstractController
{
    private $em;
    private $locationService;

    public function __construct(EntityManagerInterface $em, LocationService $locationService)
    {
        $this->em = $em;
        $this->locationService = $locationService;
    }

   //LIST
    /**
     * Lists all gymnases
     *
     * @Route("/frontMenu/gymnases/list",
     *    name="front_menu_gymnases_list",
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Product::class))
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Tag(name="Product")
     */
    public function listGymnases(Request $request)
    {
        $result = [];

        $locations = $this->locationService->findAll("gymnase")->getResult();

        foreach ($locations as $location) {

            if($location->getFrontVisibility() == 1) {
                $result[] = [
                    'name' => $location->getName(),
                    'nameFr' => $location->getNameFr(),
                    'nameEn' => $location->getNameEn(),
                    'dimension' => $location->getDimension(),
                    'address' => $location->getAddress(),
                    'ages_fr' => $location->getAgesFr(),
                    'ages_en' => $location->getAgesEn()
                ];
            }


        };



        return new JsonResponse($result);
    }

}
