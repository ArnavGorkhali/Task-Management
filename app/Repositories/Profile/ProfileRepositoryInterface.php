<?php

namespace App\Repositories\Profile;

interface ProfileRepositoryInterface
{
    public function getProfile();

    public function update($request);

    public function changePassword($request);
}
