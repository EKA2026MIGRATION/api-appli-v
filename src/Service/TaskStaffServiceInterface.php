<?php

namespace App\Service;

use App\Entity\TaskStaff;

/**
 * TaskStaffServiceInterface class
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
interface TaskStaffServiceInterface
{
    /**
     * Creates the TaskStaff
     * @return array
     */
    public function create(string $data);


}
