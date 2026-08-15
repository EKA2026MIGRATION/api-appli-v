<?php

namespace App\Service;

use App\Entity\BookletChild;
use App\Entity\BookletChildAnswer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * BookletChildService class
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
class BookletChildService implements BookletChildServiceInterface
{
    private $em;

    private $mainService;

    private $status = ['draft', 'toreread', 'ready', 'published', 'archived'];

    public function __construct(
        EntityManagerInterface $em,
        MainServiceInterface $mainService
    )
    {
        $this->em = $em;
        $this->mainService = $mainService;
    }

    public function display($bookletChild, $showNavigation = true) {

        $booklet = $bookletChild->getBooklet()->toArray();
        
        if (null !== $booklet['boards']) {

            foreach($bookletChild->getBooklet()->getBoards() as $k => $board) {
                $responseArray = [];
                foreach($board->getItems() as $item) {
                    $itemArray = $item->toArray();
                    if(!$answer = $this->em->getRepository('App\Entity\BookletChildAnswer')->findOneBy(['bookletChild' => $bookletChild, 'itemReferenceId' => $item->getId()])) {
                        $answer = new BookletChildAnswer();
                        $answer->setBookletChild($bookletChild);
                        $answer->setItemReferenceId($item->getId());
        
                        //Persists data
                        $this->mainService->create($answer);
                        $this->mainService->persist($answer);
                    }
                    $responseArray[] = [
                                        'item' => $itemArray,
                                        'answer' => $answer->toArray()
                    ];
                }
                $boardsArray[] = $board->toArray();
                unset($boardsArray[$k]['items']);
                $boardsArray[$k]['response'] = $responseArray;
            }
           $booklet['boards'] = $boardsArray;

        }

        $bookletChildArray = $bookletChild->toArray();
        $bookletChildArray['booklet'] = $booklet;

        if($showNavigation == false || $bookletChild->getStatus() == "published") return ['bookletChildArray' => $bookletChildArray];

        // list all infos for navigatioons
        $allBookletChilds =  $this->em->getRepository('App\Entity\BookletChild')->findAllByStaff($bookletChild->getStaff(), "edition");

        $totalBooklet = 0; $totalReady = 0; $totalToreread = 0;


        foreach($allBookletChilds as $k => $allBookletChild) {

            $totalBooklet++;
            if($allBookletChild->getStatus() == "ready") $totalReady++;
            if($allBookletChild->getStatus() == "toreread") $totalToreread++;


            if($allBookletChild->getId() == $bookletChild->getId()) {
                $currentId = $k;
            }
         
        }

        $prevEl = $currentId-1;
        $nextEl = $currentId+1;
    
        if(isset($allBookletChilds[$prevEl])) {
            $prevName      = $allBookletChilds[$prevEl]->getChild()->getFullname();
            $prevBookletId = $allBookletChilds[$prevEl]->getId();
        } else {
            $prevName      = null;
            $prevBookletId = null;
        }

        if(isset($allBookletChilds[$nextEl])) {
            $nextName      = $allBookletChilds[$nextEl]->getChild()->getFullname();
            $nextBookletId = $allBookletChilds[$nextEl]->getId();
        } else {
            $nextName      = null;
            $nextBookletId = null;
        }
     
        $navigation = [
                'totalBooklet' => $totalBooklet,
                'totalReady'   => $totalReady,
                'totalToreread' => $totalToreread,
                'prevBooklet'  => ['name' => $prevName, 'bookletId' => $prevBookletId],
                'nextBooklet'  => ['name' => $nextName, 'bookletId' => $nextBookletId],
        ];

        return [ 
                'navigation'        => $navigation,
                'bookletChildArray' => $bookletChildArray
            ];
    }


    public function changeDateEvaluation($dateEvaluation) {

        $staff = null;

        $season = $this->em->getRepository('App\Entity\Season')->findOneBy(['status' => 'active'], ['seasonId' => 'desc']);
        $from = $season->getDateStart()->format('Y-m-d');
        $to   = $season->getDateEnd()->format('Y-m-d');

        $bookletchilds = $this->em->getRepository('App\Entity\BookletChild')->findAllBooklet("edition", $staff, $from, $to);

        foreach($bookletchilds as $bookletChild) {
            $bookletChild->setDateEvaluation($dateEvaluation);
            $this->mainService->modify($bookletChild);
            $this->mainService->persist($bookletChild);
        }


    }


