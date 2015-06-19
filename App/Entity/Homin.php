<?php
namespace App\Entity;

use Doctrine\ORM\Mapping;

/**
 * @Entity(repositoryClass="App\Repository\HominRepository")
 * @Table(name="gi_Homins")
 */
class Homin {

	/**
	 * @Id
	 * @var integer
	 * @Column(name="id", nullable=false, type="integer")
	 */
	protected $id;

	/**
	 * @var string
	 * @Column(name="name", nullable=false, type="string")
	 */
	protected $name;

	/**
	 * @var string
	 * @Column(name="apiKey", nullable=true, type="string")
	 */
	protected $apiKey;

	/**
	 * @OneTOne(targetEntity="Guild")
	 * @JoinColumn(name="id", referencedColumnName="id")
	 * @Column(name="guildId", nullable=true)
	 */
	protected $guildId;

	public function __construct($id, $name, $apiKey, $guildId) {
		$this->id = $id;
		$this->name = $name;
		$this->apiKey = $apiKey;
		$this->guildId = $guildId;
	}

	public function getId() {
		return $this->id;
	}

	public function getName() {
		return $this->name;
	}

	public function getApiKey() {
		return $this->apiKey;
	}

	public function getGuildId() {
		return $this->guildId;
	}

	public function setId($id) {
		$this->id = $id;
	}

	public function setName($name) {
		$this->name = $name;
	}

	public function setApiKey($apiKey) {
		$this->apiKey = $apiKey;
	}

	public function setGuildId($guildId) {
		$this->guildId = $guildId;
	}
}
?>