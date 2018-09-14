<?php

namespace Tests;

use LibPBSAuth\VPPA;
use PHPUnit\Framework\TestCase;

class VPPATest extends TestCase {
  const TESTS = 9999;

  public function testParsing() {
    $s = new Sampler(SCHEMA);

    foreach (range(0, self::TESTS) as $t) {
      $sample = $s->sample('vppa');
      $result = VPPA::fromStdClass($sample);

      if ($result->isError()) {
        $this->fail("VPPA parsing failed due to {$result->getErr()->getMessage()} for " . json_encode($sample));
      }

      $this->assertFalse($result->isError());
    }
  }
}