    /**
     * {@inheritdoc}
     */
    public function create(string $data)
    {
        $data = json_decode($data, true);

        if(isset($data['child_id'])) {
            $child_id = $data['child_id'];
        } else if (isset($data['childId'])) {
            $child_id = $data['childId'];
        } else {
            return ['message' => 'child_id not present in data'];
        }

        if(isset($data['booklet_id'])) {
            $booklet_id = $data['booklet_id'];
        } else if (isset($data['bookletId'])) {
            $booklet_id = $data['bookletId'];
        } else {
            return ['message' => 'booklet_id not present in data'];
        }

        if(isset($data['staff_id'])) {
            $staff_id = $data['staff_id'];
        } else if (isset($data['staffId'])) {
            $staff_id = $data['staffId'];
        } else {
            $staff_id = null;
        }

        //Submits data
        if(!$bookletChild = $this->createManuel($booklet_id, $child_id, $staff_id)) return ['message' => 'child or booklet or staff not founded'];

        //Returns data
        return array(
            'status' => true,
            'message' => 'BookletChild ajouté',  
            'bookletchild' => $this->mainService->toArray($this->toArray($bookletChild)),
        );
    }

    public function latestList($status) {

        $currentDate = date('Y-m-d');
        $fromDate = date('Y-m-d', strtotime('-12 months', strtotime($currentDate)));


        $bookletChilds =  $this->em->getRepository('App\Entity\BookletChild')->findLatest($fromDate);
        foreach($bookletChilds as $bc) {



            if( isset($arr[$bc->getChild()->getChildId()])) {
               // if ( count($arr[$bc->getChild()->getChildId()]) > 4) continue;
            }

            $arr[$bc->getChild()->getChildId()][] = [

                        'bookletId' => $bc->getBooklet()->getId(),
                        'name' => $bc->getBooklet()->getName(),
                        'dateEval' => $bc->getDateEvaluation()->format('Y-m-d'),
                        'staffId' => $bc->getStaff()->getStaffId(),
                        'staffName' => $bc->getStaff()->getFullname(),
                        'status' => $bc->getStatus()
                    ];
        }

        return $arr;

    }

    public function previousByChild($childId, $bookletId, $bookletChildId) {

        if(!$child   = $this->em->getRepository('App\Entity\Child')->find($childId)) return "no child founded";
        if(!$booklet = $this->em->getRepository('App\Entity\Booklet')->find($bookletId)) return "no booklet founded";
        
        if(!$bookletChild =  $this->em->getRepository('App\Entity\BookletChild')->findPreviousBookletChild($childId, $bookletId, $bookletChildId)) return "no bookletChild founded";
        return $this->display($bookletChild, false);
    }

    /**
     * {@inheritdoc}
     */
    public function createMultiple(string $data)
    {
        $datas = json_decode($data, true);

        $message = [];

        foreach($datas as $k => $data) {

            $go = true;

            if(isset($data['child_id'])) {
                $child_id = $data['child_id'];
            } else if (isset($data['childId'])) {
                $child_id = $data['childId'];
            } else {
                $go = false;
                $currentMessage[$k][] = "pb child_id";
            }

            if(isset($data['booklet_id'])) {
                $booklet_id = $data['booklet_id'];
            } else if (isset($data['bookletId'])) {
                $booklet_id = $data['bookletId'];
            } else {
                $go = false;
                $currentMessage[$k][] = "pb bookletId";
            }

            if(isset($data['staff_id'])) {
                $staff_id = $data['staff_id'];
            } else if (isset($data['staffId'])) {
                $staff_id = $data['staffId'];
            } else {
                $go = false;
                $currentMessage[$k][] = "pb staff_id";
            }


            if($go == true) {
                //Submits data
                if(!$bookletChild = $this->createManuel($booklet_id, $child_id, $staff_id)) {
                    $message[] = 'cannot create booklet for child_id '.$child_id;
                }  else {
                    $message[] = 'child : '.$bookletChild->getChild()->getFullname().' - booklet '.$bookletChild->getBooklet()->getName();
                }
            } else {
                $message[] = $currentMessage;
            }
        }

        //Returns data
        return $message;
    }


