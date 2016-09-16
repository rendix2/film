<div id="results" class="shadow">
    <div id="results-search-panel">
        <div id="search-module" class="shadow">
            <form method="post" action="?akce=search" id="myform">
                <input type="search" name="search" id="search" placeholder="Zadejte prosím název filmu"
                       autocomplete="off"/><input id="search-button" name="test" type="submit" value="">
            </form>
        </div>
    </div>
    <div id="results-panel">
        {if $data|count == 1}
            <span class="bold">Byla nalezena 1 položka.</span>
        {elseif $data|count > 1 AND $data|count < 5}
            <span class="bold">Byly nalezeny {$data|count} položky.</span>
        {elseif $data|count >= 5}
            <span class="bold">Bylo nalezeno {$data|count} položek.</span>
        {/if}
        <div class="panel-wrap"><span class="bold">Řazení</span>
            <form id="sort">
                <input type="radio" name="sort" id="m-name" value="m-name" checked><label for="m-name">Název</label>
                <input type="radio" name="sort" id="m-year" value="m-year"><label for="m-year">Rok</label>
            </form>
        </div>
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