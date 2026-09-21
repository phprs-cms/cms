# Payments with Stripe

With the Stripe service a reader pays for a subscription by card themselves, and the site turns it on, extends it and, after cancellation, lets it run out – all by itself. You do not have to watch your bank statement or record anything by hand.

The payment takes place on Stripe's pages, not on your site. phpRS never sees the card number and does not store it. Recording a subscription by hand, described in [Locked content](zamceny-obsah.md), keeps working alongside the payments – it is handy for payments by bank transfer or gift subscriptions.

You need the **Readers and locked content** extension, an account at [stripe.com](https://stripe.com) and a site at an `https://` address. The **Site address** in **Settings → General** must be filled in correctly – the addresses to which Stripe returns the reader after payment are built from it.

## How it works

1. A signed-in reader clicks **Subscribe monthly** or **Subscribe yearly** in their account.
2. The site redirects them to Stripe's payment page (Checkout). There they enter the card and pay.
3. Stripe sends your site a signed message (a webhook) that the payment has gone through. The site verifies the message and records the subscription for the reader.
4. Before the end of the period Stripe takes the next payment by itself and the site extends the subscription.
5. With the **Manage subscription** button the reader gets to the Stripe customer portal. There they change the card, download receipts or cancel the subscription.

A subscription is recorded only on the basis of a verified message from Stripe, never on the basis of what the reader's browser sends. After paying, the reader therefore returns to the site with a message that the subscription will turn on in a moment – the message from Stripe usually arrives within a few seconds.

With payments turned on, the **Get a subscription** button on locked articles leads to the reader's account. Someone who is not signed in first signs in or registers (registration is still without a password, by a link from an e-mail) and finds the payment buttons right after signing in. The address from the **Where to get a subscription** field is not used at that point.

## Setting up step by step

Try everything out first in Stripe's test mode (see the section A dry run below). The procedure is the same in both modes; only the keys differ.

### 1. Product and prices

1. In Stripe open the **Product catalog** and create a product, for example “Magazine subscription”.
2. Add a recurring price (**Recurring**) to it with the period **Monthly**, and possibly a second one with the period **Yearly**. One of them is enough.
3. Copy the ID of each price (**Copy price ID**). It starts with `price_`.

You manage prices, the currency, VAT and trial periods only in Stripe. phpRS neither creates nor changes prices – Stripe charges the amount according to the price whose ID you enter.

### 2. Restricted key

In **Developers → API keys** create a restricted key (**Create restricted key**). It starts with `rk_`. Give it only these permissions and leave all the others at **None**:

| Permission in Stripe | Level | What it is for |
|---|---|---|
| **Checkout Sessions** | Write | creating the subscription payment |
| **Customer portal** | Write | the link to subscription management |
| **Subscriptions** | Read | loading the status after the return from subscription management |

The site makes only these three calls. If Stripe refused one of them because of a missing permission, the reader sees a general apology and you find the exact name of the missing permission in **Settings → System status** in the error log. A full secret key (`sk_…`) can be used too, but a restricted one is safer: if it leaked, it cannot be used to refund money or read customer data.

### 3. Webhook

1. In the phpRS administration open **Settings → Readers and payments**, section **Payments with Stripe**, and copy the **Webhook address**. It has the form `https://your-site.com/platba/stripe`.
2. In Stripe open **Developers → Webhooks**, choose **Add endpoint** and paste the address.
3. Turn on exactly these events:
   - `checkout.session.completed`
   - `invoice.paid`
   - `customer.subscription.updated`
   - `customer.subscription.deleted`
4. After saving, copy the **Signing secret**. It starts with `whsec_`.

### 4. Customer portal

Activate the portal in **Settings → Billing → Customer portal**. Allow at least cancelling the subscription and changing the payment method in it. We recommend cancelling **at the end of the period**: the reader then finishes reading what they have paid for. Without an activated portal the **Manage subscription** button ends with an apology.

### 5. Filling it in in phpRS

In **Settings → Readers and payments**, in the **Payments with Stripe** section, fill in:

| Field | What to enter |
|---|---|
| **Secret key** | the restricted key `rk_…` (or `sk_…`) |
| **Webhook secret** | `whsec_…` from step 3 |
| **Monthly price**, **Yearly price** | the price IDs `price_…`; an empty field = the period is not offered |
| **Monthly price label**, **Yearly price label** | the text next to the button, for example “€5 a month”; optional |

After saving, the key and the secret are no longer output – in the field you see only the last four characters. The site does not save a value that does not have the form of a key, a secret or a price ID, and says so. Payments are on as soon as the key, the secret and at least one price are filled in; the label next to the section heading changes to **on**.

The price label is only text. The real amount is determined by the price in Stripe – when you change it there, adjust the label too.

## A dry run

Stripe has a test mode with its own keys (`rk_test_…`, `sk_test_…`), its own prices and its own webhook. Set everything up in it first:

1. Switch Stripe to test mode and go through steps 1–5 with the test values.
2. Register on the site as a reader and click **Subscribe monthly**.
3. On the payment page enter the test card `4242 4242 4242 4242`, any future expiry date and any CVC code.
4. After the return, **subscription until** with a date should appear in the reader's account within a moment, and the label **Stripe: paying** in **Readers → Readers**.
5. Try **Manage subscription** and cancelling – the label changes to **Stripe: will not renew**.

When the subscription does not turn on, look at the delivery of messages in **Developers → Webhooks** in Stripe. A 400 response means a wrong webhook secret (or a secret from the other mode), a 404 response a wrong address. For live operation then replace the key, the secret and the price IDs with the live ones – test and live values cannot be mixed.

## What happens next

| Situation | What Stripe does | What the site does |
|---|---|---|
| A regular payment for the next period | takes the amount and sends `invoice.paid` | moves **subscription until** to the end of the paid period plus one extra day; records the payment |
| A payment fails | tries again for several days and writes to the reader (according to the settings in Stripe) | the label **Stripe: payment failed**; the reader sees a prompt in their account to check the card; access ends on the **subscription until** day |
| The reader cancels the subscription | lets it run until the end of the period | the label **Stripe: will not renew**; access stays until the end of the paid period |
| The subscription ends | sends `customer.subscription.deleted` | the label **Stripe: cancelled**; the reader can subscribe again |

The site never shortens the **subscription until** date: the later of the date recorded by a payment and the date you recorded by hand applies. The extra day covers the moment between the end of the period and the taking of the next payment. Money refunded in Stripe does not remove access by itself – if need be, cancel the reader's subscription by hand in **Readers → Readers**.

The site records only a subscription that the reader started with the button on the site. A subscription created by hand in Stripe, or any other sale through the same Stripe account, has no effect on readers' access.

Whoever already pays through Stripe does not see the buttons for a new subscription, so they cannot start a second one next to the first. An account with a running subscription cannot be deleted until the reader cancels it.

### Deleting a reader

phpRS does not call Stripe when deleting. When you delete a reader with a running subscription in the administration (or their data through **Privacy and cookies → Reader's personal data request**), the site warns you that the subscription in Stripe keeps running. Cancel it in Stripe for the given customer, otherwise further payments will be taken from them.

## Overview in the administration

- **Readers → Readers** shows the subscription date for every reader and below it the status from Stripe (**paying**, **will not renew**, **payment failed**, **cancelled**), or the note **entered manually**. The **Paying via Stripe** tile is the number of running subscriptions.
- **Readers → Revenue** adds, on the **Subscription** card, the number of those paying through Stripe and the sum of payments in the last 30 days, separately for every currency.

## What the site stores

| Where | What |
|---|---|
| with the reader | the customer and subscription IDs in Stripe, the subscription status, the **subscription until** date |
| the payments table | the ID of the message from Stripe, the reader, the amount, the currency, the date |

The card number, the billing address and any other payment details are not stored – they stay in Stripe. An export of a reader's personal data includes their payments too (date, amount, currency). After a reader is erased, the payment records remain for accounting reasons, but without a link to the person. The customer's data in Stripe are not deleted from here; delete them there.

Neither the key, the webhook secret nor the payments are part of the [site export](../zaciname/import-z-wordpressu.md). They are in the database backup, like the other settings – so keep backups safe.

## Taxes and receipts

phpRS does not issue invoices, does not calculate VAT and does not deal with sales records. Payment receipts are sent and made available to readers by Stripe (you set this in **Settings → Billing**); VAT can be calculated by Stripe Tax. You as the publisher are responsible for correct taxation, the terms and conditions, the notice about automatic renewal and the handling of complaints. Stripe deducts its fees from every payment; you find their amount in its price list.

## Related

- [Locked content and subscriptions](zamceny-obsah.md)
- [Reader accounts](ucty-ctenaru.md)
- [Support and revenue](podpora-a-prijmy.md)
- [Analytics and privacy](../seo-a-ai/mereni-a-soukromi.md)
