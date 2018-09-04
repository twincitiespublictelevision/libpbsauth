<?php

namespace LibPBSAuth;

use LibPBSAuth\Result\VPPAResult;

/**
 * Class VPPA
 * @package LibPBSAuth
 */
class VPPA implements \JsonSerializable {

  const REQUIRED = [
    'vppa_accepted', 'vppa_last_updated'
  ];

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
   * @param string $vppaAccepted
   * @param \DateTime $vppaLastUpdated
   */
  private function __construct(string $vppaAccepted, \DateTime $vppaLastUpdated) {
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
    foreach (self::REQUIRED as $req) {
      if (!property_exists($record, $req)) {
        VPPAResult::err(new \InvalidArgumentException("Malformed VPPA data. {$req} field is missing."));
      }
    }

    // Last updated date must parse properly as a date
    if (isset($record->vppa_last_updated) && $record->vppa_last_updated) {
      $lastUpdated = new \DateTime($record->activation_date);

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
      'vppa_accepted' => $this->isVPPAAccepted(),
      'vppa_last_updated' => $this->getVPPALastUpdated()->format(self::DATEFORMAT)
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
   * @return bool
   */
  public function isVPPAAccepted(): bool {
    return $this->_vppaAccepted;
  }

  /**
   * @return \DateTime
   */
  public function getVPPALastUpdated(): \DateTime {
    return $this->_vppaLastUpdated;
  }
}