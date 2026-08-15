<?php

namespace App\Service;

use App\Entity\StockOrder;
use App\Entity\StockProduct;
use App\Entity\StockProductInventory;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * StockProductService class
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
class StockProductService implements StockProductServiceInterface
{
    private $em;
    private $mainService;

    public function __construct(
        EntityManagerInterface $em,
        MainServiceInterface $mainService
    )
    {
        $this->em = $em;
        $this->mainService = $mainService;

    }

    public function latestDateInventory() {
        $inventory = $this->em->getRepository('App\Entity\StockProductInventory')->findOneBy([], array('dateInventory' => 'DESC'));
        return $inventory->getDateInventory()->format('Y-m-d');
    }


    public function findAll() {
        $products = $this->em->getRepository('App\Entity\StockProduct')->findBy(['suppressed' => 0]);
        foreach($products as $product) {
            $arr[$product->getCategory()->getName()][] = $this->toArray($product);
        }
        return $arr;
    }

    public function create($data) {

        $data = json_decode($data, true);

        $stockProduct = new StockProduct();

        $this->mainService->hydrate($stockProduct, $data);

        if(isset($data['categoryid'])) {
            if($category = $this->em->getRepository('App\Entity\StockCategory')->find($data['categoryid'])) {
                $stockProduct->setCategory($category);
            }
        }

        //Persists data
        $this->mainService->create($stockProduct);
        $this->mainService->persist($stockProduct);

        $this->updateInventory($stockProduct);

        //Returns data
        return array(
            'status' => true,
            'message' => 'Stock Product ajouté',
            'stockProduct' => $this->toArray($stockProduct),
        );

    }


    public function addStockOrder($stockProductId, $data) {
        $stockProduct = $this->em->getRepository('App\Entity\StockProduct')->find($stockProductId);
        $data = json_decode($data, true);
        $date = date('Y-m-d');
        $date =  new DateTime($date);


        ($data['quantityTarget'] == $data['quantity']) ? $isValid = 1 : $isValid = 0;


        if(!$stockOrder = $this->em->getRepository('App\Entity\StockOrder')->findOneBy(['stockProduct' => $stockProduct, 'dateOrder' => $date])) {
            $stockOrder = new StockOrder();
        }

        $stockOrder->setStockProduct($stockProduct);
        $stockOrder->setQuantity($data['quantity']);
        $stockOrder->setQuantityTarget($data['quantityTarget']);
        $stockOrder->setIsValid($isValid);
        $stockOrder->setDateOrder($date);

        //Persists data
        $this->mainService->modify($stockOrder);
        $this->mainService->persist($stockOrder);

        //Returns data
        return array(
            'status' => true,
            'message' => 'Commande mise à jour',
            'stockOrder' => $stockOrder->toArray()
        );

    }


    public function listOrderDate($date)
    {
        $date = new DateTime($date);
        if(!$orderProducts = $this->em->getRepository('App\Entity\StockOrder')->findBy(['dateOrder' => $date])) return [];

        $arr = [];
        foreach($orderProducts as $orderProduct) {
            $arr[$orderProduct->getStockProduct()->getId()] = $orderProduct->toArray();
        }

        return $arr;


    }

    /**
     * Retrieve products in alert situation
     * when current stock is lower than minimum stock
     */
    public function getAlert()
    {
        $products = $this->em->getRepository('App\Entity\StockProduct')->findBy(['suppressed' => 0]);
        $arr = [];
        foreach ($products as $product) {
            if ($product->getCurrentStock() < $product->getMinimumStock()) {
                $arr[$product->getCategory()->getName()][] = $this->toArray($product);
            }
        }
        return $arr;
    }



    public function modify($stockProductId, $data) {

        if($stockProductId == 0) return $this->create($data);

        $stockProduct = $this->em->getRepository('App\Entity\StockProduct')->find($stockProductId);

        $data = json_decode($data, true);

        $this->mainService->hydrate($stockProduct, $data);

        if(isset($data['categoryid'])) {
            if($category = $this->em->getRepository('App\Entity\StockCategory')->find($data['categoryid'])) {
                $stockProduct->setCategory($category);
            }

        }

        //Persists data
        $this->mainService->modify($stockProduct);
        $this->mainService->persist($stockProduct);

        // update inventory
        $this->updateInventory($stockProduct);

        //Returns data
        return array(
            'status' => true,
            'message' => 'Stock Produit modifié',
            'stockProduct' => $this->toArray($stockProduct)
        );
    }

    public function inventory($date) {
        $date =  new DateTime($date);

        $stockProducts = $this->em->getRepository('App\Entity\StockProductInventory')->findBy(['dateInventory' => $date]);

        if(!$stockProducts) return [];

        $arr = [];
        foreach($stockProducts as $stockProduct) {
            $arr[$stockProduct->getCategoryName()][] = $stockProduct->toArray();
        }

        return $arr;
    }


    public function updateInventory($stockProduct, $date = null) {


        $isNew = false;

        if(!$date) $date = date('Y-m-d');

        $date =  new DateTime($date);

        if(!$stockProductInventory = $this->em->getRepository('App\Entity\StockProductInventory')->findOneBy(['productRef' => $stockProduct, 'dateInventory' => $date])) {
            $stockProductInventory = new StockProductInventory();
            $isNew = true;
        }

        $stockProductInventory->setName($stockProduct->getName());
        $stockProductInventory->setDateInventory($date);
        $stockProductInventory->setProductRef($stockProduct);
        $stockProductInventory->setDescription($stockProduct->getDescription());
        $stockProductInventory->setCategoryName($stockProduct->getCategory()->getName());
        $stockProductInventory->setMinimumStock($stockProduct->getMinimumStock());
        $stockProductInventory->setCurrentStock($stockProduct->getCurrentStock());
        $stockProductInventory->setUnity($stockProduct->getUnity());
        $stockProductInventory->setPrice($stockProduct->getPrice());
        $stockProductInventory->setConditioning($stockProduct->getConditioning());

        if($isNew) {
            $this->mainService->create($stockProductInventory);
        } else {
            $this->mainService->modify($stockProductInventory);
        }

        $this->mainService->persist($stockProductInventory);


        return $stockProductInventory;

    }


    public function stockOrderList() {

        $orders = $this->em->getRepository('App\Entity\StockOrder')->findList();
        return $orders;

    }


    /**
     * {@inheritdoc}
     */
    public function toArray(StockProduct $object)
    {
        //Main data
        $objectArray = $this->mainService->toArray($object->toArray());
        $objectArray['category'] = $object->getCategory()->getName();
        $objectArray['category_id'] = $object->getCategory()->getId();
        return $objectArray;
    }
}
