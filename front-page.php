<?php get_header(); ?>

<style>
	.ofhr-text-center {
		text-align: center;
	}
</style>

<?php
	// set this to 0 if there is something not working with the stream player code
	// (people will still have the Streaming page with all the links to the streams)
	if (0) {
?>
<script>
	var g_streams_info = [
		{
			"hall": <?php echo json_encode(pll__('BULGARIA_HALL')); ?>,
			"default": true,
			"qualities": [
				{
					"label": <?php echo json_encode(pll__('LOW_QUALITY'));?>,
					"iframe_url": "http://stream.openfest.org/bulgaria-low.html",
					"rtmp_url": "rtmp://stream.openfest.org/st/bulgaria-low",
					"hls_url": "http://stream.openfest.org/hls/bulgaria-low.m3u8",
				},
				{
					"label": <?php echo json_encode(pll__('NORMAL_QUALITY'));?>,
					"default": true,
					"iframe_url": "http://stream.openfest.org/bulgaria.html",
					"rtmp_url": "rtmp://stream.openfest.org/st/bulgaria",
					"hls_url": "http://stream.openfest.org/hls/bulgaria.m3u8",
				},
				{
					"label": <?php echo json_encode(pll__('HIGH_QUALITY'));?>,
					"iframe_url": "http://stream.openfest.org/bulgaria-hd.html",
					"rtmp_url": "rtmp://stream.openfest.org/st/bulgaria-hd",
					"hls_url": "http://stream.openfest.org/hls/bulgaria-hd.m3u8",
				},
			]
		},
		{
			"hall": <?php echo json_encode(pll__('CHAMBER_HALL'));?>,
			"qualities": [
				{
					"label": <?php echo json_encode(pll__('LOW_QUALITY'));?>,
					"iframe_url": "http://stream.openfest.org/chamber-low.html",
					"rtmp_url": "rtmp://stream.openfest.org/st/chamber-low",
					"hls_url": "http://stream.openfest.org/hls/chamber-low.m3u8",
				},
				{
					"label": <?php echo json_encode(pll__('NORMAL_QUALITY'));?>,
					"default": true,
					"iframe_url": "http://stream.openfest.org/chamber.html",
					"rtmp_url": "rtmp://stream.openfest.org/st/chamber",
					"hls_url": "http://stream.openfest.org/hls/chamber.m3u8",
				},
			]
		},
		{
			"hall": <?php echo json_encode(pll__('MUSIC_HALL'));?>,
			"qualities": [
				{
					"label": <?php echo json_encode(pll__('LOW_QUALITY'));?>,
					"iframe_url": "http://stream.openfest.org/music-low.html",
					"rtmp_url": "rtmp://stream.openfest.org/st/music-low",
					"hls_url": "http://stream.openfest.org/hls/music-low.m3u8",
				},
				{
					"label": <?php echo json_encode(pll__('NORMAL_QUALITY'));?>,
					"default": true,
					"iframe_url": "http://stream.openfest.org/music.html",
					"rtmp_url": "rtmp://stream.openfest.org/st/music",
					"hls_url": "http://stream.openfest.org/hls/music.m3u8",
				}
			]
		}
	];
</script>

<section class="content">
    <h3>Streaming | <small><a href="streaming"><?php echo htmlentities(pll__('ALL_STREAMS'));?></a></small></h3>
	<h3 id="of-stream-halls-container" class="ofhr-text-center">
	</h3>
	<p id="of-stream-iframe-container">
		<iframe id="of-stream-iframe" style="border: none; height: 395px; width: 100%; overflow: none;" allowfullscreen>
			<p>Браузърът Ви не поддържа iframes</p>
		</iframe>
	</p>
	<p class="ofhr-text-center">
		<a id="of-stream-rtmp-link" href="#">RTMP</a> | <a id="of-stream-hls-link" href="#">HLS</a>
	</p>
	<p id="of-stream-qualities-container" class="ofhr-text-center">
	</p>
	<div class="separator"></div>

	<script>

		function of_hall_link_on_click(event) {
			event.preventDefault();

			var hall_node = event.target;
			var stream_id = hall_node.getAttribute("data-stream-id");

			of_update_player(stream_id);
		}

		function of_quality_link_on_click(event) {
			event.preventDefault();

			var quality_node = event.target;
			var stream_id = quality_node.getAttribute("data-stream-id");
			var quality_id = quality_node.getAttribute("data-quality-id");

			of_update_player(stream_id, quality_id);
		}

		function of_update_player(desired_stream_id, desired_quality_id) {
			var default_stream_id = -1;

			// if there is a provided stream id use it (otherwise use the one
			// with default property set to true)
			if (desired_stream_id !== undefined) {
				default_stream_id = desired_stream_id;
			}

			// clear halls list
			var halls_container = document.getElementById("of-stream-halls-container");
			while (halls_container.lastChild) {
				halls_container.removeChild(halls_container.lastChild);
			}

			// build halls list
			var stream_id = 0;
			for (stream_id = 0; stream_id < g_streams_info.length; stream_id++) {
				var stream = g_streams_info[stream_id];

				// if we have to find the default stream, find it and set it as default
				if (stream["default"] && (default_stream_id == -1)) {
					default_stream_id = stream_id;
				}

				// append the hall name to the halls list container
				var small = document.createElement("small");
				var text = document.createTextNode(stream["hall"]);

				// if this is not the default stream id, make links and attach listeners
				// so that you can switch between halls
				if (stream_id != default_stream_id) {
					var anchor = document.createElement("a");
					anchor.setAttribute("href", "#");
					anchor.setAttribute("data-stream-id", stream_id);
					anchor.appendChild(text);

					if (anchor.addEventListener) {
						// For all major browsers, except IE 8 and earlier
						anchor.addEventListener("click", of_hall_link_on_click);
					} else if (x.attachEvent) {
						// For IE 8 and earlier versions
						anchor.attachEvent("onclick", of_hall_link_on_click);
					}


					small.appendChild(anchor);
				} else {
					small.appendChild(text);
				}

				halls_container.appendChild(small);

				// insert separator if not the last element
				if (stream_id != (g_streams_info.length - 1)) {
					var separator = document.createTextNode(" | ");
					halls_container.appendChild(separator);
				}
			}

			var stream = g_streams_info[default_stream_id];
			var qualities = stream["qualities"];
			var default_quality_id = -1;

			// if there is a provided quality id use it (otherwise use the one
			// with default property set to true)
			if (desired_quality_id !== undefined) {
				default_quality_id = desired_quality_id;
			}

			// clear the qualities list
			var qualities_container = document.getElementById("of-stream-qualities-container");
			while (qualities_container.lastChild) {
				qualities_container.removeChild(qualities_container.lastChild);
			}

			// build the qualities list
			var quality_id = 0;
			for (quality_id = 0; quality_id < qualities.length; quality_id++) {
				var quality = qualities[quality_id];

				// if we have to find the default quality, find it and set it as default
				if (quality["default"] && (default_quality_id == -1)) {
					default_quality_id = quality_id;
				}

				var text = document.createTextNode(quality["label"]);

				// if this is not the default quality, make links and attach listeners
				// so that you can switch between qualities
				if (quality_id != default_quality_id) {
					var anchor = document.createElement("a");
					anchor.setAttribute("href", "#");
					anchor.setAttribute("data-stream-id", default_stream_id);
					anchor.setAttribute("data-quality-id", quality_id);
					anchor.appendChild(text);

					if (anchor.addEventListener) {
						// For all major browsers, except IE 8 and earlier
						anchor.addEventListener("click", of_quality_link_on_click);
					} else if (x.attachEvent) {
						// For IE 8 and earlier versions
						anchor.attachEvent("onclick", of_quality_link_on_click);
					}

					qualities_container.appendChild(anchor);
				} else {
					qualities_container.appendChild(text);
				}

				// append separator if this is not the last element
				if (quality_id != qualities.length - 1) {
					var separator = document.createTextNode(" | ");
					qualities_container.appendChild(separator);
				}
			}

			var default_quality = qualities[default_quality_id];

			// update the RTMP link
			var rtmp = document.getElementById("of-stream-rtmp-link");
			rtmp.setAttribute("href", default_quality["rtmp_url"]);

			// update the HLS link
			var hls = document.getElementById("of-stream-hls-link");
			hls.setAttribute("href", default_quality["hls_url"]);

			// update the iframe
			var iframe = document.getElementById("of-stream-iframe");
			iframe.setAttribute("src", default_quality["iframe_url"]);
		}

		// updates the player with the default one in g_streams_info
		of_update_player();
	</script>
</section>
<?php } ?>

<section class="content">
	<?php echo do_shortcode( '[sh-latest-posts cat="news" label="'.pll__('Новини').'"]' ); ?>
<div class="separator"></div>
<div class="col-right sponsors sponsors-frontpage">
    <?php echo do_shortcode( '[sponsors]' ); ?>
    <?php echo do_shortcode( '[partners]' ); ?>
</div>
<div class="separator"></div>
</section>

<?php echo do_shortcode( '[transport]' ); ?>

<?php get_footer(); ?>
