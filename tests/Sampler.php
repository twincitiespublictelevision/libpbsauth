<?php

namespace Tests;

use Faker\Factory;
use Faker\Generator;

const SCHEMA = [
  'root' => [
    'owner' => ['ref.owner'],
    'token' => ['ref.token']
  ],
  'owner' => [
    'pid' => ['uuid'],
    'first_name' => ['string'],
    'last_name' => ['string'],
    'email' => ['email'],
    'zip_code' => ['number'],
    'analytics_id' => ['uuid', 'null'],
    'thumbnail_URL' => ['string', 'null'],
    'vppa' => ['ref.vppa', 'null']
  ],
  'token' => [
    'token_type' => ['string'],
    'scope' => ['string'],
    'access_token' => ['string'],
    'refresh_token' => ['string'],
    'expires' => ['number']
  ],
  'vppa' => [
    'vppa_accepted' => ['bool', 'null'],
    'vppa_last_updated' => ['date', 'null']
  ]
];

function generateObject(array $arr, array $schema, string $root, Generator $gen): array {
  foreach ($schema[$root] as $key => $val) {
    $k = array_keys($val)[rand(0, count($val) - 1)];
    $entry = $val[$k];

    if (strpos($entry, 'ref.') === 0) {
      $ref = explode('.', $entry)[1];
      $arr[$key] = generateObject([], $schema, $ref, $gen);
    } else {
      switch ($entry) {
        case 'string':
          $arr[$key] = $gen->unique()->word;
          break;

        case 'date':
          $arr[$key] = $gen->unique()->date('Y-m-d H:i:s.uP');
          break;

        case 'null':
          $arr[$key] = null;
          break;

        case 'number':
          $arr[$key] = $gen->unique()->numberBetween(1000, 7000);
          break;

        case 'uuid':
          $arr[$key] = $gen->unique()->uuid;
          break;

        case 'email':
          $arr[$key] = $gen->unique()->email;
          break;

        case 'bool':
          $arr[$key] = $gen->unique()->boolean;
          break;
      }
    }
  }

  return $arr;
}

function generate(array $schema, string $root): \stdClass {
  return json_decode(json_encode(generateObject([], $schema, $root, Factory::create())));
}

/**
 * Class Sampler
 * @package Tests
 */
class Sampler {

  /**
   * @var array
   */
  private $_schema;

  /**
   * Sampler constructor.
   * @param array $schema
   */
  public function __construct(array $schema) {
    $this->_schema = $schema;
  }

  /**
   * @return \stdClass
   */
  public function sample($root = 'root') {
    return generate($this->_schema, $root);
  }
}