<?php

namespace App\Service;

use App\Entity\ShortUrl;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * ShortUrlService class
 * @author Sandy Razafitrimo <sandyrazafitrimo@gmail.com>
 */
class ShortUrlService implements ShortUrlServiceInterface
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
    public function findByShortCode($code) {
        if(!$shortUrl = $this->em->getRepository('App\Entity\ShortUrl')->findOneBy(['urlCode' => $code])) return 'not_found';
        return $shortUrl->toArray();


    }

    /**
     * {@inheritdoc}
     */
    public function create(string $data)
    {

        $data = json_decode($data, true);

        $code = $this->createShortCode();

        $shortUrl = new ShortUrl();
        $shortUrl->setUrlCode($code);
        $shortUrl->setNewUrl('https://appli-v.net/'.$code);
        $shortUrl->setOriginalUrl($data['original_url']);

        $this->em->persist($shortUrl);
        $this->em->flush();
        //Returns data
        return array(
            'status' => true,
            'message' => 'Short url ajouté',
            'shortUrl' => $shortUrl->toArray(),
        );
    }

    public function createShortCode() {


        $letters = range("a", "z");
        $l1 = rand(0,25);
        $l2 = rand(0,25);
        $l3 = rand(0,25);

        
        $code = $letters[$l1].$letters[$l2].$letters[$l3];

        $shortUrl = $this->em->getRepository('App\Entity\ShortUrl')->findOneBy(['urlCode' => $code]);

        if(!$shortUrl) {
            return $code;
        } else {
            $this->createShortCode();
        }
    }


    /**
     * {@inheritdoc}
     */
    public function delete(ShortUrl $object)
    {
      
    }

    /**
     * Returns the list of all families in the array format
     * @return array
     */
    public function findAll()
    {
        $shortUrls = $this->em
            ->getRepository('App\Entity\ShortUrl')
            ->findBy([], ['id' => 'desc'])
        ;

        foreach($shortUrls as $url) {
            $arr[] = $url->toArray();
        }

        return $arr;
    }

    /**
     * Searches the term in the Blog collection
     * @return array
     */
    public function findAllSearch(string $term)
    {
      
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(ShortUrl $object)
    {
        //Main data
        $objectArray = $this->mainService->toArray($object->toArray());

        return $objectArray;
    }
}
