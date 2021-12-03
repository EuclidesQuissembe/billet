<?php

namespace Source\Controllers;

use Source\Core\Auth;
use Source\Models\Billet\Billet;
use Source\Models\Payer\Payer;
use Source\Models\Payer\PayerAddress;
use Source\Models\Payer\PayerComplement;
use Source\Models\Payer\PayerNumber;
use Source\Support\Upload;

class Payers extends RootApi
{
    public function __construct()
    {
        parent::__construct();

        if (!Auth::user()) {
            $this->call(
                500,
                false,
                "Precisas estar autenticado para ter acesso"
            )->back();
            return;
        }
    }

    public function create(array $data): void
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        if (in_array("", $data)) {
            $this->call(400, false, 'Informe todos os campos')->back();
            return;
        }

        if (empty($_FILES['document']) || $_FILES['document']['error'] != 0) {
            $this->call(400, false, 'Envie o documento do pagador')->back();
            return;
        }

        $payer = (new Payer())->bootstrap(
            $data['user_id'] ?? 0,
          $data['name'] ?? "",
            $data['email'] ?? "",
            $data['cell_phone'] ?? "",
            $data['day'] ?? 0,
            $data['month'] ?? 0,
            $data['year'] ?? 0
        );

        if (!$payer->save()) {
            $this->call(500, false, $payer->message()->json())->back();
            return;
        }

        list($type) = explode('/', $_FILES['document']['type']);

        if ($type === 'image') {
            $documentPath = substr(
                (new Upload('payers'))->image($_FILES['document'], $payer->name),
                strlen('storage/')
            );
        } else {
            $documentPath = substr(
                (new Upload('payers'))->file($_FILES['document'], $payer->name),
                strlen('storage/')
            );
        }

        $payer->document_path = $documentPath;

        $payer->save();

        // Address
        $address = new PayerAddress();
        $address->payer_id = $payer->id;
        $address->cep = $data['cep'];
        $address->street = $data['street'];
        $address->district = $data['district'];
        $address->city = $data['city'];
        $address->state = $data['state'];

        if (!$address->save()) {
            $payer->destroy();

            $this->call(500, false, $address->message()->json())->back();
            return;
        }

        if (isset($data['complement'])) {
            // Complement
            $complement = new PayerComplement();
            $complement->payer_id = $payer->id;
            $complement->complement = $data['complement'];


            if (!$complement->save()) {
                $address->destroy();
                $payer->destroy();

                $this->call(500, false, $complement->message()->json())->back();
                return;
            }

            $payer->complement = $complement->data();
        }

        // Number
        if (isset($data['number'])) {
            $number = new PayerNumber();
            $number->payer_id = $payer->id;
            $number->number = $data['number'];

            if (!$number->save()) {
                $address->destroy();
                $payer->destroy();

                $this->call(500, false, $number->message()->json())->back();
                return;
            }


            $payer->number = $number->data();
        }

        $payer->address = $address->data();

        $payer->document_path = storage($payer->document_path);

        $this->call(201, true)->back($payer->data());
    }

    public function all(): void
    {
        $user = Auth::user();

        $payersData = (new Payer())->find('user_id = :id', "id={$user->id}")->fetch(true);

        $payers = [];
        if ($payersData) {
            foreach ($payersData as $payer) {
                $payer->address = (new PayerAddress())
                    ->find('payer_id = :id', "id={$payer->id}")
                    ->fetch()
                    ->data();

                $number = (new PayerNumber())->find('payer_id = :id', "id={$payer->id}")->fetch();

                if ($number) {
                    $payer->number = $number->data();
                }

                $complement = (new PayerComplement())->find('payer_id = :id', "id={$payer->id}")->fetch();

                if ($complement) {
                    $payer->complement = $complement->data();
                }

                $payer->document_path = storage($payer->document_path);
                $payers[] = $payer->data();
            }
        }

        $this->call(200, true)->back($payers);
    }

    public function delete(array $data)
    {
        if (!$payerId = filter_var($data['payer_id'], FILTER_VALIDATE_INT)) {
            $this->call(400, false, 'O id do pagador deve ser um número')->back();
            return;
        }

        $payer = (new Payer())->findById($payerId);

        if (!$payer) {
            $this->call(500, false, 'Pagador não encontrado')->back();
            return;
        }

        $user = Auth::user();

        $billets = (new Billet())->find(
            'user_id = :user AND payer_id = :payer',
            "user={$user->id}&payer={$payerId}"
        )->fetch(true);

        if ($billets) {
            $this
                ->call(500, false, 'Este pagador não pode ser eliminado, pois possui boletos')
                ->back();
            return;
        }

        (new PayerAddress())->delete('payer_id', $payerId);
        (new PayerComplement())->delete('payer_id', $payerId);
        (new PayerNumber())->delete('payer_id', $payerId);

        if (!$payer->destroy()) {
            $this->call(500, false, 'Houve um erro ao eliminar o pagador')->back();
            return;
        }
        $this->call(200, true, 'Pagador eliminado com sucesso')->back();
    }
}