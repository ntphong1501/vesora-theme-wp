(function ($) {
	'use strict';

	function activateTab(tab) {
		var key = tab.data('vesora-tab');

		$('[data-vesora-tab]').removeClass('nav-tab-active').attr({ 'aria-selected': 'false', tabindex: '-1' });
		tab.addClass('nav-tab-active').attr({ 'aria-selected': 'true', tabindex: '0' });
		$('[data-vesora-panel]').attr('hidden', true);
		$('[data-vesora-panel="' + key + '"]').removeAttr('hidden');
	}

	$(function () {
		var frame;
		var activeCard;
		var feedbackFrame;
		var activeFeedbackCard;
		var videoFrame;
		var posterFrame;
		var bannerFrame;
		var activeBannerCard;
		var activeBannerTarget;
		var tabs = $('[data-vesora-tab]');
		var feedbackList = $('[data-vesora-feedback-list]');

		function attachmentPreview(attachment) {
			return attachment.sizes && attachment.sizes.medium_large ? attachment.sizes.medium_large.url : attachment.url;
		}

		function refreshFeedbackCards() {
			var cards = feedbackList.find('[data-vesora-feedback-card]');
			cards.each(function (index) {
				var card = $(this);
				card.find('[data-vesora-feedback-position]').text(index + 1);
				card.find('[data-vesora-feedback-up]').prop('disabled', index === 0);
				card.find('[data-vesora-feedback-down]').prop('disabled', index === cards.length - 1);
			});
			$('[data-vesora-feedback-empty]').prop('hidden', cards.length > 0);
		}

		function createFeedbackCard(attachment) {
			var card = $('<article>', { 'class': 'vesora-feedback-admin__card', 'data-vesora-feedback-card': '' });
			var preview = $('<div>', { 'class': 'vesora-feedback-admin__preview' }).append(
				$('<img>', { src: attachmentPreview(attachment), alt: '', 'data-vesora-feedback-preview': '' })
			);
			var actions = $('<div>', { 'class': 'vesora-feedback-admin__actions' });
			var moveUpLabel = feedbackList.data('move-up-label');
			var moveDownLabel = feedbackList.data('move-down-label');

			card.append($('<div>', { 'class': 'vesora-feedback-admin__position', 'data-vesora-feedback-position': '' }));
			card.append(preview);
			card.append($('<input>', { type: 'hidden', name: 'vesora_feedback_gallery[]', value: attachment.id, 'data-vesora-feedback-id': '' }));
			actions.append($('<button>', { 'class': 'button', type: 'button', text: feedbackList.data('change-label'), 'data-vesora-feedback-change': '' }));
			actions.append($('<button>', { 'class': 'button', type: 'button', 'aria-label': moveUpLabel, 'data-vesora-feedback-up': '' }).append($('<span>', { 'class': 'dashicons dashicons-arrow-left-alt2', 'aria-hidden': 'true' })));
			actions.append($('<button>', { 'class': 'button', type: 'button', 'aria-label': moveDownLabel, 'data-vesora-feedback-down': '' }).append($('<span>', { 'class': 'dashicons dashicons-arrow-right-alt2', 'aria-hidden': 'true' })));
			actions.append($('<button>', { 'class': 'button button-link-delete', type: 'button', text: feedbackList.data('remove-label'), 'data-vesora-feedback-remove': '' }));
			card.append(actions);
			return card;
		}

		tabs.not('.nav-tab-active').attr('tabindex', '-1');

		tabs.on('click', function () {
			activateTab($(this));
		});

		tabs.on('keydown', function (event) {
			if (event.key !== 'ArrowLeft' && event.key !== 'ArrowRight') {
				return;
			}

			event.preventDefault();
			var current = tabs.index(this);
			var next = event.key === 'ArrowRight' ? current + 1 : current - 1;
			if (next >= tabs.length) {
				next = 0;
			}
			if (next < 0) {
				next = tabs.length - 1;
			}
			activateTab(tabs.eq(next));
			tabs.eq(next).trigger('focus');
		});

		$(document).on('click', '[data-vesora-select]', function () {
			activeCard = $(this).closest('[data-vesora-image-card]');

			if (!frame) {
				frame = wp.media({
					title: vesoraHomepageImages.frameTitle,
					button: { text: vesoraHomepageImages.frameButton },
					library: { type: 'image' },
					multiple: false
				});

				frame.on('select', function () {
					var attachment = frame.state().get('selection').first().toJSON();
					var previewUrl = attachment.sizes && attachment.sizes.medium_large ? attachment.sizes.medium_large.url : attachment.url;

					activeCard.find('[data-vesora-image-id]').val(attachment.id);
					activeCard.find('[data-vesora-preview]').attr('src', previewUrl);
					activeCard.find('[data-vesora-status]').text(vesoraHomepageImages.customState);
					activeCard.find('[data-vesora-select]').text(vesoraHomepageImages.changeLabel);
					activeCard.find('[data-vesora-reset]').removeAttr('hidden');
				});
			}

			frame.open();
		});

		$(document).on('click', '[data-vesora-reset]', function () {
			var card = $(this).closest('[data-vesora-image-card]');
			card.find('[data-vesora-image-id]').val('0');
			card.find('[data-vesora-preview]').attr('src', card.data('fallback-url'));
			card.find('[data-vesora-status]').text(vesoraHomepageImages.defaultState);
			card.find('[data-vesora-select]').text(vesoraHomepageImages.chooseLabel);
			$(this).attr('hidden', true);
		});

		$(document).on('click', '[data-vesora-banner-select]', function () {
			activeBannerCard = $(this).closest('[data-vesora-banner-card]');
			activeBannerTarget = $(this).data('vesora-banner-select');
			bannerFrame = wp.media({
				title: vesoraHomepageImages.frameTitle,
				button: { text: vesoraHomepageImages.frameButton },
				library: { type: 'image' },
				multiple: false
			});
			bannerFrame.on('select', function () {
				var attachment = bannerFrame.state().get('selection').first().toJSON();
				activeBannerCard.find('[data-vesora-banner-id="' + activeBannerTarget + '"]').val(attachment.id);
				activeBannerCard.find('[data-vesora-banner-preview="' + activeBannerTarget + '"]').attr('src', attachmentPreview(attachment));
			});
			bannerFrame.open();
		});

		$(document).on('click', '[data-vesora-feedback-add]', function () {
			feedbackFrame = wp.media({
				title: vesoraHomepageImages.frameTitle,
				button: { text: vesoraHomepageImages.frameButton },
				library: { type: 'image' },
				multiple: 'add'
			});
			feedbackFrame.on('select', function () {
				feedbackFrame.state().get('selection').each(function (model) {
					feedbackList.append(createFeedbackCard(model.toJSON()));
				});
				refreshFeedbackCards();
			});
			feedbackFrame.open();
		});

		$(document).on('click', '[data-vesora-feedback-change]', function () {
			activeFeedbackCard = $(this).closest('[data-vesora-feedback-card]');
			feedbackFrame = wp.media({
				title: vesoraHomepageImages.frameTitle,
				button: { text: vesoraHomepageImages.frameButton },
				library: { type: 'image' },
				multiple: false
			});
			feedbackFrame.on('select', function () {
				var attachment = feedbackFrame.state().get('selection').first().toJSON();
				activeFeedbackCard.find('[data-vesora-feedback-id]').val(attachment.id);
				activeFeedbackCard.find('[data-vesora-feedback-preview]').attr('src', attachmentPreview(attachment));
			});
			feedbackFrame.open();
		});

		$(document).on('click', '[data-vesora-feedback-remove]', function () {
			$(this).closest('[data-vesora-feedback-card]').remove();
			refreshFeedbackCards();
		});

		$(document).on('click', '[data-vesora-feedback-up]', function () {
			var card = $(this).closest('[data-vesora-feedback-card]');
			card.insertBefore(card.prev('[data-vesora-feedback-card]'));
			refreshFeedbackCards();
		});

		$(document).on('click', '[data-vesora-feedback-down]', function () {
			var card = $(this).closest('[data-vesora-feedback-card]');
			card.insertAfter(card.next('[data-vesora-feedback-card]'));
			refreshFeedbackCards();
		});

		$(document).on('click', '[data-vesora-video-select]', function () {
			videoFrame = wp.media({
				title: vesoraHomepageImages.videoFrameTitle,
				button: { text: vesoraHomepageImages.videoFrameButton },
				library: { type: 'video' },
				multiple: false
			});
			videoFrame.on('select', function () {
				var attachment = videoFrame.state().get('selection').first().toJSON();
				var preview = $('[data-vesora-video-preview]');

				$('[data-vesora-video-id]').val(attachment.id);
				$('[data-vesora-video-name]').text(attachment.title || attachment.filename);
				preview.empty().append($('<source>', { src: attachment.url, type: attachment.mime }));
				preview.get(0).load();
				$('[data-vesora-video-preview-wrap]').removeAttr('hidden');
				$('[data-vesora-video-empty]').attr('hidden', true);
				$('[data-vesora-video-select]').text(vesoraHomepageImages.videoChangeLabel);
				$('[data-vesora-video-remove]').removeAttr('hidden');
				$('[data-vesora-video-enabled]').prop('checked', true);
			});
			videoFrame.open();
		});

		$(document).on('click', '[data-vesora-video-remove]', function () {
			var preview = $('[data-vesora-video-preview]');
			preview.get(0).pause();
			preview.empty().get(0).load();
			$('[data-vesora-video-id]').val('0');
			$('[data-vesora-video-name]').text(vesoraHomepageImages.videoEmpty);
			$('[data-vesora-video-preview-wrap]').attr('hidden', true);
			$('[data-vesora-video-empty]').removeAttr('hidden');
			$('[data-vesora-video-select]').text(vesoraHomepageImages.videoChooseLabel);
			$('[data-vesora-video-enabled]').prop('checked', false);
			$(this).attr('hidden', true);
		});

		$(document).on('click', '[data-vesora-poster-select]', function () {
			posterFrame = wp.media({
				title: vesoraHomepageImages.posterFrameTitle,
				button: { text: vesoraHomepageImages.posterFrameButton },
				library: { type: 'image' },
				multiple: false
			});
			posterFrame.on('select', function () {
				var attachment = posterFrame.state().get('selection').first().toJSON();
				var previewUrl = attachmentPreview(attachment);

				$('[data-vesora-poster-id]').val(attachment.id);
				$('[data-vesora-poster-preview]').attr('src', previewUrl);
				$('[data-vesora-poster-preview-wrap]').removeAttr('hidden');
				$('[data-vesora-video-preview]').attr('poster', attachment.url);
				$('[data-vesora-poster-select]').text(vesoraHomepageImages.posterChangeLabel);
				$('[data-vesora-poster-remove]').removeAttr('hidden');
			});
			posterFrame.open();
		});

		$(document).on('click', '[data-vesora-poster-remove]', function () {
			$('[data-vesora-poster-id]').val('0');
			$('[data-vesora-poster-preview]').attr('src', '');
			$('[data-vesora-poster-preview-wrap]').attr('hidden', true);
			$('[data-vesora-video-preview]').removeAttr('poster');
			$('[data-vesora-poster-select]').text(vesoraHomepageImages.posterChooseLabel);
			$(this).attr('hidden', true);
		});

		refreshFeedbackCards();
	});
})(jQuery);
