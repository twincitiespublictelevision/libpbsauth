<?php

namespace Tests;

use LibPBSAuth\PBSAuth;
use PHPUnit\Framework\TestCase;

class PBSAuthTest extends TestCase {
  const TESTS = 9999;

  public function testParsing() {
    $s = new Sampler(SCHEMA);

    foreach (range(0, self::TESTS) as $t) {
      $sample = $s->sample('root');
      $result = PBSAuth::fromStdClass($sample);

      if ($result->isError()) {
        $this->fail("PBSAuth parsing failed due to {$result->getErr()->getMessage()} for " . json_encode($sample));
      }

      $this->assertFalse($result->isError());
    }
  }
}