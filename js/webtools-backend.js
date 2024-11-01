(function (webtools, document) {
    webtools.Context = {
        original: []
    };
    
}(window.webtools = window.webtools || {}, document));
(function (tools, document) {
    tools.insertStyle = function (linkUrl) {
        var ss = document.createElement("link");
        ss.type = "text/css";
        ss.rel = "stylesheet";
        ss.href = linkUrl;
        document.getElementsByTagName("head")[0].appendChild(ss);
    };
    tools.insertScript = function (scriptUrl) {
        var ss = document.createElement("script");
        ss.type = "text/javascript";
        ss.src = scriptUrl;
        document.getElementsByTagName("head")[0].appendChild(ss);
    };
    tools.on = function (element, type, callback) {
        element.addEventListener(type, callback);
    };

    tools.is = function ($element, tags) {
        if (tags && Array.isArray(tags)) {
            return tags.some(function (tagname) {
                return $element.tagName === tagname;
            });
        }

        return false;
    };

    tools.getPageInfo = function () {
        var filename = location.pathname.split("/");
        filename = filename[filename.length - 1];

        return {
            domain: location.hostname,
            path: location.pathname,
            page: filename,
            href : location.href,
            id : location.href
        }
    };


    tools.createElement = function (name, attributes) {
        var element = document.createElement(name);
        for (var key in attributes) {
            element.setAttribute(key, attributes[key]);
        }
        return element;
    };

    tools.uuid = function () {
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
            var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
            return v.toString(16);
        });
    }
}(window.webtools.Tools = window.webtools.Tools || {}, document));
(function (exports, d) {
	function domReady(fn, context) {

		function onReady(event) {
			d.removeEventListener("DOMContentLoaded", onReady);
			fn.call(context || exports, event);
		}

		function onReadyIe(event) {
			if (d.readyState === "complete") {
				d.detachEvent("onreadystatechange", onReadyIe);
				fn.call(context || exports, event);
			}
		}

		d.addEventListener && d.addEventListener("DOMContentLoaded", onReady) ||
			d.attachEvent && d.attachEvent("onreadystatechange", onReadyIe);
	}

	exports.domReady = domReady;
})(window.webtools = window.webtools || {}, document);
/**
 * https://codepen.io/malyw/pen/zxKJQQ
 */
(function (highlight, document) {

    // VARS
    var HIGHLIGHT_CLASS = "webtools-highlight";
    var HIGHLIGHT_ACTIVE_CLASS = "webtools-highlight-is-active";
    var highlightIsActive = false;
    var $highlightedElements = [];

    var $highlightCanvas = null;

    // HIGHLIGHT HELPERS
    highlight.activate = function ($element) {
        if (Array.isArray($element)) {
            $highlightedElements = $element
        } else {
            $highlightedElements.push($element);    
        }
        if ($highlightedElements.length === 0) {
            return;
        }
        highlightIsActive = true;
       

        $highlightCanvas = document.createElement("canvas");
        $highlightCanvas.style.position = "absolute";
        $highlightCanvas.id = "webtools_canvas";
        $highlightCanvas.style.top = 0;
        $highlightCanvas.style.left = 0;
        $highlightCanvas.style.width = document.body.clientWidth;
        $highlightCanvas.style.height = document.body.clientHeight;
        $highlightCanvas.style.zIndex = 10000; 
        $highlightCanvas.width  = document.body.clientWidth;
        $highlightCanvas.height = document.body.clientHeight;
        document.body.appendChild($highlightCanvas);

        var context = $highlightCanvas.getContext("2d");
        context.fillStyle = 'black';
        context.globalAlpha = 0.7;
        context.fillRect(0, 0, $highlightCanvas.width, $highlightCanvas.height);
        context.globalAlpha = 1.0;
        context.globalCompositeOperation = 'destination-out';
        $highlightedElements.forEach (function ($e) {
            var elementRect = $e.getBoundingClientRect();
            var offset = getOffset($e);
            // translate to fit into document.body.style.width and document.body.style.height;
            var rect   =  $highlightCanvas.getBoundingClientRect();
            var xMouse =  elementRect.left  - rect.left;
            var yMouse =  elementRect.top  - rect.top;
            context.fillRect(offset.left, offset.top, elementRect.width, elementRect.height);
        });
    }

    function getOffset(el) {
        el = el.getBoundingClientRect();
        return {
          left: el.left + window.scrollX,
          top: el.top + window.scrollY
        }
      }

    highlight.deactivate = function () {
        if ($highlightCanvas) {
            $highlightCanvas.remove();
        }

        $highlightedElements = [];
        highlightIsActive = false;
    }
    highlight.is = function () {
        return $highlightedElements.length > 0;
    }

    /*
    webtools.domReady(function () {
        webtools.Tools.on(document, "click", function () {
            if (highlightIsActive > 0) {
                highlight.deactivate();
            }
        })
    });
    */


}(window.webtools.Highlight = window.webtools.Highlight || {}, document));