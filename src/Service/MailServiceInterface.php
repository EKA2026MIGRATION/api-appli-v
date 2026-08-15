<?php

namespace App\Service;

use App\Entity\Mail;

/**
 * MailServiceInterface class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
interface MailServiceInterface
{
    /**
     * Creates the mail
     * @return array
     */
    public function create(string $data);

    /**
     * Marks the mail as deleted
     * @return array
     */
    public function delete(Mail $object);

    /**
     * Checks if the entity has been well filled
     * @throw Exception
     */
    public function isEntityFilled(Mail $object);

    /**
     * Modifies the mail
     * @return array
     */
    public function modify(Mail $object, string $data);

    /**
     * Converts entity to array
     * @return array
     */
    public function toArray(Mail $object);
}
