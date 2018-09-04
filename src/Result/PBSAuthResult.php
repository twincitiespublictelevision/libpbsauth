<?php

namespace LibPBSAuth\Result;

use LibPBSAuth\PBSAuth;

/**
 * Class PBSAuthResult
 * @package LibPBSAuth\Result
 */
class PBSAuthResult {

  /**
   * @var Result
   */
  protected $res;

  /**
   * PBSAuthResult constructor.
   * @param Result $res
   */
  private function __construct(Result $res) {
    $this->res = $res;
  }

  /**
   * @param PBSAuth|null $value
   * @return PBSAuthResult
   */
  public static function ok(?PBSAuth $value): PBSAuthResult {
    return new PBSAuthResult(Result::ok($value));
  }

  /**
   * @param \Exception $err
   * @return PBSAuthResult
   */
  public static function err(\Exception $err): PBSAuthResult {
    return new PBSAuthResult(Result::err($err));
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
   * @return PBSAuth|null
   * @throws \Exception
   */
  public function value(): ?PBSAuth {
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
   * @return PBSAuth|null|mixed
   */
  public function valueOr($fallback) {
    return $this->res->valueOr($fallback);
  }
}