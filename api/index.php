<?php
declare(strict_types=1);
$games = require __DIR__ . '/../includes/games.php';
$path = trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/', '/');
$segments = $path === '' ? [] : explode('/', $path);
$slug = null;
$page = $path === '' ? 'home' : $path;

if (count($segments) === 2 && $segments[0] === 'play') {
    $slug = $segments[1];
    $page = 'play';
}
$game = null;
if ($slug !== null) {
    foreach ($games as $candidate) {
        if ($candidate['slug'] === $slug) { $game = $candidate; break; }
    }
    if (!$game) { http_response_code(404); $page = '404'; }
}
$known = ['home','about','contact','privacy','terms','signup','play','404'];
if (!in_array($page, $known, true)) { http_response_code(404); $page = '404'; }

$pageTitle = match ($page) {
    'home' => 'Games All Day — Free Browser Games',
    'about' => 'About | Games All Day',
    'contact' => 'Contact | Games All Day',
    'privacy' => 'Privacy Policy | Games All Day',
    'terms' => 'Terms of Use | Games All Day',
    'signup' => 'Sign Up | Games All Day',
    'play' => $game['name'] . ' | Games All Day',
    default => 'Page Not Found | Games All Day'
};
require __DIR__ . '/../includes/header.php';

if ($page === 'home') {
?>
<section class="hero">
  <div class="hero-copy"><span class="eyebrow">YOUR EVERYDAY ARCADE</span><h1>Good games.<br><span>Any time.</span></h1><p>Jump into quick, colourful browser games. Pick a favourite and play right away.</p><a class="button" href="#games">Explore games <span>↓</span></a></div>
  <div class="hero-art"><img src="/banner.jpg" alt="Games All Day arcade artwork"></div>
</section>
<section class="section" id="games"><div class="section-heading"><div><span class="eyebrow">THE GAME ROOM</span><h2>Pick your next game</h2></div><p>Free to play in your browser</p></div>
<div class="game-grid">
<?php foreach ($games as $g): ?>
<a class="game-card" href="/play/<?= rawurlencode($g['slug']) ?>" style="--from:<?= htmlspecialchars($g['from']) ?>;--to:<?= htmlspecialchars($g['to']) ?>;--accent:<?= htmlspecialchars($g['accent']) ?>">
  <div class="game-image"><img src="<?= htmlspecialchars($g['image']) ?>" alt="<?= htmlspecialchars($g['name']) ?>" loading="lazy"><span class="play-badge">▶ Play</span></div>
  <div class="game-info"><div><h3><?= htmlspecialchars($g['name']) ?></h3><p><?= htmlspecialchars($g['tagline']) ?></p></div><span class="arrow">↗</span></div>
</a>
<?php endforeach; ?>
</div></section>
<?php
} elseif ($page === 'play') {
?>
<section class="play-page"><div class="play-top"><a href="/">← Back to games</a><h1><?= htmlspecialchars($game['name']) ?></h1><span><?= htmlspecialchars($game['tagline']) ?></span></div>
<div class="game-frame"><iframe src="/games/slot/index.html?theme=<?= rawurlencode($game['slug']) ?>" title="<?= htmlspecialchars($game['name']) ?>" allowfullscreen></iframe></div>
<p class="muted">This game runs in your browser. Use the game controls to play.</p></section>
<?php
} elseif ($page === 'about') {
?>
<section class="text-page"><span class="eyebrow">ABOUT US</span><h1>A little fun, whenever you need it.</h1><p>Games All Day is a simple home for casual browser games. Browse the collection, choose a game, and start playing without installing an app.</p><p>We aim to keep the experience easy to use across desktop and mobile screens.</p></section>
<?php
} elseif ($page === 'contact') {
?>
<section class="text-page"><span class="eyebrow">GET IN TOUCH</span><h1>Contact us</h1><p>Have a question, suggestion, or issue with a game? Send us a message using the form below.</p>
<form class="contact-form" method="post" action="/contact"><label>Your name<input name="name" required maxlength="100"></label><label>Email address<input type="email" name="email" required maxlength="200"></label><label>Message<textarea name="message" rows="6" required maxlength="5000"></textarea></label><button class="button" type="submit">Send message</button>
<?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?><p class="notice">Thanks for reaching out. This starter project does not send email yet; connect a mail service before launch.</p><?php endif; ?></form></section>
<?php
} elseif ($page === 'privacy') {
?>
<section class="text-page"><span class="eyebrow">LEGAL</span><h1>Privacy Policy</h1><p><strong>Last updated:</strong> <?= date('F Y') ?></p><p>This website provides browser-based games. This starter policy is a template and should be reviewed to reflect the services, analytics, advertising, cookies, and data collection actually used on your site.</p><h2>Information you provide</h2><p>If you contact us, you may provide your name, email address, and message. Do not submit sensitive personal information.</p><h2>Cookies and third-party services</h2><p>Third-party services, hosting providers, analytics, or advertising partners may process technical information according to their own policies. Add details here before publishing.</p><h2>Contact</h2><p>Use the Contact page for privacy questions.</p></section>
<?php
} elseif ($page === 'terms') {
?>
<section class="text-page"><span class="eyebrow">LEGAL</span><h1>Terms of Use</h1><p><strong>Last updated:</strong> <?= date('F Y') ?></p><p>By using this website, you agree to use it lawfully and not to disrupt the site or attempt unauthorized access.</p><h2>Games and availability</h2><p>Games are provided for entertainment and may change or become unavailable. We make no guarantee that the website will always be uninterrupted or error-free.</p><h2>Third-party content</h2><p>Some content or services may be provided by third parties and may be subject to their own terms.</p><h2>Updates</h2><p>These terms should be reviewed by the site owner and updated to reflect the actual service and applicable law before launch.</p></section>
<?php
} elseif ($page === 'signup') {
?>
<section class="text-page"><span class="eyebrow">COMING SOON</span><h1>Stay in the loop</h1><p>Account registration is not connected in this starter version. You can add an authentication provider when you are ready.</p><a class="button" href="/">Back to games</a></section>
<?php
} else {
?>
<section class="text-page"><span class="eyebrow">404</span><h1>Page not found</h1><p>Sorry, we couldn't find that page.</p><a class="button" href="/">Return home</a></section>
<?php } require __DIR__ . '/../includes/footer.php'; ?>
