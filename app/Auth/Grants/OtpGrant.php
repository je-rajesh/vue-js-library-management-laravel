<?php

namespace App\Auth\Grants;

use RuntimeException;
use Illuminate\Http\Request;
use App\Exceptions\OtpException;
use Laravel\Passport\Bridge\User;
use League\OAuth2\Server\RequestEvent;
use App\Auth\Grants\OtpVerifierFactory;
use App\Otp;
use Illuminate\Support\Facades\DB;
use Psr\Http\Message\ServerRequestInterface;
use League\OAuth2\Server\Grant\AbstractGrant;
use League\OAuth2\Server\Entities\UserEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\ResponseTypes\ResponseTypeInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
// use OtpVerifierFactory;

class OtpGrant extends AbstractGrant
{
    /**
     * @param RefreshTokenRepositoryInterface $refreshTokenRepository
     */
    public function __construct(
        RefreshTokenRepositoryInterface $refreshTokenRepository
    ) {
        $this->setRefreshTokenRepository($refreshTokenRepository);

        $this->refreshTokenTTL = new \DateInterval('P1M');
    }

    /**
     * {@inheritdoc}
     */
    public function respondToAccessTokenRequest(
        ServerRequestInterface $request,
        ResponseTypeInterface $responseType,
        \DateInterval $accessTokenTTL
    ) {
        // Validate request
        $client = $this->validateClient($request);
        $scopes = $this->validateScopes($this->getRequestParameter('scope', $request));
        $user = $this->validateUser($request, $client);

        // Finalize the requested scopes
        $scopes = $this->scopeRepository->finalizeScopes($scopes, $this->getIdentifier(), $client, $user->getIdentifier());

        // Issue and persist new tokens
        $accessToken = $this->issueAccessToken($accessTokenTTL, $client, $user->getIdentifier(), $scopes);
        $refreshToken = $this->issueRefreshToken($accessToken);

        // Inject tokens into response
        $responseType->setAccessToken($accessToken);
        $responseType->setRefreshToken($refreshToken);

        return $responseType;
    }

    protected function validateUser(ServerRequestInterface $request, ClientEntityInterface $client)
    {
        $otp = $this->getRequestParameter('otp', $request);
        if (is_null($otp)) {
            throw OAuthServerException::invalidRequest('otp');
        }

        // $otpVerifierParam = $this->getRequestParameter('otp_verifier', $request);

        // $otpVerifier = OtpVerifierFactory::getOtpVerifier(
        //     $this->getRequestParameter('otp_verifier', $request, 'BL_INTERNAL')
        // );

        // if (is_null($otpVerifier)) {
        //     throw OtpException::invalidOtpVerifier();
        // }

        // $isValidOtp = $otpVerifier->verify($otp);

        // if (!$isValidOtp) {
        //     throw OtpException::invalidOtp();
        // }

        $phone_no = $this->getRequestParameter('phone_no', $request);
        if (is_null($phone_no)) {
            throw OAuthServerException::invalidRequest('phone_no');
        }


        $session_otp = Otp::where('phone_no', $phone_no)->get()->first()->otp;
        

        if($otp != $session_otp)
        {
            throw OAuthServerException::invalidRequest('otp');
        }


        $user = $this->getUserEntityByUserOtp(
            $phone_no,
            $this->getIdentifier(),
            $client
        );

        if ($user instanceof UserEntityInterface === false) {
            $this->getEmitter()->emit(new RequestEvent(RequestEvent::USER_AUTHENTICATION_FAILED, $request));

            throw OAuthServerException::invalidCredentials();
        }

        return $user;
    }

    private function getUserEntityByUserOtp($phone_no, $grantType, ClientEntityInterface $clientEntity)
    {
        $provider = config('auth.guards.api.provider');

        if (is_null($model = config('auth.providers.' . $provider . '.model'))) {
            throw new RuntimeException('Unable to determine authentication model from configuration.');
        }

        $user = (new $model)->where('phone_no', $phone_no)->first();

        if (is_null($user)) {
            return;
        }

        return new User($user->getAuthIdentifier());
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return 'otp_grant';
    }
}
