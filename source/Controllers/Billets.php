<?php

namespace Source\Controllers;

use Source\Core\Auth;
use Source\Models\Billet\Billet;
use Source\Models\Billet\BilletDiscounts;
use Source\Models\Billet\BilletFee;
use Source\Models\Billet\BilletInstruction;
use Source\Models\Billet\BilletReference;
use Source\Models\Billet\BilletTicketFine;

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
}