<?php

namespace Tokenly\SubstationDistributionClient;

use Tokenly\CryptoQuantity\CryptoQuantity;
use Tokenly\SubstationClient\SubstationClient;

/**
 * Class SubstationDistributionClient
 */
class SubstationDistributionClient
{

    protected $substation_client = null;

    public function __construct(SubstationClient $substation_client)
    {
        $this->substation_client = $substation_client;
    }

    public function getSubstationClient()
    {
        return $this->substation_client;
    }

    public function buildPrimeSendDestinations($wallet_uuid, $address_uuid, CryptoQuantity $prime_quantity, $desired_count)
    {
        $txo_info = $this->loadTXOInfoFromSubstation($wallet_uuid, $address_uuid, $prime_quantity);

        $primes_to_create_count = $desired_count - count($txo_info['primes']);
        if ($primes_to_create_count <= 0) {
            // no primes
            return [];
        }

        // load the address hash
        $address_info = $this->substation_client->getAddressById($wallet_uuid, $address_uuid);
        $address = $address_info['address'];

        // create a destination for each prime
        $destinations = [];
        for ($i = 0; $i < $primes_to_create_count; $i++) {
            $destinations[] = [
                'address' => $address,
                'quantity' => clone $prime_quantity,
            ];
        }
        return $destinations;
    }

    public function loadTXOInfoFromSubstation($wallet_uuid, $address_uuid, CryptoQuantity $prime_quantity)
    {
        $txos_response = $this->substation_client->getTXOsById($wallet_uuid, $address_uuid);

        $prime_quantity_string = $prime_quantity->getSatoshisString();

        $unspent_non_prime_quantity = CryptoQuantity::fromSatoshis(0);

        $primes = [];
        foreach ($txos_response['items'] as $txo) {
            $is_prime = false;
            if (!$txo['spent'] and (string) $txo['amount'] === $prime_quantity_string) {
                $is_prime = true;
                $primes[] = $txo;
            }

            if (!$txo['spent'] and !$is_prime) {
                $unspent_non_prime_quantity = $unspent_non_prime_quantity->add(CryptoQuantity::fromSatoshis($txo['amount']));
            }
        }

        return [
            'primes' => $primes,
            'unspent_quantity' => $unspent_non_prime_quantity,
        ];
    }

}
