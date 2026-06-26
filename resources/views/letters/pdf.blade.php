<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Resmi — {{ $letter->letter_number }}</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.5;
            color: #000;
            background-color: #fff;
            margin: 0;
            padding: 0.5in 0.75in;
        }
        /* Kop Surat */
        .header {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
            margin-bottom: 25px;
        }
        .header .company-name {
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0;
        }
        .header .company-sub {
            font-size: 10pt;
            font-style: italic;
            color: #444;
            margin: 2px 0 0 0;
        }
        .header .company-contact {
            font-size: 9pt;
            color: #666;
            margin: 2px 0 0 0;
        }
        /* Metadata Surat */
        .meta-table {
            width: 100%;
            margin-bottom: 25px;
            font-size: 11pt;
        }
        .meta-table td {
            vertical-align: top;
            padding: 2px 0;
        }
        .meta-table .label {
            width: 15%;
        }
        .meta-table .colon {
            width: 3%;
            text-align: center;
        }
        .meta-table .value {
            width: 42%;
        }
        .meta-table .date {
            width: 40%;
            text-align: right;
        }
        /* Subject/Perihal */
        .subject-title {
            font-weight: bold;
            text-transform: uppercase;
            text-align: center;
            margin-bottom: 25px;
            font-size: 13pt;
            text-decoration: underline;
        }
        /* Content Body */
        .content {
            text-align: justify;
            margin-bottom: 40px;
            font-size: 12pt;
        }
        .content p {
            margin: 0 0 15px 0;
            text-indent: 0.5in;
        }
        /* Tanda Tangan */
        .signature-container {
            width: 100%;
            margin-top: 50px;
        }
        .signature-box {
            width: 45%;
            display: inline-block;
            vertical-align: top;
        }
        .signature-box.right {
            float: right;
            text-align: right;
        }
        .signature-title {
            margin-bottom: 60px;
            font-size: 11pt;
        }
        .signature-name {
            font-weight: bold;
            text-decoration: underline;
            margin: 0;
        }
        .signature-role {
            font-size: 10pt;
            color: #333;
            margin: 2px 0 0 0;
        }
        .digital-stamp {
            display: inline-block;
            padding: 4px 8px;
            border: 1px solid #10b981;
            color: #10b981;
            font-size: 8pt;
            font-family: sans-serif;
            border-radius: 4px;
            margin-top: 5px;
            text-transform: uppercase;
        }
    </style>
</head>
<body>

    <!-- Kop Surat -->
    <div class="header">
        <h1 class="company-name">Smart HR Portal Indonesia</h1>
        <p class="company-sub">Sistem Digital Manajemen Kehadiran & Sumber Daya Manusia</p>
        <p class="company-contact">Gedung Pusat IT, Lt. 5, Jl. Sudirman No. 45, Jakarta Selatan | Telp: (021) 555-0192 | Email: info@smarthr.co.id</p>
    </div>

    <!-- Meta Details (No, Lampiran, Hal) & Date -->
    <table class="meta-table">
        <tr>
            <td class="label">Nomor</td>
            <td class="colon">:</td>
            <td class="value">{{ $letter->letter_number }}</td>
            <td class="date" rowspan="3">
                Jakarta, {{ $letter->created_at->isoFormat('D MMMM Y') }}
            </td>
        </tr>
        <tr>
            <td class="label">Lampiran</td>
            <td class="colon">:</td>
            <td class="value">{{ $letter->file_path ? '1 Lembar (PDF)' : '-' }}</td>
        </tr>
        <tr>
            <td class="label">Hal</td>
            <td class="colon">:</td>
            <td class="value"><strong>{{ $letter->letter_type_name }}</strong></td>
        </tr>
    </table>

    <!-- Subject Header -->
    <div class="subject-title">
        {{ $letter->subject }}
    </div>

    <!-- Letter Content -->
    <div class="content">
        @if($letter->content)
            {!! nl2br(e($letter->content)) !!}
        @else
            <p>Dengan hormat,</p>
            <p>Melalui surat ini kami menerangkan bahwa pengajuan surat dengan kategori <strong>{{ $letter->letter_type_name }}</strong> dengan nomor registrasi <strong>{{ $letter->letter_number }}</strong> telah diajukan secara resmi melalui sistem Smart HR Portal.</p>
            <p>Demikian surat keterangan ini dibuat agar dapat dipergunakan sebagaimana mestinya.</p>
        @endif
    </div>

    <!-- Signatures -->
    <div class="signature-container">
        <!-- Left: Applicant -->
        <div class="signature-box">
            <div class="signature-title">Hormat Kami,</div>
            <div class="signature-name">{{ $letter->user->name ?? 'Pemohon' }}</div>
            <div class="signature-role">{{ $letter->user->position->name ?? 'Karyawan' }}</div>
        </div>

        <!-- Right: Approver (if approved) -->
        @if($letter->status == 'approved')
            <div class="signature-box right" style="text-align: left; float: right;">
                <div class="signature-title">Disetujui Oleh,</div>
                <div class="signature-name">{{ $letter->approvedBy->name ?? 'HRD Manager' }}</div>
                <div class="signature-role">Divisi HRD / Administration</div>
                <div class="digital-stamp">
                    Signed Digitally via HR Portal<br>
                    Date: {{ $letter->approved_at ? $letter->approved_at->format('d/m/Y H:i') : '' }}
                </div>
            </div>
        @endif
    </div>

</body>
</html>
