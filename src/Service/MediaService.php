<?php

namespace App\Service;

use App\Entity\Media;
use App\Service\MediaServiceInterface;
use App\Service\MainServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use \PDO;

/**
 * MediaService class
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
class MediaService implements MediaServiceInterface
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

    /**
     * {@inheritdoc}
     */
    public function create(string $data)
    {
        $datas = json_decode($data, true);

        $result = [];
        foreach($datas as $data) {
            if(isset($data['child_id'])) {
                foreach(explode(',', $data['child_id']) as $childId) {
                    if($child = $this->em->getRepository('App\Entity\Child')->find($childId)) {
                        $media = new Media();
                        $this->mainService->hydrate($media, $data);
                        $media->setChild($child);
                    }
                    //Persists data
                    $this->mainService->create($media);
                    $this->mainService->persist($media);

                    $result[] = $media->toArray();
                }
            }

        }


        //Returns data
        return array(
            'status' => true,
            'message' => 'Media ajouté',
            'media' => $result,
        );
    }

    public function list($status) {
        $medias = $this->em->getRepository('App\Entity\Media')->findBy(['status' => $status], ['createdAt' => 'desc']);

        if(!$medias) return null;
        foreach($medias as $media) {
            $result[] = $this->toArray($media);
        }
        return $result;
    }

    public function listByChild($childId, $status = "online") {

        if(!$child = $this->em->getRepository('App\Entity\Child')->find($childId)) return null;


        if($status == "all") {
            $medias = $this->em->getRepository('App\Entity\Media')->findBy(['child' => $child], ['createdAt' => 'desc']);

        } else {
            $medias = $this->em->getRepository('App\Entity\Media')->findBy(['status' => $status, 'child' => $child], ['createdAt' => 'desc']);
        }


        if(!$medias) return null;
        foreach($medias as $media) {
            $result[] = $this->toArray($media);
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function modify(Media $media, string $data)
    {
        $data = json_decode($data, true);
        if($child = $this->em->getRepository('App\Entity\Child')->find($data['child_id'])) {
            $media->setChild($child);
        }
        $this->mainService->hydrate($media, $data);
        //Persists data
        $this->mainService->modify($media);
        $this->mainService->persist($media);

        if($media->getStatus()== "online" && $child) {
            $currentDate = new \DateTime();
            $child->setDateLatestMedia($currentDate);
            $this->em->persist($child);
            $this->em->flush();
        }

        //Returns data
        return array(
            'status' => true,
            'message' => 'Media modifié',
            'media' => $media->toArray(),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function updateAllDateLatestMedia() {

        // retrieve all medias group_by child_id and order with latest updated_at where status = online with PDO and mysql native request
        $sql = "SELECT m1.*
                FROM media m1
                WHERE m1.status = 'online'
                AND m1.updated_at = (
                    SELECT MAX(m2.updated_at)
                    FROM media m2
                    WHERE m2.status = 'online' AND m2.child_id = m1.child_id
                )
        ORDER BY m1.updated_at DESC;";

        $stmt = $this->em->getConnection()->prepare($sql);
        $medias = $stmt->executeQuery()->fetchAllAssociative();

        // init $message return (list of child updated child fullname and id)
        $message = [];

        // update date_latest_media for each child
        foreach($medias as $media) {

            if(!isset($media['child_id'])) continue;
            $child = $this->em->getRepository('App\Entity\Child')->find($media['child_id']);

            if($child) {
                $currentDate = new \DateTime($media['updated_at']);
                $child->setDateLatestMedia($currentDate);
                $this->em->persist($child);
                 $this->em->flush();

                // add name to message
                $message[$child->getChildId()] = $media['updated_at'].' : '.$child->getFullname();
            }
        }

        return array(
            'status' => true,
            'message' => $message,
        );
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(Media $media)
    {
        $result = $media->toArray();
        if(!$media->getChild()) return $result;
        $child = $media->getChild();
        $result['child_id'] = $child->getChildId();
        $result['child']    = [
                                'first_name' => $child->getFirstname(),
                                'last_name' => $child->getLastname(),
                                'full_name' => $child->getFullname(),
                                'id'        => $child->getChildId()
                                ];

        return $result;

    }
}