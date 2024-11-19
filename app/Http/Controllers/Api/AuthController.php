<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\VerfiyMobileRequest;
use App\Http\Requests\ProfileRequest;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use App\Http\Requests\VerifyRequest;
use App\Http\Resources\UserResource;
use App\Repository\UserRepository;
use Illuminate\Http\Request;
use Exception;

class AuthController extends Controller
{
    private $headers = [
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
        'X-API-KEY' => '71c7B43va6lZ'
    ];

    /**
     * get mobile number and send otp
     *
     * @param VerfiyMobileRequest $request
     * @return void
     */
    public function verifyRequest(VerfiyMobileRequest $request)
    {
        $mobile = $request->get('mobile');
        try {
            // get or create user
            $user = UserRepository::create($mobile);

            // send request to iauth for otp code
            $response = Http::withHeaders($this->headers)
                ->post(config('services.iauth.verify_request'), [
                    'receptor' => $user->mobile,
                ]);

            $response->throw();

            $data = $response->json();

            //return success response to client
            return $this->successResponse([
                'user' => new UserResource($user),
                'expire_date' => $data['data']['expire_date'],
                'validity_period_in_seconds' => $data['data']['validity_period_in_seconds'],
            ], 200, $data['message']);


        } catch (Exception $e) {
            if ($e->getCode() == 400) {
                return $this->errorResponse(['error' => 'کد شما معتبر است. جهت دریافت کد جدید چند ثانیه صبر کنید'], 422);
            }

            return $this->errorResponse(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * check otp code is valid
     *
     * @param VerifyRequest $request
     * @return void
     */
    public function verify(VerifyRequest $request)
    {
        $mobile = $request->get('mobile');
        $otpCode = $request->get('otp_code');

        try {
            $user = UserRepository::getUserByMobile($mobile);

            abort_unless($user, 404);

            $response = Http::withHeaders($this->headers)->post(config('services.iauth.verify'), [
                'receptor' => $user->mobile,
                'otp_code' => $otpCode
            ]);

            $response->throw();

            $data = $response->json();

            if (isset($data['data']['profile'])) {
                if (isset($data['data']['profile']['first_name'])) {
                    $user->first_name = $data['data']['profile']['first_name'];
                }
                if (isset($data['data']['profile']['last_name'])) {
                    $user->last_name = $data['data']['profile']['last_name'];
                }
            }

            $user->at_login_last = date('Y-m-d H:i:s');
            $user->save();
            
            return $this->successResponse([
                'user' => new UserResource($user),
                'api_token' => $user->createToken('myApp', ['user'])->plainTextToken,
                'profile' => $data['data']['profile'],
                'account_status' => $data['data']['account_status'],
            ], 200, $data['message']);


        } catch (Exception $e) {
            return $this->errorResponse(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * update user profile
     *
     * @param ProfileRequest $request
     * @return void
     */
    public function update(ProfileRequest $request)
    {
        try {
            $user = $request->user();

            UserRepository::update($request, $user);

            return $this->successResponse([
                'user' => new UserResource($user->refresh()),
            ], 200, 'به روزرسانی پروفایل کاربری انجام شد');
        } catch (Exception $e) {
            return $this->errorResponse(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * get authenticated user
     *
     * @param Request $request
     * @return void
     */
    public function profile(Request $request)
    {
        return $this->successResponse([
            'user' => new UserResource($request->user())
        ]);
    }

    // logout form account
    public function logout()
    {
        auth()->user()->tokens()->delete();

        return $this->successResponse([], 200, 'خروج با موفقیت انجام شد');
    }
}
