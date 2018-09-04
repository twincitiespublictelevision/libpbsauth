<?php

namespace LibPBSAuth\Result;

use LibPBSAuth\VPPA;

/**
 * Class VPPAResult
 * @package LibPBSAuth\Result
 */
class VPPAResult {

  /**
   * @var Result
   */
  protected $res;

  /**
   * VPPAResult constructor.
   * @param Result $res
   */
  private function __construct(Result $res) {
    $this->res = $res;
  }

  /**
   * @param VPPA|null $value
   * @return VPPAResult
   */
  public static function ok(?VPPA $value): VPPAResult {
    return new VPPAResult(Result::ok($value));
  }

  /**
   * @param \Exception $err
   * @return VPPAResult
   */
  public static function err(\Exception $err): VPPAResult {
    return new VPPAResult(Result::err($err));
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
   * @return VPPA|null
   * @throws \Exception
   */
  public function value(): ?VPPA {
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
   * @return VPPA|null|mixed
   */
  public function valueOr($fallback) {
    return $this->res->valueOr($fallback);
  }
}