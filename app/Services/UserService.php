<?php

namespace App\Services;

use App\Models\Address;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Mockery\Exception;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\Translation\Exception\InvalidResourceException;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

class UserService
{

    public static function findUserById(string $userId)
    {
        $user = User::find($userId);

        if(!$user) {
            throw new NotFoundResourceException("Usuário com o id {$userId} não encontrado", 404);
        }

        return $user->load(["wallet", "address"]);
    }

    public static function getAllUsers()
    {
        $users = User::with(["address", "wallet"])->paginate();
        return $users;
    }

    public static function createUser($payload)
    {
        $userDocument = $payload["document"];
        $userEmail = $payload["email"];
        $userAddress = $payload["address"];

        $user = User::where(["document" => $userDocument])
                    ->orWhere(["email" => $userEmail])
                    ->first();

        if($user) {
            throw new ConflictHttpException("Já existe um usuário com esse " . ($userEmail === $user->email ? 'email' : 'documento'), code: 409);
        }

        // Busco dados da API externa
        $cepResponse = Http::get("https://viacep.com.br/ws/{$userAddress["zipCode"]}/json")->json();

        $isError = $cepResponse["erro"] ?? false;
        if($isError) {
            throw new InvalidResourceException("O zip_code informado não existe", 400);
        }

        $addressPayload = [
            "id" => Uuid::uuid4()->toString(),
            "zip_code" => $userAddress["zipCode"],
            "complement" => $userAddress["complement"],
            "number" => $userAddress["number"],

            "street" => $cepResponse["logradouro"],
            "neighborhood" => $cepResponse["bairro"],
            "city" => $cepResponse["localidade"],
            "uf" => $cepResponse["uf"],
            "is_complete" => $cepResponse["logradouro"] && $cepResponse["bairro"],
        ];

        $payload = array_merge($payload, [
            "id" => Uuid::uuid4()->toString(),
            "password" => Hash::make($payload["password"]),
        ]);

        DB::beginTransaction();
        try {

            $user = User::create(Arr::only($payload, ["id", "document", "email", "birthdate", "fullname", "password"]));
            $user->address()->create($addressPayload);
            $user->wallet()->create(["id" => Uuid::uuid4()->toString()]);
        } catch (\Exception $exception) {
            DB::rollBack();
            throw new Exception("Erro inesperado ao criar usuário: {$exception->getMessage()}", 500);
        } finally {
            DB::commit();
        }

        return $user->load(["wallet", "address"]);
    }

    public static function findUserByDocument(string $userDocument)
    {
        $user = User::where(["document" => $userDocument])->first();

        if(!$user) {
            throw new NotFoundResourceException("Usuário com o documento {$userDocument} não encontrado", 404);
        }

        return $user->load(["wallet", "address"]);
    }
}
