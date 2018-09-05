<?php

namespace LibPBSAuth;

use LibPBSAuth\Result\TokenResult;

/**
 * Class Token
 * @package LibPBSAuth
 */
class Token implements \JsonSerializable {

  const REQUIRED = [
    'token_type', 'scope', 'access_token', 'refresh_token', 'expires'
  ];

  /**
   * @var string
   */
  private $_tokenType;

  /**
   * @var array
   */
  private $_scope;

  /**
   * @var string
   */
  private $_accessToken;

  /**
   * @var string
   */
  private $_refreshToken;

  /**
   * @var int
   */
  private $_expires;

  /**
   * Token constructor.
   * @param string $tokenType
   * @param array $scope
   * @param string $accessToken
   * @param string $refreshToken
   * @param int $expires
   */
  private function __construct(string $tokenType, array $scope, string $accessToken, string $refreshToken, int $expires) {
    $this->_tokenType = $tokenType;
    $this->_scope = $scope;
    $this->_accessToken = $accessToken;
    $this->_refreshToken = $refreshToken;
    $this->_expires = $expires;
  }

  /**
   * @param string $record
   * @return TokenResult
   */
  public static function fromJSON(string $record): TokenResult {
    try {
      $parsed = ex_json_decode($record);
      return self::fromStdClass($parsed);
    } catch (\Exception $e) {
      return TokenResult::err($e);
    }
  }

  /**
   * @param array $record
   * @return TokenResult
   */
  public static function fromArray(array $record): TokenResult {

    // Records do not get terribly large, so for simplicity we encode and then
    // decode from JSON at the cost of a little performance
    try {
      return self::fromJSON(ex_json_encode($record));
    } catch (\Exception $e) {
      return TokenResult::err($e);
    }
  }

  /**
   * @param \stdClass $record
   * @return TokenResult
   */
  public static function fromStdClass(\stdClass $record): TokenResult {
    foreach (self::REQUIRED as $req) {
      if (!isset($record->{$req})) {
        return TokenResult::err(new \InvalidArgumentException("Malformed token data. {$req} field is missing."));
      }
    }

    if (!is_int($record->expires)) {
      return TokenResult::err(new \Exception('Malformed token data. Expires field must be an integer'));
    }

    $scopes = array_filter(
      explode(' ', $record->scope),
      function($scope) {
        return $scope !== '';
      }
    );

    return TokenResult::ok(
      new Token(
        $record->token_type,
        $scopes,
        $record->access_token,
        $record->refresh_token,
        $record->expires
      )
    );
  }

  /**
   * @return array
   */
  public function toArray(): array {
    return [
      'token_type' => $this->getTokenType(),
      'scope' => implode(' ', $this->getScope()),
      'access_token' => $this->getAccessToken(),
      'refresh_token' => $this->getRefreshToken(),
      'expires' => $this->getExpires()
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
   * @return string
   */
  public function getTokenType(): string {
    return $this->_tokenType;
  }

  /**
   * @return array
   */
  public function getScope(): array {
    return $this->_scope;
  }

  /**
   * @return string
   */
  public function getAccessToken(): string {
    return $this->_accessToken;
  }

  /**
   * @return string
   */
  public function getRefreshToken(): string {
    return $this->_refreshToken;
  }

  /**
   * @return int
   */
  public function getExpires(): int {
    return $this->_expires;
  }
}