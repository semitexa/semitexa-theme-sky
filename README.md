# Semitexa Theme Sky

`semitexa/theme-sky`

The Sky theme: a brand skin, a base layout and header/footer partials built on the `semitexa/platform-ui` tokens. It extends `theme-base` from `semitexa/theme`.

## Install

Not included by the installer. Add it to an existing project from the project root:

```bash
docker compose run --rm --no-deps --user "$(id -u):$(id -g)" app composer require semitexa/theme-sky
bin/semitexa server:restart
```

## What it provides

- `theme.json` with id `theme-sky`, `extends: theme-base`. As shipped it is active only for the tenant id `__sky_brand_parent__`; to use it elsewhere, give your project theme (`bin/semitexa theme:scaffold`) `"extends": "theme-sky"` in its `theme.json`.
- Templates: `layouts/app.html.twig`, `partials/header.html.twig`, `partials/footer.html.twig`.
- `css/theme.css`.

No PHP services, console commands or routes.

## Documentation

Theme commands: https://semitexa.com/docs/reference/commands-theme

## License

MIT, see [LICENSE](LICENSE).
