<?php
  $subs_forums_arr = bbp_get_user_subscribed_forum_ids();
  $public_forums = get_forum_query(0);
?>
<div id="forum-subscriptions-parent">
  <button id="toggle-all-forums">Toggle All</button>
  <button id="save-forums">Save</button>
  <div class="forum-subscriptions">
    <div class="forum-subscriptions-list">
      <?php foreach($public_forums as $forum): ?>
        <div class="forum-parent-card" style="height: 49px;">
          <div class="parent-check">
            <div class="fs-toggle" data-pid="'.$forum->ID.'">
              <input type="checkbox" id="<?=$forum->ID?>" <?=check_if_subs($subs_forums_arr, $forum->ID)?>>&nbsp;&nbsp;
              <label><?=$forum->post_title?></label>
            </div>
          </div>
          <div class="sub-check" data-parent="'.$forum->ID.'">
            <?php $public_forums_child = get_forum_query($forum->ID) ?>
            <?php foreach($public_forums_child as $subforum): ?>
              &nbsp;&nbsp;&nbsp;
              <input type="checkbox" id="<?=$subforum->ID?>" <?=check_if_subs($subs_forums_arr, $subforum->ID)?>>
              &nbsp;&nbsp;
              <label for="<?= $subforum->ID?>"><?= $subforum->post_title?></label><br>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>