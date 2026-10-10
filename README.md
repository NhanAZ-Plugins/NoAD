# NoAD

NoAD is a legacy PocketMine-MP plugin snapshot intended to detect server advertising in chat, signs and selected commands. Its manifest declares plugin API 1.12.0, credits Vaivez66 as author and notes an edit by Ghast Noob. The `/na` command manages domains in the historical implementation.

The bundled [configuration](resources/config.yml) defines blocked and allowed domains, sign detection and actions named `broadcast`, `block` and `kick`. These rules have not been verified against current event APIs or real server traffic. The source uses the historical `pocketmine\Player` class and event methods, so Axolotl-PM 5.x compatibility is not claimed.

This repository has no LICENSE file or documented grant to redistribute a modified build. No source, manifest, configuration or translated messages were changed for this documentation update. There is no current Axolotl-PM 5 build, runtime test or stable release.

See [plugin.yml](plugin.yml) and [the historical source](src/Vaivez66/NoAdvertisingPE/NoAdvertising.php) for the implementation. A port or release needs verified rights, updated event handling and tests for false positives and moderation behavior.
