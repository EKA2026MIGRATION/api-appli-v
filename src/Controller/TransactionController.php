<?php

namespace App\Controller;

use App\Entity\Transaction;
use App\Form\TransactionType;
use App\Service\TransactionServiceInterface;
use Knp\Component\Pager\PaginatorInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * TransactionController class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class TransactionController extends AbstractController
{
    private $transactionService;

    public function __construct(TransactionServiceInterface $transactionService)
    {
        $this->transactionService = $transactionService;
    }

//LIST BY DATE
    /**
     * Lists all the transaction for a specific date
     *
     * @Route("/transaction/list/{date}",
     *    name="transaction_list",
     *    requirements={"date": "^(([0-9]{4}-[0-9]{2}-[0-9]{2})|([0-9]{4}-[0-9]{2}))$"},
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Transaction::class))
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="date",
     *     in="path",
     *     description="Date for the ride (YYYY-MM-DD | YYYY-MM)",
     *     type="string",
     * )
     * @SWG\Parameter(
     *     name="page",
     *     in="query",
     *     description="Number of the page",
     *     type="integer",
     *     default="1",
     * )
     * @SWG\Parameter(
     *     name="size",
     *     in="query",
     *     description="Number of records",
     *     type="integer",
     *     default="50",
     * )
     * @SWG\Tag(name="Transaction")
     */
    public function listAll(Request $request, PaginatorInterface $paginator, $date)
    {
        $this->denyAccessUnlessGranted('transactionList');

        $transactions = $paginator->paginate(
            $this->transactionService->findAllByDate($date),
            $request->query->getInt('page', 1),
            $request->query->getInt('size', 50)
        );

        $transactionsArray = array();
        foreach ($transactions->getItems() as $transaction) {
            $transactionsArray[] = $this->transactionService->toArray($transaction);
        };

        return new JsonResponse($transactionsArray);
    }

//LIST BY DATE AND STATUS
    /**
     * Lists all the transaction for a specific date and status
     *
     * @Route("/transaction/list/{date}/{status}",
     *    name="transaction_list_status",
     *    requirements={
     *        "date": "^(([0-9]{4}-[0-9]{2}-[0-9]{2})|([0-9]{4}-[0-9]{2})|([0-9]{4}))$",
     *        "status": "^([a-zA-Z]+)$"
     *    },
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Transaction::class))
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="date",
     *     in="path",
     *     description="Date for the ride (YYYY-MM-DD | YYYY-MM | YYYY)",
     *     type="string",
     * )
     * @SWG\Parameter(
     *     name="status",
     *     in="path",
     *     description="DateStatus for the transaction)",
     *     type="string",
     * )
     * @SWG\Parameter(
     *     name="page",
     *     in="query",
     *     description="Number of the page",
     *     type="integer",
     *     default="1",
     * )
     * @SWG\Parameter(
     *     name="size",
     *     in="query",
     *     description="Number of records",
     *     type="integer",
     *     default="50",
     * )
     * @SWG\Tag(name="Transaction")
     */
    public function listAllStatus(Request $request, PaginatorInterface $paginator, $date, $status)
    {
        $this->denyAccessUnlessGranted('transactionList');

        $transactions = $paginator->paginate(
            $this->transactionService->findAllByDateStatus($date, $status),
            $request->query->getInt('page', 1),
            $request->query->getInt('size', 50)
        );


        $transactionsArray = array();
        foreach ($transactions->getItems() as $transaction) {
            $transactionsArray[] = $this->transactionService->toArray($transaction);
        };

        return new JsonResponse($transactionsArray);
    }

//LIST BY DATE AND PERSON
    /**
     * Lists all the transaction for a specific date and person
     *
     * @Route("/transaction/list/{date}/{personId}",
     *    name="transaction_list_person",
     *    requirements={
     *        "date": "^(([0-9]{4}-[0-9]{2}-[0-9]{2})|([0-9]{4}-[0-9]{2})|([0-9]{4}))$",
     *        "personId": "^([0-9]+)$"
     *    },
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Transaction::class))
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="date",
     *     in="path",
     *     description="Date for the ride (YYYY-MM-DD | YYYY-MM)",
     *     type="string",
     * )
     * @SWG\Parameter(
     *     name="page",
     *     in="query",
     *     description="Number of the page",
     *     type="integer",
     *     default="1",
     * )
     * @SWG\Parameter(
     *     name="size",
     *     in="query",
     *     description="Number of records",
     *     type="integer",
     *     default="50",
     * )
     * @SWG\Tag(name="Transaction")
     */
    public function listAllPerson(Request $request, PaginatorInterface $paginator, $date, int $personId)
    {
        $this->denyAccessUnlessGranted('transactionList', $personId);

        $transactions = $paginator->paginate(
            $this->transactionService->findAllByDatePerson($date, $personId),
            $request->query->getInt('page', 1),
            $request->query->getInt('size', 50)
        );

        $transactionsArray = array();
        foreach ($transactions->getItems() as $transaction) {
            $transactionsArray[] = $this->transactionService->toArray($transaction);
        };

        return new JsonResponse($transactionsArray);
    }

//LIST BY STATUS AND PERSON
    /**
     * Lists all the transaction for a specific status and person
     *
     * @Route("/transaction/list/{status}/{personId}",
     *    name="transaction_list_status_person",
     *    requirements={
     *        "status": "^([a-zA-Z]+)$",
     *        "personId": "^([0-9]+)$"
     *    },
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Transaction::class))
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="status",
     *     in="path",
     *     description="DateStatus for the transaction)",
     *     type="string",
     * )
     * @SWG\Parameter(
     *     name="page",
     *     in="query",
     *     description="Number of the page",
     *     type="integer",
     *     default="1",
     * )
     * @SWG\Parameter(
     *     name="size",
     *     in="query",
     *     description="Number of records",
     *     type="integer",
     *     default="50",
     * )
     * @SWG\Tag(name="Transaction")
     */
    public function listAllStatusPerson(Request $request, PaginatorInterface $paginator, $status, int $personId)
    {
        $this->denyAccessUnlessGranted('transactionList', $personId);

        $transactions = $paginator->paginate(
            $this->transactionService->findAllByStatusPerson($status, $personId),
            $request->query->getInt('page', 1),
            $request->query->getInt('size', 50)
        );

        $transactionsArray = array();
        foreach ($transactions->getItems() as $transaction) {
            $transactionsArray[] = $this->transactionService->toArray($transaction);
        };

        return new JsonResponse($transactionsArray);
    }

//DISPLAY WITH ID
    /**
     * Displays transaction using transactionId
     *
     * @Route("/transaction/display/{transactionId}",
     *    name="transaction_display",
     *    requirements={"transactionId": "^([0-9]+)$"},
     *    methods={"HEAD", "GET"})
     * @Entity("transaction", expr="repository.findOneById(transactionId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Transaction::class))
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
     * @SWG\Parameter(
     *     name="transactionId",
     *     in="path",
     *     required=true,
     *     description="Id of the transaction",
     *     type="integer",
     * )
     * @SWG\Tag(name="Transaction")
     */
    public function display(Transaction $transaction)
    {
        $this->denyAccessUnlessGranted('transactionDisplay', $transaction);

        $transactionArray = $this->transactionService->toArray($transaction);

        return new JsonResponse($transactionArray);
    }

//DISPLAY WITH INTERNALORDER
    /**
     * Displays transaction with its internalOrder
     *
     * @Route("/transaction/display/{internalOrder}",
     *    name="transaction_display_internal_order",
     *    requirements={"internalOrder": "^([a-zA-Z0-9\-\_]+)$"},
     *    methods={"HEAD", "GET"})
     * @Entity("transaction", expr="repository.findOneByInternalOrder(internalOrder)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="transaction", ref=@Model(type=Transaction::class)),
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
     * @SWG\Parameter(
     *     name="internalOrder",
     *     in="path",
     *     description="InternalOrder for the transaction",
     *     required=true,
     *     type="string",
     * )
     * @SWG\Tag(name="Transaction")
     */
    public function displayInternalOrder(Transaction $transaction)
    {
        $this->denyAccessUnlessGranted('transactionDisplay', $transaction);

        $transactionArray = $this->transactionService->toArray($transaction);

        return new JsonResponse($transactionArray);
    }

//CREATE
    /**
     * Creates Transaction
     *
     * @Route("/transaction/create",
     *    name="transaction_create",
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
     * )
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Data for the Transaction",
     *     required=true,
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=TransactionType::class))
     *     )
     * )
     * @SWG\Tag(name="Transaction")
     */
    public function create(Request $request)
    {
        $this->denyAccessUnlessGranted('transactionCreate');

        $createdData = $this->transactionService->create($request->getContent());

        return new JsonResponse($createdData);
    }

//MODIFY WITH INTERNALORDER
    /**
     * Modifies transaction
     *
     * @Route("/transaction/modify/{internalOrder}",
     *    name="transaction_modify",
     *    requirements={"internalOrder": "^([a-zA-Z0-9\-\_]+)$"},
     *    methods={"HEAD", "PUT"})
     * @Entity("transaction", expr="repository.findOneByInternalOrder(internalOrder)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="transaction", ref=@Model(type=Transaction::class)),
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
     * @SWG\Parameter(
     *     name="internalOrder",
     *     in="path",
     *     description="InternalOrder for the transaction",
     *     required=true,
     *     type="string",
     * )
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Data for the Transaction",
     *     required=true,
     *     @Model(type=TransactionType::class)
     * )
     * @SWG\Tag(name="Transaction")
     */
    public function modify(Request $request, Transaction $transaction)
    {
        $this->denyAccessUnlessGranted('transactionModify', $transaction);

        $modifiedData = $this->transactionService->modify($transaction, $request->getContent());

        return new JsonResponse($modifiedData);
    }

//DELETE
    /**
     * Deletes transaction using its id
     *
     * @Route("/transaction/delete/{transactionId}",
     *    name="transaction_delete",
     *    requirements={"transactionId": "^([0-9]+)$"},
     *    methods={"HEAD", "DELETE"})
     * @Entity("component", expr="repository.findOneById(transactionId)")
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
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Not Found",
     * )
     * @SWG\Parameter(
     *     name="transactionId",
     *     in="path",
     *     description="Id for the Transaction",
     *     required=true,
     *     type="integer",
     * )
     * @SWG\Tag(name="Transaction")
     */
    public function delete(Transaction $transaction)
    {
        $this->denyAccessUnlessGranted('transactionDelete', $transaction);

        $suppressedData = $this->transactionService->delete($transaction);

        return new JsonResponse($suppressedData);
    }
}
