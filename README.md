# Games All Day — PHP starter

This is a PHP rewrite starter based on the uploaded Games All Day project. It keeps the original static game assets and provides a PHP-rendered homepage, game pages, About, Contact, Privacy, and Terms pages.

## Import into GitHub
1. Download and unzip this archive on your computer.
2. Create a new GitHub repository, for example `games-all-day-php`.
3. On the repository page choose **Add file → Upload files**.
4. Upload the *contents* of this folder (not the outer folder itself), then commit.

## Deploy to Vercel
This project uses the community `vercel-php` runtime, not an official first-party PHP runtime. Import the repository into Vercel and check the current runtime instructions if deployment reports a runtime configuration error.

## Important notes
- The contact form is only a frontend placeholder and does not send email.
- Privacy/Terms are starter templates. Review and customize them for your actual service before publishing.
- Only one bundled slot-game engine is included in the source project; the game themes change its appearance.
- Adspect integration files are intentionally not included or automatically executed. Add traffic-validation code only after reviewing it, ensuring TLS verification is enabled, and confirming the intended use complies with your hosting and advertising-platform rules.
