<?php

declare(strict_types=1);

namespace Vaivez66\NoAdvertisingPE;

final class DomainMatcher{
	private const HOST_PATTERN = '~(?<![\pL\pN_-])(?:https?://)?(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+(?:xn--[a-z0-9-]{2,59}[a-z0-9]|[a-z]{2,63})(?![a-z0-9-])~iu';

	/** @return list<string> */
	public static function extractHosts(string $text) : array{
		$matches = preg_match_all(self::HOST_PATTERN, $text, $results);
		if($matches === false || $matches === 0){
			return [];
		}

		$hosts = [];
		foreach($results[0] as $match){
			$host = strtolower(rtrim((string) preg_replace('~\Ahttps?://~i', "", $match), "."));
			if($host !== ""){
				$hosts[$host] = $host;
			}
		}
		return array_values($hosts);
	}

	public static function normalizeRule(string $rule) : ?string{
		$rule = strtolower(trim($rule));
		if($rule === "" || strlen($rule) > 254 || str_contains($rule, "://") || str_contains($rule, "/") || str_contains($rule, "@")){
			return null;
		}

		$suffixRule = str_starts_with($rule, ".");
		$domain = rtrim($suffixRule ? substr($rule, 1) : $rule, ".");
		if($domain === ""){
			return null;
		}

		$labels = explode(".", $domain);
		if(count($labels) < ($suffixRule ? 1 : 2)){
			return null;
		}
		foreach($labels as $label){
			if(strlen($label) > 63 || preg_match('/\A[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\z/i', $label) !== 1){
				return null;
			}
		}

		$lastLabel = end($labels);
		if(preg_match('/\A(?:[a-z]{2,63}|xn--[a-z0-9-]{2,59}[a-z0-9])\z/i', $lastLabel) !== 1){
			return null;
		}

		return ($suffixRule ? "." : "") . $domain;
	}

	public static function matchesRule(string $host, string $rule) : bool{
		$normalized = self::normalizeRule($rule);
		if($normalized === null){
			return false;
		}
		$host = strtolower(rtrim($host, "."));

		if(str_starts_with($normalized, ".")){
			return str_ends_with($host, $normalized);
		}
		return $host === $normalized || str_ends_with($host, "." . $normalized);
	}

	public static function matchesAllowedRule(string $host, string $rule) : bool{
		$normalized = self::normalizeRule($rule);
		if($normalized === null || str_starts_with($normalized, ".")){
			return false;
		}
		$host = strtolower(rtrim($host, "."));
		return $host === $normalized || str_ends_with($host, "." . $normalized);
	}

	/**
	 * @param list<string> $blockedRules
	 * @param list<string> $allowedRules
	 */
	public static function findBlockedHost(string $text, array $blockedRules, array $allowedRules) : ?string{
		foreach(self::extractHosts($text) as $host){
			$allowed = false;
			foreach($allowedRules as $rule){
				if(self::matchesAllowedRule($host, $rule)){
					$allowed = true;
					break;
				}
			}
			if($allowed){
				continue;
			}

			foreach($blockedRules as $rule){
				if(self::matchesRule($host, $rule)){
					return $host;
				}
			}
		}
		return null;
	}
}
