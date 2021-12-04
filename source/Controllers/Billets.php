<?php

namespace Source\Controllers;

use Source\Core\Auth;
use Source\Models\Billet\Billet;
use Source\Models\Billet\BilletDiscounts;
use Source\Models\Billet\BilletFee;
use Source\Models\Billet\BilletInstruction;
use Source\Models\Billet\BilletReference;
use Source\Models\Billet\BilletTicketFine;
use Source\Models\Payer\Payer;

class Billets extends RootApi
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

    public function index(array $data): void
    {
        if (!$payerId = filter_var($data['payer_id'], FILTER_VALIDATE_INT)) {
            $this->call(500, false, 'O id do pagador deve ser inteiro')->back();
            return;
        }

        $payer = (new Payer())->findById($payerId);

        if (!$payer) {
            $this->call(500, false, 'Pagador não encontrado')->back();
            return;
        }

        $user = Auth::user();

        $billetsData = (new Billet())
            ->find('user_id = :id AND payer_id = :p', "id={$user->id}&p={$payerId}")
            ->fetch(true);

        $billets = [];
        if ($billetsData) {
            foreach ($billetsData as $billet) {
                $fee = (new BilletFee())->find('billet_id = :id', "id={$billet->id}")->fetch();
                $ticket = (new BilletTicketFine())->find('billet_id = :id', "id={$billet->id}")->fetch();
                $discount = (new BilletDiscounts())->find('billet_id = :id', "id={$billet->id}")->fetch();
                $reference = (new BilletReference())->find('billet_id = :id', "id={$billet->id}")->fetch();
                $instructions = (new BilletInstruction())->find('billet_id = :id', "id={$billet->id}")->fetch(true);

                if($fee) {
                    $billet->fee = $fee->data();
                }

                if ($ticket) {
                    $billet->ticket = $ticket->data();
                }

                if ($discount) {
                    $billet->discount = $discount->data();
                }

                if ($reference) {
                    $billet->reference = $reference->data();
                }

                if ($instructions) {
                    $ins = [];
                    foreach ($instructions as $instruction) {
                        $ins[] = $instruction->data();
                    }

                    $billet->instructions = $ins;
                }

                $billets[] = $billet->data();
            }
        }

        $this->call(200, true)->back($billets);
    }

    public function all()
    {
        $user = Auth::user();

        $billetsData = (new Billet())->find('user_id = :id', "id={$user->id}")->fetch(true);

        $billets = [];
        if ($billetsData) {
            foreach ($billetsData as $billet) {
                $fee = (new BilletFee())->find('billet_id = :id', "id={$billet->id}")->fetch();
                $ticket = (new BilletTicketFine())->find('billet_id = :id', "id={$billet->id}")->fetch();
                $discount = (new BilletDiscounts())->find('billet_id = :id', "id={$billet->id}")->fetch();
                $reference = (new BilletReference())->find('billet_id = :id', "id={$billet->id}")->fetch();
                $instructions = (new BilletInstruction())->find('billet_id = :id', "id={$billet->id}")->fetch(true);

                if($fee) {
                    $billet->fee = $fee->data();
                }

                if ($ticket) {
                    $billet->ticket = $ticket->data();
                }

                if ($discount) {
                    $billet->discount = $discount->data();
                }

                if ($reference) {
                    $billet->reference = $reference->data();
                }

                if ($instructions) {
                    $ins = [];
                    foreach ($instructions as $instruction) {
                        $ins[] = $instruction->data();
                    }

                    $billet->instructions = $ins;
                }

                $billets[] = $billet->data();
            }
        }

        $this->call(200, true)->back($billets);
    }

    public function create(): void
    {
        $data = filter_var_array((array)(json_decode(file_get_contents("php://input"))), FILTER_SANITIZE_STRIPPED);

        $billet = new Billet();
        $billet->user_id = $data['user_id'];
        $billet->payer_id = $data['payer_id'];
        $billet->due_date = $data['due_date'];
        $billet->price = $data['price'];
        $billet->description = $data['description'];

        if (!$billet->save()) {
            $this->call(500, false, $billet->message()->json())->back();
            return;
        }

        if (!empty($data['ticket_type']) && !empty($data['ticket_value'])) {
            $ticket = new BilletTicketFine();
            $ticket->billet_id = $billet->id;
            $ticket->type = $data['ticket_type'];
            $ticket->value = $data['ticket_value'];

            if (!$ticket->save()) {
                $billet->destroy();

                $this->call(500, false, $ticket->message()->json())->back();
                return;
            }

            $billet->ticket = $ticket->data();
        }

        if (!empty($data['fee_type']) && !empty($data['fee_value'])) {
            $fee = new BilletFee();
            $fee->billet_id = $billet->id;
            $fee->type = $data['fee_type'];
            $fee->value = $data['fee_value'];

            if (!$fee->save()) {
                $billet->destroy();

                $this->call(500, false, $fee->message()->json())->back();
                return;
            }

            $billet->fee = $fee->data();
        }

        if (!empty($data['discount_type']) && !empty($data['discount_value']) && !empty($data['discount_deadline'])) {
            $discount = new BilletDiscounts();
            $discount->billet_id = $billet->id;
            $discount->type = $data['discount_type'];
            $discount->value = $data['discount_value'];
            $discount->deadline_date = $data['discount_deadline'];

            if (!$discount->save()) {
                $billet->destroy();

                $this->call(500, false, $discount->message()->json())->back();
                return;
            }

            $billet->discount = $discount->data();
        }

        if ($data['instructions']) {
            $instructions = [];
            foreach ($data['instructions'] as $instruction) {
               $newInstruction = new BilletInstruction();
               $newInstruction->billet_id = $billet->id;
               $newInstruction->instruction = $instruction;

               $newInstruction->save();

               $instructions[] = $instruction;
            }

            $billet->instructions = $instructions;
        }

        if (!empty($data['reference'])) {
            $reference = new BilletReference();
            $reference->billet_id = $billet->id;
            $reference->reference = $data['reference'];

            if (!$reference->save()) {
                $billet->destroy();

                $this->call(500, false, $reference->message()->json())->back();
                return;
            }

            $billet->reference = $reference->data();
        }

        $this->call(201, true)->back($billet->data());
    }

    public function update()
    {
        $data = filter_var_array((array)(json_decode(file_get_contents("php://input"))), FILTER_SANITIZE_STRIPPED);

        $billet = (new Billet())->findById($data['billet_id']);

        $billet->due_date = $data['due_date'] ?? $billet->due_date;
        $billet->price = $data['price'] ?? $billet->price;
        $billet->description = $data['description'] ?? $billet->description;

        if (!$billet->save()) {
            $this->call(500, false, $billet->message()->json())->back();
            return;
        }

        $ticket = (new BilletTicketFine())->find('billet_id = :id', "id={$billet->id}")->fetch();
        $ticket->type = $data['ticket_type'] ?? $ticket->type;
        $ticket->value = $data['ticket_value'] ?? $ticket->value;

        if (!$ticket->save()) {
            $this->call(500, false, $ticket->message()->json())->back();
            return;
        }

        $billet->ticket = $ticket->data();

        $fee = (new BilletFee())->find('billet_id = :id', "id={$billet->id}")->fetch();
        $fee->type = $data['fee_type'] ?? $fee->type;
        $fee->value = $data['fee_value'] ?? $fee->value;

        if (!$fee->save()) {
            $this->call(500, false, $fee->message()->json())->back();
            return;
        }

        $billet->fee = $fee->data();

        $discount = (new BilletDiscounts())->find('billet_id = :id', "id={$billet->id}")->fetch();

        $discount->type = $data['discount_type'] ?? $discount->type;
        $discount->value = $data['discount_value'] ?? $discount->value;
        $discount->deadline_date = $data['discount_deadline'] ?? $discount->deadline_date;

        if (!$discount->save()) {
            $this->call(500, false, $discount->message()->json())->back();
            return;
        }

        $billet->discount = $discount->data();

        if ($data['instructions']) {
            $instructions = [];

            (new BilletInstruction())->delete('billet_id', $billet->id);

            foreach ($data['instructions'] as $instruction) {
                $newInstruction = new BilletInstruction();
                $newInstruction->billet_id = $billet->id;
                $newInstruction->instruction = $instruction;

                $newInstruction->save();

                $instructions[] = $instruction;
            }

            $billet->instructions = $instructions;
        }

        $reference = (new BilletReference())->find('billet_id = :id', "id={$billet->id}")->fetch();
        $reference->reference = $data['reference'] ?? $reference->reference;

        if (!$reference->save()) {
            $this->call(500, false, $reference->message()->json())->back();
            return;
        }

        $billet->reference = $reference->data();

        $this->call(200, true)->back($billet->data());
    }

    public function delete(array $data): void
    {
        if (!$billetId = filter_var($data['billet_id'], FILTER_VALIDATE_INT)) {
            $this->call(400, false, 'O id do boleto deve ser um número')->back();
            return;
        }

        $billet = (new Billet())->findById($billetId);

        if (!$billet) {
            $this->call(500, false, 'Boleto não encontrado')->back();
            return;
        }

        (new BilletInstruction())->delete('billet_id', $billetId);
        (new BilletTicketFine())->delete('billet_id', $billetId);
        (new BilletReference())->delete('billet_id', $billetId);
        (new BilletFee())->delete('billet_id', $billetId);
        (new BilletDiscounts())->delete('billet_id', $billetId);

        if (!$billet->destroy()) {
            $this->call(500, false, 'Houve um erro ao eliminar o boleto')->back();
            return;
        }
        $this->call(200, true, 'Boleto eliminado com sucesso')->back();
    }
}