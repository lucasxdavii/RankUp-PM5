<?php namespace Taco\RU;

use pocketmine\command\ConsoleCommandSender;
use pocketmine\player\Player;

class API {

	/**
	 * @param Player $player
	 * @return string
	 *
	 * Return the current rank of the player
	 */
	public function getRank(Player $player) : string {
		return Main::getInstance()->ranks->get($player->getName());
	}

	/**
	 * @param Player $player
	 * @param string $new
	 *
	 * Changes the current rank of the player
	 */
	public function setRank(Player $player, string $new) : void {
		Main::getInstance()->ranks->set($player->getName(), $new);
		Main::getInstance()->ranks->save();
	}

	/**
	 * @param Player $player
	 * @return string
	 *
	 * Return the players next rank
	 */
	public function getNextRank(Player $player) : string {
		$current = $this->getRank($player);
		$next = false;
		foreach(Main::getInstance()->config["ranks"] as $rank => $i) {
			if ($next) return $rank;
			if ($rank == $current) $next = true;
		}
		return "";
	}

	/**
	 * @param Player $player
	 * @return int
	 *
	 * Return the players next rank rank up price
	 */
	public function getNextRankPrice(Player $player) : int {
		$current = $this->getRank($player);
		$next = false;
		foreach(Main::getInstance()->config["ranks"] as $rank => $i) {
			if ($next) return (int)$i["price"];
			if ($rank == $current) $next = true;
		}
		return 0;
	}

	/**
	 * @param Player $player
	 *
	 * Ranks the player up to the next rank
	 */
	public function rankUp(Player $player) : void {
		$next = $this->getNextRank($player);
		$current = $this->getRank($player);
		$previousRank = $this->getRank($player);
		$previousRankPermissions = Main::getInstance()->config["ranks"][$previousRank]["permissions"];
		if ($this->getNextRank($player) !== "") {
			$money = Main::getInstance()->economy->myMoney($player);
			if ($money >= $this->getNextRankPrice($player)) {

				foreach ($previousRankPermissions as $permission) {
					$plugin = Main::getInstance();
					$player->removeAttachment($plugin, $permission);
				}

				if (isset(Main::getInstance()->config["ranks"][$nextRank]["permissions"]) && is_array(Main::getInstance()->config["ranks"][$nextRank]["permissions"])) {
					$plugin = Main::getInstance();
					$permissions = Main::getInstance()->config["ranks"][$nextRank]["permissions"];
				
					foreach ($permissions as $permission) {
						$player->addAttachment($plugin, $permission, true);
					}
				}
				$message = Main::getInstance()->config["rankup-success"];
				$message = str_replace("{new-rank}", $this->getNextRank($player), $message);
				$player->sendMessage($message);
				Main::getInstance()->economy->reduceMoney($player, $this->getNextRankPrice($player));
				$this->setRank($player, $this->getNextRank($player));
			} else {
				$message = Main::getInstance()->config["rankup-fail"];
				$message = str_replace("{new-rank}", $next, $message);
				$message = str_replace("{needs-money}", ($this->getNextRankPrice($player) - $money), $message);
				$player->sendMessage($message);
			}
		} else $player->sendMessage(Main::getInstance()->config["rankup-max"]);
	}


}
