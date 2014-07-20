# H8UoEmbed

This WordPress plugin will override WordPress' default oEmbed behavior for videos. Even if you don't watch the video, embedding a Youtube video player adds more than 500 Ko to your page weight. Instead of embedding of full video player in an iframe, this plugin will generate a static link with an image thumbnail of the video. You can see [a demo of the plugin](http://www.hteumeuleu.fr/H8UoEmbed/) on my blog.

## Installation
1. [Download the latest version](https://github.com/hteumeuleu/H8UoEmbed/archive/master.zip) and unzip it in your *wp-content/plugins* folder.
2. Go to the Plugins page in your WordPress administration, and activate H8uoEmbed.

## How does it work ?
This plugins hooks to the `oembed_dataparse` filter and uses the oEmbed data response to create a static link with a thumbnail picture.

For example, for [this Youtube video](http://www.youtube.com/watch?v=V0FCNc5aou8&rel=0), instead of embedding the following code :

```html
<iframe width="480" height="270" src="http://www.youtube.com/embed/BIe8Hhfg1-E?feature=oembed" frameborder="0" allowfullscreen </iframe>
```

This plugin will create and embed the following code :

```html
<p style="max-width:660px;" class="H8UoEmbed">
	<a data-h8uoembed-html="…" title="…" href="http://www.youtube.com/watch?v=V0FCNc5aou8" class="H8UoEmbed-link">
		<img width="480" height="360" alt="…" src="http://i1.ytimg.com/vi/V0FCNc5aou8/hqdefault.jpg"  class="H8UoEmbed-img" />
	</a>
</p>
```

The `data-h8uoembed-html` is filled with the HTML code of the full player provided via oEmbed. The `alt` and `title` attributes are filled with the title of the video provided via oEmbed.

A script is then added with the `wp_enqueue_scripts` hook to replace the static link by its iframe. This script will use the `data-h8uoembed-html` attribute generated in the static HTML. 
Styles are also added to mimic default players look and feel.

## Status
* v0.4 : Improved the alignment of thumbnails with a different ratio than the player. Also some CSS cleaning.
* v0.3 : CSS and JS are now added on separate files.
* v0.2 : Minor fixes.
* v0.1 : First version. I wouldn't advise installing this straight away on your own server, as this is still a very early version. I'm looking for testers to find edge cases and give me feedbacks on my code.

H8UoEmbed stands for *HTeuMeuLeu's oEmbed*. I know, *how creative !* But it was either that or *VideoKilledTheWebPerfStar*, or *EmbedWithMadonna*. So in the end, it's not that bad.
