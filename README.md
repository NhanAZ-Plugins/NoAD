# NoAD

NoAD filters configured server-advertising domains from player chat, sign text and selected command arguments. The original repository credits Vaivez66 and notes an edit by Ghast Noob.

## Requirements

- Axolotl-PM API 5.0.0 is the declared minimum. CI checks API 5.0.0 and 5.49.1; it does not verify every intervening or future server revision.

## Commands

| Command | Description | Permission |
| --- | --- | --- |
| `/na add <domain>` | Add an exact domain or suffix rule to the blocked list. | `no.advertising.pe` (operator by default) |
| `/na remove <domain>` | Remove a blocked rule. | `no.advertising.pe` (operator by default) |
| `/na list` | List blocked rules. | `no.advertising.pe` (operator by default) |

Players with `no.advertising.pe.bypass` are excluded from filtering.

## Configuration

The historical keys in [`resources/config.yml`](resources/config.yml) are retained. Rules in `domain` match detected domain hosts rather than arbitrary text. A rule such as `.com` matches hosts that end in `.com`; an exact rule such as `bad.example` matches that host and its subdomains. Entries in `allowed.domain` allow only the matching host and its subdomains. If a message contains both an allowed host and a different blocked host, the blocked host is still filtered.

`type` supports `block`, `broadcast` and `kick` for chat advertising. Sign matches replace all four sign lines with the configured `lines`. Selected command filtering reads `blocked.cmd`.

Axolotl-PM 5 removed the old player-command preprocess event. The API 5 port uses `CommandEvent` for selected command text; as documented by Axolotl-PM, command aliases may bypass this filter. Use `blocked.cmd` only for commands whose aliases are also controlled. This does not affect chat or sign checks.

## Build and checks

GitHub Actions uses the pinned [DevTools](https://github.com/NhanAZ/DevTools) reusable build workflow. It checks PHP syntax, domain-matching fixtures, PHPStan at maximum level against pinned API revisions, PHAR validation, and startup on an isolated Axolotl-PM 5.49.1 server.

## Provenance

The repository has no LICENSE file or documented grant for distributing a modified build. This migration does not claim a license or publish a stable release. Do not redistribute a PHAR until rights are verified.
