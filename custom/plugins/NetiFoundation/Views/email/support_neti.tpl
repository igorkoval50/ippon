<table border="0" cellspacing="0" cellpadding="0" style="padding-left: 10px; padding-top:10px">
    <tr>
        <th colspan="2">
            <font style="size: 15px"><b>{$product} - {$type} von {$name} ({$company})</b></font>
        </th>
    </tr>
    <tr>
        <td style="padding-bottom: 10px" valign="top"><b>Typ: </b></td>
        <td valign="top" style="padding-left:10px">{$type}</td>
    </tr>
    <tr>
        <td style="padding-bottom: 10px" valign="top"><b>Shopwareversion: </b></td>
        <td valign="top" style="padding-left:10px">
            <p>{$shopwareVersion}</p>
        </td>
    </tr>
    <tr>
        <td style="padding-bottom: 10px" valign="top"><b>Pluginversion: </b></td>
        <td valign="top" style="padding-left:10px">
            <p>{$pluginVersion}</p>
        </td>
    </tr>
    <tr>
        <td style="padding-bottom: 10px" valign="top"><b>Browser: </b></td>
        <td valign="top" style="padding-left:10px">{$browser}</td>
    </tr>
    <tr>
        <td style="padding-bottom: 10px" valign="top"><b>Serversoftware: </b></td>
        <td valign="top" style="padding-left:10px">{$serverSoftware}</td>
    </tr>
    <tr>
        <td style="padding-bottom: 10px" valign="top"><b>PHP-Version: </b></td>
        <td valign="top" style="padding-left:10px">{$phpVersion}</td>
    </tr>
    <tr>
        <td style="padding-bottom: 10px" valign="top"><b>Shop-URL: </b></td>
        <td valign="top" style="padding-left:10px">{$shopUrl}</td>
    </tr>
    <tr>
        <td style="padding-bottom: 10px" valign="top"><b>ShopTemplates: </b></td>
        <td valign="top" style="padding-left:10px">
            {foreach key=key from=$shops item=shop}
                - {$shop.name} ({$key}): {$shop.template.name} ({if 2 === $shop.template.version}emotion{elseif 3 === $shop.template.version}Responsive{/if})<br />
            {/foreach}
            <br />
        </td>
    </tr>
    <tr>
        <th colspan="2">
            <font style="size: 15px"><b>Kunde</b></font>
        </th>
    </tr>
    <tr>
        <td style="padding-bottom: 10px" valign="top"><b>Name: </b></td>
        <td valign="top" style="padding-left:10px">{$name}</td>
    </tr>
    <tr>
        <td style="padding-bottom: 10px" valign="top"><b>Firma: </b></td>
        <td valign="top" style="padding-left:10px">{$company}</td>
    </tr>
    <tr>
        <td style="padding-bottom: 10px" valign="top"><b>E-Mail: </b></td>
        <td valign="top" style="padding-left:10px">{$email}</td>
    </tr>
    <tr>
        <td style="padding-bottom: 10px" valign="top"><b>Telefon: </b></td>
        <td valign="top" style="padding-left:10px">{$tel}</td>
    </tr>
    <tr>
        <td style="padding-bottom: 10px" valign="top"><b>Zeitpunkt der Anfrage: </b></td>
        <td valign="top" style="padding-left:10px">{$time} Uhr</td>
    </tr>
    <tr>
        <td style="padding-bottom: 10px" valign="top"><b>Betreff: </b></td>
        <td valign="top" style="padding-left:10px">{$subject}</td>
    </tr>
    <tr>
        <td style="padding-bottom: 10px" valign="top"><b>Nachricht: </b></td>
        <td valign="top" style="padding-left:10px">
            <p>{$message}</p>
        </td>
    </tr>
</table>
