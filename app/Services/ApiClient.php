<?php

namespace App\Services;

use App\DTO\ReservationParams;
use Exception;
use Illuminate\Support\Facades\Http;

class ApiClient
{
    protected ?int $operatorId;

    protected string $cookies;
    protected string $baseUrl                = 'http://online.catttour.com/Sednaapi/';
    protected string $loginEndpoint          = 'api/Integratiion/AgencyLogin';
    protected string $hotelsEndpoint         = 'api/Integratiion/GetHotellist';
    protected string $regionEndpoint         = 'api/Integratiion/GetRegionlist';
    protected string $mainRegionMainEndpoint = 'api/Integratiion/GetMainRegions';
    protected string $subRegionEndpoint      = 'api/Integratiion/GetSubRegions';
    protected string $reservationsEndpoint   = 'api/Integratiion/GetReservations';
    protected string $roomTypesEndpoint      = 'api/Integratiion/GetRoomTypeList';
    protected string $currencyListEndpoint      = 'api/Integratiion/GetCurrencyList';
    protected string $priceSearchEndpoint      = 'api/Integratiion/HotelPriceSearch';
    protected string $getContractListsEndpoint      = 'api/Integratiion/GetContractList';
    protected string $getPacketsEndpoint      = 'api/Integratiion/GetPackets';

    /**
     * @return int
     * @throws Exception
     */
    public function login(): int
    {
        $response = Http::get($this->baseUrl . $this->loginEndpoint, [
            'username' => env('API_USERNAME'),
            'password' => env('API_PASSWORD'),
        ]);

        if ($response->successful()) {
            $this->operatorId = $response->json('RecId');
            $this->cookies = $response->header('Set-Cookie');
            return $this->operatorId;
        }

        throw new Exception('Failed to authenticate: ' . $response->body());
    }

    /**
     * @throws Exception
     */
    protected function verifyAuth(): void
    {
        if (!$this->operatorId || empty($this->cookies)) {
            throw new Exception('Please login first to set Operator ID and cookies.');
        }
    }

    /**
     * @param string $endpoint
     * @param array  $getparams
     * @param array  $postparams
     * @param string $method
     *
     * @return array
     * @throws Exception
     */
    protected function sendRequest(string $endpoint, array $getparams = [], array $postparams = [], string $method = 'post'): array
    {
        $this->verifyAuth();

        $url = $this->baseUrl . $endpoint . (empty($getparams) ? '' : '?' . http_build_query($getparams));

        $response = Http::withHeaders([
            'Cookie' => $this->cookies,
        ])->{$method}($url, $postparams);

        if ($response->successful()) {
            return $response->json();
        }

        throw new Exception(sprintf(
            'Request to %s failed with status %d: %s',
            $endpoint,
            $response->status(),
            $response->body()
        ));
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getActiveHotels(): array
    {
        return $this->sendRequest($this->hotelsEndpoint, ['operatorId' => $this->operatorId, 'isActive' => 'true']);
    }


    /**
     * @return array
     * @throws Exception
     */
    public function getRoomTypes(): array
    {
        return $this->sendRequest($this->roomTypesEndpoint, ['operatorId' => $this->operatorId]);
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getAllHotels(): array
    {
        return $this->sendRequest($this->hotelsEndpoint, ['operatorId' => $this->operatorId, 'isActive' => 'false']);
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getRegions(): array
    {
        return $this->sendRequest($this->regionEndpoint, ['operatorId' => $this->operatorId]);
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getMainRegions(): array
    {
        return $this->sendRequest($this->mainRegionMainEndpoint, ['operatorId' => $this->operatorId]);
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getSubRegions(): array
    {
        return $this->sendRequest($this->subRegionEndpoint, ['operatorId' => $this->operatorId]);
    }

    /**
     * @param ReservationParams $params
     *
     * @return array
     * @throws Exception
     */
    public function getReservations(ReservationParams $params): array
    {
        return $this->sendRequest($this->reservationsEndpoint, [], $params->toArray());
    }

    public function getCurrencyList(): array
    {
        return $this->sendRequest($this->currencyListEndpoint, [], []);
    }

    public function priceSearch(array $params): array
    {
        return $this->sendRequest($this->priceSearchEndpoint, [], $params);
    }

    public function getContracts(array $params): array
    {
        return $this->sendRequest($this->getContractListsEndpoint, [], $params);
    }

    public function getPackets(array $params): array
    {
        return $this->sendRequest($this->getContractListsEndpoint, [], $params);
    }

    /**
     * @return int|null
     */
    public function getOperatorId(): ?int
    {
        return $this->operatorId;
    }

}
