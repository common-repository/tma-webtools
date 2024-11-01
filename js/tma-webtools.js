/* 
 * Copyright (C) 2016 Thorsten Marx
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

function tma_segment_selector(segment) {
	if (!tma_segment_exists(segment)) {
		var url = window.location.href;
		var separator = (url.indexOf('?') > -1) ? "&" : "?";
		var qs = "segment[]=" + encodeURIComponent(segment);
		window.location.href = url + separator + qs;
	} else {
		var url = window.location.href;
		url = tma_removeParamValue("segment[]", segment, url);
		window.location.href = url;
	}
}
function tma_segment_clear() {
	var url = window.location.href;
	url = tma_removeParam("segment[]", url);
	window.location.href = url;
}
function tma_removeParam(key, sourceURL) {
	var rtn = sourceURL.split("?")[0],
			param,
			params_arr = [],
			queryString = (sourceURL.indexOf("?") !== -1) ? sourceURL.split("?")[1] : "";
	if (queryString !== "") {
		params_arr = queryString.split("&");
		for (var i = params_arr.length - 1; i >= 0; i -= 1) {
			param = params_arr[i].split("=")[0];
			if (param === key) {
				params_arr.splice(i, 1);
			}
		}
		rtn = rtn + "?" + params_arr.join("&");
	}
	return rtn;
}
function tma_removeParamValue(key, value, sourceURL) {
	var rtn = sourceURL.split("?")[0],
			param,
			params_arr = [],
			queryString = (sourceURL.indexOf("?") !== -1) ? sourceURL.split("?")[1] : "";
	if (queryString !== "") {
		params_arr = queryString.split("&");
		for (var i = params_arr.length - 1; i >= 0; i -= 1) {
			param = params_arr[i].split("=")[0];
			var tempValue = params_arr[i].split("=")[1];
			if (param === key && tempValue === value) {
				params_arr.splice(i, 1);
			}
		}
		rtn = rtn + "?" + params_arr.join("&");
	}
	return rtn;
}


function tma_segment_exists(segment) {
	var queryString = location.search;
	var params = queryString.substring(1).split('&');
	for (var i = 0; i < params.length; i++) {
		var pair = params[i].split('=');
		if (decodeURIComponent(pair[0]) === 'segment[]' && pair[1] === segment)
			return true;
	}
	return false;
}