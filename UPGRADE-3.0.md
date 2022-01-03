# Upgrade from 1.x/2.x to 3.0

### Updated Requirements
1. **PHP >= 7.2** _(was >= 7.1 before)_
1. **Symfony >= 4.4** _(was >= 4.3 before)_
2. **FOSRestBundle >= 3.0** _(was 2.x before)_

### Steps
1. Add the following new setting to your `config/packages/fos_rest.yaml`:

```yaml
# config/packages/fos_rest.yaml
fos_rest:
    exception:
        serializer_error_renderer: true
```

Done.
