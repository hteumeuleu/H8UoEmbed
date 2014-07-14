# H8uoEmbed

This WordPress plugin will override WordPress' default oEmbed behavior for videos. Even if you don't watch the video, embedding a Youtube video player adds more than 500 Ko to your page weight. Instead of embedding of full video player in an iframe, this plugin will generate a static link with an image thumbnail of the video. 

## Installation
1. [https://github.com/hteumeuleu/H8uoEmbed/archive/master.zip](Download the latest version) and unzip it in your *wp-content/plugins* folder.
2. Go to the Plugins page in your WordPress administration, and activate H8uoEmbed.

## How does it work ?
This plugins hooks to the `oembed_dataparse` filter and uses the oEmbed data response to create a static link with a thumbnail picture.

For example, for [this Youtube video](http://www.youtube.com/watch?v=V0FCNc5aou8&rel=0), instead of embedding the following code :

```html
<iframe width="480" height="270" src="http://www.youtube.com/embed/BIe8Hhfg1-E?feature=oembed" frameborder="0" allowfullscreen </iframe>
```

This plugin will create and embed the following code :

```html
<div style="max-width:660px; max-height:371px;" class="H8UoEmbed">
	<a data-h8uoembed-html="&lt;iframe width=&quot;660&quot; height=&quot;371&quot; src=&quot;http://www.youtube.com/embed/V0FCNc5aou8?feature=oembed&amp;autoplay=1&quot; frameborder=&quot;0&quot; allowfullscreen&gt;&lt;/iframe&gt;" title="Mickey Mouse : Pique-Nique à la Plage - Episode intégral - Exclusivité Disney !" href="http://www.youtube.com/watch?v=V0FCNc5aou8&amp;rel=0" class="H8UoEmbed-link">
		<img width="480" height="360" alt="Mickey Mouse : Pique-Nique à la Plage - Episode intégral - Exclusivité Disney !" src="http://i1.ytimg.com/vi/V0FCNc5aou8/hqdefault.jpg">
	</a>
</div>
```

A script is then included on the `wp_footer` hook to replace the static link by its iframe. This script will use the `data-h8uoembed-html` attribute generated in the static HTML. 
Styles are also added to mimic default players look and feel.
Script and styles are only added to the page if necessary. So if there's no video on the current page, nothing will be added.

## Status
* v0.1 : First version. I wouldn't advise installing this straight away on your own server, as this is still a very early version. I'm looking for testers to find edge cases and give me feedbacks on my code.

H8UoEmbed stands for *HTeuMeuLeu's oEmbed*. I know, *how creative !* But it was either that or *VideoKilledTheWebPerfStar*, or *EmbedWithMadonna*. So in the end, it's not that bad.
