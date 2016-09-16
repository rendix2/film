{foreach $data as $movie}

<div class="result-item-live" tabindex="0">
<div class="result-image-live" style="background-image: url('./images/{$movie['movie_picture']}.jpg')"></div>
<div class="result-info-live"><h3>{$movie['movie_name_czech']}</h3>
<p>{$movie['movie_year']}</p>
</div>
</div>
</div>

{foreachelse}
Bohužel nic jsem nenašel :(((
{/foreach}
