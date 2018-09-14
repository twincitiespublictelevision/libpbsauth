<?php

namespace Tests;

use LibPBSAuth\Owner;
use PHPUnit\Framework\TestCase;

class OwnerTest extends TestCase {
  const TESTS = 9999;

  public function testParsing() {
    $s = new Sampler(SCHEMA);

    foreach (range(0, self::TESTS) as $t) {
      $sample = $s->sample('owner');
      $result = Owner::fromStdClass($sample);

      if ($result->isError()) {
        $this->fail("Owner parsing failed due to {$result->getErr()->getMessage()} for " . json_encode($sample));
      }

      $this->assertFalse($result->isError());
    }
  }
}