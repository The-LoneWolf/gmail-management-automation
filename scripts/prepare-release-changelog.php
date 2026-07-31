<?php

declare(strict_types=1);

$version = trim((string) ($argv[1] ?? ''));
$previousRef = trim((string) ($argv[2] ?? ''));
$currentRef = trim((string) ($argv[3] ?? 'HEAD'));
$date = trim((string) ($argv[4] ?? date('Y-m-d')));

if ($version === '') {
    fwrite(STDERR, "Usage: php scripts/prepare-release-changelog.php <version> [previous-ref] [current-ref] [date]\n");
    exit(1);
}

if (! preg_match('/^v?\d+\.\d+\.\d+(?:[-+][0-9A-Za-z.-]+)?$/', $version)) {
    fwrite(STDERR, "Version must look like 1.2.3 or v1.2.3.\n");
    exit(1);
}

$version = ltrim($version, 'v');
$range = $previousRef !== '' ? escapeshellarg($previousRef.'..'.$currentRef) : escapeshellarg($currentRef);
$command = "git log --no-merges --pretty=format:%s $range";
$subjects = [];
exec($command, $subjects, $exitCode);

if ($exitCode !== 0) {
    fwrite(STDERR, "Unable to read git log for changelog range.\n");
    exit($exitCode);
}

$sections = [
    'Features' => [],
    'Fixes' => [],
    'Security' => [],
    'Dependencies' => [],
    'Documentation' => [],
    'Other' => [],
];

foreach ($subjects as $subject) {
    $subject = trim($subject);

    if ($subject === '') {
        continue;
    }

    $target = match (true) {
        str_starts_with($subject, 'feat') => 'Features',
        str_starts_with($subject, 'fix') => 'Fixes',
        str_starts_with($subject, 'security'), str_contains(strtolower($subject), 'security') => 'Security',
        str_starts_with($subject, 'deps'), str_contains(strtolower($subject), 'dependenc') => 'Dependencies',
        str_starts_with($subject, 'docs'), str_contains(strtolower($subject), 'readme') => 'Documentation',
        default => 'Other',
    };

    $sections[$target][] = $subject;
}

$entry = ["## [$version] - $date", ''];

foreach ($sections as $title => $items) {
    if ($items === []) {
        continue;
    }

    $entry[] = "### $title";

    foreach (array_values(array_unique($items)) as $item) {
        $entry[] = "- $item";
    }

    $entry[] = '';
}

if (count($entry) === 2) {
    $entry[] = '- No user-facing changes listed.';
    $entry[] = '';
}

$path = __DIR__.'/../CHANGELOG.md';
$existing = file_exists($path) ? (string) file_get_contents($path) : "# Changelog\n";
$marker = "All notable changes to this project are documented in this file.\n";
$newEntry = implode("\n", $entry)."\n";

if (str_contains($existing, "## [$version] -")) {
    fwrite(STDERR, "CHANGELOG.md already contains an entry for $version.\n");
    exit(1);
}

if (str_contains($existing, $marker)) {
    $existing = str_replace($marker, $marker."\n".$newEntry, $existing);
} else {
    $existing = rtrim($existing)."\n\n".$newEntry;
}

file_put_contents($path, $existing);
file_put_contents(__DIR__.'/../VERSION', $version."\n");
