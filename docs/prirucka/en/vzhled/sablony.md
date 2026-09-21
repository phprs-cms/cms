# Site templates

The template determines what the site looks like: the header, article listings, the look of an article, the footer. The system comes with three templates. Neither the content, the blocks nor the settings depend on the template, so you can swap it at any time.

## Three built-in templates

| Template | Who it is for | Layout after selection |
|---|---|---|
| **Classic Newspaper** | A serious daily: serif headlines, thin rules, a lead story and column typesetting. | 2 columns |
| **Modern Magazine** | A bold online magazine: a black bar, large headlines and photographs, a grid of cards. | Full width |
| **Minimal** | A personal magazine, blog or newsletter site: one narrow column and calm typography. | 1 column |

A new installation uses Classic Newspaper unless you chose a different one in the installer. None of the templates downloads fonts or scripts from third-party servers.

## Switching the template

The template is changed by an administrator.

1. Open **Appearance → Site identity**.
2. In the **Template** section click the card of a template.
3. Click **Save**. You can look at the result through the **View site** link.

The change takes effect immediately for all readers.

### What switching changes and what it does not

Together with the template, the page layout that belongs to it is set (see the table above). You fine-tune it in the **Blocks and layout** area. Blocks from a zone that the new layout does not have are moved the same way as when you change the layout by hand (see [Page layout](#page-layout)).

What stays unchanged:

- all content – articles, pages, sections, media, comments,
- the blocks and their settings,
- the logo, site icon, main colour, fonts and dark mode from [Site identity](identita-webu.md),
- article templates (Long read, Photo story, Interview) and content types – they work in all three templates,
- page addresses, SEO, analytics and the cookie banner.

A main colour set to **template color** changes with the new template, because every template has its own default colour. The same applies to the font option **From the template**.

## Previewing another template

An administrator can look at a template without turning it on. It is enough to be signed in to the administration and add the `sablona` parameter with the name of the template folder to the site address:

```
https://www.example.com/?sablona=modern-magazine
https://www.example.com/clanek/muj-clanek?sablona=minimal
```

The folders of the built-in templates are called `classic-newspaper`, `modern-magazine` and `minimal`. You can look at a [custom template](vlastni-sablona.md) the same way. Only you see the preview; for a reader who is not signed in, or for an editor, the parameter has no effect. It applies to a single page – after clicking a link you return to the template that is set, so the parameter has to be added again.

## Page layout

The layout says how many columns the page has and which zones for blocks exist in it. It is shared by the whole site.

| Layout | Description | Zones |
|---|---|---|
| **3 columns** | Blocks on the left and on the right, content in the middle. | Header, Left column, Above content, Below content, Right column, Footer |
| **2 columns** | Content with a narrow column of blocks on the right. | Header, Above content, Below content, Right column, Footer |
| **1 column** | A narrow column for comfortable reading, blocks below the content. | Header, Above content, Below content, Footer |
| **Full width** | Content across the full page width, blocks below the content. | Header, Above content, Below content, Footer |

You change the layout in the visual block editor: **Appearance → Blocks and layout**, the buttons next to the **Layout:** label in the top bar. The procedure is on the page [Blocks and layout](bloky-a-rozvrzeni.md).

When the new layout lacks one of the columns, the blocks from it are moved:

- when switching to **2 columns**, from the left column to the right one,
- when switching to **1 column** or **Full width**, from both columns to below the content.

The moved blocks are placed after those that are already in the target zone.

### Which template supports which layout

| Layout | Classic Newspaper | Modern Magazine | Minimal |
|---|---|---|---|
| 3 columns | yes | yes | no columns – everything in one column |
| 2 columns | yes | yes | no columns – everything in one column |
| 1 column | yes (content up to 760 px) | yes (content up to 820 px) | yes |
| Full width | yes | yes | one narrow column |

Minimal always has a single column. If you choose a layout with columns in it, the **Left column** and **Right column** zones still exist, but they are not rendered beside the content – their blocks are listed in the same narrow column, separated by a rule. The **1 column** layout, which is set by itself together with Minimal, therefore suits it best.

In the Classic Newspaper and Modern Magazine templates the side columns appear only on a wide display. On a phone and a narrower tablet everything is stacked: first the content, below it the blocks from the left and right columns. A column with no block in it takes up no space – the content stretches.

## When the template folder is missing

If the template that is set is not in the `layout/` folder (for example because you deleted it), the site is rendered with the Classic Newspaper template. Then choose another one in **Site identity**.

## Related

- [Site identity](identita-webu.md)
- [Blocks and layout](bloky-a-rozvrzeni.md)
- [Custom template](vlastni-sablona.md)
- [Content types](../psani/typy-obsahu.md)
