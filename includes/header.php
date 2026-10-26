<?php
$pageTitle = $pageTitle ?? 'Games All Day';
$description = $description ?? 'Play free browser games at Games All Day.';
$activePath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
  <meta name="description" content="<?= htmlspecialchars($description, ENT_QUOTES, 'UTF-8') ?>">
  <link rel="icon" href="/logo.png">
  <link rel="stylesheet" href="/style.css">
</head>
<body>
<header class="site-header">
  <a class="brand" href="/" aria-label="Games All Day home"><img src="/logo.png" alt="" onerror="this.style.display='none'"><span>Games <b>All Day</b></span></a>
  <nav><a href="/">Games</a><a href="/about">About</a><a href="/contact">Contact</a></nav>
</header>
<main class="site-main">
