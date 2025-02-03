<div class="notice">
    <p>
        Mẹo tìm phim không thấy, các bạn vui lòng tìm trên Google với cú pháp sau, 
        <span style="color: #ff0000;"><strong>Tên Phim + Domain</strong></span>.
        Ví dụ tìm phim 
        <strong><?php echo get_the_title(); ?></strong>, các bạn tìm là 
        <span class= "notice-domain"><strong><a  href="<?php echo get_permalink(get_the_ID()); ?>"><?php echo get_the_title(); ?></a>
        <a href="<?php echo home_url(); ?>">Motchill</a> </strong></span> bạn xem không giật lag và không quảng cáo.
    </p>
</div>
<div class="notice">
        - Tham gia nhóm Telegram <span class= "notice-domain"><a href="https://t.me/congdongtuphim" target="_blank" rel="nofollow">https://t.me/congdongtuphim</a></span> để được hỗ trợ nha.
    </div>
<style>
.notice{background-color:#4d4d4d;border:1px solid #dfe3e8;padding:10px 15px;border-radius:5px;font-size:12px;color:#fff;margin:10px 0;box-shadow: 1px 1px 10px #3a3a3a;}
.notice-domain a {color:#da843d}
.notice.warning{background-color:#fffaf0;border-color:#fff1cc;color:#856404}
.notice.success{background-color:#f3fdf7;border-color:#d8f3e0;color:#155724}
.notice.error{background-color:#fdf2f3;border-color:#f6d8da;color:#721c24}
.notice.info{background-color:#f0f8ff;border-color:#cce7ff;color:#004085}
</style>
