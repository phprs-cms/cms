# Locked content and subscriptions

You can reserve an article for signed-in readers or for subscribers. Others see the headline, the standfirst, the beginning of the text and a prompt. The system neither sells nor bills subscriptions – you accept the payment your own way and record the subscription for the reader by hand.

Everything on this page requires the **Readers and locked content** extension (main menu **Extensions**). Registration and accounts are described on the page [Reader accounts](ucty-ctenaru.md).

## Locking an article

With the extension turned on, the article form has the field **Who can read**:

| Option | Who reads the whole article |
|---|---|
| **All** | anyone (the default) |
| **Signed-in readers only** | everyone who has a reader account and is signed in |
| **Paying subscribers only** | a signed-in reader with a valid subscription |

You can change the option at any time, even for a published article. A member of the editorial team signed in to the administration sees all articles in full.

## What a reader without access sees

- the headline, the standfirst and the featured image,
- a preview: the first few paragraphs of the text,
- a box with a prompt.

The prompt differs according to the lock:

| Lock | Prompt heading | Buttons |
|---|---|---|
| Signed-in readers only | **Sign in to continue reading** | **Sign in**, **Register** (if registrations are allowed) |
| Paying subscribers only | **This article is for subscribers** | **Get a subscription** and, for those not signed in, **I already subscribe – sign in** |

After signing in, the reader returns to the article they came from. Questions and answers are not output from a locked article either.

### Settings

In **Settings → General** expand the section **Readers and locked content**:

| Field | Meaning | Default |
|---|---|---|
| **Locked article preview** | how many paragraphs of the text a reader without access sees; they always see the standfirst; 0 = the standfirst only; at most 10 | 2 |
| **Free articles per month** | a soft paywall, see below; 0 = off; at most 50 | 0 |
| **Where to get a subscription** | where the **Get a subscription** button leads | empty |
| **Prompt text below the preview** | your own sentence in the prompt, at most 300 characters; empty = the default text | empty |

## Where to get a subscription

Enter one of these in the **Where to get a subscription** field:

- a page of your site, for example `/predplatne` – create it in **Content → Pages** and describe the price and the method of payment on it (account number, QR code),
- a payment link starting with `https://`.

Any other form of address is ignored. Until you fill in the field, the **Get a subscription** button is not shown – neither on locked articles nor in the reader's account – and the reader does not know how to become a subscriber. The **Revenue** screen points this out with the message **The place where readers get a subscription is not filled in.**

## Recording a subscription

A subscription is recorded by hand, typically after a payment is received.

1. Open **Readers → Readers** and find the reader by e-mail.
2. In the **Subscription** column choose one of the options **+ 1 month**, **+ 3 months** or **+ 1 year** in the **change…** menu. The change is saved straight away.
3. A message confirms the new date: **The subscription is valid until …**

The extension is counted from the end of the running subscription; for a reader without a subscription, or with an expired one, from today. By choosing repeatedly you therefore add the periods up. The option **cancel** removes the subscription.

The label in the column shows the status: **until** with a date, **expired**, or **none**. A subscription is valid until the end of the stated day. After it passes, the reader loses access to articles for subscribers without any further action; they keep their account. The reader must have an account before you record a subscription for them – ask them to register with the e-mail they paid from or gave with the payment.

## Soft paywall

The field **Free articles per month** lets every visitor read a few locked articles a month without signing in. Only then do they see the prompt.

- Only opened locked articles are counted, each once. Listings are not counted.
- Below an article read for free there is the note **This is article 2 of 5 you can read for free this month.** with a **Sign in** link.
- Once they are used up, the prompt adds: **You have read all 5 free articles this month.**
- A new month starts again from zero.

The counter is in the signed cookie `phprs_cteno` in the reader's browser. Whoever deletes cookies or opens the site in a private window starts again. With a soft paywall this is usual and intentional: the aim is to give loyal readers a friendly nudge, not to lock things airtight.

## Listings, feeds and search engines

| Place | What it contains for a locked article |
|---|---|
| Listings on the site (home page, section, tag, search) | the headline, standfirst and image as with other articles; they have no special lock mark |
| RSS | the headline and standfirst (the same as for all articles) |
| JSON Feed, the Markdown version of an article, the Public API | the standfirst and the preview, never the whole text; the API also returns the flag `zamceno` |
| Newsletter and notifications | the headline and standfirst with a link to the site |

The text of a locked article is cut in a single place before it reaches any template or feed. So it cannot be obtained by a roundabout route.

**Search engines.** The structured data of a locked article carries the value `isAccessibleForFree: False`. A search engine thus knows that this is paid content and not cloaked text. With a hard lock (soft paywall off) a robot sees the same as a reader who is not signed in: the standfirst and the preview. With the soft paywall on, it sees articles in full, because it does not send cookies and every article is its first of the month. If you want the full texts to be indexed, turn on the soft paywall with at least one article a month.

## Related

- [Reader accounts](ucty-ctenaru.md)
- [Support and revenue](podpora-a-prijmy.md)
- [SEO](../seo-a-ai/seo.md)
- [Comments](../redakce/komentare.md)
