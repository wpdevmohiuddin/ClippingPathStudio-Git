(function($){
	$(document).ready(function() {
	  var slider_container = $('.before-after-image-comparison-slider-container');

	  slider_container.each(function() {
		var slider = $(this).find('.before-after-image-comparison-slider');
		var before = $(this).find('.comparison-slider-before-image');

		$(this).find('.comparison-slider-before-image').css('width', '50px !important');

		var beforeImage = before.find('.comparison-slider-before-img');
		var resizer = $(this).find('.image-comparison-slider-resizer');

		var active = false;

		$(document).ready(function() {
		  var width = slider.outerWidth();
		  beforeImage.css('width', width + 'px');
		});

		$(window).resize(function() {
		  var width = slider.outerWidth();
		  beforeImage.css('width', width + 'px');
		});

		resizer.mousedown(function() {
		  active = true;
		  resizer.addClass('resize');
		});

		$('body').mouseup(function() {
		  active = false;
		  resizer.removeClass('resize');
		});

		$('body').mouseleave(function() {
		  active = false;
		  resizer.removeClass('resize');
		});

		$('body').mousemove(function(e) {
		  if (!active) return;
		  var x = e.pageX;
		  x -= slider.offset().left;
		  slideIt(x);
		  pauseEvent(e);
		});

		resizer.on('touchstart', function() {
		  active = true;
		  resizer.addClass('resize');
		});

		$('body').on('touchend', function() {
		  active = false;
		  resizer.removeClass('resize');
		});

		$('body').on('touchcancel', function() {
		  active = false;
		  resizer.removeClass('resize');
		});

		$('body').on('touchmove', function(e) {
		  if (!active) return;
		  var x;

		  for (var i = 0; i < e.changedTouches.length; i++) {
			x = e.changedTouches[i].pageX;
		  }

		  x -= slider.offset().left;
		  slideIt(x);
		  pauseEvent(e);
		});

		function slideIt(x) {
		  var transform = Math.max(0, Math.min(x, slider.outerWidth()));
		  before.css('width', transform + "px");

		  if (transform == 0) {
			resizer.css('left', transform - 0 + "px");
		  } else {
			resizer.css('left', transform - 4 + "px");
		  }
		}

		function pauseEvent(e) {
		  if (e.stopPropagation) e.stopPropagation();
		  if (e.preventDefault) e.preventDefault();
		  e.cancelBubble = true;
		  e.returnValue = false;
		  return false;
		}
	  });
	});
})(jQuery)
