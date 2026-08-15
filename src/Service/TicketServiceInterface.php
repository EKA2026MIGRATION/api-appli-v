<?php

namespace App\Service;

use App\Entity\Ticket;

/**
 * TicketServiceInterface class
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
interface TicketServiceInterface
{

    /**
     * Converts entity to array
     * @return array
     */
    public function toArray(Ticket $object);
}
