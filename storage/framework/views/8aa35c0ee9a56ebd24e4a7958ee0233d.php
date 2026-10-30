<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <style>
        @page {
            margin: 0;
        }

        body {
            margin: 0;
            color: #53545a;
            font-family: DejaVu Sans, sans-serif;
        }

        .certificate {
            position: relative;
            height: 535pt;
            margin: 25pt;
            border: 1pt solid #b99a62;
            text-align: center;
        }

        .accent {
            position: absolute;
            width: 16pt;
            height: 100%;
            background: #1717a5;
        }

        .accent-left {
            top: 0;
            left: 0;
        }

        .accent-right {
            top: 0;
            right: 0;
            background: #078ca8;
        }

        .content {
            position: relative;
            padding: 28pt 52pt;
        }

        .title {
            margin: 0 0 14pt;
            color: #a17d40;
            font-family: DejaVu Serif, serif;
            font-size: 24pt;
            font-weight: bold;
            letter-spacing: 10pt;
        }

        .logo {
            width: 200pt;
            height: auto;
            margin: 0 auto 12pt;
        }

        .type {
            margin: 3pt 0 12pt;
            color: #a17d40;
            font-size: 12pt;
            font-weight: bold;
            letter-spacing: 1pt;
        }

        .intro,
        .description,
        .venue,
        .date {
            margin: 8pt 0;
            font-size: 13pt;
        }

        .participant {
            display: inline-block;
            min-width: 300pt;
            margin: 5pt 0 10pt;
            padding: 0 12pt 5pt;
            border-bottom: 1pt solid #a17d40;
            color: #a17d40;
            font-family: DejaVu Serif, serif;
            font-size: 20pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .topic {
            margin: 4pt 0;
            color: #4f5055;
            font-size: 18pt;
            font-weight: bold;
        }

        .signature {
            margin-top: 18pt;
            color: #a17d40;
            font-size: 12pt;
            font-weight: bold;
        }

        .signature-role {
            margin-top: 4pt;
            font-size: 10pt;
            letter-spacing: 1pt;
        }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="accent accent-left"></div>
        <div class="accent accent-right"></div>
        <div class="content">
            <h1 class="title">CERTIFICATE</h1>
            <img class="logo" src="<?php echo e($logo); ?>" alt="Summit Adyawinsa">
            <div class="type">OF PARTICIPATION</div>
            <p class="intro">This certificate is presented to:</p>
            <div class="participant"><?php echo e($certificate->nama_karyawan); ?></div>
            <p class="description">completed training with topic:</p>
            <p class="topic"><?php echo e($certificate->training_name); ?></p>
            <p class="venue">that held at PT Summit Adyawinsa Indonesia</p>
            <p class="date">Karawang, <?php echo e($certificateDate); ?></p>
            <div class="signature">
                <?php echo e($certificate->nara_sumber ?: 'Training Department'); ?>

                <div class="signature-role">TRAINER</div>
            </div>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views\page\training\training_certificate.blade.php ENDPATH**/ ?>