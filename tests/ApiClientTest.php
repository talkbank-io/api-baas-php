<?php

namespace tests;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Response;
use function GuzzleHttp\Psr7\stream_for;
use PHPUnit\Framework\TestCase;
use TalkBank\ApiBaaS\Client;

class ApiClientTest extends TestCase
{
    /**
     * @dataProvider clientDataProvider
     * @param array $data
     */
    public function testClient(array $data)
    {
        $client = $this->getClient();

        $method = $data['method'];

        $mockHttpClient = $data['mock']['httpClient'] ?? null;
        if ($mockHttpClient) {
            $this->configureHttpClient($client, $mockHttpClient);
        }

        $result = $client->$method(...$data['arguments']);
        $this->checkResult($result, $data['result']);
    }

    /**
     * @return array
     */
    public function clientDataProvider(): array
    {
        return json_decode(file_get_contents(__DIR__ . '/data/common.json'), true);
    }

    /**
     * @param mixed $actualResult
     * @param mixed $resultData
     */
    private function checkResult($actualResult, $resultData)
    {
        if (array_key_exists('expected', $resultData)) {
            $this->assertEquals($resultData['expected'], $actualResult);
        } else {
            $this->markTestIncomplete('Result not checked');
        }
    }

    /**
     * @param Client|\PHPUnit_Framework_MockObject_MockObject $client
     * @param array $mockHttpClientData
     */
    private function configureHttpClient($client, array $mockHttpClientData)
    {
        $httpResponse = $this->createHttpResponse(
            $mockHttpClientData['response']['code'],
            $mockHttpClientData['response']['data']
        );
        $httpClient = $this->createHttpClient();
        $httpClient->expects($this->once())->method('request')->willReturn($httpResponse);

        $reflectionClient = new \ReflectionClass($client);
        $prop = $reflectionClient->getProperty('guzzle');
        $prop->setAccessible(true);
        $prop->setValue($client, $httpClient);
    }

    /**
     * @return GuzzleClient|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createHttpClient()
    {
        return $this->createPartialMock(GuzzleClient::class, ['request']);
    }

    /**
     * @param int $code
     * @param array $data
     * @return Response
     */
    private function createHttpResponse(int $code, array $data): Response
    {
        $response = new Response(
            $code,
            ['Content-Type' => 'application/json'],
            stream_for(json_encode($data))
        );

        return $response;
    }

    /**
     * @return Client|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getClient()
    {
        return new Client('', '', '');
    }
}

