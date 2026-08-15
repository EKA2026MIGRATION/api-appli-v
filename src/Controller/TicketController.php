<?php

namespace App\Controller;

use App\Entity\Ticket;
use Knp\Component\Pager\PaginatorInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use App\Service\TicketServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * TicketController class
 * @author Sandy Razafitrimo <sandyrazafitrimo@gmail.com>
 */
class TicketController extends AbstractController
{
    private $ticketService;

    public function __construct(TicketServiceInterface $ticketService)
    {
        $this->ticketService = $ticketService;
    }

  //LIST ALL
      /**
       * List all the ticket
       *
       * @Route("/ticket/list/filter/{filter_name}/{filter_value}",
       *    name="ticket_list_filter",
       *    methods={"HEAD", "GET"})
       *
       * @SWG\Response(
       *     response=200,
       *     description="Success",
       *     @SWG\Schema(
       *         type="array",
       *         @SWG\Items(ref=@Model(type=Ticket::class))
       *     )
       * )
       * @SWG\Response(
       *     response=403,
       *     description="Access denied",
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
       * @SWG\Tag(name="Ticket")
       */
      public function listByFilter(Request $request, $filter_name, $filter_value)
      {
          //$this->denyAccessUnlessGranted('registrationList');

          $ticketsArray = array();

          $ticketsArray = $this->ticketService->findByFilter($filter_name, $filter_value);

          return new JsonResponse($ticketsArray);
        }


  //LIST CRITERIA
      /**
       * List ticket by criteria
       *
       * @Route("/ticket/list/criteria",
       *    name="ticket_list_criteria",
       *    methods={"HEAD", "POST"})
       *
       * @SWG\Response(
       *     response=200,
       *     description="Success",
       *     @SWG\Schema(
       *         @SWG\Property(property="status", type="boolean"),
       *         @SWG\Property(property="message", type="string"),
       *         @SWG\Property(property="ticket", ref=@Model(type=Ticket::class)),
       *     )
       * )
       * @SWG\Response(
       *     response=403,
       *     description="Access denied",
       * )
       * @SWG\Tag(name="Ticket")
       */
      public function listCriteria(Request $request)
      {
          //$this->denyAccessUnlessGranted('registrationCreate');
          $createdData = $this->ticketService->findByCriteria($request->getContent());
          return new JsonResponse($createdData);
      }

//LIST ALL
    /**
     * List all the ticket
     *
     * @Route("/ticket/list/{limit}",
     *    name="ticket_list",
     *    defaults={"limit": "20"},
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Ticket::class))
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
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
     * @SWG\Tag(name="Ticket")
     */
    public function list(Request $request, $limit)
    {
        //$this->denyAccessUnlessGranted('registrationList');

        $ticketsArray = array();

        $ticketsArray = $this->ticketService->findAll(null, $limit);

        return new JsonResponse($ticketsArray);
    }

    //LIST ALL
        /**
         * List all the ticket group by date
         *
         * @Route("/ticket/list/group/date",
         *    name="ticket_list_group_by_date",
         *    methods={"HEAD", "GET"})
         *
         * @SWG\Response(
         *     response=200,
         *     description="Success",
         *     @SWG\Schema(
         *         type="array",
         *         @SWG\Items(ref=@Model(type=Ticket::class))
         *     )
         * )
         * @SWG\Response(
         *     response=403,
         *     description="Access denied",
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
         * @SWG\Tag(name="Ticket")
         */
        public function listGroupByDate(Request $request, PaginatorInterface $paginator)
        {
            //$this->denyAccessUnlessGranted('registrationList');
            ini_set('memory_limit', '512M');

            $ticketsArray = array();

            $ticketsArray = $this->ticketService->findAll('groupByDate');

            return new JsonResponse($ticketsArray);
        }

//LIST ALL NEED RECALL
    /**
     * List all the ticket need recall
     *
     * @Route("/ticket/recall",
     *    name="ticket_recall",
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Ticket::class))
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
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
     * @SWG\Tag(name="Ticket")
     */
    public function listRecall(Request $request, PaginatorInterface $paginator)
    {
        //$this->denyAccessUnlessGranted('registrationList');

        $ticketsArray = array();

        $ticketsArray = $this->ticketService->findNeedCall();

        return new JsonResponse($ticketsArray);
    }

//LIST ALL NEED RECALL
    /**
     * List all the ticket need recall
     *
     * @Route("/ticket/recall/group/date",
     *    name="ticket_recall_group_date",
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Ticket::class))
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
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
     * @SWG\Tag(name="Ticket")
     */
    public function listRecallGroupDate(Request $request, PaginatorInterface $paginator)
    {
        //$this->denyAccessUnlessGranted('registrationList');

        $ticketsArray = array();

        $ticketsArray = $this->ticketService->findNeedCall('groupByDate');

        return new JsonResponse($ticketsArray);
    }




//CREATE
    /**
     * Create a ticket
     *
     * @Route("/ticket/create",
     *    name="ticket_create",
     *    methods={"HEAD", "POST"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="ticket", ref=@Model(type=Ticket::class)),
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Tag(name="Ticket")
     */
    public function create(Request $request)
    {
        //$this->denyAccessUnlessGranted('registrationCreate');
        $createdData = $this->ticketService->create($request->getContent());
        return new JsonResponse($createdData);
    }


//DISLAY
    /**
     * Display one ticket
     *
     * @Route("/ticket/display/{id}",
     *    name="display_ticket",
     *    requirements={"id": "^([0-9]+)$"},
     *    methods={"HEAD", "GET"})
     * @Entity("component", expr="repository.find(id)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Ticket::class))
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Tag(name="Ticket")
     */
    public function display(Ticket $ticket)
    {

        $ticketsArray = $ticket->toArray();


        return new JsonResponse($ticketsArray);
    }


  //DELETE
      /**
       * Delete on ticket
       *
       * @Route("/ticket/delete/{id}",
       *    name="delete_ticket",
       *    requirements={"id": "^([0-9]+)$"},
       *    methods={"HEAD", "DELETE"})
       * @Entity("component", expr="repository.find(id)")
       *
       * @SWG\Response(
       *     response=200,
       *     description="Success",
       *     @SWG\Schema(
       *         type="array",
       *         @SWG\Items(ref=@Model(type=Ticket::class))
       *     )
       * )
       * @SWG\Response(
       *     response=403,
       *     description="Access denied",
       * )
       * @SWG\Tag(name="Ticket")
       */
      public function delete(Ticket $ticket)
      {
          $message = $this->ticketService->delete($ticket);
          return new JsonResponse($message);
      }

//MODIFY
    /**
     * List all the ticket
     *
     * @Route("/ticket/modify",
     *    name="modify_ticket",
     *    methods={"PUT"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Tag(name="Ticket")
     */
    public function modify(Request $request)
    {

        $ticketArray = $this->ticketService->modify($request->getContent());

        return new JsonResponse($ticketArray);
    }


//UPDATE TREATED
    /**
     * Set to 1 updated ticket
     *
     * @Route("/ticket/treated/{id}",
     *    name="ticket_set_treated",
     *    requirements={"id": "^([0-9]+)$"},
     *    methods={"HEAD", "GET"})
     * @Entity("component", expr="repository.find(id)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Ticket::class))
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Tag(name="Ticket")
     */
    public function setTreated(Ticket $ticket)
    {

        $updated = $this->ticketService->setTreated($ticket);


        return new JsonResponse($updated);
    }


}
