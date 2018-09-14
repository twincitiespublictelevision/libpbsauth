<?php

namespace Tests;

use LibPBSAuth\Token;
use PHPUnit\Framework\TestCase;

class TokenTest extends TestCase {
  const TESTS = 9999;

  public function testParsing() {
    $s = new Sampler(SCHEMA);

    foreach (range(0, self::TESTS) as $t) {
      $sample = $s->sample('token');
      $result = Token::fromStdClass($sample);

      if ($result->isError()) {
        $this->fail("Token parsing failed due to {$result->getErr()->getMessage()} for " . json_encode($sample));
      }

      $this->assertFalse($result->isError());
    }
  }
}