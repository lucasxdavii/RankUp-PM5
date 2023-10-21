<?php namespace Taco\RU;

use pocketmine\{event\Listener,
	event\player\PlayerJoinEvent,
	event\player\PlayerLoginEvent,
	plugin\Plugin,
	plugin\PluginBase,
	utils\Config};
use Taco\RU\commands\RankUp;

use IvanCraft623\RankSystem\tag\Tag;
use IvanCraft623\RankSystem\RankSystem;

class Main extends PluginBase implements Listener  {

	/**
	 * @var array
	 */
	public $config;

	/**
	 * @var Config
	 */
	public $ranks;

	/**
	 * @var self
	 */
	protected static $instance;

	/**
	 * @var Plugin $economy
	 */
	public $economy;

	public function onEnable() : void {
		self::$instance = $this;
		@mkdir($this->getDataFolder());
		$this->saveResource("config.yml");
		$this->config = $this->getConfig()->getAll();
		$this->ranks = new Config($this->getDataFolder() . "ranks.yml", Config::YAML);
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		if ($this->getServer()->getPluginManager()->getPlugin("EconomyAPI")) {
			$this->economy = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
		} else throw new \Exception("You must have EconomyAPI installed to use this plugin! You can download this plugin at https://poggit.pmmp.io/p/EconomyAPI");
		$this->getServer()->getCommandMap()->register("RankUp", new RankUp($this));
	}

	/**
	 * @return self
	 */
	public static function getInstance() : self {
		return self::$instance;
	}

	/**
	 * @return API
	 */
	public function getAPI() : API {
		return new API();
	}

	/**
	 * @param PlayerLoginEvent $event
	 */
	public function preJoin(PlayerLoginEvent $event) : void {
		$player = $event->getPlayer();
		//$playerName = $player->getName();
		if (!$this->ranks->exists($player->getName())) {
			$this->ranks->set($player->getName(), $this->config["default-rank"]);
			$this->ranks->save();
		}
		if ($this->ranks->exists($player->getName())) {
            $rank = $this->ranks->get($player->getName());
            if (isset($this->config["ranks"][$rank])) {
                $permissions = $this->config["ranks"][$rank]["permissions"];
                foreach ($permissions as $permission) {
                    $player->addAttachment($this, $permission, true);
                }
            }
        }
	}
}
