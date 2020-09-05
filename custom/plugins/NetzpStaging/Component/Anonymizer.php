<?php namespace NetzpStaging\Component;

class Anonymizer {

	private $faker;

	public function __construct() {

        require_once(__DIR__ . '/../lib/faker/autoload.php');

        $repo = Shopware()->Container()->get('models')->getRepository('Shopware\Models\Shop\Shop');
        $shop = $repo->getActiveById(1);
        $locale = $shop->getLocale()->getLocale();
        $this->faker = \Faker\Factory::create($locale);
	}

	function anonymize($tableName, $colName, $colValue) {

        $value = $colValue;

        if($value != '') {
        if($tableName == 's_user') {
            if($colName == 'email') {
                $value = $this->faker->safeEmail;
            }
            else if($colName == 'firstname') {
                $value = $this->faker->firstName;
            }
            else if($colName == 'lastname') {
                $value = $this->faker->lastName;
            }
            else if($colName == 'internalcomment') {
                $value = $this->faker->sentence;
            }
            else if($colName == 'birthday') {
                $value = $this->faker->dateTimeThisCentury->format('Y-m-d');
            }
        }

        else if($tableName == 's_user_addresses' ||
                $tableName == 's_user_billingaddress' ||
                $tableName == 's_user_shippingaddress' ||
                $tableName == 's_order_billingaddress' ||
                $tableName == 's_order_shippingaddress'
            ) {

            if($colName == 'company') {
                $value = $this->faker->company;
            }
            else if($colName == 'department') {
                $value = $this->faker->jobTitle;
            }
            else if($colName == 'firstname') {
                $value = $this->faker->firstName;
            }
            else if($colName == 'lastname') {
                $value = $this->faker->lastName;
            }
            else if($colName == 'street') {
                $value = $this->faker->streetAddress;
            }
            else if($colName == 'zipcode') {
                $value = $this->faker->postcode;
            }
            else if($colName == 'city') {
                $value = $this->faker->city;
            }
            else if($colName == 'phone') {
                $value = $this->faker->phoneNumber;
            }
            else if($colName == 'ustid') {
                $value = 'XX999999999';
            }
            else if($colName == 'additional_address_line1' || $colName == 'additional_address_line2') {
                $value = $this->faker->streetName;
            }
        }

        else if($tableName == 's_campaigns_mailaddresses') {
            if($colName == 'email') {
                $value = $this->faker->safeEmail;
            }
        }

        else if($tableName == 's_campaigns_maildata') {
            if($colName == 'email') {
                $value = $this->faker->safeEmail;
            }
            else if($colName == 'firstname') {
                $value = $this->faker->firstName;
            }
            else if($colName == 'lastname') {
                $value = $this->faker->lastName;
            }
            else if($colName == 'street') {
                $value = $this->faker->streetAddress;
            }
            else if($colName == 'zipcode') {
                $value = $this->faker->postcode;
            }
            else if($colName == 'city') {
                $value = $this->faker->city;
            }
        }

        else if($tableName == 's_order') {
            if($colName == 'comment' || $colName == 'customercomment' || $colName == 'internalcomment') {
                $value = $this->faker->sentence;
            }
            else if($colName == 'remote_addr') {
                $value = $this->faker->localIpv4;
            }
        }
        }

        return $value;
    }
}
