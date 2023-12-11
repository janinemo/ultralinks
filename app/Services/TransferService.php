<?php

namespace App\Services;

use App\Models\Transfer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

class TransferService
{

    public static function findTransferByCod(string $transferCod)
    {
        $user = Auth::user();

        if (!$user) {
            throw new UnauthorizedHttpException('Usuário não autenticado.', code:401);
        }

        $transfer = Transfer::where(["cod" => $transferCod, "payer_id" => $user->id])->get()->first();

        if(!$transfer) {
            throw new NotFoundResourceException("Transferência com o código {$transferCod} não encontrado", 404);
        }

        return $transfer;
    }

    public static function findTransferById(string $transferId)
    {
        $user = Auth::user();

        if (!$user) {
            throw new UnauthorizedHttpException('Usuário não autenticado.', code:401);
        }

        $transfer = Transfer::where(["payer_id" => $user->id])->find($transferId);

        if(!$transfer) {
            throw new NotFoundResourceException("Transferência com o id {$transferId} não encontrado", 404);
        }

        return $transfer;
    }

    public static function getAllTransfers()
    {
        $user = Auth::user();

        if (!$user) {
            throw new UnauthorizedHttpException('Usuário não autenticado.', code:401);
        }

        $transfers = Transfer::where(["payer_id" => $user->id])->paginate(5);
        return $transfers;
    }

    public static function makeTransfer(Request $request)
    {
        $payer = Auth::user();
        $payeeDocument = $request["payee"];
        $amount = $request["amount"];

        if (!$payer) {
            throw new UnauthorizedHttpException('Usuário não autenticado.', code:401);
        }

        if($amount <= 0) {
            throw new UnprocessableEntityHttpException("O valor da transferência precisa ser maior que 0", code: 422);
        }

        // Payee e Payer são diferentes
        if($payeeDocument === $payer->document) {
            throw new UnprocessableEntityHttpException("Você não pode transferir valores para você mesmo", code: 422);
        }

        // Payee Existe?

        $payee = User::where(["document" => $payeeDocument])->get()->first();

        if(!$payee) {
            throw new NotFoundResourceException("Usuário com documento {$payeeDocument} não encontrado", 404);
        }

        // Tem saldo suficiente

        $hasBalanceAvailable = $payer->wallet->balance >= $amount;
        if(!$hasBalanceAvailable) {
            throw new BadRequestHttpException("O saldo não é suficiente para realizar a transferência", code: 400);
        }

        $payload = [
            "id" => Uuid::uuid4()->toString(),
            "amount" => $amount,
            "payee_id" => $payee["id"],
            "payer_id" => $payer["id"]
        ];

        DB::beginTransaction();
        try {
            $model = Transfer::create($payload);

            $payer->wallet->balance -= $amount;
            $payer->wallet->save();

            $payee->wallet->balance += $amount;
            $payee->wallet->save();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw new Exception("Erro inesperado ao realizar transferência: {$exception->getMessage()}", 500);
        } finally {
            DB::commit();
        }

        return $model;
    }
}
