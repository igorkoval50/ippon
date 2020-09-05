Ext.define('Shopware.apps.Promotionbanner.view.detail.Promotionbanner', {
    extend: 'Shopware.model.Container',
    padding: 25,
    title: '{s name=PromotionbannerDetailTitle}Promotionbanner{/s}',
    configure: function () {
        return {
            searchController: 'Promotionbanner',
            fieldSets: [
                {
                    title: '{s name=PromotionbannerDetailFieldsetTitleGeneral}Allgemeine Einstellungen{/s}',
                    layout: 'anchor',
                    columnWidth: 1.0,
                    fields: {
                        active: { fieldLabel: '{s name=PromotionbannerDetailActive}Aktiv{/s}', xtype: 'checkboxfield', inputValue: true, uncheckedValue: false},
                        positionnumber: { fieldLabel: '{s name=PromotionbannerDetailPositionnumber}Positionsnummer{/s}', anchor: "25%"},
                        label: { fieldLabel: '{s name=PromotionbannerDetailLabel}Interne Bezeichnung des Promotionbanners{/s}', allowBlank: false},
                        collapsible: { fieldLabel: '{s name=PromotionbannerDetailCollapsible}Promotionbanner ist dauerhaft zuklapp- und ausblendbar{/s}', xtype: 'checkboxfield', inputValue: true, uncheckedValue: false},
                        collapsiblecookielifetime: { fieldLabel: '{s name=PromotionbannerDetailCollapsiblecookielifetime}Cookie - Lebensdauer in Tagen, solange der Promotionbanner ausgeblendet bleiben soll{/s}', anchor: "25%"},
                        hidecollapseicon: { fieldLabel: '{s name=PromotionbannerDetailShowcollapseicon}Aufklapp - Icon beim zugeklappten und ausgeblendeten Promotionbanner ausblenden{/s}', xtype: 'checkboxfield', inputValue: true, uncheckedValue: false},
                        collapseiconbackgroundcolor: { fieldLabel: '{s name=PromotionbannerDetailCollapseiconbackgroundcolor}Hintergrundfarbe des Aufklapp - Icons (Hexcode){/s}', anchor: "50%"},
                        collapseiconfontcolor: { fieldLabel: '{s name=PromotionbannerDetailCollapseiconfontcolor}Schriftfarbe des Aufklapp - Icons (Hexcode){/s}', anchor: "25%"},
                        position: { fieldLabel: '{s name=PromotionbannerDetailPosition}Position des Promotionbanners{/s}', xtype: 'combo', mode: 'local', queryMode: 'local', triggerAction: 'all', forceSelection: true, editable: false, store: [[0, '0 - unterhalb des body-Tags'], [1, '1 - unterhalb des Menüs'], [2, '2 - am unteren Bildschirmrand (fixiert)'], [3, '3 - im OffCanvas - Menü / der linken Sidebar unterhalb der Kategorien'], [4, '4 - in einer Modalbox'], [5, '5 - unbestimmt (beispielsweise über ein Auswahlfeld beim Artikel, bei einer Kategorie, uvm.)']]},
                        shop_id: { fieldLabel: '{s name=PromotionbannerDetailShopId}Anzeigen im Shop (Pflichtfeld){/s}', allowBlank: false},
                        showinallshops: { fieldLabel: '{s name=PromotionbannerDetailShowinallshops}In allen anderen Shops auch anzeigen{/s}', xtype: 'checkboxfield', inputValue: true, uncheckedValue: false},
                        showoncontroller: { fieldLabel: '{s name=PromotionbannerDetailShowoncontroller}Anzeigen auf folgender Seite{/s}', xtype: 'combo', mode: 'local', queryMode: 'local', triggerAction: 'all', forceSelection: true, editable: false, store: [[0, '0 - im gesamten Shop'], [1, '1 - Startseite'], [2, '2 - Kategorie Listings'], [3, '3 - Artikeldetailseite'], [4, '4 - Registrierung'], [5, '5 - Warenkorb'], [6, '6 - Registrierung im Checkout'], [7, '7 - Zahlungs- und Versandauswahlseite'], [8, '8 - Bestellabschluss'], [9, '9 - Bestellbestätigungsseite'], [10, '10 - kompletter Checkout'], [11, '11 - Bloglisting'], [12, '12 - Blogdetailseite'], [13, '13 - kompletter Blog'], [14, '14 - Landingpage'], [15, '15 - Merkzettel'], [16, '16 - Shopseiten'], [17, '17 - Formulare'], [18, '18 - Suchergebnisseite'], [19, '19 - Newsletterseite'], [20, '20 - Kundenkonto']]},
                        cssclass: { fieldLabel: '{s name=PromotionbannerDetailCssclass}Individuelle CSS - Klasse für den Promotionbanner{/s}', anchor: "50%"},
                        modalboxtimedelay: { fieldLabel: '{s name=PromotionbannerDetailModalboxtimedelay}Zeitverzögerung der Modalbox in Sekunden{/s}', anchor: "25%"}
                    }
                },
                {
                    title: '{s name=PromotionbannerDetailFieldsetTitleBackground}Allgemeine Einstellungen - Hintergrund{/s}',
                    layout: 'anchor',
                    columnWidth: 1.0,
                    fields: {
                        backgroundimage: { fieldLabel: '{s name=PromotionbannerDetailBackgroundimage}Hintergrundbild{/s}', xtype: 'mediafield'},
                        backgroundposition: { fieldLabel: '{s name=PromotionbannerDetailBackgroundposition}Ausrichtung des Hintergrundbildes{/s}', xtype: 'combo', mode: 'local', queryMode: 'local', triggerAction: 'all', forceSelection: true, editable: false, store: [[0, '0 - links oben'], [1, '1 - mittig oben'], [2, '2 - rechts oben'], [3, '3 - links mittig'], [4, '4 - mittig mittig'], [5, '5 - rechts mittig'], [6, '6 - links unten'], [7, '7 - mittig unten'], [8, '8 - rechts unten']]},
                        backgroundsize: { fieldLabel: '{s name=PromotionbannerDetailBackgroundsize}Abmessung des Hintergrundbildes{/s}', xtype: 'combo', mode: 'local', queryMode: 'local', triggerAction: 'all', forceSelection: true, editable: false, store: [[0, '0 - cover (behält Seitenverhältnis des Hintergrundbildes bei und skaliert es so, dass es den Hintergrund vollständig abdeckt)'], [1, '1 - contain (behält Seitenverhältnis des Hintergrundbildes bei und skaliert es so, dass es vollständig im Hintergrund enthalten ist)'], [2, '2 - auto (skaliert Hintergrundbild so in die entsprechende Richtung, dass das ursprüngliche Seitenverhältnis beibehalten wird)']]},
                        backgroundcolor: { fieldLabel: '{s name=PromotionbannerDetailBackgroundcolor}Hintergrundfarbe (Hexcode){/s}', anchor: "50%"},
                        backgroundopacity: { fieldLabel: '{s name=PromotionbannerDetailBackgroundopacity}Deckkraft des Hintergrunds{/s}', xtype: 'combo', mode: 'local', queryMode: 'local', triggerAction: 'all', forceSelection: true, editable: false, store: [[0, '0 - 100% (1)'], [1, '1 - 95% (0.95)'], [2, '2 - 90% (0.9)'], [3, '3 - 85% (0.85)'], [4, '4 - 80% (0.8)'], [5, '5 - 75% (0.75)'], [6, '6 - 70% (0.7)'], [7, '7 - 65% (0.65)'], [8, '8 - 60% (0.6)'], [9, '9 - 55% (0.55)'], [10, '10 - 50% (0.5)'], [11, '11 - 45% (0.45)'], [12, '12 - 40% (0.4)'], [13, '13 - 35% (0.35)'], [14, '14 - 30% (0.3)'], [15, '15 - 25% (0.25)'], [16, '16 - 20% (0.2)'], [17, '17 - 15% (0.15)'], [18, '18 - 10% (0.1)'], [19, '19 - 5% (0.05)']]}
                    }
                },
                {
                    title: '{s name=PromotionbannerDetailFieldsetTitlePercentage}Allgemeine Einstellungen - Prozentzahlbereichs{/s}',
                    layout: 'anchor',
                    columnWidth: 1.0,
                    fields: {
                        percentagebackgroundcolor: { fieldLabel: '{s name=PromotionbannerDetailPercentagebackgroundcolor}Hintergrundfarbe des Prozentzahlbereichs (Hexcode){/s}', anchor: "50%"},
                        percentagefontcolor: { fieldLabel: '{s name=PromotionbannerDetailPercentagefontcolor}Schriftfarbe der Prozentzahlbereichs (Hexcode){/s}', anchor: "25%"},
                        percentagecssclass: { fieldLabel: '{s name=PromotionbannerDetailPercentagecssclass}Individuelle CSS - Klasse des Prozentzahlbereichs{/s}', anchor: "50%"},
                        percentagealignment: { fieldLabel: '{s name=PromotionbannerDetailPercentagealignment}Inhaltsausrichtung{/s}', xtype: 'combo', mode: 'local', queryMode: 'local', triggerAction: 'all', forceSelection: true, editable: false, store: [[0, '0 - links'], [1, '1 - mittig'], [2, '2 - rechts']]},
                        percentagewidth: { fieldLabel: '{s name=PromotionbannerDetailPercentagewidth}Inhaltsbreite{/s}', xtype: 'combo', mode: 'local', queryMode: 'local', triggerAction: 'all', forceSelection: true, editable: false, store: [[0, '0 - auto'], [1, '1 - 5%'], [2, '2 - 10%'], [3, '3 - 15%'], [4, '4 - 20%'], [5, '5 - 25%'], [6, '6 - 30%'], [7, '7 - 33%'], [8, '8 - 35%'], [9, '9 - 40%'], [10, '10 - 45%'], [11, '11 - 50%'], [12, '12 - 55%'], [13, '13 - 60%'], [14, '14 - 65%'], [15, '15 - 66%'], [16, '16 - 70%'], [17, '17 - 75%'], [18, '18 - 80%'], [19, '19 - 85%'], [20, '20 - 90%'], [21, '21 - 95%'], [22, '22 - 100%']]},
                        percentagepadding: { fieldLabel: '{s name=PromotionbannerDetailPercentagepadding}Innenabstand (padding), z. B. "10px"{/s}', anchor: "50%"},
                        percentage: { fieldLabel: '{s name=PromotionbannerDetailPercentage}Text der Prozentzahl{/s}'}                        
                   }
                },
                {
                    title: '{s name=PromotionbannerDetailFieldsetTitleContent}Allgemeine Einstellungen - Inhaltsbereich{/s}',
                    layout: 'anchor',
                    columnWidth: 1.0,
                    fields: {
                        contentbackgroundcolor: { fieldLabel: '{s name=PromotionbannerDetailContentbackgroundcolor}Hintergrundfarbe des Inhaltsbereichs (Hexcode){/s}', anchor: "50%"},
                        contentpadding: { fieldLabel: '{s name=PromotionbannerDetailContentpadding}Innenabstand (padding), z. B. "10px"{/s}', anchor: "50%"},
                        contentcssclass: { fieldLabel: '{s name=PromotionbannerDetailContentcssclass}Individuelle CSS - Klasse des Inhaltsbereichs{/s}', anchor: "50%"},
                        headlinefontcolor: { fieldLabel: '{s name=PromotionbannerDetailHeadlinefontcolor}Schriftfarbe der Überschrift (Hexcode){/s}', anchor: "25%"},
                        headlinealignment: { fieldLabel: '{s name=PromotionbannerDetailHeadlinealignment}Inhaltsausrichtung der Überschrift{/s}', xtype: 'combo', mode: 'local', queryMode: 'local', triggerAction: 'all', forceSelection: true, editable: false, store: [[0, '0 - links'], [1, '1 - mittig'], [2, '2 - rechts']]},
                        headlinewidth: { fieldLabel: '{s name=PromotionbannerDetailHeadlinewidth}Inhaltsbreite der Überschrift{/s}', xtype: 'combo', mode: 'local', queryMode: 'local', triggerAction: 'all', forceSelection: true, editable: false, store: [[0, '0 - auto'], [1, '1 - 5%'], [2, '2 - 10%'], [3, '3 - 15%'], [4, '4 - 20%'], [5, '5 - 25%'], [6, '6 - 30%'], [7, '7 - 33%'], [8, '8 - 35%'], [9, '9 - 40%'], [10, '10 - 45%'], [11, '11 - 50%'], [12, '12 - 55%'], [13, '13 - 60%'], [14, '14 - 65%'], [15, '15 - 66%'], [16, '16 - 70%'], [17, '17 - 75%'], [18, '18 - 80%'], [19, '19 - 85%'], [20, '20 - 90%'], [21, '21 - 95%'], [22, '22 - 100%']]},
                        headline: { fieldLabel: '{s name=PromotionbannerDetailHeadline}Überschrift{/s}', xtype: 'textareafield'},
                        txtfontcolor: { fieldLabel: '{s name=PromotionbannerDetailTxtfontcolor}Schriftfarbe des Textes (Hexcode){/s}', anchor: "25%"},
                        txtalignment: { fieldLabel: '{s name=PromotionbannerDetailTxtalignment}Inhaltsausrichtung des Textes{/s}', xtype: 'combo', mode: 'local', queryMode: 'local', triggerAction: 'all', forceSelection: true, editable: false, store: [[0, '0 - links'], [1, '1 - mittig'], [2, '2 - rechts']]},
                        txtwidth: { fieldLabel: '{s name=PromotionbannerDetailTxtwidth}Inhaltsbreite des Textes{/s}', xtype: 'combo', mode: 'local', queryMode: 'local', triggerAction: 'all', forceSelection: true, editable: false, store: [[0, '0 - auto'], [1, '1 - 5%'], [2, '2 - 10%'], [3, '3 - 15%'], [4, '4 - 20%'], [5, '5 - 25%'], [6, '6 - 30%'], [7, '7 - 33%'], [8, '8 - 35%'], [9, '9 - 40%'], [10, '10 - 45%'], [11, '11 - 50%'], [12, '12 - 55%'], [13, '13 - 60%'], [14, '14 - 65%'], [15, '15 - 66%'], [16, '16 - 70%'], [17, '17 - 75%'], [18, '18 - 80%'], [19, '19 - 85%'], [20, '20 - 90%'], [21, '21 - 95%'], [22, '22 - 100%']]},
                        txt: { fieldLabel: '{s name=PromotionbannerDetailTxt}Text{/s}', xtype: 'textareafield'}    
                    }
                },
                {
                    title: '{s name=PromotionbannerDetailFieldsetTitleText}Allgemeine Einstellungen - Linkbereich{/s}',
                    layout: 'anchor',
                    columnWidth: 1.0,
                    fields: {
                        completelinking: { fieldLabel: '{s name=PromotionbannerDetailCompletelinking}Promotionbanner komplett verlinken{/s}', xtype: 'checkboxfield', inputValue: true, uncheckedValue: false},
                        linkbelowcontent: { fieldLabel: '{s name=PromotionbannerDetailLinkbelowcontent}Link unterhalb des Inhaltsbereichs{/s}', xtype: 'checkboxfield', inputValue: true, uncheckedValue: false},
                        linkbackgroundcolor: { fieldLabel: '{s name=PromotionbannerDetailLinkbackgroundcolor}Hintergrundfarbe des Linkbereichs (Hexcode){/s}', anchor: "50%"},
                        linkpadding: { fieldLabel: '{s name=PromotionbannerDetailLinkpadding}Innenabstand (padding), z. B. "10px"{/s}', anchor: "50%"},
                        target: { fieldLabel: '{s name=PromotionbannerDetailTarget}Auf eine JavaScript - Funktion verlinken (andernfalls auf eine Seite){/s}', xtype: 'checkboxfield', inputValue: true, uncheckedValue: false},
                        link: { fieldLabel: '{s name=PromotionbannerDetailLink}Ziel des Links{/s}', anchor: "50%"},
                        linkcssclass: { fieldLabel: '{s name=PromotionbannerDetailLinkcssclass}Individuelle CSS - Klasse für den Link{/s}', anchor: "50%"},
                        linktransparent: { fieldLabel: '{s name=PromotionbannerDetailLinktransparent}Transparenter Hintergrund des Links{/s}', xtype: 'checkboxfield', inputValue: true, uncheckedValue: false},
                        linkbgcolor: { fieldLabel: '{s name=PromotionbannerDetailLinkbgcolor}Hintergrundfarbe des Links (Hexcode){/s}', anchor: "50%"},
                        linkfontcolor: { fieldLabel: '{s name=PromotionbannerDetailLinkfontcolor}Schriftfarbe des Links (Hexcode){/s}', anchor: "25%"},
                        linkbordercolor: { fieldLabel: '{s name=PromotionbannerDetailLinkbordercolor}Rahmenfarbe des Links (Hexcode){/s}', anchor: "25%"},
                        linktext: { fieldLabel: '{s name=PromotionbannerDetailLinktext}Linktext, wenn eine Schaltfläche genutzt wird{/s}', anchor: "50%"},
                        linkalignment: { fieldLabel: '{s name=PromotionbannerDetailLinkalignment}Inhaltsausrichtung{/s}', xtype: 'combo', mode: 'local', queryMode: 'local', triggerAction: 'all', forceSelection: true, editable: false, store: [[0, '0 - links'], [1, '1 - mittig'], [2, '2 - rechts']]},
                        linkwidth: { fieldLabel: '{s name=PromotionbannerDetailLinkwidth}Inhaltsbreite{/s}', xtype: 'combo', mode: 'local', queryMode: 'local', triggerAction: 'all', forceSelection: true, editable: false, store: [[0, '0 - auto'], [1, '1 - 5%'], [2, '2 - 10%'], [3, '3 - 15%'], [4, '4 - 20%'], [5, '5 - 25%'], [6, '6 - 30%'], [7, '7 - 33%'], [8, '8 - 35%'], [9, '9 - 40%'], [10, '10 - 45%'], [11, '11 - 50%'], [12, '12 - 55%'], [13, '13 - 60%'], [14, '14 - 65%'], [15, '15 - 66%'], [16, '16 - 70%'], [17, '17 - 75%'], [18, '18 - 80%'], [19, '19 - 85%'], [20, '20 - 90%'], [21, '21 - 95%'], [22, '22 - 100%']]}
                    }
                },
                {
                    title: '{s name=PromotionbannerDetailFieldsetTitleDisplaydate}Allgemeine Einstellungen - Anzeigezeitraum{/s}',
                    layout: 'anchor',
                    columnWidth: 1.0,
                    fields: {
                        displaydatefrom: { fieldLabel: '{s name=PromotionbannerDetailDisplaydatefrom}Promotionbanner anzeigen von (im Datumsformat yyyy-mm-dd){/s}', anchor: "50%", xtype: 'datefield', format: 'Y-m-d'},
                        displaydateto: { fieldLabel: '{s name=PromotionbannerDetailDisplaydateto}Promotionbanner anzeigen bis (im Datumsformat yyyy-mm-dd){/s}', anchor: "50%", xtype: 'datefield', format: 'Y-m-d'}
                    }
                },
                {
                    title: '{s name=PromotionbannerDetailFieldsetTitleHideInResolutions}Ausblenden in den Auflösungen{/s}',
                    layout: 'anchor',
                    columnWidth: 1.0,
                    fields: {
                        hideinsmartphoneportrait: { fieldLabel: '{s name=PromotionbannerDetailHideinsmartphoneportrait}Smartphone-Hochformat{/s}', xtype: 'checkboxfield', inputValue: true, uncheckedValue: false},
                        hideinsmartphonelandscape: { fieldLabel: '{s name=PromotionbannerDetailHideinsmartphonelandscape}Smartphone-Querformat{/s}', xtype: 'checkboxfield', inputValue: true, uncheckedValue: false},
                        hideintabletportrait: { fieldLabel: '{s name=PromotionbannerDetailHideintabletportrait}Tablet-Hochformat{/s}', xtype: 'checkboxfield', inputValue: true, uncheckedValue: false},
                        hideintabletlandscape: { fieldLabel: '{s name=PromotionbannerDetailHideintabletlandscape}Tablet-Querformat{/s}', xtype: 'checkboxfield', inputValue: true, uncheckedValue: false},
                        hideindesktop: { fieldLabel: '{s name=PromotionbannerDetailHideindesktop}Desktop{/s}', xtype: 'checkboxfield', inputValue: true, uncheckedValue: false}
                    }
                }
            ]
        }
    }
});