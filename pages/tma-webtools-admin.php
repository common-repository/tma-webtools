<?php
/*
 * Copyright (C) 2016 marx
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
?>
<style>
	#webtools table {
		border-collapse: collapse;
	}
	#webtools td.bold {
		font-weight: bold;
	}

	#webtools table, #webtools th, #webtools td {
		border: 1px solid black;
	}
</style>
<h2>TMA WebTools</h2>
<div id="webtools">
	<h2>Help</h2>
	<div>
		The plugin integrates the webTools platform into WordPress. webTools must be hosted by yourself. The current
		version can be downloaded from bintray: <a src="https://bintray.com/thmarx/generic/webTools/view">Download WebTools</a>
	</div>
	<h3>Scoring</h3>
	<div>
		After enabling scoring in the settings you must add scores to your posts, pages or products. To add scores you can simply 
		use &quot;Segment Scoring&quot; MetaBox at the editor site. In the box all in the webTools-Platform configured
		segments are available.
		<br/>
		The score with the name <b>&quot;clothing&quot;</b> must be configured in webTools for a specific segment. Please read the 
		webTools documentation for details.
		<h4>Get Segments for current user</h4>
		<div>
			The user id of the current user is stored in a cookie with the name <b>_tma_uid</b>. This id can be used to get the segmenst 
			from webTools via REST: <i>http://<b>WEBTOOLS_URL</b>/rest/segments/user?apikey=<b>APIKEY</b>&user=<b>USER_ID</b></i>
		</div>
	</div>
	<h3>ShortCodes</h3>
	<div>
		<h4>tma_content</h4>
		<div>
			With the tma_content shortcode it is possible to display content, targeting a specific user segment.
			<h5>Attributes<h5>
					<table>
						<thead>
							<tr>
								<th>Attribute</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td class="bold">group</td>
								<td>
									With the group attribute you can group content that belong together. If no group is given, &quot;default&quot; is used.
								</td>
							</tr>
							<tr>
								<td class="bold">segments</td>
								<td>The segments a user must match to get the content displayed. The segments in the list must be separated by commas.</td>
							</tr>
							<tr>
								<td class="bold">mode</td>
								<td>
									Possible values are <b>&quot;single&quot;</b> and <b>&quot;all&quot;</b>.<br/>
									A user must match at least one segment (mode single) or all segments (mode all).
								</td>
							</tr>
							<tr>
								<td class="bold">default</td>
								<td>
									If no tag of the group is matching a user, the default tag is displayed.
								</td>
							</tr>
						</tbody>
					</table>
					<h5>Examples</h5>
					[tma_content segments='clothing' group='testgroup']Domain: hammer und segments: mode1[/tma_content]
					<br/>
					[tma_content segments='clothing,shoes' group='testgroup']Domain: hammer und segments: mode1 oder handwerk[/tma_content]
					<br/>
					[tma_content segments='mode1,handwerk' mode='all' group='testgroup']Domain: hammer und segments: mode1 oder handwerk[/tma_content]
					<br/>
					[tma_content default='true' mode='all' group='testgroup']Domain: hammer und segments: mode1 oder handwerk[/tma_content]
					</div>
					</div>
					<div id="tmc_credits">
						<h2>Credits</h2>
						<div>Icons made by <a href="http://www.freepik.com" title="Freepik">Freepik</a> from <a href="http://www.flaticon.com" title="Flaticon">www.flaticon.com</a> is licensed by <a href="http://creativecommons.org/licenses/by/3.0/" title="Creative Commons BY 3.0" target="_blank">CC 3.0 BY</a></div>
					</div>
					</div>