# Scheduling and revisions

Whether and when an article is on the site is decided by two fields in the **Publishing** panel: **Status** and **Publish date**.

## Article statuses

| Status | What it means | Who can set it |
|---|---|---|
| **Draft – in progress** | the article is being worked on; only the editorial team sees it | everyone |
| **For review – done, please check** | the author is done and hands the article over for checking | everyone |
| **Approved – waiting to be published** | checked; it only remains to publish it | whoever may publish |
| **Published** | the article is on the site, or will appear there at the given time | whoever may publish |

An author without the right to publish has only the first two options in the menu. Who has the right to publish is explained on the page [Roles and permissions](../redakce/role-a-opravneni.md); how the editorial team hands an article over is described in [Handover and review](../redakce/predavka-a-korektura.md).

In the article list the statuses correspond to the tabs **All**, **Published**, **Scheduled**, **Drafts**, **For proofreading** and **Approved**.

## Scheduled publishing

1. Set the **Status** to **Published**.
2. Enter a date and time in the future into the **Publish date** field.
3. Save.

The article has the mark *scheduled* in the list, and you find it in the **Scheduled** tab and in the editorial calendar. It appears on the site by itself at the given time. Until then nobody sees it except the signed-in editorial team (with the **Preview** button).

The publish date follows the time zone of the site. An administrator sets it in **Settings → General → Time zone**; the hint next to the field shows what time it is right now according to it.

The **Publish** button, which whoever may publish sees next to an unpublished article in the list, does the same as the **Published** status: if the publish date is in the future, the article is scheduled; otherwise it is published immediately.

### The role of background jobs

A scheduled article is displayed at the given time without any further setup. Publishing is, however, followed by work done by background jobs: refreshing the cached pages, so that the article also appears on the home page, and sending out notifications. By default the jobs are run during visits to the site. On a site that nobody visits at night, a morning article may therefore be delayed. Cron ensures the exact time – the procedure is on the page [Background jobs](../provoz/ulohy-na-pozadi.md).

### Removing from the home page

The field **Remove from the home page on** in the **More settings** panel is optional. After the given date the article disappears from the home page; it stays in its section and in search.

## Updated

For a published article the **Publishing** panel has the option **Mark as updated (readers see “Updated” with today's date)**. Tick it when you add substantial new information to the article, and save. Above the text the reader sees **Updated** with the date and time.

The option applies to one save. When you fix a typo, leave it empty – the update date does not change.

## Revisions

Whenever you save an article with a changed headline, standfirst or text, the previous wording is saved as a version. The last 20 versions are kept. Changes to other fields (section, tags, date) do not create a version.

You find the versions at the bottom of the settings column in the **Version history** panel. Each one has a date, the name of the person who saved the change and a **what changed** link.

### Comparing versions

The **what changed** link opens the **Compare versions** screen. It shows the difference between the chosen version and the current wording separately for the headline, standfirst and text; added and deleted text is distinguished by colour, and their totals are at the top. Changes to formatting and images are not compared.

### Restoring an older version

1. In **Version history** click the date of a version, or click **Load this version into the editor** in the comparison.
2. The older wording is loaded into the editor. A notice at the top says that it takes effect only after saving.
3. Check the text and click **Save**. The current wording is saved by itself as another version, so you can return to it.

If you change your mind about the restore, simply leave the form without saving.

## Editing right on the site

A signed-in user who may edit an article sees an **Edit here** link on its page on the site.

1. Click **Edit here**. Instead of the article, an editor with the fields **Title**, **Lead** and **Text** opens in the site template.
2. Edit the text and click **Save**. You return to the article page. **Cancel** returns without any change.

The same rules apply as in the administration: a published article may be changed only by someone with the right to publish, the previous wording is saved as a version, and the article is locked while being edited. If a colleague has it open right now, their name is displayed and the editor does not open.

Other settings (section, tags, date) cannot be changed this way – the link **All settings in the administration** leads to them.

Site pages (**Content → Pages**) work the same way: the **Edit here** link is seen by whoever has access to the Pages area, and the title and text are edited. Pages do not have a version history.

## Related

- [Article editor](editor.md)
- [Front page and calendar](../redakce/titulni-strana-a-kalendar.md)
