{{-- resources/views/pdf/analyses/styles.blade.php --}}
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    @page {
        size: A4;
        margin: 12mm 16mm 12mm 16mm; /* ✅ Pages 2+ : 12mm partout */
    }

    @page:first {
        margin-top: 1mm;   /* ✅ Page 1 : PAS de marge en haut (juste logo) */
        margin-bottom: 12mm; /* ✅ Page 1 : MAIS 12mm en bas */
    }

    html, body { 
        margin: 0; 
        padding: 0;
    }

    body {
        font-family: Arial, sans-serif;
        font-size: 11pt;
        color: black;
        line-height: 1.08;
        orphans: 2;
        widows: 2;
    }

    /* ✅ Pagination tables */
    table { 
        page-break-inside: auto;
        border-collapse: collapse;
    }
    thead { 
        display: table-header-group;
    }
    tbody { 
        display: table-row-group;
    }
    tr { 
        page-break-inside: avoid;
        page-break-after: auto;
    }

    /* ✅ Espace en tête pages 2+ : 12mm (correspond au margin-top de @page) */
    .repeat-gap-cell {
        height: 12mm;  /* ✅ Crée l'espace de 12mm en haut des pages 2+ */
        line-height: 0;
        font-size: 0;
        padding: 0;
        border: 0;
        background: transparent;
    }

    /* ✅ Séparateurs ultra-serrés */
    .thead-sep { 
        padding: 0; 
        border: 0;
        margin: 0;
    }
    .thead-sep .red-line { 
        margin: 0.3px 0;
    }
    .thead-sep .spacing { 
        height: 0.5px;
    }

    /* Header logo */
    .header-section {
        width: 100%;
        display: block;
        margin: 0;
        padding: 0;
        line-height: 0;
        page-break-after: avoid;
    }

    .header-logo {
        width: 100%;
        max-height: 180px;
        object-fit: contain;
        margin: 0;
        padding: 0;
    }

    .content-wrapper { 
        padding: 0 35px;
    }

    /* ✅ Entre examens - ULTRA-COMPACT */
    .examen-wrapper {
        margin-bottom: 5px;
        page-break-inside: auto;
    }

    .examen-wrapper::before {
        content: none !important;
        display: none !important;
        height: 0 !important;
    }

    /* Mini separator bas */
    .mini-separator {
        page-break-inside: avoid;
        page-break-after: avoid;
        margin: 5px 0;
    }

    /* Information patient */
    .patient-info {
        margin: 8px 0;
        width: 100%;
        border-bottom: 1px solid #ddd;
        padding-bottom: 3px;
        display: table;
        table-layout: fixed;
        page-break-inside: avoid;
        page-break-after: avoid;
    }

    .patient-info-row { display: table-row; }
    .patient-info-left {
        display: table-cell;
        width: 50%;
        padding-right: 20px;
        vertical-align: top;
        line-height: 1.35;
    }
    .patient-info-right {
        display: table-cell;
        width: 50%;
        padding-left: 20px;
        vertical-align: top;
        line-height: 1.35;
    }

    .info-label { color: #374151; font-size: 9pt; }
    .info-value { color: #111827; font-size: 9pt; }
    .text-fine { font-weight: normal; font-size: 9pt; }
    .patient-name { font-weight: bold; }
    .medecin-name { font-weight: bold; }
    .bold { font-weight: bold; }

    /* Tables */
    .main-table {
        width: 100%;
        border-collapse: collapse;
        margin: 0;
        padding: 0;
    }

    .main-table td {
        padding: 0.3px 0;
        line-height: 1.08;
        vertical-align: middle;
    }

    .red-line {
        border-top: 0.5px solid #0b48eeff;
        margin: 0.5px 0;
        width: 100%;
    }

    /* Colonnes */
    .col-designation { 
        width: 40%; 
        text-align: left; 
        padding-right: 10px; 
        font-size: 10.5pt;
    }
    .col-resultat { 
        width: 20%; 
        text-align: left; 
        padding-left: 20px; 
        font-size: 9pt;
    }
    .col-valref { 
        width: 20%; 
        text-align: left; 
        padding-left: 20px; 
        font-size: 8pt;
    }
    .col-anteriorite { 
        width: 20%; 
        padding-left: 10px; 
        text-align: left; 
        font-size: 8pt;
    }

    /* Titres */
    .section-title {
        color: #042379ff;
        font-weight: bold;
        text-transform: uppercase;
        page-break-after: avoid;
    }

    .header-cols {
        font-size: 8pt;
        color: #000;
        font-style: italic;
    }

    /* Hiérarchie */
    .parent-row { 
        font-weight: bold;
        page-break-inside: avoid;
    }
    .child-row {
        page-break-inside: avoid;
    }
    .child-row td:first-child { 
        padding-left: 20px;
    }
    .subchild-row {
        page-break-inside: avoid;
    }
    .subchild-row td:first-child { 
        padding-left: 40px;
    }

    /* Antibiogrammes */
    .antibiogramme-header {
        page-break-inside: avoid !important;
        page-break-after: avoid !important;
    }
    
    .antibiogramme-header td {
        background-color: #f8f9fa;
        border-top: 1px solid #ccc;
        border-bottom: 1px solid #e9ecef;
        font-weight: bold;
        font-size: 10pt;
        color: #333;
        padding: 3px 0 2px 0;
    }

    .antibiogramme-row {
        page-break-inside: avoid;
    }
    
    .antibiogramme-row td {
        padding: 0.5px 0;
        font-size: 9pt;
        line-height: 1.20;
    }

    .antibiogramme-row td:first-child {
        color: #666;
        font-weight: 500;
    }

    .antibiotique-sensible { color: #28a745; }
    .antibiotique-resistant { color: #0542ebff; font-weight: bold; }
    .antibiotique-intermediaire { color: #ffc107; font-style: italic; }

    .indent-1 { padding-left: 20px !important; }
    .indent-2 { padding-left: 40px !important; }

    .signature {
        margin-top: 10px;
        text-align: right;
        padding-right: 10px;
        page-break-inside: avoid;
    }

    .spacing { height: 0.5px; }
    .pathologique { font-weight: bold; color: #000; }

    /* Conclusions */
    .conclusion-section {
        margin-top: 5px;
        margin-bottom: 4px;
        border-top: 1px solid #ddd;
        padding-top: 4px;
        page-break-inside: avoid;
    }

    .conclusion-title {
        font-weight: bold;
        font-size: 11pt;
        margin-bottom: 3px;
        color: #333;
    }

    .conclusion-content {
        font-size: 10pt;
        line-height: 1.30;
        text-align: justify;
        color: #000;
    }

    .conclusion-examen {
        margin-top: 4px;
        margin-bottom: 3px;
        page-break-inside: avoid;
    }

    .conclusion-examen-title {
        font-weight: bold;
        font-size: 10pt;
        margin-bottom: 2px;
        color: #666;
    }

    .conclusion-examen-content {
        font-size: 9.5pt;
        line-height: 1.20;
        text-align: justify;
        margin-left: 10px;
    }

    .conclusion-row {
        page-break-inside: avoid;
    }
    
    .conclusion-row td {
        padding: 1.5px 0;
        font-size: 9pt;
        color: #666;
        font-style: italic;
    }

    .keep-with-next {
        page-break-after: avoid !important;
    }
</style>