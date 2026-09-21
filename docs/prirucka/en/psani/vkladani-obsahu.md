# Embedding video and social media posts

You embed a video, a podcast or a social media post in an article by putting its address on a line of its own. You do not need any embed code.

## Procedure

1. Copy the address of the video or post in your browser.
2. Start a new paragraph in the article text and paste the address into it. There must be nothing else in the paragraph.
3. Save the article. The address stays visible in the editor; the player or post is created from it only on the site. You check the result with the **Preview** button.

An address in the middle of a sentence stays plain text or a link.

## Supported services

| Service | Which address |
|---|---|
| YouTube | a video, Shorts and live streams (`youtube.com/watch?v=…`, `youtu.be/…`) |
| Vimeo | the address of a video (`vimeo.com/123456789`) |
| Spotify | an episode, show or track (`open.spotify.com/episode/…`) |
| X (Twitter) | a single post (`x.com/account/status/…`) |
| Instagram | a post, reel or video (`instagram.com/p/…`, `/reel/…`) |
| Facebook | a post, video or photo (`facebook.com/…/posts/…`) |
| TikTok | a video (`tiktok.com/@account/video/…`) |
| Mastodon | a post on any server (`https://server/@account/1234567890`) |

Addresses of other services stay in the article as you wrote them.

## Loaded only after a click

In place of the video the reader first sees a button – **Play video**, **Play audio** or **Show the post from …** – and below it the name of the service the content is loaded from. The content from the external service is really loaded only after the click.

There are two reasons for this:

- **Privacy.** Until the reader clicks, the external service does not learn about their visit.
- **Speed.** The page is not slowed down by loading external players.

YouTube videos are played through the address `youtube-nocookie.com`. For a social media post, the link **Open the original post** stays below the button and works even without clicking the button.

## Your own audio and video

Upload an MP3, M4A, OGG, WAV, MP4 or WebM file to Media as an attachment. You then get it into an article in two ways:

- as a **download link** – with the **image** button in the editor toolbar, see [Images, galleries and attachments](obrazky-a-galerie.md),
- as a **player above the text** – paste the address of the file into the **Audio or video** field in the **Podcast, video, live, review** panel, see [Content types](typy-obsahu.md).

Your own file is played directly from your site, so the load button is not shown for it.

## Accessibility

If you insert your own frame (`iframe`) in **HTML** mode, give it a `title` attribute with the name of the content. The **Accessibility check** in the editor reports a frame without a title.

## Related

- [Article editor](editor.md)
- [Content types](typy-obsahu.md)
