<?php

namespace fahmifitu\UniLaravel;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class Uni
{
	private $client;
	private $distEmail;
	private $distPassword;

	public function __construct()
	{
		if (! config('uni.distributor_email') )
			$this->$distEmail = config('uni.distributor_email');
        if (! config('uni.distributor_password') )
        	$this->distPassword = config('uni.distributor_password');

		$this->client = new Client([
			'base_uri' => config('uni.url'),
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ],
            'http_errors' => false,
		]);
	}
	public function generateVoucher(string $email, string $password, string $phone, float $amount, int $number = 1)
	{
		$data = [
		  	'IdDist' => $email,
		  	'DistPassword' => $password,
		  	'PhoneNumber' => $phone,
		  	'NumberOfVoucher' => $number,
		 	'AmountOfRecharges' => $amount,
			'VoucherType' => 1,
		];
		$this->request('POST', '/BuyRechargeCode', $data);
	}
	public function consumeVoucher(string $email, string $password, string $voucherNumber,string $pin, float $amount)
	{
		$data = [
			'Command' => 1,
			'RechargeCode' => $voucherNumber,
			'IdOp' => $email,
			'Password' => $password,
			'AmountToConsume' => $amount,
			'codePin' => $pin
		];
		$this->request('POST', '/TopUpRequest', $data);
	}
	public function sendPincode(string $email, string $password, string $voucherNumber)
	{
		$data = [
			'userId' => $email,
			'userPassword' => $password,
			'voucherNumber' => $voucherNumber
		];
		$this->request('POST', '/sendSecret', $data);
	}
	private function request($verb, $uri, array $payload = [])
    {
        $response = $this->client->request($verb, $uri,
            empty($payload) ? [] : ['form_params' => $payload]
        );
        if ($response->getStatusCode() != 200) {
            return $this->handleRequestError($response);
        }
        $responseBody = (string) $response->getBody();
        return json_decode($responseBody, true) ?: $responseBody;
    }

    private function handleRequestError(ResponseInterface $response)
    {
        if ($response->getStatusCode() == 422) {
            throw new ValidationException(json_decode((string) $response->getBody(), true));
        }
        if ($response->getStatusCode() == 404) {
            throw new NotFoundException();
        }
        if ($response->getStatusCode() == 400) {
            throw new FailedActionException((string) $response->getBody());
        }
        throw new \Exception((string) $response->getBody());
    }
}
