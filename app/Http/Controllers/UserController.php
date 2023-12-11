<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Rules\CpfValidation;
use App\Services\UserService;
use App\Utils\Cpf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Mockery\Exception;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\Translation\Exception\InvalidResourceException;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

class UserController extends Controller
{
    public function getUsers(): JsonResponse
    {
        $users = UserService::getAllUsers();


        return response()->json($users, 200);
    }

    public function getUserById(string $userId): JsonResponse
    {

        $validator = Validator::make(
            ['userId' => $userId],
            ['userId' => 'required|string|uuid'],
            [
                "uuid" => "O uuid informado não é válido",
                "required" => "O id do usuário é obrigatório"
            ]
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 422);
        }

        try {
            $user = UserService::findUserById($userId);
        } catch (NotFoundResourceException $exception) {
            return response()->json(["error" => $exception->getMessage()], $exception->getCode());
        }

        return response()->json(["data" => $user], 200);

    }

    public function getUserByDocument(string $userDocument): JsonResponse
    {
        $validator = Validator::make(
            ['cpf' => $userDocument],
            ['cpf' => ['required', 'numeric', new CpfValidation] ],
            [
                "numeric" => "O documento informado está inválido",
                "required" => "O documento do usuário é obrigatório"
            ]
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 422);
        }

        try {
            $user = UserService::findUserByDocument($userDocument);
        } catch (NotFoundResourceException $exception) {
            return response()->json(["error" => $exception->getMessage()], $exception->getCode());
        }

        return response()->json(["data" => $user], 200);

    }

    public function createUser(Request $request): JsonResponse
    {
        $payload = $request->only(["fullname", "birthdate", "document", "email", "password", "address"]);

        $validator = Validator::make(
            $payload,
            [
                "fullname" => ['required', 'min:5', 'max:50'],
                "birthdate" => ['required', 'date', 'date_format:Y-m-d'],
                "document" => ['required', 'numeric', new CpfValidation],
                "email" => ['email', 'string'],
                "password" => ['string', 'min:8'],

                "address" => ['required'],
                "address.zipCode" => ['required', 'digits:8', 'numeric'],
                "address.number" => ['required', 'numeric', 'min_digits:1'],
                "address.complement" => ['max:40'],
            ],
            [
                "digits" => "O campo :attribute precisa ter :digits dígitos",
                "min_digits" => "O campo :attribute precisa ser ao menos :min",
                "numeric" => "O campo :attribute informado está inválido",
                "required" => "O campo :attribute é obrigatório",
                "min" => "O campo :attribute precisa ter ao menos :min dígitos",
                "max" => "O campo :attribute precisa ter no máximo :max dígitos",
                "email" => "email informado inválido",
                "date" => "O campo :attribute com valor de data inválido",
                "date_format" => "A data informada no campo :attribute é inválida",
            ]
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 422);
        }

        try {
            $model = UserService::createUser($payload);
        } catch (ConflictHttpException $exception) {
            return response()->json(["error" => $exception->getMessage()], $exception->getCode());
        } catch (InvalidResourceException $exception) {
            return response()->json(["error" => $exception->getMessage()], $exception->getCode());
        } catch (Exception $exception) {
            return response()->json(["error" => $exception->getMessage()], $exception->getCode());
        }

        return response()->json(["data" => $model], 201);
    }

}
