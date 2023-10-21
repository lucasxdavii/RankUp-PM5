<?php

namespace Taco\RU\commands;

use Taco\RU\Main;
use pocketmine\command\Command;
use pocketmine\command\PluginCommand;
use pocketmine\command\CommandSender;
use pocketmine\command\plugin\Plugin;

class RankUp extends Command {

	/**
	 * RankUp constructor.
	 * @param Main $plugin
	 */
	public function __construct(Main $plugin) {
		parent::__construct("rankup", "upar seu rank");
		$this->setPermission("rankup.command");
		$this->setDescription("Upar seu rank!");
		$this->setAliases(["ru"]);
	}

	/**
	 * @param CommandSender $sender
	 * @param string $commandLabel
	 * @param array $args
	 * @return bool
	 */
	public function execute(CommandSender $sender, string $commandLabel, array $args) : bool {
		Main::getInstance()->getAPI()->rankUp($sender);
		return true;
	}
}