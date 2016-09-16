<div id="main-panel">
    <div class="panel-text-wrap">
        <h2>Zadejte název filmu, který se vám líbí</h2>
    </div>
    <div id="search-wrap">
        <form method="post" action="?akce=search" id="myform">
            <input type="search" name="search" id="search" class="search-live" placeholder="Zadejte prosím název filmu"
                   autocomplete="off"/>
            <input id="search-button" name="test" type="submit" value="">
            <div id="result-live"></div>
            <p id="mysubmit">Zobrazit všechny výsledky</p>
        </form>
    </div>
    <div class="panel-text-wrap">
        <p id="search-info">Zadejte co nejpřesnější název filmu...</p>
    </div>
</div>
<div id="main-button">
    <a href="./?akce=register" class="main-button">RYCHLÁ REGISTRACE</a><a href="./?akce=login" class="main-button">PŘIHLÁŠENÍ</a>
</div>