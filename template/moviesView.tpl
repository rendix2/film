<div id="results">
    <div id="results-panel">
        {if $data|count == 1}
            <h2>Byla nalezena 1 položka.</h2>
        {elseif $data|count > 1 AND $data|count < 5}
            <h2>Byly nalezeny {$data|count} položky.</h2>
        {elseif $data|count >= 5}
            <h2>Bylo nalezeno {$data|count} položek.</h2>
        {/if}
    </div>
    <div class="space-killer">
        {foreach $data as $movie}
            <div class="result-marginer">
                <div class="result-item">
                    <div class="result-image"
                         style="background-image: url('./images/{$movie['movie_picture']}.jpg')"></div>
                    <div class="result-info"><h3>{$movie['movie_name_czech']}</h3>
                        <p>{$movie['movie_year']}</p>
                    </div>
                </div>
            </div>
            {foreachelse}
            Bohužel nic jsem nenašel :(((
        {/foreach}
    </div>
</div>