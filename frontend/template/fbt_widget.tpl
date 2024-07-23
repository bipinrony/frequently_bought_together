<div class="fbt_widget_container container" data-current-article-id="{$fbt_article_id}" style="border: {$pluginData['fbt_border_width']} solid {$pluginData['fbt_border_color']};">
    <h2>{$pluginData['fbt_title']}</h2>
    <div class="row">
        <div class="col-sm-6" style="display: inline-flex;">
            {foreach $fbtProducts as $key => $product}
                <div class="fbt-product-box"  {if count($fbtProducts)==2 } style="width: 20% !important;"   {/if}>
                    {if $key == 0}
                        <img src="{$product->image_url}" class="img-fluid fbt-img">
                    {else}
                        <a href="{$product->cSeo}"><img src="{$product->image_url}" class="img-fluid fbt-img"></a>
                    {/if}
                </div>
                {if $key < sizeof($fbtProducts) - 1 }
                    <div class="fbt-thumbnailPlus">
                        <i class="fbt-icon-plus"></i>
                    </div>
                {/if}
            {/foreach}
        </div>
        <div class="col-sm-6">
            {foreach $fbtProducts as $key => $product}
            
                <div>
                    <span class="fbt-product-title">
                        <input type="checkbox" checked="checked" class="fbt-checkbox" name="product_item" value="{$product->kArtikel}">
                        {if $key == 0}
                            <span class="fbt-product-self">Dieser Artikel:</span>
                            <span> {$product->cName} </span>
                        {else}
                            <a href="{$product->cSeo}"><span> {$product->cName} </span></a> - <strong class="{($product->alterVKLocalized != NULL) ? 'fbt-clr-highlight' : ''}">{$product->localizedPrices[0]}</strong> <span>{($product->alterVKLocalized != NULL) ? "(<span class='fbt-alter-price'>{$product->alterVKLocalized}</span>)" : ''}</span>
                        {/if}
                    </span>
                    <span class="fbt-form-area">
                        <form class="jtl-validate fbt-buy-form" target="_self" id="fbt_form{$key}" action="{$product->cSeo}" method="POST">
                            <input type="hidden" class="jtl_token" name="jtl_token" value="{$fbt_jtl_token}">
                            {foreach $product->teigenschaftkombiwert as $key => $kombiwert}
                                <select class="fbt-hideme" name="eigenschaftwert[{$kombiwert->kEigenschaft}]">
                                    <option class="bs-title-option" value="{$kombiwert->kEigenschaftWert}"></option>
                                </select>
                            {/foreach}
                            <input type="hidden" class="form-control " placeholder="Additional contact mail (leave blank)*" id="sp_additional_mail_{$key}" name="sp_additional_mail">
                            {if $product->kVaterArtikel > 0}
                                <input type="hidden" class="form-control VariKindArtikel" value="{$product->kArtikel}" name="VariKindArtikel">
                                <input type="hidden" class="form-control current_article" value="{$product->kVaterArtikel}" name="a">
                            {else}
                                 <input type="hidden" class="form-control current_article" value="{$product->kArtikel}" name="a">
                            {/if}
                            <input type="hidden" class="form-control " value="0" name="wlPos">
                            <input type="hidden" class="form-control " value="1" name="inWarenkorb">

                            <input type="hidden" class="form-control fbt-wke" value="1" name="wke">
                            <input type="hidden" class="form-control " value="1" name="show">
                            <input type="hidden" class="form-control fbt-kKundengruppe" value="1" name="kKundengruppe">
                            <input type="hidden" class="form-control fbt-kSprache" value="1" name="kSprache">
                            {if ($product->fMindestbestellmenge)}
                                <input type="hidden" class="form-control quantity" id="quantity-{$key}" value="{$product->fMindestbestellmenge}" min="{$product->fMindestbestellmenge}" step="1" name="anzahl" aria-label="Menge" data-decimals="0">
                            {else}
                                <input type="hidden" class="form-control quantity" id="quantity-{$key}" value="1" min="1" step="1" name="anzahl" aria-label="Menge" data-decimals="0">
                            {/if}
                            <button type="submit" class="btn js-cfg-validate btn-primary btn-block fbt-hideme" name="inWarenkorb" value="In den Warenkorb" aria-label="In den Warenkorb" data-add-cart="basket-add">
                        </form>
                    </span>
                </div>
            {/foreach}
            <br>
            <button class="fbt-add-to-cart-btn" style="background-color:{$pluginData['fbt_button_color']};border-radius:{$pluginData['fbt_button_radius']}">{$pluginData['fbt_button_text']}</button>
        </div>
    </div>
