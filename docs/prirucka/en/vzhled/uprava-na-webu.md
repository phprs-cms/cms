# Editing right on the site

You can fix a typo in a published article or an outdated sentence on the About us page right where you noticed it. The **Edit here** button opens the text within the site template in the same editor you know from the administration. You do not have to look for the article in the list.

## Who sees the button

The **Edit here** button is at the bottom right of an article page and of a standalone page (About us, Contact…). Only a person signed in to the administration who may edit the given text sees it:

| Content | Who may |
|---|---|
| Page | whoever has access to the **Pages** area (editor, administrator) |
| Published article | whoever has access to the **Articles** area, may publish, and the article is among those they manage |

The same rules apply to articles as in the administration. An author without the right to publish may not change a published article, so they do not see the button there. A user restricted to selected sections sees it only with articles from their sections. Details are on the page [Roles and permissions](../redakce/role-a-opravneni.md).

Readers never see the button. There is no button on article listings, on a section page or on the home page – only with a single article or page.

Editing on the site is meant mainly for published content. But it also works in the preview of a draft (the **Preview** button in the article editor): both **Edit here** and the return after saving stay in the preview.

## Procedure

1. Sign in to the administration and open an article or a page on the site.
2. Click **Edit here**. A form with the editor is shown in place of the text.
3. Edit the text.
4. Click **Save**. You return to the same page and see the result. **Cancel** returns without any change.

## What can be changed

| Content | Fields |
|---|---|
| Article | **Title**, **Lead**, **Text** |
| Page | **Title**, **Text** |

The editor has the same tools as in the administration, including inserting images from Media. The title must not stay empty – otherwise the form comes back with the message **The title must not be empty.**

Everything else – the section, tags, featured image, date, status, search engine settings, page address – is changed through the link **All settings in the administration**, which opens the full form. Unsaved changes from the editor on the site are not carried over when you do so; save first.

Editing on the site is not a page builder. You cannot use it to change the layout, the columns or the blocks around the content. Blocks have their own editor – see [Blocks and layout](bloky-a-rozvrzeni.md).

## Article lock

Two people cannot rewrite the same article at the same time. The lock is shared with the administration:

- by opening the editor you lock the article; as long as you have it open, the lock extends itself,
- a colleague who tries to open the article (on the site or in the administration) sees the message **… has this text open right now. Try again in a moment.** instead of the editor,
- saving releases the lock; after the window is closed without saving, it expires by itself within three minutes.

Pages have no lock.

## Revisions and the record of changes

Saving an article in which the headline, the standfirst or the text has changed creates a revision, just like saving in the administration. You return to a previous wording in the full article form – see [Scheduling and revisions](../psani/planovani-a-revize.md). Pages have no revisions.

Every save is recorded in the **Change log** as “úprava přímo na webu” (editing right on the site) with the headline of the article or the title of the page. An administrator sees the log in **Administration → Change log**.

After a published article is saved, the system also:

- updates the search on the site,
- deletes the page cache, so readers see the correction straight away,
- sends a notification to search engines through IndexNow, if you have it turned on (see [SEO](../seo-a-ai/seo.md)).

The publication date does not change and the notification to readers (Web Push, webhook) is not sent again.

## Related

- [Article editor](../psani/editor.md)
- [Scheduling and revisions](../psani/planovani-a-revize.md)
- [Blocks and layout](bloky-a-rozvrzeni.md)
- [Roles and permissions](../redakce/role-a-opravneni.md)
