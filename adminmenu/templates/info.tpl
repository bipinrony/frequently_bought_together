{*only show custom messages when using an old version*}
{if isset($modern) && $modern === false}
    {if isset($cFehler) && $cFehler|strlen > 0}
        <p class="box_error">{$cFehler}</p>
    {/if}
    {if isset($cHinweis) && $cHinweis|strlen > 0}
        <p class="box_success">{$cHinweis}</p>
    {/if}
{/if}
<p> 

</p>