</div>
<script type="text/javascript">
    var fbt_url = "fbtResponse";
    function saveResponse(){
        postData = {
            fbt_article_id :$('.fbt_widget_container').data('currentArticleId')
        };
        $.ajax({
            url: fbt_url,
            type: "post",
            data: postData ,
            success: function (response) {
                console.log(response);
                location.reload();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
                if (jqXHR.status == 404) {
                    fbt_url = "fbtResponse_1";
                    saveResponse();
                }
            }
        });
    }

    function postToIO() {
        var isFbtAdded = false;
        $('.fbt-checkbox').each(function(i, obj) {
            $('.fbt-kKundengruppe').val($("input[name=kKundengruppe]").val());
            $('.fbt-kSprache').val($("input[name=kSprache]").val());
            $('.fbt-wke').val($("input[name=wke]").val());
            if(obj.checked){
                let params =[];
                let productId = 0;
                if($('#fbt_form' + i).find('.VariKindArtikel').val() === undefined) {
                    productId = $('#fbt_form' + i).find('.current_article').val();
                } else {
                    productId = $('#fbt_form' + i).find('.VariKindArtikel').val()
                }
                productId = parseInt(productId);
                params.push(productId);

                let qty = $('#fbt_form' + i).find('#quantity-'+i).val();
                params.push(qty);
                
                params.push($('#fbt_form' + i).serializeObject());
                
                let postData = {
                    name: 'pushToBasket',
                    params: params
                };

                let formData = {
                    io: JSON.stringify(postData)
                };
                // $.post('io.php', formData, function(data){
                //     console.log('submitted successfully!');
                // });
                // isFbtAdded = true;

                $.ajax({
                    type: 'POST',
                    url: 'io.php',
                    data: formData,
                    dataType: "json",
                    async: false
                }).done(function (data , textStatus, jqXHR) {
                    isFbtAdded = true;
                    console.log('submitted successfully!');
                });
            }
        });

        // Save usage record.
        if(isFbtAdded) {
            setTimeout(function(){
                saveResponse();
            }, 1000);
        }
    }

    function postForms() {
        var isFbtAdded = false;
        $('.fbt-checkbox').each(function(i, obj) {
            if(obj.checked){
                let articleId = '';
                if($('#fbt_form' + i).find('.VariKindArtikel').val() === undefined) {
                    articleId = $('#fbt_form' + i).find('.current_article').val();
                } else {
                    articleId = $('#fbt_form' + i).find('.VariKindArtikel').val();
                }

                let formData = {
                    jtl_token: $('#fbt_form' + i).find('.jtl_token').val(),
                    a: articleId,
                    wke: $("input[name=wke]").val(),
                    show: 1,
                    kKundengruppe: $("input[name=kKundengruppe]").val(),
                    kSprache: $("input[name=kSprache]").val(),
                    anzahl: 1,
                    inWarenkorb: "In den Warenkorb"
                };

                $.ajax({
                    type: 'POST',
                    url: $('#fbt_form' + i).attr('action'),
                    data: formData,
                    async:false
                }).done(function (data , textStatus, jqXHR) {
                    isFbtAdded = true;
                    console.log('submitted successfully!');
                });
            }
        });

        // Save usage record.
        if(isFbtAdded) {
            setTimeout(function(){
                saveResponse();
            }, 1000);
        }
    }

    $(document).ready(function() {
        $(".fbt-add-to-cart-btn").click(function(){
            showLoader();
            postToIO();
            // postForms();
        });
    });

    function showLoader()
    {
        let loaderHtml = $(".fbt-add-to-cart-btn").text()+ ` <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
        `;
        $(".fbt-add-to-cart-btn").css('background-color','#7588cc').attr('disabled', true).html(loaderHtml);
    }
</script>
