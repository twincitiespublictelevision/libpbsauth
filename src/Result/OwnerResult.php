<?php

namespace LibPBSAuth\Result;

use LibPBSAuth\Owner;

/**
 * Class OwnerResult
 * @package LibPBSAuth\Result
 */
class OwnerResult {

  /**
   * @var Result
   */
  protected $res;

  /**
   * OwnerResult constructor.
   * @param Result $res
   */
  private function __construct(Result $res) {
    $this->res = $res;
  }

  /**
   * @param Owner|null $value
   * @return OwnerResult
   */
  public static function ok(?Owner $value): OwnerResult {
    return new OwnerResult(Result::ok($value));
  }

  /**
   * @param \Exception $err
   * @return OwnerResult
   */
  public static function err(\Exception $err): OwnerResult {
    return new OwnerResult(Result::err($err));
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
   * @return Owner|null
   * @throws \Exception
   */
  public function value(): ?Owner {
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
   * @return Owner|null|mixed
   */
  public function valueOr($fallback) {
    return $this->res->valueOr($fallback);
  }
}