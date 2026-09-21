# AI search engines

Some readers today do not search in a search engine but ask an assistant – ChatGPT, Claude, Perplexity, Gemini. These assistants crawl the web much like search engines and link to sources in their answers. Optimising for them is called GEO; that is why the tab in the settings is called **SEO and GEO**.

phpRS gives the publisher two things: the decision whether to let AI robots onto the site, and – if so – materials from which they read the site easily and correctly. All the options are in **Settings → SEO and GEO**.

## Allow or disallow

The field **AI search engines and assistants** in the **Site visibility** section:

| Option | What it does |
|---|---|
| **allow – content may appear in AI answers with a link to the site** | The default. The robots of AI services have the same rules as the others. |
| **disallow – ChatGPT, Claude, Perplexity, Gemini and others** | A disallow rule for the whole site for known AI robots is added to `robots.txt`. |

The ban concerns these robots: GPTBot, OAI-SearchBot, ChatGPT-User, ClaudeBot, Claude-User, anthropic-ai, PerplexityBot, Perplexity-User, Google-Extended, Applebot-Extended, CCBot, Bytespider, Amazonbot, meta-externalagent and cohere-ai. The list is part of the system; you add another robot with a custom rule (see below).

Three things that are good to know:

- **It is not a technical protection.** `robots.txt` is a request. Well-behaved robots respect the rule; a robot that ignores it still gets onto the site.
- **Ordinary search does not change.** Google-Extended and Applebot-Extended control only the use of content for AI. The robots of ordinary search, Googlebot and Bingbot, are not on the list.
- **The ban is not retroactive.** It does not remove from the services what they have read earlier.

How to decide is an editorial and business question. Allowing brings mentions and links in AI answers. A ban makes sense for a publisher who does not want their texts to be used for training models, or an AI answer to replace a visit to the site. Paid content is protected independently of this option – from a locked article a robot gets only the preview, see [Locked content](../ctenari-a-prijmy/zamceny-obsah.md).

Write custom rules, for example a ban on a single robot, into the **Custom robots.txt rules** field in the **Advanced** section:

```
User-agent: Bytespider
Disallow: /
```

## The llms.txt file

The option **llms.txt file** (default: on) exposes a guide to the site for language models at the address `/llms.txt`, following the llmstxt.org proposal. It is plain text in Markdown format that the system puts together by itself:

- the site name and the **Site description** from **Settings → General**,
- a list of the displayed sections with links and descriptions,
- the 30 latest articles with a link and the beginning of the standfirst (without articles with the option **Hide from search engines (noindex)**).

You do not maintain anything in it by hand. It does pay, though, to have an apt **Site description** and section descriptions – these are the sentences from which a model forms its first idea of the site.

On a multilingual site every version has its own file (`/en/llms.txt`) with its sections and articles. If the clean version of articles is turned on as well, the links in the file lead straight to it.

After the option is turned off, the address `/llms.txt` ceases to exist.

## Clean version of articles (.md)

The option **Clean version of articles (.md)** (default: on) makes every published article available as plain text in Markdown format too. The address is created by adding `.md` after the article address:

```
https://www.example.com/clanek/muj-clanek
https://www.example.com/clanek/muj-clanek.md
```

The clean version contains:

- the headline,
- the author, the dates of publication and update, the section and a link to the original article as the source,
- the **In brief** points, if the article has them,
- the standfirst and the text converted to Markdown.

It contains no navigation, blocks, ads, comments or scripts. A model thus gets the text itself with the source stated and does not have to pick it out of an HTML page.

Other properties:

- The article page links to the clean version in the head (`rel="alternate"`, type `text/markdown`), so tools find it by themselves.
- The clean version is sent with the header `X-Robots-Tag: noindex`. It does not appear in the results of ordinary search and no duplicate content arises.
- A locked article has only the standfirst and the preview in the clean version, the same as on the site.
- Briefs have no clean version.

The clean version is useful for people too: for archiving, for a partner site taking over a text, or for reading in a terminal.

## What helps when writing

A clearly structured text with the author and date stated is easier to read by machine. The editor offers three tools for this; all are described in the Writing chapter:

- **In brief** – three to five points with the main facts. They are shown above the article and also go into the clean version.
- **Questions and answers** – they are listed below the article and go into the structured data as `FAQPage`.
- **Mark as updated** – the reader sees “Updated” with a date on the article; the date of the change is carried over into the structured data too.

Structured data (author, publisher, dates, breadcrumb navigation) are added by the system by itself – see [SEO](seo.md).

## Public API

The **Public API** extension (main menu **Extensions**; default: off) serves for machine reading of content by your own application. It is a read-only JSON API at the addresses `/api/clanky`, `/api/clanky/<address>` and `/api/rubriky`. It returns locked articles with the preview and the flag `zamceno`.

## Related

- [SEO](seo.md)
- [Claude connection](napojeni-na-claude.md)
- [Article editor](../psani/editor.md)
- [Locked content](../ctenari-a-prijmy/zamceny-obsah.md)
