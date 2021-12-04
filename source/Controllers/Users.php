<?php

namespace Source\Controllers;

use Source\Core\Auth;

class Users extends RootApi
{
    public function me(): void
    {
        if (!Auth::user()) {
            $this->call(
                500,
                false,
                "Precisas estar autenticado para ter acesso"
            )->back();
            return;
        }

        if (!Auth::user()) {
            $this->call(
                500,
                false,
                "Precisas estar autenticado para ter acesso"
            )->back();
            return;
        }

        $user = Auth::user();

        unset($user->data()->password_hash);

        $this->call(200, true)->back($user->data());
    }
}