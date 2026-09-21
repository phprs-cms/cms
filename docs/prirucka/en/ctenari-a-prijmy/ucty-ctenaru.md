# Reader accounts

Readers can register on the site. An account lets them save articles for later, read locked articles and – if you set it so – comment. Reader accounts are separate from the accounts of the editorial team: a reader never gets into the administration.

## Turning it on

1. In the main menu open **Extensions**, tick **Readers and locked content** and save.
2. Add the **Reader account** block to the site (**Appearance → Blocks and layout**). It outputs the **Sign in / My account** button. Without the block a reader gets to the sign-in only from the prompt on a locked article, from the **Save for later** link below an article and directly at the address `/ctenar`.
3. Check that the site sends e-mails – both registration and sign-in by link depend on them. See [Mail](../provoz/posta.md).

In the administration the **Readers → Readers** area is added, and in Settings the **Readers and payments** tab. Only an administrator sees both.

## Registration

On the `/ctenar` page, in the part **I am new here**, the reader fills in the **E-mail** and possibly the **Name** (optional) and clicks **Register for free**. If the Newsletter extension is turned on, they can tick **I want to receive the newsletter**.

No password is entered during registration. The reader receives an e-mail with a link where they set a password (at least 8 characters) – this completes the registration and signs them in straight away. The link is valid for 3 days. Someone who registers another person's address therefore does not get to the account. A newsletter subscription chosen during registration is confirmed by the same link.

If someone tries to register an e-mail that already has an account, the site answers the same way as for a new registration. The owner of the address receives an e-mail saying that they already have an account. This way the site does not reveal which addresses are registered.

You stop new registrations with the option **Allow new registrations** in **Settings → Readers and payments**. Existing readers keep signing in.

## Sign-in

A reader has three options:

| Method | How it works |
|---|---|
| **E-mail and password** | the usual sign-in |
| **Sign in with an e-mail link** | the reader fills in only the e-mail and receives a single-use link; valid for 20 minutes |
| **Forgot your password?** | sends a link for setting a new password; valid for 2 hours |

The link from the e-mail does not sign the reader in immediately – it shows a **Sign in** button. This is intentional: some mail programs open links in advance and would use up the single-use link.

After ten wrong passwords within 15 minutes, signing in with a password is temporarily blocked for the given e-mail. Signing in with an e-mail link keeps working.

The sign-in is held by the `phprs_ctenar` cookie for 60 days. It is a technical cookie necessary for signing in. Pages are not served from the cache to a signed-in reader.

## What a reader has in their account

After signing in, the `/ctenar` page shows:

- the e-mail and, where applicable, the date until which the subscription is valid (or the **Get a subscription** button),
- with [payments through Stripe](platby-stripe.md) turned on, the **Subscription** section: the buttons **Subscribe monthly** and **Subscribe yearly**, and for a paying reader **Manage subscription**,
- **Saved articles** – a list with the option **Remove from saved**,
- changing the name and the password (**Change password** requires the current password),
- **Sign out**,
- **Delete account** – after the password is entered, it deletes the account and all data about it; this cannot be undone. A reader with a running subscription through Stripe must cancel it first (**Manage subscription**), otherwise payments would keep being taken from them.

### Saved articles

Below every article there is the link **☆ Save for later**. It takes a reader who is not signed in to the sign-in and brings them back to the article. With a saved article the button changes to **★ Saved – remove** and the link **My saved articles** is added. One reader can save at most 500 articles.

## Readers in the administration

The **Readers → Readers** area shows three counts at the top: **Registered**, **Paying subscribers** and **Locked articles**. Below them there is a search by e-mail or name and a table of the last 300 accounts:

| Column | Content |
|---|---|
| **E-mail** | with the label “e-mail not confirmed” for an unfinished registration |
| **Name** | if the reader filled it in |
| **Registration** | the date of registration |
| **Last** | the date of the last sign-in |
| **Subscription** | the status and the **change…** menu – see [Locked content](zamceny-obsah.md) |
| **Actions** | **Delete** – deletes the account after confirmation |

**Download CSV** saves the file `ctenari.csv` with all confirmed accounts: e-mail, name, date of registration and end of subscription. It contains neither passwords nor saved articles.

An administrator does not see a reader's password and cannot change it. The reader sets a new one themselves through the link from the e-mail.

## Comments for signed-in readers only

The option **Only signed-in readers may comment** (**Settings → Readers and payments**) restricts the discussion to registered readers. A reader then comments under their account. More on the page [Comments](../redakce/komentare.md).

## A request for an export or erasure of data

A reader deletes their account themselves. When they ask you (GDPR), use **Settings → Privacy and cookies**, the section **Reader's personal data request**:

1. Enter their address in the **Reader's e-mail** field.
2. **Download their data** saves the file `osobni-udaje.json` with their comments, newsletter subscription and reader account.
3. **Delete their data**, after confirmation, irreversibly deletes their comments, newsletter subscription and reader account. The message after deletion lists what was removed.

## Related

- [Locked content](zamceny-obsah.md)
- [Newsletter](newsletter.md)
- [Comments](../redakce/komentare.md)
- [Analytics and privacy](../seo-a-ai/mereni-a-soukromi.md)
