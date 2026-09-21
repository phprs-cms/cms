# Translating content

On a multilingual site every language version has its own sections, articles and pages. It is not one article with two texts, but two separate articles that you can link as translations. Each thus has its own address, publication date, comments and search engine settings – and one version can have content that the other does not.

The prerequisite is the **Language versions of the site** extension turned on and at least one other version – see [Site languages](jazyky-webu.md).

## Sections determine the language

The language is not chosen for an article but for a section. An article takes it over from the section you save it into.

1. Open **Content → Sections** and create a new section.
2. In the **Language version** field choose the language, for example **English – /en/**. Articles in the section belong to this language version of the site.
3. In the **Is a translation of** field select the corresponding section in the default language, if it exists. The language switcher will then lead from the section Sport directly to Sports, and search engines get hreflang tags. Otherwise leave **– not a translation –**.
4. Save.

Start by creating, for every other version, sections corresponding to the main ones. The navigation, the Sections block and the section selection on the site show only the version's own sections in every version.

When you change the language of an existing section, all its articles move to the new version too.

## Translating an article by hand

1. Create a new article and put it into a section of the target language.
2. Write or paste the translated headline, standfirst and text. Do not forget **In brief**, questions and answers, the **Search engine title** and the **Search engine description**.
3. Expand the section **Article translation** and enter the address or number of the original article in the field **Original in the default language**. It is enough to paste the whole address of the article from the browser.
4. Save and publish like any other article.

The field **Original in the default language** is filled in only for an article in another language version. The original must be an article in the default language; the system discards any other value on saving. If you have three versions, link both translations to the same original – the switcher then leads between all three.

For an article in the default language the **Article translation** section shows the row **Language versions of the article**: for every language either the link **open translation** or the note **no translation yet**.

### What linking does

- The language switcher on an article leads directly to its translation, not to the home page of the other version.
- hreflang tags pointing to all published versions of the article are inserted into the page head.
- Linking has no effect on the content, comments, rating or read counts – every article has its own.

An unpublished translation appears neither in the switcher nor in the tags.

## Translating with the AI assistant

With the **AI assistant in the editor** extension turned on and a key entered, the **Article translation** section offers the button **Translate with the assistant** for every language without a translation.

1. Save the article in the default language – the last saved version is translated.
2. Click **Translate with the assistant** next to the chosen language and confirm. The translation can take up to a minute.
3. A new article in the target language opens: a draft, linked to the original.
4. Read the translation and correct it. The assistant can make mistakes in names, numbers and technical terms. Then publish the article.

Rules:

- Only an article in the default language can be translated, and only once into each language. If a translation already exists, the button takes you to it.
- The target section is the counterpart of the original's section (the **Is a translation of** field). When it does not exist, the first section of the target language that you may write into is used. Without such a section the translation is not created.
- The headline, standfirst, text, In brief, questions and answers, keywords, and the search engine title and description are translated. Formatting, images and links stay as in the original.
- The image, article template, author, co-authors, tags, lock and other settings are carried over.
- The article address is created from the translated headline.

What is sent where and what limits the assistant has is described on the page [AI assistant](../psani/ai-asistent.md).

## Pages

Pages (About us, Contact, Privacy policy) have the same two fields in the form as sections: **Language version** and **Is a translation of**. A page is shown only in its language version – in the navigation, in the Pages block and at its address (`/en/about`). With **Is a translation of** filled in, the language switcher leads from one to the other.

The assistant does not translate pages. Translate them by hand.

## News briefs and polls

Both a news brief and a poll have the **Language version** field and are shown only in it. In a language that has no active poll, the latest open poll of that language is shown.

## Tags

Tags are shared by all versions and are not translated. A tag page lists only the version's own articles in every version. The Tags block shows only tags that have at least one article in the given version. For articles in another language you can create separate tags in that language.

## Blocks

System blocks (Sections, Articles from a section, Most read, Archive, Authors, Pages, News briefs, Poll) list the content of the version currently displayed by themselves. The block heading and the content of the **Text** and **Menu** blocks are not translated. The procedure:

1. In the visual block editor open the block settings and expand **When and where to show the block**.
2. In the **Language version** field choose the default language.
3. Add a second block of the same type, give it a heading and content in the second language and choose the second language in the **Language version** field.

The option **in all languages** suits blocks without text, for example Advertising, or blocks with the heading turned off.

## Newsletter and notifications

- **Newsletter:** a subscriber belongs to the version on which they signed up. An issue is always in one language and only the subscribers of the same language receive it; the automatic newsletter creates a separate issue for every language. The texts of the e-mail are in the language of the issue. Details: [Newsletter](../ctenari-a-prijmy/newsletter.md).
- **E-mails to readers** (subscription confirmation, registration, sign-in link) arrive in the language of the version on which the reader performed the action.
- **Browser notifications** are not divided by language. A subscriber receives notifications about articles from all versions.
- **Newsroom notifications** (handover for review, publication, return) go to members of the editorial team in the language of their administration.

## Related

- [Site languages](jazyky-webu.md)
- [AI assistant](../psani/ai-asistent.md)
- [Sections, tags and series](../psani/rubriky-stitky-serialy.md)
- [Blocks and layout](../vzhled/bloky-a-rozvrzeni.md)
