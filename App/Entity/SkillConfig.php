<?php
namespace App\Entity;

use Doctrine\ORM\Mapping;

/**
 * @Entity(repositoryClass="App\Repository\SkillConfigRepository")
 * @Table(name="gi_SkillConfigs")
 */
class SkillConfig {

	/**
	 * @Id
	 * @var integer
	 * @OneTOne(targetEntity="Homin")
	 * @JoinColumn(name="id", referencedColumnName="id")
	 * @Column(name="hominId", nullable=false, type="integer")
	 */
	protected $hominId;

	/**
	 * @Id
	 * @var string
	 * @Column(name="skillCode", nullable=false, type="string")
	 */
	protected $skillCode;

	/**
	 * @var boolean
	 * @Column(name="visible", nullable=false, type="integer")
	 */
	protected $visible;

	public function __construct($hominId, $skillCode, $visible) {
		$this->hominId = $hominId;
		$this->skillCode = $skillCode;
		$this->visible = $visible;
	}

	public function getHominId() {
		return $this->hominId;
	}

	public function getSkillCode() {
		return $this->skillCode;
	}

	public function getVisible() {
		return $this->visible;
	}

	public function setHominId($hominId) {
		$this->hominId = $hominId;
	}

	public function setSkillCode($skillCode) {
		$this->skillCode = $skillCode;
	}

	public function setVisible($visible) {
		$this->visible = $visible;
	}
}
?>