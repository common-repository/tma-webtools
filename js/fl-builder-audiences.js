(function ($) {

	/**
	 * Revisions manager for the builder.
	 *
	 * @since 2.0
	 * @class Revisions
	 */
	var Audiences = {

		previewActive: false,
		/**
		 * Initialize builder revisions.
		 *
		 * @since 2.0
		 * @method init
		 */
		init: function ()
		{
//			FLBuilder.addHook('audienceItemClicked', this.itemClicked.bind(this));
			this.setupMenuStructure();


			//$(window).on('click', this.windowClicked.bind(this));


		},

		setupMenuStructure: function () {
			var THAT = this;
//			FLBuilder.addHook('didInitUI', function () {
			var htmlString = '<div class="tma-fl-dropdown">';
			htmlString += '<div id="myDropdown" class="tma-fl-dropdown-content">';
			htmlString += "<h4>Segments</h4>";
			htmlString += '<hr style="margin: 0;"/>';
			if (typeof TMA_CONFIG.segments !== "undefined") {
				TMA_CONFIG.segments.forEach(function (segment) {
					htmlString += '<a href="#" class="segmentSelector" data-tma-segment="' + segment.id + '">' + segment.name + '</a>';
				});
			}
			htmlString += '</div>';
			htmlString += '</div>';

			var el_wait_counter = 0;
			var waitForEl = function (selector, callback) {
				if (!jQuery(selector).size()) {
					el_wait_counter++;
					if (el_wait_counter > 5) {
						return;
					}
					setTimeout(function () {
						window.requestAnimationFrame(function () {
							waitForEl(selector, callback)
						});
					}, 500);
				} else {
					callback();
				}
			};

			waitForEl(".fl-builder-tma-targeting-button", function () {
				$(".fl-builder-tma-targeting-button").after(htmlString);

				$('.fl-builder-tma-targeting-button').on('click', THAT.buttonClicked.bind(THAT));
				$('.segmentSelector').on('click', THAT.selectSegment.bind(THAT));
				$('.fl-builder-tma-highlight-button').on('click', THAT.highlight.bind(THAT));
			})

//			$(".fl-builder-tma-targeting-button").after(htmlString);
//
//			$('.fl-builder-tma-targeting-button').on('click', THAT.buttonClicked.bind(THAT));
//			$('.segmentSelector').on('click', THAT.selectSegment.bind(THAT));
//			$('.fl-builder-tma-highlight-button').on('click', THAT.highlight.bind(THAT));
//			});
		},

		highlight: function () {
			if (webtools.Highlight.is()) {
				webtools.Highlight.deactivate();
			} else {
				webtools.Highlight.activate(Array.apply([], document.querySelectorAll('[data-tma-group]')));
			}
		},
		updatePreview: function () {
			$(".tma-hide").removeClass("tma-hide");
			if (this.previewActive) {
				var groups = this.collectGroups();
				var selectedSegments = this.selectedSegments();


				var THAT = this;
				groups.forEach(function (group) {
					var matches = [];
					$("[data-tma-group=" + group + "]").each(function () {
						if ($(this).data("tmaPersonalization") !== "enabled") {
							return;
						}
						if (!THAT.matchs(this, selectedSegments)) {
							$(this).addClass("tma-hide");
						} else {
							matches.push(this);
						}
					});
					// remove the default
					if (matches.length > 1) {
						matches.filter(function (item) {
							return item.dataset.tmaDefault === "yes"
						}).forEach(function (item) {
							$(item).addClass("tma-hide");
						});
					}
				});
			}
		},
		matchs: function ($element, selectedSegments) {
			if ($element.dataset.tmaDefault === "yes") {
				return true;
			} else if ($element.dataset.tmaMatching === "all") {
				var segments = $element.dataset.tmaSegments.split(",");
				var matching = true;
				segments.forEach(function (s) {
					if (!selectedSegments.includes(s)) {
						matching = false;
					}
				});
				return matching;
			} else if ($element.dataset.tmaMatching === "single") {
				var segments = $element.dataset.tmaSegments.split(",");
				var matching = false;
				segments.forEach(function (s) {
					if (selectedSegments.includes(s)) {
						matching = true;
					}
				});
				return matching;
			}
			return false;
		},
		selectedSegments: function () {
			var selectedSegments = [];
			$(".tma-selected-segment").each(function () {
				selectedSegments.push($(this).data("tma-segment"));
			});
			return selectedSegments;
		},
		collectGroups: function () {
			var groups = [];
			$("[data-tma-group]").each(function () {
				var group = $(this).data("tma-group").trim();
				if (!groups.includes(group) && group != "") {
					groups.push(group);
				}
			});
			return groups;
		},
		/**
		 * Callback for when a revision item is clicked
		 * to preview a revision.
		 *
		 * @since 2.0
		 * @method itemClicked
		 * @param {Object} e
		 * @param {Object} item
		 */
		buttonClicked: function (e)
		{
			var position = $('.fl-builder-tma-targeting-button').position();
			var height = $('.fl-builder-tma-targeting-button').height();
			var top = position.top + height;
			var left = position.left;
			$('.tma-fl-dropdown').css({
				'position': 'absolute',
				'left': left,
				'top': top
			});
			if ($('.tma-fl-dropdown').is(':visible')) {
				$(".tma-fl-dropdown").hide();
				this.previewActive = false;
			} else {
				$(".tma-fl-dropdown").show();
				this.previewActive = true;
			}
			this.updatePreview();
		},

		windowClicked: function (event) {
			if (!event.target.matches('.fl-builder-tma-targeting-button')) {
				$(".tma-fl-dropdown").hide();
			}
		},

		selectSegment: function (event) {
			event.preventDefault();
			$(event.target).toggleClass("tma-selected-segment");
			this.updatePreview();
		}
	};

	$(function () {
		Audiences.init();
	});


})(jQuery); 