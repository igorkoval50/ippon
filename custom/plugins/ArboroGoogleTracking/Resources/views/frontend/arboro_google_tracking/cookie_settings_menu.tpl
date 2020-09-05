{block name='arboro_tracking_cookie_settings_menu'}
    <div class="arboro-cookie-settings-menu">
        <p class="panel--title is--underline">
            {s namespace='frontend/ArboroGoogleTracking' name='cookieMenuTitle'}Cookie Einstellungen{/s}
        </p>
        <div class="panel--body">
            <p class="arboro-cookie-settings-menu--text">
                {s namespace='frontend/ArboroGoogleTracking' name='cookieMenuText'}
                    Hier können Sie Ihre Cookie Einstellungen für dieses Gerät anpassen.
                {/s}
            </p>
            <table>
                <tr>
                    <td><strong>{s namespace='frontend/ArboroGoogleTracking' name='cookieMenuTechnicalCookies'}Technisch notwendige Cookies{/s}</strong></td>
                    <td>{include file='frontend/arboro_google_tracking/switch.tpl' switchId="arboro-switch-technical" isTechnical=true}</td>
                </tr>

                <tr>
                    <td><strong>Google Analytics</strong></td>
                    <td>{include file='frontend/arboro_google_tracking/switch.tpl' switchId="arboro-switch-ga" isTechnical=false}</td>
                </tr>
            </table>
            <p class="arboro-cookie-settings-menu--btn btn is--primary">
                {s namespace='frontend/ArboroGoogleTracking' name='cookieMenuBtn'}
                    Speichern
                {/s}
            </p>
            <p class="arboro-cookie-settings-menu--label">
                {s namespace='frontend/ArboroGoogleTracking' name='cookieMenuLabel'}
                    Einstellungen wurden übernommen
                {/s}
            </p>
        </div>
    </div>
{/block}