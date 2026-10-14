<?php

declare(strict_types=1);

namespace Vaivez66\NoAdvertisingPE;

use pocketmine\utils\TextFormat;

final class NoAdvertisingFormat{
	public function translate(string $message) : string{
		$colors = [
			"{BLACK}" => TextFormat::BLACK,
			"{DARK_BLUE}" => TextFormat::DARK_BLUE,
			"{DARK_GREEN}" => TextFormat::DARK_GREEN,
			"{DARK_AQUA}" => TextFormat::DARK_AQUA,
			"{DARK_RED}" => TextFormat::DARK_RED,
			"{DARK_GRAY}" => TextFormat::DARK_GRAY,
			"{DARK_PURPLE}" => TextFormat::DARK_PURPLE,
			"{LIGHT_PURPLE}" => TextFormat::LIGHT_PURPLE,
			"{GRAY}" => TextFormat::GRAY,
			"{GOLD}" => TextFormat::GOLD,
			"{BLUE}" => TextFormat::BLUE,
			"{GREEN}" => TextFormat::GREEN,
			"{AQUA}" => TextFormat::AQUA,
			"{RED}" => TextFormat::RED,
			"{YELLOW}" => TextFormat::YELLOW,
			"{WHITE}" => TextFormat::WHITE,
			"{OBFUSCATED}" => TextFormat::OBFUSCATED,
			"{BOLD}" => TextFormat::BOLD,
			"{STRIKETHROUGH}" => TextFormat::STRIKETHROUGH,
			"{UNDERLINE}" => TextFormat::UNDERLINE,
			"{ITALIC}" => TextFormat::ITALIC,
			"{RESET}" => TextFormat::RESET
		];
		return strtr($message, $colors);
	}
}
