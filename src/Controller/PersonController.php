<?php

namespace App\Controller;

use App\Entity\Person;
use App\Form\PersonType;
use App\Service\PersonServiceInterface;
use Knp\Component\Pager\PaginatorInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * PersonController class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class PersonController extends AbstractController
{
    private $personService;

    public function __construct(PersonServiceInterface $personService)
    {
        $this->personService = $personService;
    }

//LIST
    /**
     * Lists all the persons
     *
     * @Route("/person/list",
     *    name="person_list",
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Person::class))
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
     * @SWG\Tag(name="Person")
     */
    public function listAll(Request $request, PaginatorInterface $paginator)
    {
        $this->denyAccessUnlessGranted('personList');

        $persons = $paginator->paginate(
            $this->personService->findAll(),
            $request->query->getInt('page', 1),
            $request->query->getInt('size', 50)
        );

        $personsArray = array();
        foreach ($persons->getItems() as $person) {
            $personsArray[] = $this->personService->toArray($person);
        };

        return new JsonResponse($personsArray);
    }

//SEARCH
    /**
     * Searches for %{term}% in firstname|lastname for Person
     *
     * @Route("/person/search/{term}",
     *    name="person_search",
     *    requirements={"term": "^([a-zA-Z0-9\ \-]+)"},
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Person::class))
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
     * @SWG\Tag(name="Person")
     */
    public function search(Request $request, PaginatorInterface $paginator, string $term)
    {
        $this->denyAccessUnlessGranted('personList');

        $persons = $paginator->paginate(
            $this->personService->findAllSearch($term),
            $request->query->getInt('page', 1),
            $request->query->getInt('size', 50)
        );

        $personsArray = array();
        foreach ($persons->getItems() as $person) {
            $personsArray[] = $this->personService->toArray($person);
        };

        return new JsonResponse($personsArray);
    }


//SEARCH
    /**
     * Searches for %{term}% in firstname|lastname for Person
     *
     * @Route("/person/searchTerm/{term}",
     *    name="person_search_term",
     *    requirements={"term": "^([a-zA-Z0-9\ \-]+)"},
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Person::class))
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
     * @SWG\Tag(name="Person")
     */
    public function searchByTerm(Request $request, string $term)
    {
        $this->denyAccessUnlessGranted('personList');

        $personsArray = $this->personService->searchByTerm($term);

        return new JsonResponse($personsArray);
    }

    /**
     * Searches person by criteria
     *
     * @Route("/person/search/criterias",
     *    name="person_search_criteria",
     *    methods={"HEAD", "POST"}
     *     )
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Person::class))
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
     * @SWG\Tag(name="Person")
*/
    public function searchByCriteria(Request $request)
    {

        $personsArray = $this->personService->searchByCriteria($request->getContent());

        return new JsonResponse($personsArray);
    }






//associate child to personn
    /**
     * Displays person using its user's identifier
     *
     * @Route("/person/associateChild/{personId}/{childId}",
     *    name="person_associateChild",
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Person::class))
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
     *     name="persoinId",
     *     in="path",
     *     required=true,
     *     description="User's identifier of the person",
     *     type="string",
     * )
     * @SWG\Tag(name="Person")
     */
    public function associatedPersonChild($personId, $childId)
    {
        $result = $this->personService->associate($personId, $childId);

        return new JsonResponse($result);
    }



    //DISPLAY WITH ID
    /**
     * Displays person using its id
     *
     * @Route("/person/blackListed/{personId}/{value}",
     *    name="person_display",
     *    requirements={"personId": "^([0-9]+)$"},
     *    methods={"HEAD", "GET"})
     * @Entity("person", expr="repository.findOneById(personId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Person::class))
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
     *     name="personId",
     *     in="path",
     *     required=true,
     *     description="Id of the person",
     *     type="integer",
     * )
     * @SWG\Tag(name="Person")
     */
    public function updateBlackListed(Person $person, $value)
    {
        $personArray = $this->personService->updateBlackListed($person, $value);

        return new JsonResponse($personArray);
    }






//DISPLAY WITH ID
    /**
     * Displays person using its id
     *
     * @Route("/person/display/{personId}",
     *    name="person_display",
     *    requirements={"personId": "^([0-9]+)$"},
     *    methods={"HEAD", "GET"})
     * @Entity("person", expr="repository.findOneById(personId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Person::class))
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
     *     name="personId",
     *     in="path",
     *     required=true,
     *     description="Id of the person",
     *     type="integer",
     * )
     * @SWG\Tag(name="Person")
     */
    public function display(Person $person)
    {
        $this->denyAccessUnlessGranted('personDisplay', $person);

        $personArray = $this->personService->toArray($person);

        return new JsonResponse($personArray);
    }

//DISPLAY WITH USER'S IDENTIFIER
    /**
     * Displays person using its user's identifier
     *
     * @Route("/person/display/{identifier}",
     *    name="person_display_identifier",
     *    requirements={"identifier": "^([a-z0-9]{32})"},
     *    methods={"HEAD", "GET"})
     * @Entity("person", expr="repository.findByUserIdentifier(identifier)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Person::class))
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
     *     name="identifier",
     *     in="path",
     *     required=true,
     *     description="User's identifier of the person",
     *     type="string",
     * )
     * @SWG\Tag(name="Person")
     */
    public function displayByIdentifier(Person $person)
    {
        $this->denyAccessUnlessGranted('personDisplay', $person);

        $personArray = $this->personService->toArray($person);

        return new JsonResponse($personArray);
    }

//CREATE
    /**
     * Creates a Person
     *
     * @Route("/person/create",
     *    name="person_create",
     *    methods={"HEAD", "POST"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="person", ref=@Model(type=Person::class)),
     *     )
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Data for the Person",
     *     required=true,
     *     @Model(type=PersonType::class)
     * )
     * @SWG\Tag(name="Person")
     */
    public function create(Request $request)
    {
        $this->denyAccessUnlessGranted('personCreate');

        $createdData = $this->personService->create($request->getContent());

        return new JsonResponse($createdData);
    }

//MODIFY
    /**
     * Modifies person
     *
     * @Route("/person/modify/{personId}",
     *    name="person_modify",
     *    requirements={"personId": "^([0-9]+)$"},
     *    methods={"HEAD", "PUT"})
     * @Entity("person", expr="repository.findOneById(personId)")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         @SWG\Property(property="status", type="boolean"),
     *         @SWG\Property(property="message", type="string"),
     *         @SWG\Property(property="person", ref=@Model(type=Person::class)),
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
     *     name="personId",
     *     in="path",
     *     required=true,
     *     description="Id of the person",
     *     type="integer",
     * )
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="Data for the Person",
     *     required=true,
     *     @Model(type=PersonType::class)
     * )
     * @SWG\Tag(name="Person")
     */
    public function modify(Request $request, Person $person)
    {
        $this->denyAccessUnlessGranted('personModify', $person);

        $modifiedData = $this->personService->modify($person, $request->getContent());

        return new JsonResponse($modifiedData);
    }

//DELETE
    /**
     * Deletes person
     *
     * @Route("/person/delete/{personId}",
     *    name="person_delete",
     *    requirements={"personId": "^([0-9]+)$"},
     *    methods={"HEAD", "DELETE"})
     * @Entity("person", expr="repository.findOneById(personId)")
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
     *     name="personId",
     *     in="path",
     *     required=true,
     *     description="Id of the person",
     *     type="integer",
     * )
     * @SWG\Tag(name="Person")
     */
    public function delete(Person $person)
    {
        $this->denyAccessUnlessGranted('personDelete', $person);

        $suppressedData = $this->personService->delete($person);

        return new JsonResponse($suppressedData);
    }


//GET PERSONN BY NUMBER
    /**
     * Return person by number
     *
     * @Route("/person/from/{number}",
     *    name="person_from_number",
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Person::class))
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
     *     name="number",
     *     in="path",
     *     required=true,
     *     description="phone number of the person",
     *     type="string",
     * )
     * @SWG\Tag(name="Person")
     */
    public function getPersonByNumber($number)
    {
        
        $this->denyAccessUnlessGranted('personList');

        $result = $this->personService->getPersonFromNumber($number);

        return new JsonResponse($result);
    }



//UNASSOCIATE PERSON
    /**
     * unassociate 2 persones
     *
     * @Route("/person/unassociate/{personId1}/{personId2}",
     *    name="person_unassociate",
     *    methods={"HEAD", "DELETE"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Person::class))
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
     * @SWG\Tag(name="Person")
     */
    public function personUnassociate($personId1, $personId2)
    {
        $this->denyAccessUnlessGranted('personList');

      
        $result = $this->personService->unassociate($personId1, $personId2);
        

        return new JsonResponse($result);
    }



//LIST DOUBLON
    /**
     * Lists all the persons doublon
     *
     * @Route("/person/doublon",
     *    name="person_doublon",
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Success",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Person::class))
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
     * @SWG\Tag(name="Person")
     */
    public function listDoublon(Request $request)
    {
        $this->denyAccessUnlessGranted('personList');

      
        $result = $this->personService->listDoublon();
        

        return new JsonResponse($result);
    }


//SEARCH BY EMAIL (including suppressed)
    /**
     * Searches persons by email including soft-deleted
     *
     * @Route("/person/search/email/{email}",
     *    name="person_search_email",
     *    methods={"HEAD", "GET"})
     *
     * @SWG\Response(response=200, description="Success")
     * @SWG\Response(response=403, description="Access denied")
     * @SWG\Parameter(name="email", in="path", required=true, description="Email to search", type="string")
     * @SWG\Tag(name="Person")
     */
    public function searchByEmail(string $email)
    {
        $this->denyAccessUnlessGranted('personList');

        $result = $this->personService->searchByEmail($email);

        return new JsonResponse($result);
    }


//FREE EMAIL (rename email to email_old to unblock duplicate)
    /**
     * Renames the email of a person (even suppressed) to email_old
     *
     * @Route("/person/free-email/{userId}",
     *    name="person_free_email",
     *    requirements={"userId": "^([0-9]+)$"},
     *    methods={"HEAD", "POST"})
     *
     * @SWG\Response(response=200, description="Success")
     * @SWG\Response(response=403, description="Access denied")
     * @SWG\Response(response=404, description="Not Found")
     * @SWG\Parameter(name="userId", in="path", required=true, description="Id of the user", type="integer")
     * @SWG\Tag(name="Person")
     */
    public function freeEmail(int $userId)
    {

        $this->denyAccessUnlessGranted('personList');
        // to free email, we rename it to email_old + userId + timestamp to avoid duplicate if we want to reuse the same email after

        $result = $this->personService->freeEmail($userId);

        return new JsonResponse($result);
    }

}
