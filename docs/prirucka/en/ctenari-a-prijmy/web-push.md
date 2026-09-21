# Browser notifications (Web Push)

With one click a reader turns on notifications, and their browser then alerts them to every new article – even when they do not have your site open. It works without a third-party service, without registration and without personal data.

## What is needed

| Requirement | Why |
|---|---|
| A site on **HTTPS** | Browsers allow notifications only for secure sites. |
| The PHP extensions **openssl** and **curl** | Notifications are signed with the site's key and sent to the browsers' services. Without them the feature turns itself off and the block is not shown. |
| The **Browser notifications** extension turned on | Main menu **Extensions**. It is off by default. |
| The **Notifications** block on the site | The button with which a reader turns notifications on. |

You do not enter any keys. The site creates the pair of signing keys (VAPID) by itself on first use.

## Turning it on

1. In the main menu open **Extensions**, tick **Browser notifications** and save.
2. Open **Appearance → Blocks and layout** and add the **Notifications** block from the **Readers and newsroom** group to a suitable zone.
3. In the block settings you can change the heading. The default prompt text is **We will let you know when a new article is published.**
4. Open the site in an ordinary browser window and turn notifications on for yourself. After the next article is published, you can verify that they arrive.

## How the reader sees it

The block contains a short text and the **Turn on notifications** button.

1. The reader clicks the button. The browser asks whether to allow notifications for the site.
2. After they are allowed, the block shows **Notifications are turned on in this browser.** and the button changes to **Turn off notifications**.
3. They can be turned off at any time with the same button, or in the browser settings.

Other behaviour:

- In a browser that does not support notifications the block stays hidden.
- When the reader has blocked notifications in the browser, the block advises them: **Notifications from this site are blocked in your browser. Allow them in the site settings next to the address bar.**
- Turning on applies to one browser on one device. The reader turns them on separately on a phone and on a computer.
- On an iPhone and iPad, site notifications work only when the reader adds the site to the home screen. It is a limitation of the system, not of phpRS.

## What is sent and when

A notification goes out by itself after an article is published – immediately on publication as well as at the moment a scheduled article comes out. It contains:

- the headline of the article,
- the beginning of the standfirst (at most 160 characters),
- the featured image of the article, if it has one,
- the site icon,
- a link to the article; clicking the notification opens it.

Rules:

- Every article is announced once. A later edit of a published article does not send a new notification.
- Articles with the option of exclusion from search engines (noindex) and articles with a publication date older than two days are not announced – so the whole archive is not sent out after an outage.
- Locked articles are announced too; a reader without access sees the preview and the prompt after opening.
- A notification cannot be written by hand, nor can a message be sent without an article.
- On a multilingual site all subscribers receive notifications about articles from all language versions.
- A new notification replaces the previous one on the reader's device if they have not dismissed it yet. They do not pile up.

Sending goes in batches of 300 subscriptions as part of the [background jobs](../provoz/ulohy-na-pozadi.md). For a site with thousands of subscribers the sending takes several runs; with cron set up it is smoother.

## Privacy

About a subscription the site stores only the technical address assigned to it by the browser's service (Google, Mozilla, Microsoft, Apple). It stores no name, e-mail or IP address and does not link the subscription to a reader account. Notifications can be sent only to the addresses of these four services.

The message itself goes out without content. After waking up, the reader's browser downloads the headline and the address of the last notification from your site. The browsers' services therefore do not see what you write about.

A subscription that has lapsed – the reader cancelled notifications or uninstalled the browser – deletes itself during the next sending. There is no list of subscriptions in the administration and the number of subscribers is not shown.

## When notifications do not arrive

- Check that the site runs on HTTPS and the extension is turned on.
- The block is not shown at all: `openssl` or `curl` is missing on the server, or the browser does not support notifications.
- Notifications arrive with a delay: the background jobs run only on visits to the site. Set up cron.
- The server must have outgoing HTTPS connections to the browsers' services allowed. Some hosting providers block them.

## Related

- [Blocks and layout](../vzhled/bloky-a-rozvrzeni.md)
- [Background jobs](../provoz/ulohy-na-pozadi.md)
- [Newsletter](newsletter.md)
- [Scheduling and revisions](../psani/planovani-a-revize.md)
