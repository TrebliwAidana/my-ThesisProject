<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Financial Report</title>

<style>
body {
    font-family: "Times New Roman", serif;
    font-size: 11pt;
    margin: 0;
    padding: 0;
}

.page {
    width: 100%;
    margin: 0 auto;
}

/* HEADER */
.header-table {
    width: 100%;
    text-align: center;
    margin-bottom: 5px;
}

.logo {
    width: 60px;
    height: 60px;
    border: 1px solid #000;
    border-radius: 50%;
    line-height: 60px;
    font-size: 8pt;
    margin: auto;
}

.divider {
    border-top: 1px solid #000;
    margin: 5px 0;
}

.office {
    text-align: center;
    font-weight: bold;
    margin: 8px 0;
}

/* TITLE */
.title {
    text-align: center;
    font-weight: bold;
    font-size: 13pt;
    margin: 10px 0 5px;
}

.center {
    text-align: center;
}

.underline {
    border-bottom: 1px solid #000;
    display: inline-block;
    min-width: 140px;
    padding: 0 5px;
}

.underline.short {
    min-width: 60px;
}

/* TABLE */
.financial-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
    margin-bottom: 15px;
}

.financial-table th,
.financial-table td {
    border: 1px solid #000;
    padding: 5px;
    vertical-align: top;
}

.financial-table th {
    text-align: center;
    font-weight: bold;
}

.financial-table td.amount {
    text-align: right;
    white-space: nowrap;
}

.row-sub {
    font-size: 9pt;
    font-style: italic;
    display: block;
    margin-top: 2px;
}

/* SIGNATURE - MODIFIED TO BRING NAME CLOSER TO UNDERLINE */
.signature-table {
    width: 100%;
    margin-top: 20px;
    margin-bottom: 15px;
    border-collapse: collapse;
}

.signature-table td {
    padding: 4px 6px;        /* reduced vertical padding */
    vertical-align: bottom;
}

.signature-line {
    border-bottom: 1px solid #000;
    height: 16px;            /* reduced from 24px */
}

.signature-line.has-name {
    text-align: center;
    font-weight: bold;
}

.sig-name {
    font-weight: bold;
    text-align: center;
    margin-bottom: 0;
    line-height: 1.2;
}

.sig-title {
    font-size: 10pt;
    font-weight: normal;
    text-align: center;
    margin-top: 2px;
}

/* RECEIPT */
.receipt {
    margin-top: 15px;
    font-size: 10.5pt;
    line-height: 1.5;
}

/* NOTE */
.note {
    margin-top: 12px;
    font-size: 9.5pt;
    font-style: italic;
    font-weight: bold;
    text-align: center;
}

/* FOOTER */
.footer {
    margin-top: 20px;
    border-top: 1px solid #000;
    padding-top: 8px;
    font-size: 9pt;
    text-align: center;
    line-height: 1.4;
}

.generated-date {
    text-align: center;
    font-size: 8pt;
    color: #555;
    margin-top: 6px;
}
</style>
</head>

<body>
<div class="page">

<!-- HEADER -->
<table class="header-table">
    <tr>
        <td width="20%">
            <div class="logo">VSU</div>
        </td>
        <td width="60%">
            <b>VSU INTEGRATED HIGH SCHOOL</b><br>
            Baybay City, Leyte
        </td>
        <td width="20%"></td>
    </tr>
</table>

<div class="divider"></div>
<div class="office">OFFICE OF THE VSUIHS GUIDANCE FACILITATOR</div>

<!-- TITLE -->
<div class="title">FINANCIAL REPORT</div>

<div class="center">
    of the <span class="underline">{{ $org_name }}</span><br>
    <small>Name of Organization/Club</small>
</div>

<div class="center">
    For the period 
    <span class="underline">{{ \Carbon\Carbon::parse($start_date)->format('F d, Y') }}</span>
    to
    <span class="underline">{{ \Carbon\Carbon::parse($end_date)->format('F d, Y') }}</span>
</div>

<!-- FINANCIAL TABLE -->
<table class="financial-table">
    <thead>
        <tr>
            <th>Details</th>
            <th>Amount (PHP)</th>
            <th>Remarks</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><b>A. Cash on Hand</b><br><span class="row-sub">(attach list)</span></td>
            <td class="amount">{{ number_format($income_total, 2) }}</td>
            <td></td>
        </tr>
        <tr>
            <td><b>B. Less: Expenses</b><br><span class="row-sub">(attach receipts)</span></td>
            <td class="amount">{{ number_format($expense_total, 2) }}</td>
            <td></td>
        </tr>
        <tr>
            <td><b>C. Total Income</b><br><span class="row-sub">(A minus B)</span></td>
            <td class="amount">{{ number_format($net_from_ops, 2) }}</td>
            <td></td>
        </tr>
        <tr>
            <td><b>D. Receivables</b><br><span class="row-sub">(attach list)</span></td>
            <td class="amount">{{ number_format($receivables, 2) }}</td>
            <td></td>
        </tr>
        <tr>
            <td><b>E. Previous Cash Deposited</b></td>
            <td class="amount">{{ number_format($prev_cash, 2) }}</td>
            <td></td>
        </tr>
        <tr style="font-weight:bold">
            <td><b>NET INCOME</b><br><span class="row-sub">(C + E)</span></td>
            <td class="amount"><b>{{ number_format($net_final, 2) }}</b></td>
            <td></td>
        </tr>
    </tbody>
