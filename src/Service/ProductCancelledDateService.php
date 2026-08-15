<?php

namespace App\Service;

use App\Entity\Registration;
use App\Entity\ProductCancelledDate;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use App\Service\ProductService;

/**
 * ProductCancelledDateService class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class ProductCancelledDateService implements ProductCancelledDateServiceInterface
{
    private $em;

    private $mainService;
    private $productService;

    public function __construct(
        EntityManagerInterface $em,
        MainServiceInterface $mainService,
        ProductService $productService

    )
    {
        $this->em = $em;
        $this->mainService = $mainService;
        $this->productService = $productService;
    }

    /**
     * {@inheritdoc}
     */
    public function create(string $data)
    {
        //Submits data
        $object = new ProductCancelledDate();
        $this->mainService->create($object);
        $data = $this->mainService->submit($object, 'product-cancelled-date-create', $data);

        //Checks if entity has been filled
        $this->isEntityFilled($object);

        //Persists data
        $this->mainService->persist($object);

        //Returns data
        return array(
            'status' => true,
            'message' => 'ProductCancelledDate ajoutée',
            'productCancelledDate' => $this->toArray($object),
        );
    }


    public function createByCategory(string $data, $category_name)
    {

        $category_name ??= "EKA-DAYCAMP";

        // list of product
        $products = $this->em->getRepository('App\Entity\Product')->getProductByCategoryName($category_name);

        // data
        $data = json_decode($data, true);
        if (!is_array($data)) {
            throw new \InvalidArgumentException('Invalid JSON payload');
        }

        $fromDate = $data['dateClosedFrom'];
        $toDate = $data['dateClosedTo'];
        $messageFr = $data['messageFr'];
        $messageEn = $data['messageEn'];


        // create array of dates with the from to date
        $fromDate = new DateTime($fromDate);
        $toDate = new DateTime($toDate);
        $toDate->modify('+1 day');
        $dateRange = [];
        $interval = new \DateInterval('P1D');
        $datePeriod = new \DatePeriod($fromDate, $interval, $toDate);
        foreach ($datePeriod as $date) {
            $dateRange[] = $date->format('Y-m-d');
        }

        foreach($products as $product)
        {
            // create a new ProductCancelledDate with all range of dates
            foreach($dateRange as $date)
            {
                $newProductCancelledDate = new ProductCancelledDate();
                $newProductCancelledDate->setProduct($product);
                $newProductCancelledDate->setDate(new DateTime($date));
                $newProductCancelledDate->setMessageFr($messageFr);
                $newProductCancelledDate->setMessageEn($messageEn);

                //Checks if entity has been filled
                $this->isEntityFilled($newProductCancelledDate);
                //Persists data
                $this->em->persist($newProductCancelledDate);

            }

            $this->em->flush();

        }

        //Returns data
        return array(
            'status' => true,
            'message' => 'Tout est à jour',
        );

    }

    /**
     * {@inheritdoc}
     */
    public function delete(ProductCancelledDate $object)
    {
        //Persists data
        $this->mainService->delete($object);
        $this->mainService->persist($object);

        return array(
            'status' => true,
            'message' => 'ProductCancelledDate supprimée',
        );
    }

    /**
     * Returns the list of all productCancelledDates
     * @return array
     */
    public function findAll()
    {
        return $this->em
            ->getRepository('App\Entity\ProductCancelledDate')
            ->findAll()
        ;
    }


      /**
     * Returns the list of all productCancelledDates
     * @return array
     */
    public function findCurrent()
    {
        $productCancelled = $this->em->getRepository('App\Entity\ProductCancelledDate')->findCurrent();

        $productCancelledDatesArray = [];
        foreach($productCancelled as $product) {
            $productCancelledDatesArray[] = $this->toArray($product);
        }

        return $productCancelledDatesArray;
    }



    /**
     * Returns the list of all productCancelledDates for a specific category and date
     * @return array
     */
    public function findAllByCategoryDate($categoryId, $date)
    {
        return $this->em
            ->getRepository('App\Entity\ProductCancelledDate')
            ->findAllByCategoryDate($categoryId, $date)
        ;
    }

    /**
     * Returns the list of all productCancelledDates for a specific date
     * @return array
     */
    public function findAllByDate($date)
    {
        return $this->em
            ->getRepository('App\Entity\ProductCancelledDate')
            ->findAllByDate($date)
        ;
    }

    /**
     * Returns the list of all productCancelledDates for a specific product and date
     * @return array
     */
    public function findAllByProductDate($productId, $date)
    {
        return $this->em
            ->getRepository('App\Entity\ProductCancelledDate')
            ->findAllByProductDate($productId, $date)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function isEntityFilled(ProductCancelledDate $object)
    {
        if (null === $object->getDate() ||
            null === $object->getProduct()) {
            throw new UnprocessableEntityHttpException('Missing data for ProductCancelledDate -> ' . json_encode($object->toArray()));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function modify(ProductCancelledDate $object, string $data)
    {
        //Submits data
        $data = $this->mainService->submit($object, 'product-cancelled-date-modify', $data);

        //Checks if entity has been filled
        $this->isEntityFilled($object);

        //Persists data
        $this->mainService->modify($object);
        $this->mainService->persist($object);

        //Returns data
        return array(
            'status' => true,
            'message' => 'ProductCancelledDate modifiée',
            'productCancelledDate' => $this->toArray($object),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(ProductCancelledDate $object)
    {
        //Main data
        $objectArray = $this->mainService->toArray($object->toArray());

        //Gets related category
        if (null !== $object->getCategory() && !$object->getCategory()->getSuppressed()) {
            $objectArray['category'] = $this->mainService->toArray($object->getCategory()->toArray());
        }

        //Gets related product
        if (null !== $object->getProduct() && !$object->getProduct()->getSuppressed()) {
            $objectArray['product'] = $this->productService->toArray($object->getProduct());
        }

        return $objectArray;
    }
}
