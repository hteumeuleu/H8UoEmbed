document.addEventListener('DOMContentLoaded', H8UoEmbedInit);

function H8UoEmbedInit() {
	var H8UoEmbedVideos = document.querySelectorAll('.H8UoEmbed-link[data-H8UoEmbed-html]');
	for(var i=0; i < H8UoEmbedVideos.length; i++) {
		H8UoEmbedVideos[i].addEventListener('click', function(e) {
			e.preventDefault();
			var oEmbedHTML = this.getAttribute('data-H8UoEmbed-html');
			var container = this.parentNode;
			container.innerHTML = oEmbedHTML;
			container.className = container.className.replace(/(?:^|\s)H8UoEmbed--ratio-16-9(?!\S)/g , '');
			var iframe = container.getElementsByTagName('iframe');
			if(iframe.length > 0)
			{
				iframe[0].addEventListener('load', function() {
					container.className = container.className.replace(/(?:^|\s)H8UoEmbed(?!\S)/g , '');
				});
			}
		});
	}
}