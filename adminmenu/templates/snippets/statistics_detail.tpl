{foreach $bought_product as $bt_product}
    <tr>
        <td>{$bt_product['original_product']}</td>
        <td>{$bt_product['bought_with']}</td>
        <td>{$bt_product['total']}</td>
    </tr>
{/foreach}