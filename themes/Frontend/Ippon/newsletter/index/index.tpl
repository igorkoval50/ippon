{extends file="parent:newsletter/index/index.tpl"}

{block name="newsletter_index_index_head"}
    <meta charset="UTF-8" />
    <title>Newsletter</title>
    <style type="text/css">
        a:link, a:visited {
            color:#808080;
            font-size:13px;
            text-decoration:none;
        }
        a:hover, a:active {
            color:#e20612;
            font-size:13px;
            text-decoration:none;
        }
        a:hover {
            color:#e20612;
            font-size:13px;
            text-decoration:none;
        }
        div#navi_unten a {
            color:#808080;
            font-size: 13px !important;
            text-decoration:none;
        }

        body,
        td {
            font-size: 14px;
            font-family: "Montserrat", "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif;
            font-weight: 400;
            color: #333;
            text-rendering: optimizeLegibility;
            -webkit-font-smoothing: antialiased;
        }

        /* Bebas Neue - latin */
        @font-face {
            font-family: 'Bebas Neue';
            src: url('{url module='frontend'}themes/Frontend/Ippon/frontend/_public/src/fonts/bebasneue_thin-webfont.woff2') format('woff2'),
            url('{url module='frontend'}themes/Frontend/Ippon/frontend/_public/src//fonts/bebasneue_thin-webfont.woff') format('woff');
            font-weight: 200;
            font-style: normal;
        }

        @font-face {
            font-family: 'Bebas Neue';
            src: url('{url module='frontend'}themes/Frontend/Ippon/frontend/_public/src//fonts/bebasneue_light-webfont.woff2') format('woff2'),
            url('{url module='frontend'}themes/Frontend/Ippon/frontend/_public/src//fonts/bebasneue_light-webfont.woff') format('woff');
            font-weight: 300;
            font-style: normal;
        }

        @font-face {
            font-family: 'Bebas Neue';
            src: url('{url module='frontend'}themes/Frontend/Ippon/frontend/_public/src//fonts/bebasneue_book-webfont.woff2') format('woff2'),
            url('{url module='frontend'}themes/Frontend/Ippon/frontend/_public/src//fonts/bebasneue_book-webfont.woff') format('woff');
            font-weight: 400;
            font-style: normal;
        }

        @font-face {
            font-family: 'Bebas Neue';
            src: url('{url module='frontend'}themes/Frontend/Ippon/frontend/_public/src//fonts/bebasneue_regular-webfont.woff2') format('woff2'),
            url('{url module='frontend'}themes/Frontend/Ippon/frontend/_public/src//fonts/bebasneue_regular-webfont.woff') format('woff');
            font-weight: 500;
            font-style: normal;
        }

        @font-face {
            font-family: 'Bebas Neue';
            src: url('{url module='frontend'}themes/Frontend/Ippon/frontend/_public/src//fonts/bebasneue_bold-webfont.woff2') format('woff2'),
            url('{url module='frontend'}themes/Frontend/Ippon/frontend/_public/src//fonts/bebasneue_bold-webfont.woff') format('woff');
            font-weight: 700;
            font-style: normal;
        }

        /* montserrat-300 - latin */
        @font-face {
            font-family: 'Montserrat';
            font-style: normal;
            font-weight: 300;
            src: url('{url module='frontend'}themes/Frontend/Ippon/frontend/_public/src//fonts/montserrat-v12-latin-300.eot'); /* IE9 Compat Modes */
            src: local('Montserrat Light'), local('Montserrat-Light'),
            url('{url module='frontend'}themes/Frontend/Ippon/frontend/_public/src//fonts/montserrat-v12-latin-300.eot?#iefix') format('embedded-opentype'), /* IE6-IE8 */
            url('{url module='frontend'}themes/Frontend/Ippon/frontend/_public/src//fonts/montserrat-v12-latin-300.woff2') format('woff2'), /* Super Modern Browsers */
            url('{url module='frontend'}themes/Frontend/Ippon/frontend/_public/src//fonts/montserrat-v12-latin-300.woff') format('woff'), /* Modern Browsers */
            url('{url module='frontend'}themes/Frontend/Ippon/frontend/_public/src//fonts/montserrat-v12-latin-300.ttf') format('truetype'), /* Safari, Android, iOS */
            url('{url module='frontend'}themes/Frontend/Ippon/frontend/_public/src//fonts/montserrat-v12-latin-300.svg#Montserrat') format('svg'); /* Legacy iOS */
        }
        /* montserrat-regular - latin */
        @font-face {
            font-family: 'Montserrat';
            font-style: normal;
            font-weight: 400;
            src: url('{url module='frontend'}themes/Frontend/Ippon/frontend/_public/src//fonts/montserrat-v12-latin-regular.eot'); /* IE9 Compat Modes */
            src: local('Montserrat Regular'), local('Montserrat-Regular'),
            url('{url module='frontend'}themes/Frontend/Ippon/frontend/_public/src//fonts/montserrat-v12-latin-regular.eot?#iefix') format('embedded-opentype'), /* IE6-IE8 */
            url('{url module='frontend'}themes/Frontend/Ippon/frontend/_public/src//fonts/montserrat-v12-latin-regular.woff2') format('woff2'), /* Super Modern Browsers */
            url('{url module='frontend'}themes/Frontend/Ippon/frontend/_public/src//fonts/montserrat-v12-latin-regular.woff') format('woff'), /* Modern Browsers */
            url('{url module='frontend'}themes/Frontend/Ippon/frontend/_public/src//fonts/montserrat-v12-latin-regular.ttf') format('truetype'), /* Safari, Android, iOS */
            url('{url module='frontend'}themes/Frontend/Ippon/frontend/_public/src//fonts/montserrat-v12-latin-regular.svg#Montserrat') format('svg'); /* Legacy iOS */
        }

        /* montserrat-500 - latin */
        @font-face {
            font-family: 'Montserrat';
            font-style: normal;
            font-weight: 500;
            src: url('{url module='frontend'}themes/Frontend/Ippon/frontend/_public/src//fonts/montserrat-v12-latin-500.eot'); /* IE9 Compat Modes */
            src: local('Montserrat Medium'), local('Montserrat-Medium'),
            url('{url module='frontend'}themes/Frontend/Ippon/frontend/_public/src//fonts/montserrat-v12-latin-500.eot?#iefix') format('embedded-opentype'), /* IE6-IE8 */
            url('{url module='frontend'}themes/Frontend/Ippon/frontend/_public/src//fonts/montserrat-v12-latin-500.woff2') format('woff2'), /* Super Modern Browsers */
            url('{url module='frontend'}themes/Frontend/Ippon/frontend/_public/src//fonts/montserrat-v12-latin-500.woff') format('woff'), /* Modern Browsers */
            url('{url module='frontend'}themes/Frontend/Ippon/frontend/_public/src//fonts/montserrat-v12-latin-500.ttf') format('truetype'), /* Safari, Android, iOS */
            url('{url module='frontend'}themes/Frontend/Ippon/frontend/_public/src//fonts/montserrat-v12-latin-500.svg#Montserrat') format('svg'); /* Legacy iOS */
        }

        /* montserrat-600 - latin */
        @font-face {
            font-family: 'Montserrat';
            font-style: normal;
            font-weight: 600;
            src: url('{url module='frontend'}themes/Frontend/Ippon/frontend/_public/src//fonts/montserrat-v12-latin-600.eot'); /* IE9 Compat Modes */
            src: local('Montserrat SemiBold'), local('Montserrat-SemiBold'),
            url('{url module='frontend'}themes/Frontend/Ippon/frontend/_public/src//fonts/montserrat-v12-latin-600.eot?#iefix') format('embedded-opentype'), /* IE6-IE8 */
            url('{url module='frontend'}themes/Frontend/Ippon/frontend/_public/src//fonts/montserrat-v12-latin-600.woff2') format('woff2'), /* Super Modern Browsers */
            url('{url module='frontend'}themes/Frontend/Ippon/frontend/_public/src//fonts/montserrat-v12-latin-600.woff') format('woff'), /* Modern Browsers */
            url('{url module='frontend'}themes/Frontend/Ippon/frontend/_public/src//fonts/montserrat-v12-latin-600.ttf') format('truetype'), /* Safari, Android, iOS */
            url('{url module='frontend'}themes/Frontend/Ippon/frontend/_public/src//fonts/montserrat-v12-latin-600.svg#Montserrat') format('svg'); /* Legacy iOS */
        }

        /* montserrat-700 - latin */
        @font-face {
            font-family: 'Montserrat';
            font-style: normal;
            font-weight: 700;
            src: url('{url module='frontend'}themes/Frontend/Ippon/frontend/_public/src//fonts/montserrat-v12-latin-700.eot'); /* IE9 Compat Modes */
            src: local('Montserrat Bold'), local('Montserrat-Bold'),
            url('{url module='frontend'}themes/Frontend/Ippon/frontend/_public/src//fonts/montserrat-v12-latin-700.eot?#iefix') format('embedded-opentype'), /* IE6-IE8 */
            url('{url module='frontend'}themes/Frontend/Ippon/frontend/_public/src//fonts/montserrat-v12-latin-700.woff2') format('woff2'), /* Super Modern Browsers */
            url('{url module='frontend'}themes/Frontend/Ippon/frontend/_public/src//fonts/montserrat-v12-latin-700.woff') format('woff'), /* Modern Browsers */
            url('{url module='frontend'}themes/Frontend/Ippon/frontend/_public/src//fonts/montserrat-v12-latin-700.ttf') format('truetype'), /* Safari, Android, iOS */
            url('{url module='frontend'}themes/Frontend/Ippon/frontend/_public/src//fonts/montserrat-v12-latin-700.svg#Montserrat') format('svg'); /* Legacy iOS */
        }
    </style>
{/block}