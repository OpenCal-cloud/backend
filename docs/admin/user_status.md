# Set user status

The `opencal:user:status` console command allows administrators to enable or disable an existing user account in
the system by specifying the action and the user's email address.

## Command Syntax

```
Usage:
  opencal:user:status <action> <email>
```

## Arguments

| Argument | Required | Description                                                  |
|----------|----------|--------------------------------------------------------------|
| `action` | Yes      | The action to perform. Must be either `enable` or `disable`. |
| `email`  | Yes      | The email address of the target user.                        |

Example:

```bash
bin/console opencal:user:status enable user@example.com
```

```bash
bin/console opencal:user:status disable user@example.com
```
