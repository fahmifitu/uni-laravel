<?php

namespace fahmifitu\UniLaravel;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class Uni
{
	private $client;

	public function __construct()
	{
		$this->client = new Client([
			'base_uri' => config('uni.url'),
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ],
            'http_errors' => false,
		]);
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
		try {
			$res = $this->client->request('POST', '/TopUpRequest', [
				'json' => $data
			]);
			return response()->json([
				'code' => $res->getStatusCode(),
				'message' => $res->getReasonPhrase()
			]);
		} catch (GuzzleException $e) {
			return response()->json([
				'error' => $e->getMessage()
			], 500);
		}
	}

	public function sendPincode(string $email, string $password, string $voucherNumber)
	{
		$data = [
			'userId' => $email,
			'userPassword' => $password,
			'voucherNumber' => $voucherNumber
		];
		try {
			$res = $this->client->request('POST', '/sendSecret', [
				'json' => $data
			]);
			return response()->json([
				'code' => $res->getStatusCode(),
				'message' => $res->getReasonPhrase()
			]);
		} catch (GuzzleException $e) {
			return response()->json([
				'error' => $e->getMessage()
			], 500);
		}
	}

}
