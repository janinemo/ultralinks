<?php

namespace App\Http\Controllers;

use App\Models\Transfer;
use App\Models\User;
use App\Rules\CpfValidation;
use App\Services\TransferService;
use Illuminate\Support\Facades\Validator;
use Mockery\Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

class TransferController extends Controller
{
    public function getTransfers(): JsonResponse
    {
        try {
            $transfers = TransferService::getAllTransfers();
        } catch (UnauthorizedHttpException $exception) {
            return response()->json(["error" => $exception->getMessage()], $exception->getCode());
        }
        return response()->json($transfers, 200);
    }

    public function getTransferById(string $transferId): JsonResponse
    {
        $validator = Validator::make(
            ['transferId' => $transferId],
            ['transferId' => 'required|uuid'],
            [
                "uuid" => "O id da transferência informado é inválido",
                "required" => "O id da transferência é obrigatório"
            ]
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 422);
        }

        try {
            $transfer = TransferService::findTransferById($transferId);
        } catch (NotFoundResourceException $exception) {
            return response()->json(["error" => $exception->getMessage()], $exception->getCode());
        } catch (UnauthorizedHttpException $exception) {
            return response()->json(["error" => $exception->getMessage()], $exception->getCode());
        }
        return response()->json($transfer, 200);
    }

    public function getTransferByCod(string $transferCod): JsonResponse
    {
        $validator = Validator::make(
            ['transferCod' => $transferCod],
            ['transferCod' => 'required|regex:/^TRANSF\d{4}$/'],
            [
                "regex" => "O código da transferência informado é inválido, deve obedecer ao formato TRANSF0000",
                "required" => "O código da transferência é obrigatório"
            ]
        );
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 422);
        }

        try {
            $transfer = TransferService::findTransferByCod($transferCod);
        } catch (NotFoundResourceException $exception) {
            return response()->json(["error" => $exception->getMessage()], $exception->getCode());
        } catch (UnauthorizedHttpException $exception) {
            return response()->json(["error" => $exception->getMessage()], $exception->getCode());
        }
        return response()->json(["data" => $transfer], 200);
    }

    public function postMakeTransfer(Request $request): JsonResponse
    {
        $payload = $request->only(["amount", "payee", "payer"]);

        $validator = Validator::make(
            $payload,
            [
                "amount" => ["required", "min:1", "numeric"],
                "payee" => ['required', 'numeric', new CpfValidation]
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
         $model = TransferService::makeTransfer($request);
        } catch (NotFoundResourceException $exception) {
            return response()->json(["error" => $exception->getMessage()], $exception->getCode());
        } catch (UnprocessableEntityHttpException $exception) {
            return response()->json(["error" => $exception->getMessage()], $exception->getCode());
        } catch (BadRequestHttpException $exception) {
            return response()->json(["error" => $exception->getMessage()], $exception->getCode());
        } catch (UnauthorizedHttpException $exception) {
            return response()->json(["error" => $exception->getMessage()], $exception->getCode());
        } catch (Exception $exception) {
            return response()->json(["error" => $exception->getMessage()], $exception->getCode());
        }

        return response()->json(["data" => $model], 201);
    }

}
