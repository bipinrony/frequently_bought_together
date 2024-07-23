<style>
    table {
        font-family: arial, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }

    td, th {
        border: 1px solid #dddddd;
        text-align: left;
        padding: 8px;
    }

    tr:nth-child(even) {
        background-color: #dddddd;
    }
    .fbt-used-times
    {
        background: #5cbcf6;
        padding: 5px 7px;
        border-radius: 4px;
        color: #fff;
        width: 10%;
        margin-bottom: 15px;
        text-align: center;
        float: left;
    }
    .fbt-pagination{
        float: right;
        margin-top: 30px;
        width: 35%;
    }
    .fbt-count
    {
        font-size: 16px;
        font-weight: 700;
    }

    .fbt-pagination-btns{
        float: right;
    }
    .fbt-pagination img{
        float: left;
    }

    .fbt-ajax-loader{
        display: none;
    }

</style>

<div class="fbt-stats-header">
    <div class="fbt-used-times">
        Used <br>
        <span class="fbt-count"> {$click_count} </span><br>
        times
    </div>
    <div class="fbt-pagination">
        <span class="fbt-ajax-loader">
            <img src="../plugins/frequently_bought_together/adminmenu/templates/images/loader.gif">
        </span>

        <div class="fbt-pagination-btns">
            <button type="button" class="btn btn-default fbt-previous-btn" onclick="window.loadStatistics.prevPage();"><i class="fa fas fa-backward" aria-hidden="true"></i></button>
            <button type="button" class="btn btn-default disabled fbt-current-page-indicator" disabled="disabled">1</button>
            <button type="button" class="btn btn-default fbt-next-btn" onclick="window.loadStatistics.nextPage();"><i class="fa fas fa-forward" aria-hidden="true"></i></button>
        </div>
    </div>

</div>

<table id="fbt-statistics-table">
    <thead>
        <th>Product</th>
        <th>Bought With</th>
        <th>Bought Together Times</th>
    </thead>
    <tbody>
{*        {include file='./snippets/statistics_detail.tpl'}*}
    </tbody>
</table>

<script>
    (function ($) {
        let isMorePages = true;
        var loadStatistics = function () {
            var currentPageIndex = 0;
            var init = function () {
                loadOrders();
            };
            var loadOrders = function () {
                var params = {
                    page_number: currentPageIndex,
                };
                $('.fbt-current-page-indicator').html(currentPageIndex + 1);
                doAjaxCall(
                    'action=loadStatistics&isStatisticsAjax=1&page_number=' + currentPageIndex,
                    params,
                    function (response) {
                        console.log(response);
                        if (response.status === "success") {
                            $('#fbt-statistics-table tbody').html(response.data.content);
                            if(response.data.count < 10) {
                                $('.fbt-next-btn').addClass('disabled');
                                isMorePages = false;
                            }
                        } else if (currentPageIndex == 0) {
                            $('.fbt-next-btn').addClass('disabled');
                            // end reached, go back. (Check for > 0 is needed to avoid endless loop for globally zero orders)
                            prevPage();
                        } else if(data.content && !data.content.length) {
                            // simply no orders received
                        }
                    }
                );
            };
            var nextPage = function () {
                $('.fbt-previous-btn').removeClass('disabled');
                if(isMorePages) {
                    currentPageIndex = currentPageIndex + 1;
                    loadOrders();
                }
            };
            var prevPage = function () {
                isMorePages = true;
                $('.fbt-next-btn').removeClass('disabled');
                if(currentPageIndex > 0) {
                currentPageIndex = Math.max(currentPageIndex - 1, 0);
                    loadOrders();
                }
            };
            var firstPage = function () {
                currentPageIndex = 0;
                loadOrders();
            };
            var doAjaxCall = function (action, parameters, successCallback, failureCallback) {
                var $content = $('#fbt-statistics-table tbody');
                $('.fbt-ajax-loader').show();
                var ajaxURL = '{$ajaxUrl}';

                var request = $.ajax({
                    url: ajaxURL + '&' + action,
                    method: 'POST',
                    dataType: 'json',
                    data: parameters
                });
                request.done(function (response) {
                    if (response.status === 'success') {
                        if (typeof successCallback === 'function') {
                            successCallback(response);
                        }
                    } else {
                        console.log('Something went wrong!');
                    }
                });
                request.fail(function (jqXHR, textStatus, errorThrown) {
                    console.log('Failed: ' + jqXHR + "," + textStatus + "," + errorThrown);
                });
                request.always(function () {
                    $('.fbt-ajax-loader').hide();
                });
            };
            var updateCurrentPageIndicator = function () {
                $('.fbt-current-page-indicator').text(currentPageIndex + 1);
            };

            init();

            return {
                nextPage: nextPage,
                prevPage: prevPage,
                firstPage: firstPage
            }
        };
        $(document).ready(function () {
            window.loadStatistics = loadStatistics();
        });
    })(jQuery);
</script>

