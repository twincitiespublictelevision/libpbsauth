<?php

namespace LibPBSAuth;

use LibPBSAuth\Result\PBSAuthResult;

/**
 * Class PBSAuth
 * @package LibPBSAuth
 */
class PBSAuth implements \JsonSerializable {

  const REQUIRED = [
    'owner', 'token'
  ];

  /**
   * @var Owner
   */
  private $_owner;

  /**
   * @var Token
   */
  private $_token;


  private function __construct(Owner $owner, Token $token) {
    $this->_owner = $owner;
    $this->_token = $token;
  }

  /**
   * @param string $record
   * @return PBSAuthResult
   */
  public static function fromJSON(string $record): PBSAuthResult {
    try {
      $parsed = ex_json_decode($record);
      return self::fromStdClass($parsed);
    } catch (\Exception $e) {
      return PBSAuthResult::err($e);
    }
  }

  /**
   * @param array $record
   * @return PBSAuthResult
   */
  public static function fromArray(array $record): PBSAuthResult {

    // Records do not get terribly large, so for simplicity we encode and then
    // decode from JSON at the cost of a little performance
    try {
      return self::fromJSON(ex_json_encode($record));
    } catch (\Exception $e) {
      return PBSAuthResult::err($e);
    }
  }

  /**
   * @param \stdClass $record
   * @return PBSAuthResult
   */
  public static function fromStdClass(\stdClass $record): PBSAuthResult {
    foreach (self::REQUIRED as $req) {
      if (!isset($record->{$req})) {
        return PBSAuthResult::err(new \InvalidArgumentException("Malformed PBS auth. {$req} field is missing."));
      }
    }

    $ownerRes = Owner::fromStdClass($record->owner);

    if ($ownerRes->isError()) {
      return PBSAuthResult::err($ownerRes->getErr());
    } else {
      $owner = $ownerRes->value();
    }

    $tokenRes = Token::fromStdClass($record->token);

    if ($tokenRes->isError()) {
      return PBSAuthResult::err($tokenRes->getErr());
    } else {
      $token = $tokenRes->value();
    }

    return PBSAuthResult::ok(new PBSAuth($owner, $token));
  }

  /**
   * @return array
   */
  public function toArray(): array {
    return [
      'owner' => $this->getOwner()->toArray(),
      'token' => $this->getToken()->toArray()
    ];
  }

  /**
   * @return \stdClass
   */
  public function toStdClass(): \stdClass {
    return json_decode(json_encode($this));
  }

  /**
   * @return mixed
   */
  public function jsonSerialize() {
    return $this->toArray();
  }

  /**
   * @return Owner
   */
  public function getOwner(): Owner {
    return $this->_owner;
  }

  /**
   * @return Token
   */
  public function getToken(): Token {
    return $this->_token;
  }
}