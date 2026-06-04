
/**
 * Config
 * -------------------------------------------------------------------------------------
 * ! IMPORTANT: Make sure you clear the browser local storage In order to see the config changes in the template.
 * ! To clear local storage: (https://www.leadshook.com/help/how-to-clear-local-storage-in-google-chrome-browser/).
 */

'use strict';

// JS global variables
let config = {
    colors: {
        primary: '#5a8dee',
        secondary: '#69809a',
        success: '#39da8a',
        info: '#00cfdd',
        warning: '#fdac41',
        danger: '#ff5b5c',
        dark: '#495563',
        black: '#000',
        white: '#fff',
        cardColor: '#fff',
        bodyBg: '#f2f2f6',
        bodyColor: '#677788',
        headingColor: '#516377',
        textMuted: '#a8b1bb',
        borderColor: '#e9ecee'
    },
    colors_label: {
        primary: '#5a8dee29',
        secondary: '#69809a29',
        success: '#39da8a29',
        info: '#00cfdd29',
        warning: '#fdac4129',
        danger: '#ff5b5c29',
        dark: '#49556329'
    },
    colors_dark: {
        cardColor: '#283144',
        bodyBg: '#1c222f',
        bodyColor: '#a1b0cb',
        headingColor: '#d8deea',
        textMuted: '#8295ba',
        borderColor: '#36445d'
    },
    enableMenuLocalStorage: true // Enable menu state with local storage support
};

let assetsPath = document.documentElement.getAttribute('data-assets-path'),
    templateName = document.documentElement.getAttribute('data-template'),
    rtlSupport = true; // set true for rtl support (rtl + ltr), false for ltr only.


if (typeof TemplateCustomizer !== 'undefined') {
    window.templateCustomizer = new TemplateCustomizer({
        cssPath: assetsPath + 'vendor/css' + (rtlSupport ? '/rtl' : '') + '/',
        themesPath: assetsPath + 'vendor/css' + (rtlSupport ? '/rtl' : '') + '/',
        displayCustomizer: true,
        // lang: 'fr',
        // defaultTheme: 2,
        // defaultStyle: 'light',
        // defaultTextDir: 'ltr',
        // defaultLayoutType: 'fixed',
        // defaultMenuCollapsed: true,
        // defaultNavbarFixed: true,
        // defaultFooterFixed: false
        defaultShowDropdownOnHover: true
        // controls: [
        //   'rtl',
        //   'style',
        //   'layoutType',
        //   'showDropdownOnHover',
        //   'layoutNavbarFixed',
        //   'layoutFooterFixed',
        //   'themes'
        // ],
    });
}

const purpleColor = '#836AF9',
    yellowColor = '#ffe800',
    cyanColor = '#28dac6',
    orangeColor = '#FF8132',
    orangeLightColor = '#FDAC34',
    oceanBlueColor = '#299AFF',
    greyColor = '#4F5D70',
    greyLightColor = '#EDF1F4',
    blueColor = '#2B9AFF',
    blueLightColor = '#84D0FF';

let cardColor, headingColor, labelColor, borderColor, legendColor;

if (blueLightColor) {
    cardColor = config.colors_dark.cardColor;
    headingColor = config.colors_dark.headingColor;
    labelColor = config.colors_dark.textMuted;
    legendColor = config.colors_dark.bodyColor;
    borderColor = config.colors_dark.borderColor;
} else {
    cardColor = config.colors.cardColor;
    headingColor = config.colors.headingColor;
    labelColor = config.colors.textMuted;
    legendColor = config.colors.bodyColor;
    borderColor = config.colors.borderColor;
}

// Set height according to their data-height
// --------------------------------------------------------------------
const chartList = document.querySelectorAll('.chartjs');
chartList.forEach(function (chartListItem) {
    chartListItem.height = chartListItem.dataset.height;
});

