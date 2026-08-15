<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Form\InvoiceType;
use App\Service\CascadeService;
use App\Service\InvoiceServiceInterface;
use Knp\Component\Pager\PaginatorInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * InvoiceController class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class InvoiceController extends AbstractController
{
    private $invoiceService;
    private $cascadeService;

    public function __construct(InvoiceServiceInterface $invoiceService, CascadeService $cascadeService)
    {
        $this->invoiceService = $invoiceService;
        $this->cascadeService = $cascadeService;
    }

//LIST
    /**
     * Lists all the invoices
     *
     * @Route("/invoice/listByPerson/{person_id}/{year}",
     *    name="invoice_list_by_person",
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Invoice::class))
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Tag(name="Invoice")
     */
    public function listByPerson($person_id, $year)
    {
      //  $this->denyAccessUnlessGranted('invoiceList'); // changer la route pour le client

        $invoicesArray = $this->invoiceService->finByPerson($person_id, $year);

        return new JsonResponse($invoicesArray);
    }

//LIST LATEST
    /**
     * Lists all the latest invoices
     *
     * @Route("/invoice/latest/{dateStart}/{dateEnd}",
     *    name="invoice_list_latest",
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Invoice::class))
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
     * @SWG\Tag(name="Invoice")
     */
    public function listlatest(Request $request, PaginatorInterface $paginator, $dateStart = null, $dateEnd = null)
    {
        ini_set('memory_limit', '4096M');


        $this->denyAccessUnlessGranted('invoiceList');

        $invoicesArray = $this->invoiceService->findLatest($dateStart, $dateEnd);

        return new JsonResponse($invoicesArray);
    }




//LIST
    /**
     * Lists all the invoices
     *
     * @Route("/invoice/list",
     *    name="invoice_list",
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Invoice::class))
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
     * @SWG\Tag(name="Invoice")
     */
    public function listAll(Request $request, PaginatorInterface $paginator)
    {
        $this->denyAccessUnlessGranted('invoiceList');

        $invoicesArray = $this->invoiceService->findAll();

        return new JsonResponse($invoicesArray);
    }



//SEARCH
    /**
     * Searches for %{term}% in name_fr for Invoice
     *
     * @Route("/invoice/search/{term}",
     *    name="invoice_search",
     *    requirements={"term": "^([a-zA-Z0-9\ \-]+)"},
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Invoice::class))
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
     *     name="term",
     *     in="path",
     *     required=true,
     *     description="Searched term",
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
     * @SWG\Tag(name="Invoice")
     */
    public function search(Request $request, $term)
    {
        $this->denyAccessUnlessGranted('invoiceList');
        $invoicesArray = array();
        $invoicesArray = $this->invoiceService->findAllSearch($term);

        return new JsonResponse($invoicesArray);
    }

//SEARCH BY DATES
    /**
     * Searches by dates of invoices
     *
     * @Route("/invoice/search/{dateStart}/{dateEnd}/{mode}",
     *    name="invoice_search_by_dates",
     *    requirements={
     *        "dateStart": "^(([0-9]{4}-[0-9]{2}-[0-9]{2})|([0-9]{4}-[0-9]{2}))$",
     *        "dateEnd": "^(([0-9]{4}-[0-9]{2}-[0-9]{2})|([0-9]{4}-[0-9]{2}))$"
     *    },
     *    defaults={"dateEnd": "null"},
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Invoice::class))
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
     *     name="dateStart",
     *     in="path",
     *     description="Date start for the invoices (YYYY-MM-DD | YYYY-MM)",
     *     type="string",
     * )
     * @SWG\Parameter(
     *     name="dateEnd",
     *     in="path",
     *     description="Date for the invoices (YYYY-MM-DD | YYYY-MM | null)",
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
     * @SWG\Tag(name="Invoice")
     */
    public function searchByDates( string $dateStart, string $dateEnd, $mode = 'all')
    {

        $this->denyAccessUnlessGranted('invoiceList');
        $invoicesArray = array();
        $invoicesArray = $this->invoiceService->findAll('payed', 2000, $dateStart, $dateEnd, $mode);
    
        return new JsonResponse($invoicesArray);
    }

//DISPLAY
    /**
     * Displays invoice
     *
     * @Route("/invoice/display/{invoiceId}",
     *    name="invoice_display",
     *    requirements={"invoiceId": "^([0-9]+)$"},
     *    methods={"HEAD", "GET"})
     * @Entity("invoice", expr="repository.findOneById(invoiceId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @Model(type=Invoice::class)
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
     *     name="invoiceId",
     *     in="path",
     *     description="Id of the invoice",
     *     type="integer",
     * )
     * @SWG\Tag(name="Invoice")
     */
    public function display(Invoice $invoice)
    {
      //  $this->denyAccessUnlessGranted('invoiceDisplay', $invoice); // changer la route pour le client

        $invoiceArray = $this->invoiceService->toArray($invoice);

        return new JsonResponse($invoiceArray);
    }



//DISPLAY
    /**
     * Displays invoice
     *
     * @Route("/invoice/cascade/{invoiceId}",
     *    name="invoice_cascade",
     *    requirements={"invoiceId": "^([0-9]+)$"},
     *    methods={"HEAD", "GET"})
     * @Entity("invoice", expr="repository.findOneById(invoiceId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @Model(type=Invoice::class)
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
     *     name="invoiceId",
     *     in="path",
     *     description="Id of the invoice",
     *     type="integer",
     * )
     * @SWG\Tag(name="Invoice")
     */
    public function cascadeFromInvoice(Invoice $invoice)
    {
        $this->denyAccessUnlessGranted('invoiceDisplay', $invoice);

        $invoiceArray = $this->cascadeService->cascadeFromInvoice($invoice);

        return new JsonResponse($invoiceArray);
    }

//CREATE
    /**
     * Creates invoice
     *
     * @Route("/invoice/create",
     *    name="invoice_create",
     *    methods={"HEAD", "POST"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="invoice", ref=@Model(type=Invoice::class)),
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Data for the Invoice",
     *     required=true,
     *     @Model(type=InvoiceType::class)
     * )
     * @SWG\Tag(name="Invoice")
     */
    public function create(Request $request)
    {
        $createdData = $this->invoiceService->createManuel($request->getContent());

        return new JsonResponse($createdData);
    }



//MODIFY
    /**
     * Modifies invoice
     *
     * @Route("/invoice/modify/{invoiceId}",
     *    name="invoice_modify",
     *    requirements={"invoiceId": "^([0-9]+)$"},
     *    methods={"HEAD", "PUT"})
     * @Entity("invoice", expr="repository.findOneById(invoiceId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="invoice", ref=@Model(type=Invoice::class)),
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
     *     name="invoiceId",
     *     in="path",
     *     description="Id for the invoice",
     *     required=true,
     *     type="integer",
     * )
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Data for the Invoice",
     *     required=true,
     *     @Model(type=InvoiceType::class)
     * )
     * @SWG\Tag(name="Invoice")
     */
    public function modify(Request $request, Invoice $invoice)
    {
        $modifiedData = $this->invoiceService->modify($invoice, $request->getContent());

        return new JsonResponse($modifiedData);
    }


//ADD COMPONENT IN INVOICE PRODUCT
    /**
     * Modifies invoice
     *
     * @Route("invoice/invoiceProduct/addComponent",
     *    name="invoice_invoiceProduct_addComponent",
     *    methods={"HEAD", "PUT"})
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="invoice", ref=@Model(type=Invoice::class)),
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
     *     name="data",
     *     in="body",
     *     description="Data for the Invoice",
     *     required=true,
     *     @Model(type=InvoiceType::class)
     * )
     * @SWG\Tag(name="Invoice")
     */
    public function addComponentInvoiceProduct(Request $request)
    {

        $modifiedData = $this->invoiceService->addComponentInvoiceProduct($request->getContent());

        return new JsonResponse($modifiedData);
    }

    
//DELETE COMPONENT IN INVOICE PRODUCT
    /**
     * Modifies invoice
     *
     * @Route("invoiceProduct/deleteComponent",
     *    name="invoice_invoiceProduct_deleteComponent",
     *    methods={"HEAD", "DELETE"})
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="invoice", ref=@Model(type=Invoice::class)),
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
     *     name="data",
     *     in="body",
     *     description="Data for the Invoice",
     *     required=true,
     *     @Model(type=InvoiceType::class)
     * )
     * @SWG\Tag(name="Invoice")
     */
    public function deleteComponentInvoiceProduct(Request $request)
    {

        $modifiedData = $this->invoiceService->deleteComponentInvoiceProduct($request->getContent());

        return new JsonResponse($modifiedData);
    }




//DELETE
    /**
     * Deletes invoice
     *
     * @Route("/invoice/delete/{invoiceId}",
     *    name="invoice_delete",
     *    requirements={"invoiceId": "^([0-9]+)$"},
     *    methods={"HEAD", "DELETE"})
     * @Entity("invoice", expr="repository.findOneById(invoiceId)")
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
     *     name="invoiceId",
     *     in="path",
     *     description="Id for the invoice",
     *     required=true,
     *     type="integer",
     * )
     * @SWG\Tag(name="Invoice")
     */
    public function delete(Invoice $invoice)
    {
        $suppressedData = $this->invoiceService->delete($invoice);

        return new JsonResponse($suppressedData);
    }


//CONSOLIDATION
    /**
     * CONSOLIDATION invoice
     *
     * @Route("/invoice/consolidation/{invoiceId}",
     *    name="invoice_consolidation",
     *    requirements={"invoiceId": "^([0-9]+)$"},
     *    defaults={"invoiceId": null},
     *    methods={"HEAD", "POST"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="invoice", ref=@Model(type=Invoice::class)),
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="invoiceId",
     *     in="path",
     *     description="Optional invoice ID to consolidate a specific invoice",
     *     required=false,
     *     type="integer",
     * )
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Conslidation of Invoice",
     *     required=false,
     *     @Model(type=InvoiceType::class)
     * )
     * @SWG\Tag(name="Invoice")
     */
    public function consolidation(Request $request, $invoiceId = null)
    {
        $this->denyAccessUnlessGranted('invoiceCreate');

        $messages = $this->invoiceService->consolidation($request->getContent(), $invoiceId);

        return new JsonResponse($messages);
    }
}
