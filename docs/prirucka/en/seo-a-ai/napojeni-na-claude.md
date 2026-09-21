# Claude connection

The **Claude connection** extension opens the site to the Claude assistant through the MCP protocol (Model Context Protocol). On your instruction Claude can then read and write articles, create sections, manage blocks and build custom site templates – with the rights of your account and only within the limits described below.

Not to be confused with the [AI assistant](../psani/ai-asistent.md). That is part of the article editor and only suggests texts. The Claude connection works the other way round: Claude runs on your side (the Claude app, Claude Code) and the site is a tool for it.

## What the connection can and cannot do

| Area | Tools | Who |
|---|---|---|
| Overview | information about the site, the role and permissions of the signed-in user | everyone |
| Articles | list, load, create, edit | by role, see below |
| Sections | the section tree; creating a section | reading everyone; creating editor and administrator |
| Media | a list of the most recently uploaded images with addresses | everyone |
| Blocks | a list of blocks by zone; creating and editing a block | administrator |
| Templates | list, copying a built-in template, reading and saving a file of a custom template, switching the site to a template | administrator |

**The boundary is fixed: only content and custom templates are changed through the connection.** No tool can:

- change the system code, files in `system/`, `admin.php`, `index.php` or the built-in templates,
- write a file anywhere other than into the folder of a custom template `layout/<name>/` – and there only `.php` and `.css` files up to 300 kB,
- run code, a command or a database query,
- manage users, passwords, tokens or permissions,
- change Settings, extensions, mail, backups or updates,
- upload files to Media, delete articles, read comments or the data of readers and subscribers.

Every PHP file saved into a template goes through a check that allows only the output of the data passed in. It refuses work with files, the network, processes and the database, and the file is not saved. Details are on the page [Custom template](../vzhled/vlastni-sablona.md).

If you are missing a feature in the system, Claude will not add it through the connection. This is intentional: the system is meant to be the same for everyone and updatable. Suggestions belong to the authors of phpRS – see [How to contribute](../pro-vyvojare/jak-prispet.md).

## Permissions follow the role

Claude acts in your name and with your rights. The same applies to it as to you in the administration:

- An **Author** sees and edits only their own articles. Without the right to publish they save only drafts and cannot change a published article.
- A user restricted to selected sections works only with articles from these sections and cannot save anything into another section.
- An **Editor** works with all articles and may create sections.
- An **Administrator** additionally has blocks and templates.

A new article is always created as a draft. Claude may publish it only with an account that has the right to publish, and only on your explicit instruction. When an article is edited, the previous version is saved in the revision history. After saving, Claude returns the preview address and a link to editing in the administration.

## Turning it on and the token

1. An administrator opens **Extensions** in the main menu, ticks **Claude connection** and saves. The extension is off by default.
2. Every user who wants to use the connection opens **My account**, the section **Claude connection**.
3. They fill in the **Name of the new token** – for example “Claude on my laptop” – and click **Create token**.
4. The token is shown **only once**. Copy it straight away. Only its hash is stored in the database, so it cannot be displayed later – only revoked and a new one created.

Below the token there is a ready-made command for Claude Code:

```
claude mcp add --transport http phprs https://www.example.com/mcp --header "Authorization: Bearer phprs_…"
```

In the Claude app add a custom connector with the address `https://www.example.com/mcp` and the same `Authorization` header.

The connection address is always the site address ending in `/mcp`. It works only over HTTPS (development on `localhost` is the exception) and only with the extension turned on. It stays accessible in maintenance mode too.

### Managing tokens

The list of tokens in **My account** shows, for each one, the name, the date of creation and when it was last used. **Revoke token** invalidates it immediately.

When you change your password, the option **also revoke connection tokens (Claude, API)** is ticked in advance. If you are changing the password because you suspect misuse, leave it ticked and then create the connection again. The token of a blocked user does not work.

## The record in the Change log

Every action that changes something – creating and editing an article, a section or a block, creating a template, saving a template file, switching the template – is recorded in **Administration → Change log** as the module `claude` with the name of the tool and the headline, name or number of the item concerned. It is recorded under the user to whom the token belongs. The creation of a token is recorded too.

Reading (lists, loading an article or a template file) is not recorded.

## Security recommendations

- **Protect the token like a password.** It works without a password and without two-factor sign-in. Do not paste it into shared documents, repositories or a chat.
- **A separate token for every device and purpose.** If you lose a laptop, you revoke one and the others keep working. Delete unused tokens.
- **Use an account with the lowest role needed.** For writing articles an author or editor account is enough. Create an administrator token only for work on blocks and templates and revoke it afterwards.
- **Read what Claude has done.** New articles are drafts – read them before publishing. Look at a template in the preview `/?sablona=name` before switching to it.
- **Check the Change log regularly**, especially after work with templates.
- After 20 invalid attempts to sign in with a token from one address within 15 minutes, the server temporarily refuses further attempts from that address.

When you are not using the connection, turn the extension off. The `/mcp` address then stops responding and the tokens stay stored for the next time it is turned on.

## Related

- [Custom template](../vzhled/vlastni-sablona.md)
- [AI assistant](../psani/ai-asistent.md)
- [Security](../provoz/bezpecnost.md)
- [Roles and permissions](../redakce/role-a-opravneni.md)
