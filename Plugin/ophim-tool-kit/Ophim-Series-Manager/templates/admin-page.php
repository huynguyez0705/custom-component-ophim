<?php defined('ABSPATH') or die(); ?>
<div class="wrap">
  <h1>OPhim Series Manager Pro <small style="color:#666">v5.0</small></h1>

  <div style="margin:30px 0;position:relative;">
    <input type="text" id="osm-search" placeholder="Tìm phim theo tên hoặc ID..." style="width:600px;padding:12px;font-size:16px;">
    <button id="osm-select-all" class="button" style="margin-left:10px;height:44px;">Chọn tất cả</button>
    <button id="osm-add-selected" class="button button-primary" style="height:44px;margin-left:5px;">
      Thêm vào danh sách (<span id="osm-count">0</span>)
    </button>
    <div id="osm-suggestions"></div>
  </div>

  <div id="osm-selected-films" style="margin:40px 0;"></div>
  <div id="osm-groups"></div>
</div>