<?php
$episodeList = episodeList();
$episodeData = isset($episodeList[0]) && !empty($episodeList[0]) ? $episodeList[0] : (isset($episodeList[1]) && !empty($episodeList[1]) ? $episodeList[1] : null);
if ((empty($episodeData['server_data'][intval(episodeName()) - 1]["link_m3u8"])  && !empty($episodeData['server_data'][intval(episodeName()) - 1]["link_embed_new"])) || count($episodeData['server_data']) == 1 && !empty($episodeData['server_data'][0]["link_embed_new"])) {  ?>
  <a data-id="<?= episodeName() ?>" data-link="<?= embedNewEpisodeUrl() ?>" data-type="embed"
    onclick="chooseStreamingServer(this)" class="streaming-server btn-sv btn-hls btn btn-primary">VIP #1</a>
  <?php
} else if ($episodeData) {
?>
    <a data-id="<?= episodeName() ?>"
      data-link="<?= embedEpisodeUrl() ?>"
      data-type="embed" onclick="chooseStreamingServer(this)"
      class="streaming-server btn-sv btn-hls btn btn-primary">VIP #1</a>
    <a data-id="<?= episodeName() ?>"
      data-link="<?= m3u8EpisodeUrl() ?>"
      data-type="m3u8" onclick="chooseStreamingServer(this)"
      class="streaming-server btn-sv btn-hls btn btn-primary">VIP #2</a>
<?php }?>