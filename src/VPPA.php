<?php

namespace LibPBSAuth;

use LibPBSAuth\Result\VPPAResult;

/**
 * Class VPPA
 *
 * Describes VPPA agreement information for the authenticated user and the
 * authenticating station. Requires only that the vppa_accepted and
 * vppa_last_updated fields are present. If vppa_last_updated contains a truthy
 * value, then it must be able to be parsed into a \DateTime.
 *
 * @package LibPBSAuth
 */
class VPPA implements \JsonSerializable {

  /**
   * @var array
   */
  const EXISTS = [
    'vppa_accepted', 'vppa_last_updated'
  ];

  /**
   * @var string
   */
  const DATEFORMAT = 'Y-m-d H:i:s.uP';

  /**
   * @var bool
   */
  private $_vppaAccepted;

  /**
   * @var \DateTime
   */
  private $_vppaLastUpdated;

  /**
   * VPPA constructor.
   * @param bool|null $vppaAccepted
   * @param \DateTime|null $vppaLastUpdated
   */
  private function __construct(?bool $vppaAccepted, ?\DateTime $vppaLastUpdated) {
    $this->_vppaAccepted = $vppaAccepted;
    $this->_vppaLastUpdated = $vppaLastUpdated;
  }

  /**
   * @param string $record
   * @return VPPAResult
   */
  public static function fromJSON(string $record): VPPAResult {
    try {
      $parsed = ex_json_decode($record);
      return self::fromStdClass($parsed);
    } catch (\Exception $e) {
      return VPPAResult::err($e);
    }
  }

  /**
   * @param array $record
   * @return VPPAResult
   */
  public static function fromArray(array $record): VPPAResult {

    // Records do not get terribly large, so for simplicity we encode and then
    // decode from JSON at the cost of a little performance
    try {
      return self::fromJSON(ex_json_encode($record));
    } catch (\Exception $e) {
      return VPPAResult::err($e);
    }
  }

  /**
   * @param \stdClass $record
   * @return VPPAResult
   */
  public static function fromStdClass(\stdClass $record): VPPAResult {
    foreach (self::EXISTS as $req) {
      if (!property_exists($record, $req)) {
        return VPPAResult::err(new \InvalidArgumentException("Malformed VPPA data. {$req} field must be present."));
      }
    }

    // Last updated date must parse properly as a date if it is set
    $lastUpdated = null;

    if ($record->vppa_last_updated) {
      $lastUpdated = new \DateTime($record->vppa_last_updated);

      if ($lastUpdated === false) {
        return VPPAResult::err(new \InvalidArgumentException("Malformed VPPA data. Last updated date field is not correctly formatted."));
      }
    }

    return VPPAResult::ok(
      new VPPA(
        $record->vppa_accepted,
        $lastUpdated
      )
    );
  }

  /**
   * @return array
   */
  public function toArray(): array {
    return [
      'vppa_accepted' => $this->_getVPPAAccepted(),
      'vppa_last_updated' => $this->getVPPALastUpdated() === null ? null : $this->getVPPALastUpdated()->format(self::DATEFORMAT)
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
   * @return bool|null
   */
  private function _getVPPAAccepted(): ?bool {
    return $this->_vppaAccepted;
  }

  /**
   * Gets the represented VPPA status. This does not perform validation as to
   * when VPPA was accepted. The caller must use this in conjunction with
   * getVPPALastUpdated() to determine if the acceptance is still valid.
   *
   * @return bool
   */
  public function isVPPAAccepted(): bool {
    if ($this->_getVPPAAccepted()) {
      return true;
    }

    return false;
  }

  /**
   * Gets the last time that VPPA was accepted by the user. Returns a \DateTime
   * value if an acceptance date was available. If this represents a
   * non-accepted VPPA agreement, then null if returned.
   *
   * @return \DateTime|null
   */
  public function getVPPALastUpdated(): ?\DateTime {
    return $this->_vppaLastUpdated;
  }
}