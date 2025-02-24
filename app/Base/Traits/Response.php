<?php

namespace App\Base\Traits;

use App\Exceptions\AuthenticationFailedException;
use App\Exceptions\UserNotFoundException;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response as ResponseCode;

trait Response {

    /**
     * @param mixed $data
     * @param string|null $message
     * @param int|null $status_code
     * @return JsonResponse
     */
    public static function successResponse(
        mixed       $data = null,
        string|null $message = 'Requisição processada com sucesso.',
        int|null    $status_code = ResponseCode::HTTP_OK
    ): JsonResponse {
        $response = self::defineResponseData(
            response: self::defineResponseBase(success: true, message: $message),
            data_error: false,
            data: $data
        );

        return response()->json($response, $status_code);
    }

    /**
     * @param mixed|null $data
     * @param string|null $message
     * @param int|null $status_code
     * @return JsonResponse
     */
    public static function successResponseJson(
        mixed       $data = null,
        string|null $message = 'Requisição processada com sucesso.',
        int|null    $status_code = ResponseCode::HTTP_OK
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $status_code);
    }

    /**
     * @param Exception $exception
     * @param string|null $message
     * @return JsonResponse
     */
    public static function internalServerErrorResponse(
        Exception   $exception,
        string|null $message = 'Não foi possível processar a requisição. Tente novamente mais tarde.',
    ): JsonResponse {
        $response = self::defineResponseData(
            response: self::defineResponseBase(success: false, message: $message),
            data_error: true,
            data: $exception->getMessage()
        );

        return response()->json($response, ResponseCode::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * @param mixed $exception
     * @return ResponseCode|JsonResponse
     */
    public static function returnError(
        mixed       $exception,
    ): ResponseCode|JsonResponse {
        if ($exception instanceof HttpResponseException) {
            return self::httpResponseException($exception);
        }

        if ($exception instanceof QueryException) {
            return self::queryExceptionResponse($exception, $exception->getMessage());
        }

        if ($exception instanceof ModelNotFoundException) {
            return self::modelNotFoundResponse($exception, $exception->getMessage());
        }

        if ($exception instanceof InvalidArgumentException) {
            return self::invalidArgumentResponse($exception, $exception->getMessage());
        }

        if ($exception instanceof UserNotFoundException) {
            return self::userNotFoundExceptionResponse($exception, $exception->getMessage());
        }

        if ($exception instanceof AuthenticationFailedException) {
            return self::authenticationFailedExceptionResponse($exception, $exception->getMessage());
        }

        return self::internalServerErrorResponse($exception);
    }

    /**
     * @param QueryException $queryException
     * @param string|null $message
     * @return JsonResponse
     */
    public static function queryExceptionResponse(
        QueryException $queryException,
        string|null    $message = 'Não foi possível processar a requisição. Tente novamente mais tarde.',
    ): JsonResponse {
        $response = self::defineResponseData(
            response: self::defineResponseBase(success: false, message: self::defineQueryMessage($queryException->getMessage())),
            data_error: true,
            data: self::defineQueryMessage($queryException->getMessage())
        );

        return response()->json($response, $status_code ?? ResponseCode::HTTP_BAD_REQUEST);
    }

    /**
     * @param UserNotFoundException $userNotFoundException
     * @param string|null $message
     * @param int|null $status_code
     * @return JsonResponse
     */
    public static function userNotFoundExceptionResponse(
        UserNotFoundException $userNotFoundException,
        string|null    $message = 'Por favor verifique os campos preenchidos.',
        int|null     $status_code = ResponseCode::HTTP_NOT_FOUND
    ): JsonResponse {
        $response = self::defineResponseData(
            response: self::defineResponseBase(success: false, message: $message),
            data_error: true,
            data: self::defineQueryMessage($userNotFoundException->getMessage())
        );

        return response()->json($response, $status_code);
    }

    /**
     * @param AuthenticationFailedException $authenticationFailedException
     * @param string|null $message
     * @param int|null $status_code
     * @return JsonResponse
     */
    public static function authenticationFailedExceptionResponse(
        AuthenticationFailedException $authenticationFailedException,
        string|null    $message = 'Por favor verifique os campos preenchidos.',
        int|null     $status_code = ResponseCode::HTTP_UNAUTHORIZED
    ): JsonResponse {
        $response = self::defineResponseData(
            response: self::defineResponseBase(success: false, message: $message),
            data_error: true,
            data: self::defineQueryMessage($authenticationFailedException->getMessage())
        );

        return response()->json($response, $status_code);
    }

    /**
     * @param string|null $message
     * @return string
     */
    public static function defineQueryMessage(string|null $message): string {
        if (!$message) {
            return 'Não foi possível processar a requisição. Tente novamente mais tarde.';
        }

        if (preg_match('/ERROR:\s+(.*?)\s+CONTEXT:/s', $message, $matches)) {
            $custom_message = $matches[1];
        } else {
            $custom_message = "Erro desconhecido";
        }

        return $custom_message;
    }

    /**
     * @param ModelNotFoundException $modelNotFoundException
     * @param string|null $message
     * @return JsonResponse
     */
    public static function modelNotFoundResponse(
        ModelNotFoundException $modelNotFoundException,
        string|null            $message = null
    ): JsonResponse {
        $response = self::defineResponseData(
            response: self::defineResponseBase(success: false, message: $message ?? $modelNotFoundException->getMessage()),
            data_error: true,
            data: $modelNotFoundException->getMessage()
        );
        return response()->json($response, ResponseCode::HTTP_NOT_FOUND);
    }

    /**
     * @param InvalidArgumentException $invalidArgumentException
     * @param string|null $message
     * @return JsonResponse
     */
    public static function invalidArgumentResponse(
        InvalidArgumentException $invalidArgumentException,
        string|null              $message = null,
    ): JsonResponse {
        $response = self::defineResponseData(
            response: self::defineResponseBase(success: false, message: $message ?? $invalidArgumentException->getMessage()),
            data_error: true,
            data: $invalidArgumentException->getMessage()
        );

        return response()->json($response, ResponseCode::HTTP_BAD_REQUEST);
    }

    /**
     * @param HttpResponseException $httpResponseException
     * @return ResponseCode
     */
    public static function httpResponseException(HttpResponseException $httpResponseException): ResponseCode
    {
        return $httpResponseException->getResponse();
    }

    /**
     * @param $validator
     * @return mixed
     */
    public static function failedValidationResponse($validator): mixed {
        $response = self::defineResponseData(
            response: self::defineResponseBase(success: false, message: 'Por favor verifique os campos preenchidos.'),
            data_error: true,
            data: $validator->errors(),
            error_key: 'errors',
            validator_error: true
        );

        throw new HttpResponseException(
            response()->json($response, ResponseCode::HTTP_UNAUTHORIZED)
        );
    }

    /**
     * @param array $response
     * @param bool $data_error
     * @param mixed $data
     * @param string|null $error_key
     * @param bool $validator_error
     * @return array
     */
    private static function defineResponseData(
        array   $response,
        bool    $data_error,
        mixed   $data,
        ?string $error_key = 'message_error',
        bool    $validator_error = false
    ): array {
        if ($data_error && !config('params.config.show_error_message') && !$validator_error) {
            return $response;
        }

        if ($data_error) {
            $response['data'] = [
                $error_key => $data
            ];

            return $response;
        }

        if (!is_null($data)) {
            $response['data'] = $data;
        }

        return $response;
    }

    /**
     * @param bool $success
     * @param $message
     * @return array
     */
    private static function defineResponseBase(bool $success, $message): array {
        return [
            'success' => $success,
            'message' => $message
        ];
    }

    public static function notAuthorizeExceptionResponse(
        string $message = 'Não autorizado.'
    ) {
        return throw new HttpResponseException(response()->json([
            'success' => false,
            'data' => ['errors' => $message],
            'message' => $message
        ], ResponseCode::HTTP_UNAUTHORIZED));
    }

    /**
     * @param bool $success
     * @param string $message
     * @param array $errors
     * @param int $status_code
     * @return JsonResponse
     */
    public static function customValidationResponse(
        bool   $success = false,
        string $message = 'Por favor verifique os campos preenchidos.',
        array  $errors = [],
        int    $status_code = ResponseCode::HTTP_UNAUTHORIZED
    ): JsonResponse {
        $response = self::defineResponseData(
            response: self::defineResponseBase(success: $success, message: $message),
            data_error: true,
            data: $errors,
            error_key: 'errors',
            validator_error: true
        );

        throw new HttpResponseException(
            response()->json($response, $status_code)
        );
    }
}
