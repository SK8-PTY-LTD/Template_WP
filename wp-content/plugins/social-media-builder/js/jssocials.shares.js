(function(window, $, jsSocials, undefined) {

	$.extend(jsSocials.shares, {

		reddit: {
			label: "Post",
			logo: "fa fa-tumblrapp",
			shareUrl: "https://www.reddit.com/submit?url={url}&title={text}",
			countUrl: ""
		},

		tumblr: {
			label: "Post",
			logo: "fa fa-tumblrapp",
			shareUrl: "https://www.tumblr.com/share?v=3&u={url}&t={text}",
			countUrl: ""
		},

		whatsapp: {
			label: "WhatsApp",
			logo: "fa fa-whatsapp",
			shareUrl: "whatsapp://send?text={url} {text}",
			countUrl: ""
		},

		email: {
			label: "E-mail",
			logo: "fa fa-at",
			shareUrl: "mailto:{to}?subject={text}&body={url}",
			countUrl: "",
			shareIn: "self"
		},

		twitter: {
			label: "Tweet",
			logo: "fa fa-twitter",
			shareUrl: "https://twitter.com/share?url={url}&text={text}&via={via}&hashtags={hashtags}",
			countUrl: ""
		},

		facebook: {
			label: "Like",
			logo: "fa fa-facebook",
			shareUrl: "https://facebook.com/sharer/sharer.php?u={url}",
			countUrl: "https://graph.facebook.com/?id={url}",
			getCount: function(data) {
				return data.share && data.share.share_count || 0;
			}
		},

		mewe: {
			label: "mewe",
			logo: "fa fa-mewe",
			shareUrl: "https://mewe.com/share?link={url}",
			countUrl: ""
		},

		googleplus: {
			label: "+1",
			logo: "fa fa-google",
			shareUrl: "https://plus.google.com/share?url={url}",
			countUrl: ""
		},

		linkedin: {
			label: "Share",
			logo: "fa fa-linkedin",
			shareUrl: "https://www.linkedin.com/shareArticle?mini=true&url={url}",
			countUrl: "https://www.linkedin.com/countserv/count/share?format=jsonp&url={url}&callback=?",
			getCount: function(data) {
				return data.count;
			}
		},

		pinterest: {
			label: "Pin it",
			logo: "fa fa-pinterest",
			shareUrl: "https://pinterest.com/pin/create/bookmarklet/?media={media}&url={url}&description={text}",
			countUrl: "https://api.pinterest.com/v1/urls/count.json?&url={url}&callback=?",
			getCount: function(data) {
				return data.count;
			}
		},

		stumbleupon: {
			label: "Share",
			logo: "fa fa-stumbleupon",
			shareUrl: "https://www.stumbleupon.com/submit?url={url}&title={title}",
			countUrl:  "https://cors-anywhere.herokuapp.com/https://www.stumbleupon.com/services/1.01/badge.getinfo?url={url}",
			getCount: function(data) {
				return data.result.views;
			}
		},

		line: {
			label: "LINE",
			logo: "fa fa-comment",
			shareUrl: "https://line.me/R/msg/text/?{text} {url}",
			countUrl: ""
		},

		vk: {
			label: "Like",
			logo: "fa fa-vk",
			shareUrl: "https://vk.com/share.php?url={url}&title={title}&description={text}",
			countUrl: "https://vk.com/share.php?act=count&index=1&url={url}",
			getCount: function(data) {
				return parseInt(data.slice(15, -2).split(', ')[1]);
			}
		},

	});
}(window, jQuery, window.jsSocials));