</table>

<!-- SIGNATURES -->
<table class="signature-table">
    <!-- Treasurer (Certified True and Correct) -->
    <tr>
        <td colspan="3" style="text-align: center;"><b>Certified True and Correct:</b></td>
    </tr>
    <tr>
        <td width="33%"></td>
        <td width="33%" style="text-align: center; border-bottom: 1px solid #000; padding-bottom: 0; vertical-align: bottom;">
            <b style="display: block; margin-bottom: 2px;">{{ $treasurer_name ?? 'SHEERWINA MAE G. BALOTITE' }}</b>
        </td>
        <td width="33%"></td>
    </tr>
    <tr>
        <td colspan="3" style="text-align: center; padding-top: 2px;">Treasurer</td>
    </tr>
    <tr><td colspan="3" height="15"></td></tr>

    <!-- Auditor and President -->
    <tr>
        <td width="33%" style="text-align: center; border-bottom: 1px solid #000; padding-bottom: 0; vertical-align: bottom;">
            <b style="display: block; margin-bottom: 2px;">{{ $auditor_name ?? '_________________________' }}</b>
        </td>
        <td width="33%"></td>
        <td width="33%" style="text-align: center; border-bottom: 1px solid #000; padding-bottom: 0; vertical-align: bottom;">
            <b style="display: block; margin-bottom: 2px;">{{ $president_name ?? '_________________________' }}</b>
        </td>
    </tr>
    <tr>
        <td style="text-align: center; padding-top: 2px;">Auditor</td>
        <td></td>
        <td style="text-align: center; padding-top: 2px;">President</td>
    </tr>
    <tr><td colspan="3" height="15"></td></tr>

    <!-- Adviser and Guidance Facilitator -->
    <tr>
        <td width="33%" style="text-align: center; border-bottom: 1px solid #000; padding-bottom: 0; vertical-align: bottom;">
            <b style="display: block; margin-bottom: 2px;">{{ $adviser_name ?? '_________________________' }}</b>
        </td>
        <td width="33%"></td>
        <td width="33%" style="text-align: center; border-bottom: 1px solid #000; padding-bottom: 0; vertical-align: bottom;">
            <b style="display: block; margin-bottom: 2px;">{{ $guidance_name ?? 'NOEMI ELISA L. OQUIAS' }}</b>
        </td>
    </tr>
    <tr>
        <td style="text-align: center; padding-top: 2px;">Adviser</td>
        <td></td>
        <td style="text-align: center; padding-top: 2px;">Guidance Facilitator</td>
    </tr>
</table>

<!-- RECEIPT ACKNOWLEDGMENT (Treasurer) -->
<div class="receipt">
    Received the amount of 
    <span class="underline">{{ number_format($net_final, 2) }}</span>
    (PHP <span class="underline short">{{ number_format($net_final, 2) }}</span>)
    from the Organization Treasurer being designated as VSUIHS Treasurer on 
    <span class="underline short">{{ now()->format('m/d/Y') }}</span>.
</div>

<div class="center" style="margin-top: 15px;">
    <div style="display: inline-block; border-bottom: 1px solid #000; padding-bottom: 2px; margin-bottom: 2px;">
        <b>{{ $treasurer_name ?? 'SHEERWINA MAE G. BALOTITE' }}</b>
    </div>
    <br>
    VSUIHS Treasurer
</div>

<!-- NOTE -->
<div class="note">
    Note: To be submitted in TRIPLICATE 3 DAYS BEFORE scheduled quarterly examination.
</div>

<!-- FOOTER -->
<div class="footer">
    <b>VSU INTEGRATED HIGH SCHOOL</b><br>
    Visayas State University, Baybay City, Leyte<br>
    Email: jhs@vsu.edu.ph / integrated.hs@vsu.edu.ph<br>
    Website: www.vsu.edu.ph<br>
    Phone: +63 53 565 0600 Local 1074 (JHS) 1075 (SHS)
</div>

<div class="generated-date">
    Generated on {{ $generated_at->format('F d, Y \a\t h:i A') }}
</div>

</div>
</body>
</html>