    public function createManuel($booklet_id, $child_id, $staff_id = null, $status = "draft", $datas = [] ) {
         
        if(!$child   = $this->em->getRepository('App\Entity\Child')->find($child_id)) return null;
        if(!$booklet = $this->em->getRepository('App\Entity\Booklet')->find($booklet_id)) return null;

        if($staff_id != null) {
            $staff = $this->em->getRepository('App\Entity\Staff')->find($staff_id);
        } else {
            $staff = null;
        }

        (!isset($datas['date_evaluation'])) ? $date_evaluation = date('Y-m-d') : $date_evaluation = $datas['date_evaluation'];

        //Submits data
         $object = new BookletChild();
         $object->setBooklet($booklet);
         $object->setChild($child);
         $object->setStatus($status);
         $object->setStaff($staff);
         $object->setDateEvaluation($date_evaluation);

         //Persists data
         $this->mainService->create($object);
         $this->mainService->persist($object);


         // create answer
         foreach($booklet->getBoards() as $board) {

            foreach($board->getItems() as $item) {

                $answer = new BookletChildAnswer();
                $answer->setBookletChild($object);
                $answer->setItemReferenceId($item->getId());

                //Persists data
                $this->mainService->create($answer);
                $this->mainService->persist($answer);

            }
         }

         return $object;
 
    }

    /**
     * {@inheritdoc}
     */
    public function delete(BookletChild $object)
    {
       
        //Persists data
        $this->mainService->delete($object);
        $this->mainService->persist($object);

        return array(
            'status' => true,
            'message' => 'BookletChild supprimé',
        );
    }

    /**
     * Returns the list of all persons in the array format
     * status : edition or published
     * seasonStatus : active or all
     * @return array
     */
    public function findAllBooklet($seasonStatus, $status, $staff_id = null)
    {

        if($staff_id) {
            $staff = $this->em->getRepository('App\Entity\Staff')->find($staff_id);
        } else {
            $staff = null;
        }

        if($seasonStatus == 'active') {
            $season = $this->em->getRepository('App\Entity\Season')->findOneBy(['status' => 'active'], ['seasonId' => 'desc']);
            $from = $season->getDateStart()->format('Y-m-d');
            $to   = $season->getDateEnd()->format('Y-m-d');
        } else {
            $from = null; $to = null;
        }

        $bookletchilds = $this->em->getRepository('App\Entity\BookletChild')->findAllBooklet($status, $staff, $from, $to);

        $results = [];

        foreach($bookletchilds as $bookletChild) {
            $results[] = $this->mainService->toArray($this->toArray($bookletChild));
        }

        return $results;
    }

    /**
     * {@inheritdoc}
     */
    public function isEntityFilled(BookletChild $object)
    {
        if (
            null === $object->getChild()
            ) {
            throw new UnprocessableEntityHttpException('Missing data for BookletChild -> ' . json_encode($this->toArray($object)));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function modify(BookletChild $object, string $data)
    {

        //Submits data
        $data = json_decode($data, true);

        if(isset($data['staff_id'])) {
            $staff = $this->em->getRepository('App\Entity\Staff')->find($data['staff_id']);
            $object->setStaff($staff);
        } 

        if(isset($data['staffId'])) {
            $staff = $this->em->getRepository('App\Entity\Staff')->find($data['staffId']);
            $object->setStaff($staff);
        } 

        //Submits data

        $this->mainService->hydrate($object, $data);

        //Checks if entity has been filled
        $this->isEntityFilled($object);

        //Persists data
        $this->mainService->modify($object);
        $this->mainService->persist($object);


        //Returns data
        return array(
            'status' => true,
            'message' => 'BookletChild modifié',
            'bookletchild' => $this->toArray($object),
        );
    }

    public function findByChild($childId, $status) {
        if( !$child = $this->em->getRepository('App\Entity\Child')->find($childId)) return "no child founded with id :".$childId;


        if(!$bookletChilds = $this->em->getRepository('App\Entity\BookletChild')->findAllBychild($child, $status)) return "no bookletchild found for status ".$status." and childId ".$childId;

        $arr = [];
        foreach($bookletChilds as $bookletChild) {
            $arr[] = $this->display($bookletChild, false);
        }

        return $arr;

    }


    /**
     * {@inheritdoc}
     */
    public function updateAnswer(BookletChildAnswer $bookletChildAnswer, string $data)
    {

        //Submits data
        $data = json_decode($data, true);

        //Submits data
        $this->mainService->hydrate($bookletChildAnswer, $data);

        //Persists data
        $this->mainService->modify($bookletChildAnswer);
        $this->mainService->persist($bookletChildAnswer);


        //Returns data
        return array(
            'status' => true,
            'message' => 'BookletChildAnswer modifié',
            'bookletChildAnswer' => $bookletChildAnswer->toArray()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(BookletChild $object)
    {
        //Main data
        $objectArray = $object->toArray();

        return $objectArray;
    }
}