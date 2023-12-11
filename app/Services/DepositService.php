<?php

namespace App\Services;

use App\Models\Deposit;
use App\Models\User;
use Illuminate\Contracts\Queue\EntityNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

class DepositService
{

    public static function findDepositByCod(string $depositCod)
    {
        $user = Auth::user();

        if (!$user) {
            throw new UnauthorizedHttpException('Usuário não autenticado.', code:401);
        }

        $deposit = Deposit::where(["cod" => $depositCod, "recipient_id" => $user->id])->get()->first();

        if(!$deposit) {
            throw new NotFoundResourceException("Deposito com o código {$depositCod} não encontrado", 404);
        }

        return $deposit;
    }

    public static function findDepositById(string $depositId)
    {
        $user = Auth::user();

        if (!$user) {
            throw new UnauthorizedHttpException('Usuário não autenticado.', code:401);
        }

        $deposit = Deposit::where(["recipient_id" => $user->id])->find($depositId);

        if(!$deposit) {
            throw new NotFoundResourceException("Deposito com o id {$depositId} não encontrado", 404);
        }

        return $deposit;
    }

    public static function getAllDeposits()
    {
        $user = Auth::user();

        if (!$user) {
            throw new UnauthorizedHttpException('Usuário não autenticado.', code:401);
        }

        $deposits = Deposit::where(["recipient_id" => $user->id])->paginate(5);
        return $deposits;
    }

    public static function makeDeposit(Request $request)
    {

        $document = $request["recipient"];
        $amount = $request["amount"];

        if($amount <= 0) {
            throw new UnprocessableEntityHttpException("O valor do depósito precisa ser maior que 0", code: 422);
        }

        $user = User::where(["document" => $document])->get()->first();

        if(!$user) {
            throw new NotFoundResourceException("usuário com documento {$document} não encontrado", 404);
        }

        $payload = [
            "id" => Uuid::uuid4()->toString(),
            "amount" => $amount,
            "recipient_id" => $user["id"]
        ];

        $model = Deposit::create($payload);
        $user->wallet->balance += $amount;
        $user->wallet->save();

        return $model;

    }
}
