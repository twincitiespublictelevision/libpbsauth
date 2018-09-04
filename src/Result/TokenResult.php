<?php

namespace LibPBSAuth\Result;

use LibPBSAuth\Token;

/**
 * Class TokenResult
 * @package LibPBSAuth\Result
 */
class TokenResult {

  /**
   * @var Result
   */
  protected $res;

  /**
   * TokenResult constructor.
   * @param Result $res
   */
  private function __construct(Result $res) {
    $this->res = $res;
  }

  /**
   * @param Token|null $value
   * @return TokenResult
   */
  public static function ok(?Token $value): TokenResult {
    return new TokenResult(Result::ok($value));
  }

  /**
   * @param \Exception $err
   * @return TokenResult
   */
  public static function err(\Exception $err): TokenResult {
    return new TokenResult(Result::err($err));
  }

  /**
   * @return bool
   */
  public function isOk(): bool {
    return $this->res->isOk();
  }

  /**
   * @return bool
   */
  public function isError(): bool {
    return $this->res->isError();
  }

  /**
   * @return Token|null
   * @throws \Exception
   */
  public function value(): ?Token {
    return $this->res->value();
  }

  /**
   * @return \Exception|null
   */
  public function getErr() {
    return $this->res->getErr();
  }

  /**
   * @param mixed $fallback
   * @return Token|null|mixed
   */
  public function valueOr($fallback) {
    return $this->res->valueOr($fallback);
  }
}