$(document).ready(function(){

	function setCookie(cname, cvalue, exdays) {
		var d = new Date();

		if (!$('.newsletterbox--wrapper--inner').data('hideafterregistration')) {
			d.setTime(d.getTime() + exdays * 24 * 60 * 60 * 1000);
		} else {
			d.setTime(d.getTime() + 1825 * 24 * 60 * 60 * 1000);
		}

		document.cookie = cname + "=" + cvalue + ";path=/;expires=" + d.toGMTString() + ";";
	}

	function getCookie(cname) {
		var name = cname + "=";
		var decodedCookie = decodeURIComponent(document.cookie);
		var ca = decodedCookie.split(';');
		for(var i = 0; i <ca.length; i++) {
			var c = ca[i];
			while (c.charAt(0) == ' ') {
				c = c.substring(1);
			}
			if (c.indexOf(name) == 0) {
				return c.substring(name.length, c.length);
			}
		}
		return "";
	}

	var newsletterCookie = getCookie("mag-newsletterbox");

	if (!newsletterCookie && $('.newsletterbox--wrapper--inner').css('display') != 'none') {

		var displaytime = $('.newsletterbox--wrapper--inner').data('displaytime');

		newsletterBoxInit = function() {

			$(".newsletterbox--privacy a").click(function(event) {
				if (!$(".privacy--content--inner").html()) {
					$(".privacy--content--inner").load($('.privacy--content').data('privacy-url'), function () {
						$('<p class="privacy--content--more"></p>').insertAfter( $( ".privacy--content--inner" ) );
						$(".privacy--content").slideToggle("slow");
					});
				} else {
					$(".privacy--content").slideToggle("slow");
				}
				event.preventDefault();
			});

			$("#privacy-checkbox, #newsletterbox_email").click(function(e) {
				if (!$(".privacy--content").is(':hidden')) {
					$(".privacy--content").slideToggle("slow");
				}
			});

			$("#newsletterbox--form").submit(function(event) {

				var email = $("#newsletterbox_email").val();
				var email_regex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/i;

				if (email_regex.test(email)) {

					// Check mail address before
					$.ajax({
						url: unescape($('.newsletterbox--wrapper--inner').data('validatecontroller')),
						type: 'POST',
						cache: false,
						async: false,
						datatype: "json",
						data: {
							email: unescape(email),
						},
						beforeSend: function (xhr) {
							xhr.setRequestHeader('X-CSRF-Token', CSRF.getToken());
						},
						success: function (data) {
							if (data == 'true') {
								$('.newsletterbox--wrapper--inner .is--error').fadeIn('slow');
								$('.newsletterbox--wrapper--inner .alert--content').html($('.newsletterbox--wrapper--inner').data('errorvalidate'));
							} else {
								$('.newsletterbox--wrapper--inner .is--error').hide();

								// Submit newsletter form
								$.ajax({
									url: unescape($('.newsletterbox--wrapper--inner').data('controller')),
									type: 'POST',
									cache: false,
									async: false,
									datatype: "json",
									data: {
										subscribeToNewsletter: 1,
										newsletter: unescape(email),
										captcha: unescape($(".captcha--placeholder input").val())
									},
									beforeSend: function ( xhr ) {
										xhr.setRequestHeader('X-CSRF-Token', CSRF.getToken());
										$('.js--modal .is--error').hide();
										$(".js--modal .newsletterbox--wrapper--inner--content--form .btn").replaceWith('<div class="newsletterbox_ajax"><div class="form--ajax-loader"></div> <span>'+$('.newsletterbox--wrapper--inner').data('sending')+'</span></div>');
									},
									success: function(data) {
										if(data) {
											$('.js--modal .newsletterbox--wrapper--inner--content--form, .js--modal .newsletterbox_ajax').hide();
											$('.js--modal .is--success .alert--content').html(data);
											$('.js--modal .is--success').fadeIn('slow');

											setCookie("newsletterbox", 1, $('.newsletterbox--wrapper--inner').data('cookielife'));

											if ($('.newsletterbox--wrapper--inner').data('autohide')) {
												window.setTimeout("$.modal.close()", 4000);
											}
										}
									},
									error: function(data) {
										console.log($('.newsletterbox--wrapper--inner').data('errorsend'));
									}
								});
							}
						},
					});
				} else {
					$("#newsletterbox_email").addClass('has--error');
					$('.js--modal .is--error').fadeIn('slow');
				}

				event.preventDefault();
			});

			$('#newsletterbox_email').keypress(function(e) {
				if (e.which == 13) {
					$("#newsletterbox_submit").click();
					e.preventDefault();
				}
			});
		}

		modalOpen = function() {

			var newslettercontent = $.parseHTML($('.newsletterbox--wrapper').html());

			if (!newsletterCookie && $('.newsletterbox--wrapper').length) {

				$('.page-wrap--cookie-permission').css('z-index', 5000);

				$.modal.open(
					newslettercontent, {
						sizing: 'content',
						title: $('.newsletterbox--wrapper--inner').data('header'),
						width: $('.newsletterbox--wrapper--inner').data('maxwidth'),
						closeOnOverlay: true,
						additionalClass: 'newsletterbox--modal',
						onClose: function() {
							if (!getCookie('mag-newsletterbox')) {
								setCookie("mag-newsletterbox", 1, $('.newsletterbox--wrapper--inner').data('cookielife'));
							}

							localStorage.removeItem('newsletterboxTimeout');

							$('.page-wrap--cookie-permission').css("z-index", '');
						}
					});

				$('.newsletterbox--wrapper').remove();

				newsletterBoxInit();
			}
		}

		if (!newsletterCookie) {
			var newsletterboxCounter = 0;
			var newsletterboxTimeout = setInterval(function() {
				newsletterboxCounter++;
				//console.log(newsletterboxCounter);

				if (localStorage.getItem('newsletterboxTimeout')) {
					var newDisplaytime = displaytime-localStorage.getItem('newsletterboxTimeout');
				} else {
					var newDisplaytime = displaytime;
				}

				if (newsletterboxCounter >= newDisplaytime) {
					modalOpen();

					clearInterval(newsletterboxTimeout);
				}
			}, 1000);

			window.onbeforeunload = function () {
				if (localStorage.getItem('newsletterboxTimeout')) {
					var newLocalStorageDisplaytime = parseInt(localStorage.getItem('newsletterboxTimeout')) + newsletterboxCounter;
					localStorage.setItem('newsletterboxTimeout', newLocalStorageDisplaytime);
				} else {
					localStorage.setItem('newsletterboxTimeout', newsletterboxCounter);
				}
			};
		}

	}
});
