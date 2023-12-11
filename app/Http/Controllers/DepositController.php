<?php

namespace App\Http\Controllers;

use App\Models\Deposit;
use App\Models\User;
use App\Rules\CpfValidation;
use App\Services\DepositService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\MockObject\Stub\Exception;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

class DepositController extends Controller
{
    public function getDeposits(): JsonResponse
    {
        try{
            $deposits = DepositService::getAllDeposits();
        } catch (UnauthorizedHttpException $exception) {
            return response()->json(["error" => $exception->getMessage()], $exception->getCode());
        }
        return response()->json($deposits, 200);
    }

    public function getDepositById(string $depositId): JsonResponse
    {
        $validator = Validator::make(
            ['depositId' => $depositId],
            ['depositId' => 'required|uuid'],
            [
                "uuid" => "O id do depósito informado é inválido",
                "required" => "O id do depósito é obrigatório"
            ]
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 422);
        }

        try {
            $deposit = DepositService::findDepositById($depositId);
        } catch (NotFoundResourceException $exception) {
            return response()->json(["error" => $exception->getMessage()], $exception->getCode());
        } catch (UnauthorizedHttpException $exception) {
            return response()->json(["error" => $exception->getMessage()], $exception->getCode());
        }
        return response()->json($deposit, 200);
    }

    public function getDepositByCod(string $depositCod): JsonResponse
    {
        $validator = Validator::make(
            ['depositCod' => $depositCod],
            ['depositCod' => 'required|regex:/^DEP\d{4}$/'],
            [
                "regex" => "O código do depósito informado é inválido, deve obedecer ao formato DEP0000",
                "required" => "O código do depósito é obrigatório"
            ]
        );
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 422);
        }

        try {
            $deposit = DepositService::findDepositByCod($depositCod);
        } catch (NotFoundResourceException $exception) {
            return response()->json(["error" => $exception->getMessage()], $exception->getCode());
        } catch (UnauthorizedHttpException $exception) {
            return response()->json(["error" => $exception->getMessage()], $exception->getCode());
        }
        return response()->json(["data" => $deposit], 200);
    }

    public function postMakeDeposit(Request $request): JsonResponse
    {
        $payload = $request->only(["amount", "recipient"]);

        $validator = Validator::make(
            $payload,
            [
                "amount" => ["required", "min:1", "numeric"],
                "recipient" => ['required', 'numeric', new CpfValidation]
            ],
            [
                "min" => "O campo :attribute precisa ser maior que 0",
                "numeric" => "O campo :attribute informado está inválido",
                "required" => "O campo :attribute é obrigatório"
            ]
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 422);
        }

        try {
         $model = DepositService::makeDeposit($request);
        } catch (NotFoundResourceException $exception) {
            return response()->json(["error" => $exception->getMessage()], $exception->getCode());
        } catch (UnprocessableEntityHttpException $exception) {
            return response()->json(["error" => $exception->getMessage()], $exception->getCode());
        } catch (UnauthorizedHttpException $exception) {
            return response()->json(["error" => $exception->getMessage()], $exception->getCode());
        }

        return response()->json(["data" => $model], 201);
    }

}
