# AI assistant

The AI assistant is an optional helper in the article editor. It suggests headlines, a standfirst, a summary, tags and a search engine description, proofreads, describes images and can translate an article into another language version of the site. It only suggests – it never saves or publishes anything by itself.

## Turning it on

The assistant is off after installation. An administrator turns it on:

1. Open **Extensions** in the main menu and tick **AI assistant in the editor**.
2. Further down, in the **AI assistant – key and model** panel, paste your own key into the **Claude API key** field. You create it at console.anthropic.com under API Keys.
3. In the **Model** field choose one of the three options: fast and economical, balanced (recommended), or most thorough.
4. Click **Save settings**.

You pay Anthropic for usage according to actual consumption. The key is stored only on your site and is never printed in the form again – only its ending is shown. The option **Remove saved key** deletes it. Without a key the assistant's buttons do not appear in the editor.

## What the assistant can do

Buttons with the ✦ sign are added next to the field labels in the article form:

| Field | Button | What you get |
|---|---|---|
| **Headline** | **✦ Suggest** | several headlines with different angles |
| **Lead paragraph** | **✦ Suggest** | variants of the standfirst |
| **Article text** | **✦ Proofread** | a list of corrections of spelling, typos, punctuation and typography |
| **Tags** | **✦ Suggest** | tags, preferably from those the site already has |
| **In brief** | **✦ Suggest** | three to five points with the main facts |
| **Search engine description** | **✦ Suggest** | variants of a short description |

The assistant works from the article text. While the article is too short, it asks you to write a bit first.

### Suggestions

After a click a window with suggestions opens. Click **Use** next to the one you choose – the suggestion is inserted into the field and you can edit it further. Suggested tags are added to those already in the field. Nothing is saved until you save the article yourself.

### Proofreading

Proofreading shows a list of corrections: the original wording, the corrected wording and the reason. Leave the corrections you want ticked and click **Fix selected**. The assistant does not change style, facts or meaning. A correction whose passage runs across formatting (a part is bold, for instance) cannot be ticked – fix it by hand.

### Image descriptions

In the **Accessibility check** panel there is a **✦** button next to every image without a description. The assistant looks at the image and suggests an alternative text. The suggestion is inserted into the field; edit it as needed and confirm with Enter. It works only for images uploaded to Media.

### Translating an article

Translation is offered on a site that has the **Language versions of the site** extension turned on and at least one more language version.

1. Save the article in the default language.
2. In the **Article translation** panel click **Translate with the assistant** next to the chosen language and confirm the prompt. The translation may take up to a minute.
3. A new article in the target language opens. It is created as a draft in a section of that language and linked to the original.
4. Read the translation. The assistant may make mistakes in names, numbers and technical terms. Only then publish the article.

The last saved version is translated: headline, standfirst, text, In brief, questions and answers, keywords, and the search engine title and description. Formatting, images and links stay as in the original. The image, template, author, tags and other settings are carried over. Only an article in the default language can be translated, and only once into each language; a section you may write into must exist in the target language.

## What is sent where

Nothing is sent anywhere without a click on an assistant button. Writing, saving and the autosave of unsaved work have nothing to do with the assistant.

On a click, the following goes to Anthropic (Claude):

- for suggestions and proofreading, the headline, standfirst and text of the article you are working on,
- for tags, additionally the list of your site's tags,
- for an image description, the image in question and the beginning of the article as context,
- for a translation, the saved texts of the article listed above.

Data about readers, comments and other articles are not sent. Do not use the assistant for texts that must not leave the newsroom – for example unverified information from a protected source.

## Limits

- Every user can use the assistant at most 60 times per hour. It is a safeguard against unintended spending.
- Use of the assistant is recorded in the **Change log** (an administrator sees it).
- The assistant refuses to translate a very long article.

## Related

- [Article editor](editor.md)
- [Content types](typy-obsahu.md)
