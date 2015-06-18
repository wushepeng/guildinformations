<?php
namespace App\Entity;

use Doctrine\ORM\Mapping;

/**
 * @Entity(repositoryClass="App\Repository\GeneralConfigRepository")
 * @Table(name="gi_GeneralConfig")
 */
class GeneralConfig {

	/**
	 * @Id
	 * @var string
	 * @Column(name="RYAPI_APP_KEY", nullable=false, type="string")
	 */
	protected $appKey;

	/**
	 * @var string
	 * @Column(name="RYAPI_APP_URL", nullable=false, type="string")
	 */
	protected $appUrl;

	/**
	 * @var integer
	 * @Column(name="RYAPI_APP_MAXAGE", nullable=false, type="integer")
	 */
	protected $appMaxAge;

	public function __construct($appKey, $appUrl, $appMaxAge) {
		$this->appKey = $appKey;
		$this->appUrl = $appUrl;
		$this->appMaxAge = $appMaxAge;
	}

	public function getAppKey() {
		return $this->appKey;
	}

	public function getAppUrl() {
		return $this->appUrl;
	}

	public function getAppMaxAge() {
		return $this->appMaxAge;
	}

	public function setAppKey($appKey) {
		$this->appKey = $appKey;
	}

	public function setAppUrl($appUrl) {
		$this->appUrl = $appUrl;
	}

	public function setAppMaxAge($appMaxAge) {
		$this->appMaxAge = $appMaxAge;
	}
}
?>