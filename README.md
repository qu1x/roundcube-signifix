# Signifix Plugin for Roundcube Webmail

Formats bytes with metric or binary prefixes using four significant figures.

[![Kilobyte](https://imgs.xkcd.com/comics/kilobyte.png "I would take 'kibibyte' more seriously if it didn't sound so much like 'Kibbles N Bits'.")](https://xkcd.com/394)

Roundcube uses non-standardized prefixes in byte units (i.e., B, KB, MB, GB).
They are neither [metric prefixes] nor [binary prefixes] but are meant to be
binary prefixes. This plugin makes Roundcube use standardized prefixes by
hooking into the byte formatting routine thanks to its powerful plugin API.
By default, binary prefixes are used as quota is reported in units of 1 KiB.

Additionally, this plugin uses a fixed number of four significant figures
providing constant and maximum accuracy while having compactly formatted
strings. It is four figures as they enclose three different decimal mark
positions, covering the three different decimal powers of a particular metric
prefix (e.g, 9.999 MB, 99.99 MB, 999.9 MB), and together with a thousands
separator, covering the four different decimal powers of a particular binary
prefix (e.g., 1.023 MiB, 10.23 MiB, 102.3 MiB, and 1 023 MiB). All three/four
formatted strings have eight/nine characters. For more details and examples,
see the [documentation] of the original Rust crate.

The decimal mark and thousands separator are locale-aware as defined in
`./localization/*.inc`. The thousands separater can be configured to always
be a space, as [internationally] recommended, instead of a comma or a point.

## Configuration

The configuration file `./config.inc.php` is loaded if it exists. Its keys are
documented in `config.inc.php.dist`.

## License

This plugin is licensed under [`Fair`].

> Copyright (c) 2023 Rouven Spreckels <rs@qu1x.dev>
>
> Usage of the works is permitted provided that
> this instrument is retained with the works, so that
> any entity that uses the works is notified of this instrument.
>
> DISCLAIMER: THE WORKS ARE WITHOUT WARRANTY.

[metric prefixes]: https://en.wikipedia.org/wiki/Metric_prefix
[binary prefixes]: https://en.wikipedia.org/wiki/Binary_prefix
[documentation]: https://docs.rs/signifix
[internationally]: https://en.wikipedia.org/wiki/Decimal_separator
[`Fair`]: https://en.wikipedia.org/wiki/Fair_License
