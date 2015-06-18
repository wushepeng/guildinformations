<?php
namespace App\Entity;

use Doctrine\ORM\Mapping;

/**
 * @Entity(repositoryClass="App\Repository\GuildRepository")
 * @Table(name="gi_Guilds")
 */
class Guild {

	/**
	 * @Id
	 * @var integer
	 * @Column(name="id", nullable=false, type="integer")
	 */
	protected $id;

	/**
	 * @var string
	 * @Column(name="name", nullable=true, type="string")
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
	 * @Column(name="mainGuildId", nullable=true)
	 */
	protected $mainGuildId;

	public function __construct($id, $name, $apiKey, $mainGuildId) {
		$this->id = $id;
		$this->name = $name;
		$this->apiKey = $apiKey;
		$this->mainGuildId = $mainGuildId;
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

	public function getMainGuildId() {
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

	public function setMainGuildId($mainGuildId) {
		$this->mainGuildId = $mainGuildId;
	}
}
?>