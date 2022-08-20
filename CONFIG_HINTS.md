## Config values

### Localization

This configuration options planned to be deprecated since version 2.0 because localization data
will be loaded with `setLocalization( <array> )` method. By default, will be used 'en_GB' dictionary.

- `translation_source` - Possible values 'SQL|PHP|INI'.
- `table_translations` - Name of SQL table with all localized messages. Required for `translation_source === SQL`.

