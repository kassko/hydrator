<?php

namespace Kassko\ObjectHydratorTest\Integration;

use Kassko\ObjectHydrator\{Annotation\Doctrine as BHY, HydratorBuilder};
use Kassko\ObjectHydratorTest\Integration\Fixture\Model\Address\AddressSimple;
use PHPUnit\Framework\TestCase;

class _020_HydrateProperty_RawDataLocation_Prefix_Test extends TestCase
{
    /**
     * @test
     */
    public function letsGo()
    {
        $rawData = [
            'first_name' => 'Dany',
            'last_name' => 'Gomes',
            'billing_street' => '01 Lloyd Road',
            'billing_city' => 'South Siennaborough',
            'billing_country' => 'Tonga',
            'delivery_street' => '12 Lloyd Road',
            'delivery_city' => 'North Siennaborough',
            'delivery_country' => 'Tonga',
        ];

        $person = new class(1) {
            private int $id;
            private ?string $firstName = null;
            private ?string $lastName = null;
            /**
             * @BHY\PropertyConfig\SingleType(
             *      class="\Kassko\ObjectHydratorTest\Integration\Fixture\Model\Address\AddressSimple",
             *      rawDataLocation=@BHY\RawDataLocation(
             *          locationName="parent",
             *          keysMappingPrefix="billing_"
             *      )
             * )
             */
            private ?AddressSimple $billingAddress = null;
            /**
             * @BHY\PropertyConfig\SingleType(
             *      class="\Kassko\ObjectHydratorTest\Integration\Fixture\Model\Address\AddressSimple",
             *      rawDataLocation=@BHY\RawDataLocation(
             *          locationName="parent",
             *          keysMappingPrefix="delivery_"
             *      )
             * )
             */
            private ?AddressSimple $deliveryAddress = null;

            public function __construct(int $id) { $this->id = $id; }

            public function getId() : int { return $this->id; }

            public function getFirstName() : ?string { return $this->firstName; }
            public function setFirstName(string $firstName) { $this->firstName = $firstName; }

            public function getLastName() : ?string { return $this->lastName; }
            public function setLastName(string $lastName) { $this->lastName = $lastName; }

            public function getBillingAddress() : ?AddressSimple { return $this->billingAddress; }
            public function setBillingAddress(AddressSimple $billingAddress) { $this->billingAddress = $billingAddress; }

            public function getDeliveryAddress() : ?AddressSimple { return $this->deliveryAddress; }
            public function setDeliveryAddress(AddressSimple $deliveryAddress) { $this->deliveryAddress = $deliveryAddress; }
        };

        $hydrator = (new HydratorBuilder())->build();
        $hydrator->hydrate($person, $rawData);

        $this->assertSame(1, $person->getId());
        $this->assertSame('Dany', $person->getFirstName());
        $this->assertSame('Gomes', $person->getLastName());

        $this->assertSame('01 Lloyd Road', $person->getBillingAddress()->getStreet());
        $this->assertSame('South Siennaborough', $person->getBillingAddress()->getCity());
        $this->assertSame('Tonga', $person->getBillingAddress()->getCountry());

        $this->assertSame('12 Lloyd Road', $person->getDeliveryAddress()->getStreet());
        $this->assertSame('North Siennaborough', $person->getDeliveryAddress()->getCity());
        $this->assertSame('Tonga', $person->getDeliveryAddress()->getCountry());
    }
}
