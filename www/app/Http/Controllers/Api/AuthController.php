<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *      path="/api/v1/auth/login",
     *      operationId="apiLogin",
     *      tags={"Autenticação API"},
     *      summary="Autentica operador por PIN",
     *      description="Gera o tokenBearer Sanctum baseado no PIN do Empregado (Cashier Number).",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"pin"},
     *              @OA\Property(property="pin", type="string", example="2222")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Acesso concedido",
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="PIN Inválido"
     *      )
     * )
     */
    public function login(Request $request)
    {
        $request->validate([
            'pin' => 'required|string',
            'device_name' => 'nullable|string'
        ]);

        $employee = \App\Modules\AccessControl\Models\Employee::where('pin', $request->pin)->first();

        if (!$employee) {
            return response()->json([
                'message' => 'PIN de Operador não reconhecido ou inválido.'
            ], 401);
        }

        if (!$employee->user_id) {
            return response()->json([
                'message' => 'Operador não possui Credencial Retaguarda atrelada ao PIN (Falta Usuário Master).'
            ], 401);
        }

        $user = User::find($employee->user_id);

        if (!$user) {
            return response()->json([
                'message' => 'Usuário Master do Operador não encontrado.'
            ], 401);
        }

        // Deleta tokens antigos do device_name específico para não inchar o DB de caixas
        $deviceName = $request->device_name ?? 'PDV_TERMINAL';
        $user->tokens()->where('name', $deviceName)->delete();

        $token = $user->createToken($deviceName)->plainTextToken;

        return response()->json([
            'token' => $token,
            'user_name' => $employee->name, // Mostra o nome real do Operador (ex: João do Caixa)
            'user_id' => $user->id,
            'branch_id' => $user->branch_id ?? 1
        ], 200);
    }
}
