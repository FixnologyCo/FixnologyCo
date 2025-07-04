<?php

namespace App\Policies;

use Core\Models\ClienteFixgi;

class RolePolicy
{
    /**
     * Permite el acceso si el rol coincide
     */
    public function accessRole(ClienteFixgi $user, $role)
    {
        return $user->id_rol == $role;
    }
}
