<?php

namespace AppBundle\FlysystemAdapter;

use AppBundle\Entity\User;

interface FlysystemAdapterInterface
{
    public function getFilesystem(User $user);
}
