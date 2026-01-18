## Config values

Validate E-Mail values:

- `verify_email_min_length` - Minimal E-Mail length, default: 5.
- `verify_email_max_length` - Maximum E-Mail lenght, default: 100

Zero value (0) means ignore each check.

- `verify_email_use_banlist` - legacy name. Must be: `verify_email_use_custom_validator`. Means: call custom validator.


### Localization

This configuration options planned to be deprecated since version 2.0 because localization data
will be loaded with `setLocalization( <array> )` method. By default, will be used 'en_GB' dictionary.

- `translation_source` - Possible values 'SQL|PHP|INI'.
- `table_translations` - Name of SQL table with all localized messages. Required for `translation_source === SQL`.

