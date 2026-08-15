<?php

namespace App\Service;


/**
 * MainServiceInterface class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
interface MainServiceInterface
{
    /**
     * Sets common data when creating an object
     */
    public function create($object);

    /**
     * Sets common data when deleting an object
     */
    public function delete($object);

    /**
     * Sets common data when modifying an object
     */
    public function modify($object);

    /**
     * Persists the object in the DB
     */
    public function persist($object);

    /**
     * Submits the data to hydrate the object
     */
    public function submit($object, $formName, $data);

    /**
     * Converts common data from object to array
     */
    public function toArray($objectArray);
}
