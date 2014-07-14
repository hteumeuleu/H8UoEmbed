document.addEventListener('DOMContentLoaded', H8UoEmbedInit);

function H8UoEmbedInit() {
    var H8UoEmbedVideos = document.querySelectorAll('.H8UoEmbed-link[data-H8UoEmbed-html]');
    for (var i = 0; i < H8UoEmbedVideos.length; i++) {
        H8UoEmbedVideos[i].addEventListener('click', function(e) {
            e.preventDefault();
            var oEmbedHTML = this.getAttribute('data-H8UoEmbed-html');
            this.parentNode.innerHTML = oEmbedHTML;
        });
    }
}