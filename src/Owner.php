<?php

namespace LibPBSAuth;

use LibPBSAuth\Result\OwnerResult;

/**
 * Class Owner
 *
 * Represents the owner data associated with a PBS Account authentication
 * response. Requires that the pid, first_name, last_name, email, zip_code,
 * analytics_id, and thumbnail_URL fields are present. Analytics_id and
 * thumbnail_URL are the only two fields of the required fields that are allowed
 * to be null.
 *
 * Additionally a VPPA field may or may not be present. The VPPA field may also
 * potentially be null. In the case that the VPPA field contains a truthy value,
 * then it must correctly parse to a VPPA object.
 *
 * @package LibPBSAuth
 */
class Owner implements \JsonSerializable {

  /**
   * @var array
   */
  const REQUIRED = [
    'pid', 'first_name', 'last_name', 'email'
  ];

  /**
   * @var array
   */
  const EXISTS = [
    'analytics_id', 'thumbnail_URL', 'zip_code'
  ];

  /**
   * @var string
   */
  private $_pid;

  /**
   * @var string
   */
  private $_firstName;

  /**
   * @var string
   */
  private $_lastName;

  /**
   * @var string
   */
  private $_email;

  /**
   * @var string
   */
  private $_zipCode;

  /**
   * @var null|string
   */
  private $_analyticsId;

  /**
   * @var null|string
   */
  private $_thumbnailUrl;

  /**
   * @var null|VPPA
   */
  private $_vppa;

  /**
   * Owner constructor.
   * @param string $pid
   * @param string $firstName
   * @param string $lastName
   * @param string $email
   * @param string $zipCode
   * @param null|string $analyticsId
   * @param null|string $thumbnailUrl
   * @param VPPA|null $vppa
   */
  private function __construct(string $pid, string $firstName, string $lastName, string $email, string $zipCode, ?string $analyticsId, ?string $thumbnailUrl, ?VPPA $vppa) {
    $this->_pid = $pid;
    $this->_firstName = $firstName;
    $this->_lastName = $lastName;
    $this->_email = $email;
    $this->_zipCode = $zipCode;
    $this->_analyticsId = $analyticsId;
    $this->_thumbnailUrl = $thumbnailUrl;
    $this->_vppa = $vppa;
  }

  /**
   * @param string $record
   * @return OwnerResult
   */
  public static function fromJSON(string $record): OwnerResult {
    try {
      $parsed = ex_json_decode($record);
      return self::fromStdClass($parsed);
    } catch (\Exception $e) {
      return OwnerResult::err($e);
    }
  }

  /**
   * @param array $record
   * @return OwnerResult
   */
  public static function fromArray(array $record): OwnerResult {

    // Records do not get terribly large, so for simplicity we encode and then
    // decode from JSON at the cost of a little performance
    try {
      return self::fromJSON(ex_json_encode($record));
    } catch (\Exception $e) {
      return OwnerResult::err($e);
    }
  }

  /**
   * @param \stdClass $record
   * @return OwnerResult
   */
  public static function fromStdClass(\stdClass $record): OwnerResult {
    foreach (self::REQUIRED as $req) {
      if (!isset($record->{$req})) {
        return OwnerResult::err(new \InvalidArgumentException("Malformed owner. {$req} field is missing."));
      }
    }

    foreach (self::EXISTS as $req) {
      if (!property_exists($record, $req)) {
        return OwnerResult::err(new \InvalidArgumentException("Malformed owner. {$req} field must be present."));
      }
    }

    $vppa = null;

    if (isset($record->vppa)) {
      $vppaRes = VPPA::fromStdClass($record->vppa);

      if ($vppaRes->isError()) {
        return OwnerResult::err($vppaRes->getErr());
      }

      $vppa = $vppaRes->value();
    }

    return OwnerResult::ok(
      new Owner(
        $record->pid,
        $record->first_name,
        $record->last_name,
        $record->email,
        $record->zip_code,
        $record->analytics_id,
        $record->thumbnail_URL,
        $vppa
      )
    );
  }

  /**
   * @return array
   */
  public function toArray(): array {
    return [
      'pid' => $this->getPid(),
      'first_name' => $this->getFirstName(),
      'last_name' => $this->getLastName(),
      'email' => $this->getEmail(),
      'zip_code' => $this->getZipCode(),
      'analytics_id' => $this->getAnalyticsId(),
      'thumbnail_URL' => $this->getThumbnailUrl(),
      'vppa' => $this->getVPPA() ? $this->getVPPA()->toArray() : null
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
   * Gets the PID of the authenticated user.
   *
   * @return string
   */
  public function getPid(): string {
    return $this->_pid;
  }

  /**
   * Gets the first name of the authenticated user.
   *
   * @return string
   */
  public function getFirstName(): string {
    return $this->_firstName;
  }

  /**
   * Gets the last name of the authenticated user.
   *
   * @return string
   */
  public function getLastName(): string {
    return $this->_lastName;
  }

  /**
   * Gets the email of the authenticated user.
   *
   * @return string
   */
  public function getEmail(): string {
    return $this->_email;
  }

  /**
   * Gets the zipcode of the authenticated user.
   *
   * @return string
   */
  public function getZipCode(): ?string {
    return $this->_zipCode;
  }

  /**
   * Gets the optional analytics id of the authenticated user.
   *
   * @return string|null
   */
  public function getAnalyticsId(): ?string {
    return $this->_analyticsId;
  }

  /**
   * Gets the optional thumbnail url of the authenticated user.
   *
   * @return string|null
   */
  public function getThumbnailUrl(): ?string {
    return $this->_thumbnailUrl;
  }

  /**
   * Gets the VPPA object representing the authenticated user's VPPA agreement.
   * This may be null if VPPA information is missing or the VPPA scope has not
   * been required during authentication.
   *
   * @return VPPA|null
   */
  public function getVPPA(): ?VPPA {
    return $this->_vppa;
  }
}