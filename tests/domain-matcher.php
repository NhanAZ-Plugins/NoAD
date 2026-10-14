<?php

declare(strict_types=1);

use Vaivez66\NoAdvertisingPE\DomainMatcher;

require __DIR__ . "/../src/Vaivez66/NoAdvertisingPE/DomainMatcher.php";

function assertSameValue(mixed $expected, mixed $actual, string $message) : void{
	if($expected !== $actual){
		throw new RuntimeException($message . " Expected " . var_export($expected, true) . ", got " . var_export($actual, true) . ".");
	}
}

assertSameValue(
	["example.com", "foo.minesao.sytes.net"],
	DomainMatcher::extractHosts("Visit HTTPS://Example.COM/path or foo.minesao.sytes.net!"),
	"Host extraction must be case-insensitive and ignore URL paths."
);
assertSameValue(null, DomainMatcher::findBlockedHost("I like .com websites", [".com"], []), "A bare TLD mention must not match as a host.");
assertSameValue(null, DomainMatcher::findBlockedHost("example.commerce is a product", [".com"], []), "A longer TLD must not match the .com suffix rule.");
assertSameValue("example.com", DomainMatcher::findBlockedHost("example.com", [".com"], []), "A blocked host at the start of a message must be detected.");
assertSameValue(null, DomainMatcher::findBlockedHost("https://minesao.sytes.net", [".net"], ["minesao.sytes.net"]), "An allowed domain must be exempt.");
assertSameValue(null, DomainMatcher::findBlockedHost("www.minesao.sytes.net", [".net"], ["minesao.sytes.net"]), "Allowed rules must include subdomains.");
assertSameValue("example.com", DomainMatcher::findBlockedHost("minesao.sytes.net and example.com", [".net", ".com"], ["minesao.sytes.net"]), "An allowed host must not exempt another blocked host in the same message.");
assertSameValue("notminesao.sytes.net", DomainMatcher::findBlockedHost("notminesao.sytes.net", [".net"], ["minesao.sytes.net"]), "A similarly suffixed but distinct host must not inherit the allow rule.");
assertSameValue("example.com", DomainMatcher::findBlockedHost("EXAMPLE.COM", [".com"], []), "Host matching must ignore case.");
assertSameValue("sub.bad.example", DomainMatcher::findBlockedHost("sub.bad.example", ["bad.example"], []), "Exact rules must cover subdomains.");
assertSameValue(null, DomainMatcher::normalizeRule("https://example.com/path"), "Rules must be host values, not arbitrary URLs.");
assertSameValue(null, DomainMatcher::normalizeRule("exa(mple).com"), "Regex metacharacters must be rejected from domain rules.");
assertSameValue(".co.uk", DomainMatcher::normalizeRule(".CO.UK"), "Valid suffix rules must normalize consistently.");
assertSameValue("example.net", DomainMatcher::findBlockedHost("example.net", [".net"], [".net"]), "An allowed rule cannot use a broad TLD suffix to bypass every blocked host.");

fwrite(STDOUT, "NoAD domain-matching and false-positive fixtures passed.\n");
